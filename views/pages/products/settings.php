<div class="product-settings">
    <div style="margin-bottom: 2rem;">
        <a href="/products" style="color: #8b949e; text-decoration: none;">← Back to Products</a>
        <h1>Branch Settings: <?= htmlspecialchars($product['name']) ?></h1>
        <p style="color: #8b949e;">Configure thresholds and pricing for the current branch only</p>
    </div>

    <div class="card" style="max-width: 600px; border-top: 3px solid var(--accent-color);">
        <form action="/products/settings/<?= $product['id'] ?>" method="POST">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label>Min Stock Alert</label>
                    <input type="number" name="min_stock_alert" value="<?= $settings['min_stock_alert'] ?? $product['min_stock_alert'] ?>" required>
                    <small style="color: #8b949e;">Notify when stock falls below this</small>
                </div>
                <div class="form-group">
                    <label>Reorder Level</label>
                    <input type="number" name="reorder_level" value="<?= $settings['reorder_level'] ?? 20 ?>" required>
                    <small style="color: #8b949e;">Auto-reorder trigger level</small>
                </div>
            </div>

            <div class="form-group" style="margin-top: 1rem;">
                <label>Default Sale Price (In this Branch)</label>
                <div style="display: flex; align-items: center; gap: 10px; background: #0d1117; border: 1px solid var(--border-color); padding: 5px 15px; border-radius: 4px;">
                    <span style="color: var(--accent-color);">₹</span>
                    <input type="number" name="default_sale_price" step="0.01" value="<?= $settings['default_sale_price'] ?>" style="border: none; background: transparent; margin: 0; padding: 5px 0;">
                </div>
                <small style="color: #8b949e;">Overrides system default for new batches in this branch</small>
            </div>

            <div style="margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                <button type="submit" class="btn btn-primary" style="width: 100%;">Save Branch Specific Settings</button>
            </div>
        </form>
    </div>

    <div style="margin-top: 1.5rem; padding: 1rem; background: var(--accent-dim); border-radius: 6px; max-width: 600px;">
        <small style="color: var(--accent-color);">
            <strong>Note:</strong> These settings only apply to <strong><?= \App\Core\Auth::getBranchName() ?></strong>. 
            Other branches maintain their own threshold configurations for this product.
        </small>
    </div>
</div>
