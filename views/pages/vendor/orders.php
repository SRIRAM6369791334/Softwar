<div class="orders">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1>Purchase Orders</h1>
            <p style="color: var(--text-dim);">Historical and active inventory requests from Supermarket OS branches.</p>
        </div>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>PO Number</th>
                    <th>Destination Branch</th>
                    <th>Total Value</th>
                    <th>Status</th>
                    <th>Date Created</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $po): ?>
                <tr>
                    <td style="font-weight: 600;"><?= $po['order_no'] ?></td>
                    <td><?= htmlspecialchars($po['branch_name']) ?></td>
                    <td>₹<?= number_format($po['total_amount'], 2) ?></td>
                    <td><span class="badge badge-<?= $po['status'] ?>"><?= ucfirst($po['status']) ?></span></td>
                    <td style="color: var(--text-dim);"><?= date('d M Y', strtotime($po['created_at'])) ?></td>
                    <td><a href="/vendor/orders/<?= $po['id'] ?>" class="btn" style="padding: 6px 12px;">View Details</a></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($orders)): ?>
                <tr><td colspan="6" style="text-align: center; color: var(--text-dim); padding: 40px;">No purchase orders available.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
