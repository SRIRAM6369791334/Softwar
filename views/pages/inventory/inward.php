<div style="max-width: 800px; margin: 0 auto;">
    <h2>Inward Stock (GRN Entry)</h2>
    
    <div class="card">
        <form action="/inventory/store" method="POST">
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                
                <div class="form-group" style="grid-column: span 3;">
                    <label>Select Product <span style="color:red">*</span></label>
                    <select name="product_id" required>
                        <option value="">-- Choose Item --</option>
                        <?php foreach ($products as $p): ?>
                            <option value="<?= $p['id'] ?>">
                                <?= htmlspecialchars($p['name']) ?> 
                                <?= $p['sku'] ? '('.$p['sku'].')' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Batch No.</label>
                    <input type="text" name="batch_no" required placeholder="e.g. BATCH-001">
                </div>

                <div class="form-group">
                    <label>Expiry Date</label>
                    <input type="date" name="expiry_date">
                </div>
                
                <div class="form-group">
                    <label>Quantity Inward</label>
                    <input type="number" name="quantity" step="0.001" required placeholder="0.00">
                </div>

                <div class="form-group">
                    <label>Purchase Price (Cost)</label>
                    <input type="number" name="purchase_price" step="0.01" required placeholder="0.00">
                </div>

                <div class="form-group">
                    <label>MRP</label>
                    <input type="number" name="mrp" step="0.01" required placeholder="0.00">
                </div>

                <div class="form-group">
                    <label>Sale Price</label>
                    <input type="number" name="sale_price" step="0.01" required placeholder="0.00" 
                           style="border-color: var(--accent-color); font-weight: bold;">
                </div>

            </div>

            <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 10px;">
                <a href="/products" class="btn" style="text-decoration: none; color: #8b949e;">Cancel</a>
                <button type="submit" class="btn btn-primary">Process Inward >></button>
            </div>
        </form>
    </div>
</div>
