<div class="container-fluid">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>🔄 Open Shifts</h1>
        <a href="/admin/employee/open-shifts/create" class="btn btn-primary">+ Post Open Shift</a>
    </div>

    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body p-0">
            <table>
                <thead>
                    <tr>
                        <th>Shift Time</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Claimed By</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($shifts as $shift): ?>
                    <tr>
                        <td>
                            <strong><?= date('M d, Y H:i', strtotime($shift['start_time'])) ?></strong>
                            <br>
                            <small>to <?= date('H:i', strtotime($shift['end_time'])) ?></small>
                        </td>
                        <td><?= ucfirst($shift['type']) ?></td>
                        <td>
                            <?php if($shift['claimed_by']): ?>
                                <span style="color: var(--success)">✓ Claimed</span>
                            <?php else: ?>
                                <span style="color: var(--accent-color)">◯ Available</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $shift['claimed_by_name'] ?? '-' ?></td>
                        <td><?= htmlspecialchars($shift['notes']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php if(empty($shifts)): ?>
                <div class="p-4 text-center text-muted">No open shifts posted</div>
            <?php endif; ?>
        </div>
    </div>
</div>
