<div class="admin-quotations">
    <h1>Pending Price Quotations</h1>
    <p style="color: var(--text-dim); margin-bottom: 2rem;">Suppliers are requesting price adjustments. Approve or reject to update procurement costs.</p>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Vendor</th>
                    <th>Product</th>
                    <th>Current Price</th>
                    <th>Proposed Price</th>
                    <th>Change %</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quotations as $q): 
                    $diff = $q['proposed_price'] - $q['current_price'];
                    $pct = $q['current_price'] > 0 ? ($diff / $q['current_price']) * 100 : 0;
                ?>
                <tr>
                    <td style="font-weight: 600;"><?= htmlspecialchars($q['vendor_name']) ?></td>
                    <td><?= htmlspecialchars($q['product_name']) ?></td>
                    <td>₹<?= number_format($q['current_price'], 2) ?></td>
                    <td style="color: var(--accent-color); font-weight: 600;">₹<?= number_format($q['proposed_price'], 2) ?></td>
                    <td style="color: <?= $pct > 0 ? '#f85149' : '#3fb950' ?>;">
                        <?= $pct > 0 ? '+' : '' ?><?= number_format($pct, 1) ?>%
                    </td>
                    <td>
                        <a href="/admin/vendor/quotations/approve/<?= $q['id'] ?>" class="btn" style="padding: 4px 8px; font-size: 0.75rem; background: #238636;">Approve</a>
                        <button class="btn" style="padding: 4px 8px; font-size: 0.75rem; background: #da3633;">Reject</button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($quotations)): ?>
                <tr><td colspan="6" style="text-align: center; color: var(--text-dim); padding: 40px;">No pending price proposals.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
