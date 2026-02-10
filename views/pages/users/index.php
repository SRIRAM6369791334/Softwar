<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Employee Management</h2>
    <a href="/users/create" class="btn btn-primary">+ New Employee</a>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Contact (Masked)</th>
                <th>Username</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td>#<?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['full_name']) ?></td>
                <td>
                    <div><small>📧 <?= \App\Core\Helpers::mask($user['email'] ?? '', 'email') ?></small></div>
                    <div><small>📱 <?= \App\Core\Helpers::mask($user['phone'] ?? '', 'phone') ?></small></div>
                </td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td>
                    <span style="padding: 2px 6px; background: rgba(255,255,255,0.1); border-radius: 4px; font-size: 0.8rem;">
                        <?= htmlspecialchars($user['role_name']) ?>
                    </span>
                </td>
                <td style="color: <?= $user['status'] == 'active' ? 'var(--success)' : 'var(--danger)' ?>">
                    <?= strtoupper($user['status']) ?>
                </td>
                <td>
                    <!-- TODO: Edit/Delete -->
                    <button class="btn" style="padding: 4px 8px; font-size: 0.7rem;">Edit</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
