<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Current Inventory (Batches)</h2>
    <div>
        <a href="/inventory/transfers" class="btn btn-primary" style="margin-right: 10px; background: rgba(0, 243, 255, 0.05);">Stock Transfers</a>
        <a href="/inventory/inward" class="btn btn-primary">+ Inward New Stock</a>
    </div>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Batch No</th>
                <th>Expiry</th>
                <th>MRP</th>
                <th>Sale Price</th>
                <th>Qty Available</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($batches as $b): ?>
            <tr>
                <td>
                    <b><?= htmlspecialchars($b['product_name']) ?></b><br>
                    <small style="color:#666"><?= $b['sku'] ?></small>
                </td>
                <td><?= $b['batch_no'] ?></td>
                <td>
                    <?php if ($b['expiry_date']): ?>
                        <?= $b['expiry_date'] ?>
                    <?php else: ?>
                        <span style="color:#666">-</span>
                    <?php endif; ?>
                </td>
                <td><?= $b['mrp'] ?></td>
                <td style="color: var(--accent-color); font-weight: bold;"><?= $b['sale_price'] ?></td>
                <td>
                    <span style="font-size: 1.1rem;"><?= number_format($b['stock_qty'], 2) ?></span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
