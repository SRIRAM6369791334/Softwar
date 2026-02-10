<div class="forecasting">
    <h1>Stock Forecasting (7-Day Run-out)</h1>
    <p style="color: var(--text-dim); margin-bottom: 2rem;">Products predicted to run out within the next 7 days based on recent sales velocity.</p>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Current Stock</th>
                    <th>Avg. Daily Sales</th>
                    <th>Est. Days Left</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($forecast as $item): 
                    $daysLeft = $item['avg_daily_sales'] > 0 ? floor($item['current_stock'] / $item['avg_daily_sales']) : 'N/A';
                ?>
                <tr>
                    <td style="font-weight: 600;"><?= htmlspecialchars($item['name']) ?></td>
                    <td style="color: var(--text-dim);"><?= $item['sku'] ?></td>
                    <td style="color: <?= $daysLeft <= 2 ? '#f85149' : 'inherit' ?>;"><?= (float)$item['current_stock'] ?> <?= $item['unit'] ?></td>
                    <td><?= number_format($item['avg_daily_sales'], 2) ?> / day</td>
                    <td>
                        <strong style="color: <?= $daysLeft <= 2 ? '#f85149' : '#e3b341' ?>;">
                            <?= $daysLeft ?> days
                        </strong>
                    </td>
                    <td><a href="/vendor/orders" class="btn" style="padding: 4px 10px; font-size: 0.8rem;">Check Active POs</a></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($forecast)): ?>
                <tr><td colspan="6" style="text-align: center; color: var(--text-dim); padding: 40px;">Your stock levels look healthy for the next 7 days!</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
