<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Product Master</h2>
    <a href="/products/create" class="btn btn-primary">+ Add New Item</a>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>SKU / Barcode</th>
                <th>Product Name</th>
                <th>HSN</th>
                <th>Tax</th>
                <th>Unit</th>
                <th>Stock (Live)</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
            <tr>
                <td style="font-family: monospace; color: var(--accent-color);">
                    <?= $p['sku'] ?? '<span style="color:#666">N/A</span>' ?>
                </td>
                <td>
                    <div style="font-weight: bold;"><?= htmlspecialchars($p['name']) ?></div>
                </td>
                <td><?= $p['hsn_code'] ?></td>
                <td><?= $p['tax_name'] ?></td>
                <td><?= $p['unit'] ?></td>
                <td>
                    <?php 
                        $stock = $p['total_stock'] ?? 0;
                        $color = $stock > $p['min_stock_alert'] ? 'var(--success)' : '#ff9e00';
                        if ($stock == 0) $color = 'var(--danger)';
                    ?>
                    <span style="color: <?= $color ?>; font-weight: bold;">
                        <?= number_format($stock, 2) ?>
                    </span>
                </td>
                <td>
                    <a href="/products/settings/<?= $p['id'] ?>" class="btn" style="padding: 4px 8px; font-size: 0.7rem; text-decoration: none; background: var(--accent-dim); color: var(--accent-color);">Settings</a>
                    <button class="btn" style="padding: 4px 8px; font-size: 0.7rem;">History</button>
                    <button class="btn" style="padding: 4px 8px; font-size: 0.7rem;">Edit</button>
                </td>
            </tr>
            <?php endforeach; ?>
            
            <?php if (empty($products)): ?>
                <tr><td colspan="7" style="text-align:center; padding: 2rem; color: #888;">No products found in database.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
