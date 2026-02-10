<div class="container-fluid">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>⚖️ Tax Rules</h1>
        <a href="/admin/taxes/create" class="btn btn-primary">+ New Tax Group</a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Rate (%)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($taxes as $tax): ?>
                    <tr>
                        <td>#<?= $tax['id'] ?></td>
                        <td><strong><?= htmlspecialchars($tax['name']) ?></strong></td>
                        <td><?= number_format($tax['rate'], 2) ?>%</td>
                        <td>
                            <a href="/admin/taxes/edit/<?= $tax['id'] ?>" class="btn btn-sm btn-mode">Edit</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php if(empty($taxes)): ?>
                <div class="p-4 text-center text-muted">No tax groups defined.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
