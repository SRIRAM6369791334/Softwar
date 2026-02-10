<div class="create-po">
    <div style="margin-bottom: 2rem;">
        <a href="/inventory/po" style="color: var(--text-dim); text-decoration: none;">← Back to Purchase Orders</a>
        <h1>Create Purchase Order</h1>
    </div>

    <form action="/inventory/po/store" method="POST">
        <div class="card" style="max-width: 800px;">
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label>Target Supplier / Vendor</label>
                <select name="vendor_id" required style="width: 100%; background: var(--bg-color); border: 1px solid var(--border); padding: 10px; border-radius: 4px; color: #fff;">
                    <option value="">-- Select Vendor --</option>
                    <?php foreach ($vendors as $v): ?>
                        <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="items-container">
                <h3 style="margin: 2rem 0 1rem 0; border-bottom: 1px solid var(--border); padding-bottom: 10px;">Order Items</h3>
                <div class="po-item" style="display: grid; grid-template-columns: 2fr 1fr 1fr 40px; gap: 1rem; margin-bottom: 1rem; align-items: flex-end;">
                    <div>
                        <label style="font-size: 0.8rem; color: var(--text-dim);">Product</label>
                        <select name="product_id[]" required style="width: 100%; background: var(--bg-color); border: 1px solid var(--border); padding: 10px; border-radius: 4px; color: #fff;">
                            <option value="">-- Select Product --</option>
                            <?php foreach ($products as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?> (<?= $p['sku'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; color: var(--text-dim);">Quantity</label>
                        <input type="number" name="qty[]" step="0.01" required style="width: 100%; background: var(--bg-color); border: 1px solid var(--border); padding: 10px; border-radius: 4px; color: #fff;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; color: var(--text-dim);">Est. Unit Price</label>
                        <input type="number" name="price[]" step="0.01" required style="width: 100%; background: var(--bg-color); border: 1px solid var(--border); padding: 10px; border-radius: 4px; color: #fff;">
                    </div>
                    <div></div>
                </div>
            </div>

            <button type="button" onclick="addItem()" class="btn" style="background: var(--accent-dim); color: var(--accent-color); font-size: 0.8rem; padding: 5px 15px; margin-top: 10px;">+ Add Another Item</button>

            <div style="margin-top: 3rem; text-align: right;">
                <button type="submit" class="btn btn-primary" style="padding: 12px 30px; font-weight: 600;">Finalize & Save Purchase Order</button>
            </div>
        </div>
    </form>
</div>

<script>
function addItem() {
    const container = document.getElementById('items-container');
    const firstItem = document.querySelector('.po-item');
    const newItem = firstItem.cloneNode(true);
    
    // Clear values
    newItem.querySelectorAll('input').forEach(i => i.value = '');
    newItem.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
    
    // Add delete button
    newItem.querySelector('div:last-child').innerHTML = '<button type="button" onclick="this.closest(\'.po-item\').remove()" style="background: none; border: none; color: var(--danger); font-size: 1.2rem; cursor: pointer;">×</button>';
    
    container.appendChild(newItem);
}
</script>
