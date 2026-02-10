<div class="reorder-report">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1>Automated Reorder Alerts</h1>
            <p style="color: #8b949e;">Products currently below minimum stock thresholds for this branch</p>
        </div>
        <div style="background: var(--danger); color: #fff; padding: 10px 20px; border-radius: 8px; font-weight: bold;">
            <?= count($reorder_list) ?> Items Need Reordering
        </div>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Product Details</th>
                    <th>Alert Level</th>
                    <th>Current Stock</th>
                    <th>Shortage</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reorder_list as $item): 
                    $shortage = $item['alert_level'] - $item['current_stock'];
                    $stockPercent = ($item['current_stock'] / $item['alert_level']) * 100;
                ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($item['name']) ?></strong><br>
                        <small style="color: #8b949e;"><?= $item['sku'] ?></small>
                    </td>
                    <td><?= number_format($item['alert_level'], 2) ?> <?= $item['unit'] ?></td>
                    <td style="color: var(--danger); font-weight: bold;"><?= number_format($item['current_stock'], 2) ?> <?= $item['unit'] ?></td>
                    <td>
                        <span style="color: var(--danger); font-weight: bold;">-<?= number_format($shortage, 2) ?></span>
                    </td>
                    <td style="width: 150px;">
                        <div style="background: #30363d; height: 6px; border-radius: 3px; overflow: hidden; margin-top: 5px;">
                            <div style="background: var(--danger); width: <?= min(100, $stockPercent) ?>%; height: 100%;"></div>
                        </div>
                        <small style="color: #8b949e; font-size: 0.7rem;"><?= round($stockPercent) ?>% of threshold left</small>
                    </td>
                    <td>
                        <a href="/inventory/inward?product_id=<?= $item['id'] ?>" class="btn btn-primary" style="font-size: 0.75rem; padding: 5px 10px; text-decoration: none;">Inward Stock</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($reorder_list)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 3rem; color: #8b949e;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">✅</div>
                        <strong>All Clear!</strong><br>
                        All products in this branch are above their minimum stock levels.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .reorder-report h1 {
        margin: 0;
        background: linear-gradient(90deg, #fff, var(--danger));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        letter-spacing: -1px;
    }
</style>
