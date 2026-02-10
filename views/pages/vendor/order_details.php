<div class="order-details">
    <div style="margin-bottom: 2rem;">
        <a href="/vendor/orders" style="color: var(--text-dim); text-decoration: none;">← Back to Orders</a>
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 1rem;">
            <div>
                <h1 style="margin: 0;"><?= $order['order_no'] ?></h1>
                <p style="color: var(--text-dim); margin: 5px 0;">Destination: <strong><?= htmlspecialchars($order['branch_name']) ?></strong></p>
            </div>
            <div style="text-align: right;">
                <span class="badge badge-<?= $order['status'] ?>" style="font-size: 1rem; padding: 8px 15px;"><?= ucfirst($order['status']) ?></span>
                <p style="color: var(--text-dim); font-size: 0.85rem; margin-top: 10px;">Created: <?= date('d M Y, H:i', strtotime($order['created_at'])) ?></p>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <div class="card">
            <h3>Requested Items</h3>
            <table style="margin-top: 1rem;">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th style="text-align: right;">Est. Price</th>
                        <th style="text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td style="font-weight: 600;"><?= htmlspecialchars($item['product_name']) ?></td>
                        <td style="color: var(--text-dim);"><?= $item['sku'] ?></td>
                        <td><?= (float)$item['qty'] ?></td>
                        <td><?= $item['unit'] ?></td>
                        <td style="text-align: right;">₹<?= number_format($item['estimated_price'], 2) ?></td>
                        <td style="text-align: right; font-weight: 600;">₹<?= number_format($item['qty'] * $item['estimated_price'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" style="text-align: right; border: none; padding-top: 20px;">Order Total:</th>
                        <th style="text-align: right; border: none; padding-top: 20px; font-size: 1.2rem; color: #fff;">₹<?= number_format($order['total_amount'], 2) ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="document-upload">
            <div class="card" style="margin-bottom: 1.5rem;">
                <h4>Order Management</h4>
                <div style="margin-top: 1rem;">
                    <form action="/vendor/orders/backorder/<?= $order['id'] ?>" method="POST">
                        <label style="font-size: 0.8rem; color: var(--text-dim);">Dispatch Status:</label>
                        <select name="status" onchange="this.form.submit()" style="width: 100%; padding: 8px; background: var(--bg-color); border: 1px solid var(--border); color: #fff; margin-top: 5px;">
                            <option value="all_active" <?= $order['backorder_status'] == 'all_active' ? 'selected' : '' ?>>All Items Shipping</option>
                            <option value="partial_backorder" <?= $order['backorder_status'] == 'partial_backorder' ? 'selected' : '' ?>>Partial Backorder</option>
                            <option value="full_backorder" <?= $order['backorder_status'] == 'full_backorder' ? 'selected' : '' ?>>Full Backorder</option>
                        </select>
                    </form>
                </div>
            </div>

            <div class="card" style="border-top: 3px solid var(--accent); margin-bottom: 1.5rem;">
                <h3>Invoice PDF</h3>
                <p style="color: var(--text-dim); font-size: 0.85rem; line-height: 1.5;">
                    Please upload the final invoice for this shipment. 
                </p>

                <?php if ($order['invoice_pdf']): ?>
                    <div style="background: rgba(88, 166, 255, 0.1); padding: 15px; border-radius: 8px; border: 1px solid var(--border); margin: 1rem 0;">
                        <span style="display: block; font-size: 0.8rem; color: var(--text-dim);">Current File:</span>
                        <a href="/uploads/invoices/<?= $order['invoice_pdf'] ?>" target="_blank" style="color: var(--accent); font-weight: 600; text-decoration: none; word-break: break-all;">
                            📄 <?= $order['invoice_pdf'] ?>
                        </a>
                    </div>
                <?php endif; ?>

                <form action="/vendor/orders/upload/<?= $order['id'] ?>" method="POST" enctype="multipart/form-data" style="margin-top: 1rem;">
                    <div style="border: 1px dashed var(--border); padding: 20px; text-align: center; border-radius: 8px; margin-bottom: 1rem;">
                        <input type="file" name="invoice" accept="application/pdf" required style="width: 100%;">
                    </div>
                    <button type="submit" class="btn" style="width: 100%;">Upload & Confirm</button>
                </form>
            </div>

            <?php if ($order['status'] !== 'delivered'): ?>
            <div class="card">
                <h3>🤝 Confirm Delivery (GRN)</h3>
                <form action="/vendor/orders/grn/<?= $order['id'] ?>" method="POST" enctype="multipart/form-data" style="margin-top: 1rem;">
                    <div style="margin-bottom: 1rem;">
                        <label style="font-size: 0.8rem; color: var(--text-dim);">Receiver Signature (Name)</label>
                        <input type="text" name="signature" required placeholder="E.g. John Doe" style="width: 100%; padding: 8px; background: var(--bg-color); border: 1px solid var(--border); color: #fff;">
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="font-size: 0.8rem; color: var(--text-dim);">Delivery Photo (Optional)</label>
                        <input type="file" name="grn_photo" accept="image/*" style="font-size: 0.8rem; width: 100%;">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Confirm Stock Inward</button>
                </form>
            </div>
            <?php else: ?>
            <div class="card" style="border-left: 4px solid #3fb950;">
                <h3 style="color: #3fb950;">✔ Delivery Confirmed</h3>
                <p style="font-size: 0.85rem; color: var(--text-dim);">Signed By: <strong><?= htmlspecialchars($order['grn_signature']) ?></strong></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
