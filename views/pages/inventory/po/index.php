<div class="po-management">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1>Purchase Orders</h1>
            <p style="color: var(--text-dim);">Manage stock procurement from external suppliers.</p>
        </div>
        <a href="/inventory/po/create" class="btn btn-primary" style="text-decoration: none;">+ Create New PO</a>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>PO Number</th>
                    <th>Vendor</th>
                    <th>Total Value</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $po): ?>
                <tr>
                    <td style="font-weight: 600;"><?= $po['order_no'] ?></td>
                    <td><?= htmlspecialchars($po['vendor_name']) ?></td>
                    <td>₹<?= number_format($po['total_amount'], 2) ?></td>
                    <td>
                        <span class="badge badge-<?= $po['status'] ?>"><?= ucfirst(str_replace('_', ' ', $po['status'])) ?></span>
                    </td>
                    <td><?= htmlspecialchars($po['created_by_name']) ?></td>
                    <td style="color: var(--text-dim);"><?= date('d M Y', strtotime($po['created_at'])) ?></td>
                    <td>
                        <?php if ($po['status'] == 'pending'): ?>
                            <a href="/inventory/po/status/<?= $po['id'] ?>/ordered" class="btn" style="padding: 4px 8px; font-size: 0.75rem; background: #238636;">Place Order</a>
                        <?php endif; ?>
                        <button class="btn" style="padding: 4px 8px; font-size: 0.75rem; background: var(--bg-hover);">View</button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($orders)): ?>
                <tr><td colspan="7" style="text-align: center; color: var(--text-dim); padding: 40px;">No purchase orders found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .badge-pending { background: #382d0b; color: #e3b341; }
    .badge-ordered { background: #112d19; color: #3fb950; }
    .badge-delivered { background: #112d19; color: #3fb950; }
</style>
