<div style="max-width: 700px; margin: 0 auto;">
    <h2>Add New Product</h2>
    
    <div class="card">
        <form action="/products/store" method="POST">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                
                <div class="form-group" style="grid-column: span 2;">
                    <label>Product Name <span style="color:red">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. Amul Gold Milk 500ml">
                </div>

                <div class="form-group">
                    <label>Barcode / SKU</label>
                    <input type="text" name="sku" placeholder="Scan or Type...">
                    <small style="color: #666; font-size: 0.7rem;">Leave empty to auto-generate</small>
                </div>

                <div class="form-group">
                    <label>HSN Code</label>
                    <input type="text" name="hsn_code" placeholder="e.g. 0401">
                </div>

                <div class="form-group">
                    <label>Unit Type</label>
                    <select name="unit">
                        <option value="Nos">Numbers (Nos)</option>
                        <option value="Kg">Kilogram (Kg)</option>
                        <option value="Ltr">Liters (Ltr)</option>
                        <option value="Pkt">Packet (Pkt)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tax Group</label>
                    <select name="tax_group_id">
                        <?php foreach ($tax_groups as $tg): ?>
                            <option value="<?= $tg['id'] ?>"><?= $tg['name'] ?> (<?= $tg['percentage'] ?>%)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Low Stock Alert Qty</label>
                    <input type="number" name="min_stock_alert" value="10">
                </div>

            </div>

            <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 10px;">
                <a href="/products" class="btn" style="text-decoration: none; color: #8b949e;">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Product</button>
            </div>
        </form>
    </div>
</div>
