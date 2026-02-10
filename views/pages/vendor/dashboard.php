<div class="dashboard">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Supplier Dashboard</h1>
        <div style="color: var(--text-dim);"><?= date('l, d M Y') ?></div>
    </div>

    <!-- Broadcast Alerts -->
    <?php foreach ($broadcasts as $b): ?>
    <div class="card" style="border-left: 4px solid var(--accent); margin-bottom: 1.5rem; background: rgba(88, 166, 255, 0.05);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <h4 style="color: var(--accent-color); margin: 0;">📣 <?= htmlspecialchars($b['title']) ?></h4>
            <span style="font-size: 0.75rem; color: var(--text-dim);"><?= date('d M', strtotime($b['created_at'])) ?></span>
        </div>
        <p style="margin: 10px 0 0 0; font-size: 0.9rem;"><?= nl2br(htmlspecialchars($b['message'])) ?></p>
    </div>
    <?php endforeach; ?>

    <p style="color: var(--text-dim); margin-bottom: 2rem;">Overview of your performance and pending actions.</p>

    <div class="stat-grid" style="margin-bottom: 3rem;">
        <div class="card" style="border-left: 4px solid var(--accent);">
            <div style="font-size: 0.85rem; color: var(--text-dim); text-transform: uppercase;">Pending Orders</div>
            <div style="font-size: 2.5rem; font-weight: 600; margin: 10px 0;"><?= $pending_count ?></div>
            <div style="font-size: 0.8rem; color: var(--accent);">Action required for delivery</div>
        </div>

        <div class="card" style="border-left: 4px solid #3fb950;">
            <div style="font-size: 0.85rem; color: var(--text-dim); text-transform: uppercase;">Delivered (MTD)</div>
            <div style="font-size: 2.5rem; font-weight: 600; margin: 10px 0; color: #3fb950;"><?= $delivered_month ?></div>
            <div style="font-size: 0.8rem; color: var(--text-dim);">Current month performance</div>
        </div>
    </div>

    <div class="card">
        <h3>Recent Purchase Orders</h3>
        <table style="margin-top: 1rem;">
            <thead>
                <tr>
                    <th>PO Number</th>
                    <th>Branch</th>
                    <th>Total Value</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_orders as $po): ?>
                <tr>
                    <td style="font-weight: 600;"><?= $po['order_no'] ?></td>
                    <td><?= htmlspecialchars($po['branch_name']) ?></td>
                    <td>₹<?= number_format($po['total_amount'], 2) ?></td>
                    <td><span class="badge badge-<?= $po['status'] ?>"><?= ucfirst($po['status']) ?></span></td>
                    <td style="color: var(--text-dim); font-size: 0.9rem;"><?= date('d M Y', strtotime($po['created_at'])) ?></td>
                    <td><a href="/vendor/orders/<?= $po['id'] ?>" class="btn" style="padding: 4px 10px; font-size: 0.8rem;">View</a></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($recent_orders)): ?>
                <tr><td colspan="6" style="text-align: center; color: var(--text-dim); padding: 40px;">No recent orders found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
