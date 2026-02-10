<h1>Vendor Management</h1>

<div style="margin-bottom: 20px;">
    <a href="/admin/vendors/create" class="btn btn-primary">➕ Add New Vendor</a>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($vendors as $vendor): ?>
            <tr>
                <td><?= htmlspecialchars($vendor['name']) ?></td>
                <td><?= htmlspecialchars($vendor['email']) ?></td>
                <td><?= htmlspecialchars($vendor['phone']) ?></td>
                <td>
                    <span style="padding: 2px 8px; border-radius: 4px; background: <?= $vendor['is_active'] ? 'rgba(46, 160, 67, 0.2)' : 'rgba(218, 54, 51, 0.2)' ?>; color: <?= $vendor['is_active'] ? '#2ea043' : '#da3633' ?>">
                        <?= $vendor['is_active'] ? 'Active' : 'Inactive' ?>
                    </span>
                </td>
                <td>
                    <button class="btn" style="padding: 4px 8px; font-size: 0.8rem;">Edit</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
