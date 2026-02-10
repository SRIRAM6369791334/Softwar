<?php

namespace App\Services;

use App\Core\Database;
use App\Core\Auth;

class CheckoutService
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Process the POS Transaction: Validation, Stock Deduction, Invoice Creation.
     * 
     * @param array $items List of items with batch_id, qty
     * @param int $userId Cashier User ID
     * @param int $branchId Branch ID
     * @return array Result ['status' => 'success', 'invoice_no' => '...']
     * @throws \Exception
     */
    public function processTransaction(array $items, int $userId, int $branchId, array $input = []): array
    {
        if (empty($items)) {
            throw new \Exception("Cart is empty");
        }

        $result = $this->db->transactional(function($db) use ($items, $userId, $branchId, $input) {
            $pdo = $db->getConnection();
            
            // 0. DATE GUARD: Prevent future-dated transactions
            $now = new \DateTime();
            if (isset($input['date']) && new \DateTime($input['date']) > $now->modify('+1 minute')) {
                throw new \Exception("Cannot process transaction with future date.");
            }
            $now = new \DateTime(); // Reset
            // 1. Calculate Totals Server-Side & Lock Rows
            $calculatedSubTotal = 0;
            $calculatedTaxTotal = 0;
            $finalItems = [];

            // Prepared Statements for high efficiency and locking
            // Changed from product_batches to product_variants
            $stockCheckStmt = $pdo->prepare("
                SELECT pv.id, pv.selling_price as sale_price, pv.current_stock as stock_qty, pv.product_id, pv.tax_slab_id, tg.percentage as tax_percent 
                FROM product_variants pv
                JOIN products p ON pv.product_id = p.id
                LEFT JOIN tax_groups tg ON pv.tax_slab_id = tg.id
                WHERE pv.id = ? AND p.branch_id = ?
                FOR UPDATE
            ");
            
            $deductStockStmt = $pdo->prepare("UPDATE product_variants SET current_stock = current_stock - ?, version_id = version_id + 1 WHERE id = ? AND current_stock >= ?");

            foreach ($items as $item) {
                $qty = (float) ($item['qty'] ?? 1);
                $batchId = (int) ($item['batch_id'] ?? 0); // This is now variant_id

                if ($qty <= 0) throw new \Exception("Invalid quantity for items.");

                // Fetch & Lock (Pessimistic)
                $stockCheckStmt->execute([$batchId, $branchId]);
                $batchData = $stockCheckStmt->fetch();

                if (!$batchData) throw new \Exception("Product variant not found or unauthorized.");

                // Deduct Stock with Version Update (Atomic)
                $deductStockStmt->execute([$qty, $batchId, $qty]);
                if ($deductStockStmt->rowCount() === 0) {
                    throw new \Exception("Insufficient stock or race condition detected for variant $batchId.");
                }

                // Calculate Line
                $unitPrice = (float) $batchData['sale_price'];
                $taxPercent = (float) $batchData['tax_percent'];
                
                $lineBase = $unitPrice * $qty;
                $taxAmount = ($lineBase * $taxPercent) / 100;
                $lineTotal = round($lineBase + $taxAmount, 2);

                $calculatedSubTotal += $lineBase;
                $calculatedTaxTotal += $taxAmount;

                $finalItems[] = [
                    'product_id' => $batchData['product_id'],
                    'batch_id' => $batchId, // variant_id
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                    'tax_percent' => $taxPercent,
                    'tax_amount' => $taxAmount,
                    'total' => $lineTotal
                ];
            }

            // 2. Handle Discounts
            $discountAmount = (float) ($input['discount_amount'] ?? 0);
            $maxDiscountPercent = 10; 
            $maxAllowedDiscount = ($calculatedSubTotal * $maxDiscountPercent) / 100;
            
            if ($discountAmount > $maxAllowedDiscount) {
                throw new \Exception("Discount exceeds maximum allowed limit.");
            }

            // 3. Thread-Safe Invoice Numbering (Sequential)
            $db->query("UPDATE invoice_sequences SET last_val = last_val + 1 WHERE branch_id = ? FOR UPDATE", [$branchId]);
            $seq = $db->query("SELECT last_val, prefix FROM invoice_sequences WHERE branch_id = ?", [$branchId])->fetch();
            $invNo = $seq['prefix'] . '-' . str_pad($seq['last_val'], 8, '0', STR_PAD_LEFT);

            $taxTotal = round($calculatedTaxTotal, 2);
            $grandTotal = round(($calculatedSubTotal - $discountAmount) + $taxTotal, 2);

            // SECURITY: Final Server-Side Total Validation (Prevent tampering from front-end)
            if (isset($input['grand_total']) && abs((float)$input['grand_total'] - $grandTotal) > 0.01) {
                throw new \Exception("Financial integrity check failed: Total mismatch.");
            }

            $db->query(
                "INSERT INTO invoices (user_id, invoice_no, sub_total, tax_total, discount_amount, grand_total, payment_mode, branch_id) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                [$userId, $invNo, $calculatedSubTotal, $taxTotal, $discountAmount, $grandTotal, 'cash', $branchId]
            );
            $invoiceId = $db->lastInsertId();

            // 4. Insert Items
            $insertStmt = $pdo->prepare("
                INSERT INTO invoice_items (invoice_id, product_id, batch_id, qty, unit_price, tax_percent, tax_amount, total)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            foreach ($finalItems as $fItem) {
                $insertStmt->execute([
                    $invoiceId, $fItem['product_id'], $fItem['batch_id'], $fItem['qty'],
                    $fItem['unit_price'], $fItem['tax_percent'], $fItem['tax_amount'], $fItem['total']
                ]);
            }

            // 5. Audit & Activity Monitoring (Deferred to post-commit logic or handled here if safe)
            $db->query(
                "INSERT INTO audit_logs (user_id, action, description, branch_id) VALUES (?, ?, ?, ?)",
                [$userId, 'POS_CHECKOUT', "Processed POS transaction $invNo for amount $grandTotal", $branchId]
            );

            // Return results for post-transaction activities
            return [
                'status' => 'success',
                'invoice_no' => $invNo,
                'invoice_id' => $invoiceId,
                'items' => $finalItems,
                'grand_total' => $grandTotal
            ];
        });

        // Phase 8: High-Value Transaction Alert [#59] ($1000+)
        if ($result['grand_total'] >= 1000) {
            (new \App\Core\ActivityMonitor())->logAdminAction(
                $userId, 
                'RISK_ALERT', 
                'HIGH_VALUE_TRANSACTION', 
                "Transaction {$result['invoice_no']} exceeds \$1000 limit (Total: \${$result['grand_total']})"
            );
        }

        // 6. Post-Transaction Activities (Outside the transaction to prevent locking bottlenecks)
        \App\Core\Automation::trigger('pos_transaction_completed', [
            'invoice_no' => $result['invoice_no'],
            'items' => $result['items'],
            'user_id' => $userId,
            'branch_id' => $branchId
        ]);

        (new \App\Core\ActivityMonitor())->monitorTransaction([
            'user_id' => $userId,
            'branch_id' => $branchId,
            'invoice_no' => $result['invoice_no'],
            'grand_total' => $result['grand_total'],
            'type' => 'checkout'
        ]);

        return [
            'status' => 'success',
            'invoice_no' => $result['invoice_no'],
            'invoice_id' => $result['invoice_id']
        ];
    }
}
