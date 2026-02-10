<div style="max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2>Invoice Details</h2>
        <a href="/reports/daybook" class="btn" style="text-decoration: none; color: #8b949e;">← Back to Day Book</a>
    </div>

    <div class="card">
        <!-- Invoice Header -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid #30363d;">
            <div>
                <h3 style="margin: 0; color: var(--accent-color);"><?= $invoice['invoice_no'] ?></h3>
                <p style="color: #888; margin: 5px 0;">Date: <?= date('d M Y, h:i A', strtotime($invoice['created_at'])) ?></p>
                <p style="margin: 5px 0;">Cashier: <strong><?= $invoice['cashier_name'] ?></strong></p>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 0.9rem; color: #888;">Grand Total</div>
                <div style="font-size: 3rem; font-weight: bold; color: var(--success);">₹<?= number_format($invoice['grand_total'], 2) ?></div>
                <div style="color: #888; font-size: 0.85rem;">Payment: <?= strtoupper($invoice['payment_mode']) ?></div>
            </div>
        </div>

        <!-- Line Items -->
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Batch</th>
                    <th>Unit Price</th>
                    <th>Qty</th>
                    <th>Tax</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $idx => $item): ?>
                <tr>
                    <td><?= $idx + 1 ?></td>
                    <td>
                        <strong><?= htmlspecialchars($item['product_name']) ?></strong><br>
                        <small style="color: #666;"><?= $item['sku'] ?></small>
                    </td>
                    <td><?= $item['batch_no'] ?></td>
                    <td>₹<?= number_format($item['unit_price'], 2) ?></td>
                    <td><?= $item['qty'] ?></td>
                    <td>₹<?= number_format($item['tax_amount'], 2) ?> <small>(<?= $item['tax_percent'] ?>%)</small></td>
                    <td style="font-weight: bold;">₹<?= number_format($item['total'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot style="border-top: 2px solid var(--accent-color);">
                <tr>
                    <td colspan="6" style="text-align: right; font-weight: bold;">Sub Total:</td>
                    <td style="font-weight: bold;">₹<?= number_format($invoice['sub_total'], 2) ?></td>
                </tr>
                <tr>
                    <td colspan="6" style="text-align: right; font-weight: bold;">Tax Total:</td>
                    <td style="font-weight: bold;">₹<?= number_format($invoice['tax_total'], 2) ?></td>
                </tr>
                <?php if ($invoice['discount_total'] > 0): ?>
                <tr>
                    <td colspan="6" style="text-align: right; font-weight: bold; color: #d29922;">Discount:</td>
                    <td style="font-weight: bold; color: #d29922;">-₹<?= number_format($invoice['discount_total'], 2) ?></td>
                </tr>
                <?php endif; ?>
                <tr style="font-size: 1.2rem;">
                    <td colspan="6" style="text-align: right; font-weight: bold; color: var(--success);">GRAND TOTAL:</td>
                    <td style="font-weight: bold; color: var(--success);">₹<?= number_format($invoice['grand_total'], 2) ?></td>
                </tr>
            </tfoot>
        </table>

        <div style="margin-top: 2rem; text-align: center;">
            <button class="btn btn-primary" onclick="window.print()">🖨 Print Invoice</button>
        </div>
    </div>
</div>
