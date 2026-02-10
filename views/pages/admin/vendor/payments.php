<div class="admin-payments">
    <h1>Record Supplier Payment</h1>
    <p style="color: var(--text-dim); margin-bottom: 2rem;">Log payments made to vendors to maintain their ledger accuracy.</p>

    <div class="card" style="max-width: 600px;">
        <form action="/admin/vendor/payments" method="POST">
            <div class="form-group" style="margin-bottom: 1rem;">
                <label>Vendor</label>
                <select name="vendor_id" required style="width: 100%; padding: 10px; background: var(--bg-color); border: 1px solid var(--border); color: #fff;">
                    <?php foreach ($vendors as $v): ?>
                        <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label>Amount (₹)</label>
                <input type="number" name="amount" step="0.01" required style="width: 100%; padding: 10px; background: var(--bg-color); border: 1px solid var(--border); color: #fff;">
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label>Reference # / Transaction ID</label>
                <input type="text" name="reference_no" style="width: 100%; padding: 10px; background: var(--bg-color); border: 1px solid var(--border); color: #fff;">
            </div>
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label>Internal Note</label>
                <input type="text" name="description" style="width: 100%; padding: 10px; background: var(--bg-color); border: 1px solid var(--border); color: #fff;">
            </div>
            <button type="submit" class="btn" style="width: 100%; background: #238636;">Confirm Payment Receipt</button>
        </form>
    </div>
</div>
