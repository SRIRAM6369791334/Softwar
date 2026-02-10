<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Product Performance Analysis</h2>
    <div>
        <input type="date" id="startDate" value="<?= $startDate ?>" style="padding: 8px; background: #0d1117; color: #fff; border: 1px solid var(--border-color);">
        <input type="date" id="endDate" value="<?= $endDate ?>" style="padding: 8px; background: #0d1117; color: #fff; border: 1px solid var(--border-color);">
        <button class="btn btn-primary" onclick="filterReport()" style="padding: 8px 16px; margin-left: 10px;">Load</button>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
    <!-- Top Sellers -->
    <div class="card">
        <h3 style="color: var(--success);">🏆 Top 20 Best Sellers</h3>
        <table>
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Product</th>
                    <th>Qty Sold</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topSellers as $idx => $item): ?>
                <tr>
                    <td style="color: var(--accent-color); font-weight: bold;">#<?= $idx + 1 ?></td>
                    <td>
                        <strong><?= htmlspecialchars($item['name']) ?></strong><br>
                        <small style="color: #666;"><?= $item['sku'] ?></small>
                    </td>
                    <td><?= number_format($item['total_qty'], 2) ?></td>
                    <td style="color: var(--success); font-weight: bold;">₹<?= number_format($item['total_revenue'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($topSellers)): ?>
                    <tr><td colspan="4" style="text-align:center; padding: 2rem; color: #888;">No sales in selected period</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Slow Movers -->
    <div class="card">
        <h3 style="color: #ff9e00;">⚠️ Slow Moving Stock</h3>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Stock</th>
                    <th>Sold (Period)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($slowMovers as $item): ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($item['name']) ?></strong><br>
                        <small style="color: #666;"><?= $item['sku'] ?></small>
                    </td>
                    <td style="color: #ff9e00;"><?= number_format($item['current_stock'], 2) ?></td>
                    <td style="color: var(--danger);"><?= number_format($item['qty_sold'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($slowMovers)): ?>
                    <tr><td colspan="3" style="text-align:center; padding: 2rem; color: #888;">All products moving well!</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function filterReport() {
        const start = document.getElementById('startDate').value;
        const end = document.getElementById('endDate').value;
        window.location.href = `/reports/top-products?start=${start}&end=${end}`;
    }
</script>
