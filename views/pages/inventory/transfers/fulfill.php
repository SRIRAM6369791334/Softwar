<div class="fulfill-request">
    <div style="margin-bottom: 2rem;">
        <a href="/inventory/transfers" style="color: #8b949e; text-decoration: none;">← Back to Transfers</a>
        <h1>Fulfill Stock Request</h1>
        <p style="color: #8b949e;">Fulfilling request <strong><?= $transfer['transfer_no'] ?></strong> from <strong><?= htmlspecialchars($transfer['requester_branch']) ?></strong></p>
    </div>

    <div class="card" style="max-width: 600px; border-top: 3px solid #2ea043;">
        <form action="/inventory/transfers/fulfill/<?= $transfer['id'] ?>" method="POST">
            <div style="background: var(--accent-dim); padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem;">
                <h4 style="margin:0; color: var(--accent-color);">Requested Item</h4>
                <div style="font-size: 1.2rem; margin: 10px 0;">
                    <?= htmlspecialchars($transfer['product_name']) ?> (Qty: <?= $transfer['qty'] ?>)
                </div>
                <small style="color: #8b949e;">SKU: <?= $transfer['sku'] ?></small>
            </div>

            <div class="form-group">
                <label>Select Batch to Fulfill from</label>
                <select name="batch_id" required>
                    <option value="">-- Select Local Batch --</option>
                    <?php foreach ($batches as $batch): ?>
                        <option value="<?= $batch['id'] ?>">Batch: <?= $batch['batch_no'] ?> | Stock: <?= $batch['stock_qty'] ?> | Exp: <?= $batch['expiry_date'] ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (empty($batches)): ?>
                    <div style="color: var(--danger); margin-top: 10px;">⚠️ You have no stock of this item in your branch!</div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Quantity to Send</label>
                <input type="number" name="qty" value="<?= $transfer['qty'] ?>" step="0.01" max="<?= $transfer['qty'] ?>" required>
                <small style="color: #8b949e;">You can send partial stock if needed (updates request total)</small>
            </div>

            <div style="margin-top: 2rem;">
                <button type="submit" class="btn btn-primary" style="width: 100%; <?= empty($batches) ? 'opacity:0.5; pointer-events:none' : '' ?>">Approve & Dispatch</button>
            </div>
        </form>
    </div>
</div>

<style>
    .btn-primary { background: #2ea043; border-color: #2ea043; }
    .btn-primary:hover { background: #3fb950; }
</style>
