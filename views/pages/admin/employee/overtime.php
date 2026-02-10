<div class="container-fluid">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>⏰ Overtime Approvals</h1>
        <a href="/admin/employee/overtime/report" class="btn btn-mode">📊 Monthly Report</a>
    </div>

    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body p-0">
            <table>
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Date</th>
                        <th>Hours</th>
                        <th>Rate</th>
                        <th>Reason</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($records as $rec): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($rec['full_name']) ?></strong></td>
                        <td><?= date('M d, Y', strtotime($rec['date'])) ?></td>
                        <td><?= number_format($rec['overtime_hours'], 2) ?> hrs</td>
                        <td><?= $rec['overtime_rate'] ?>x</td>
                        <td><?= htmlspecialchars($rec['reason'] ?? 'Auto-detected') ?></td>
                        <td>
                            <a href="/admin/employee/overtime/approve/<?= $rec['id'] ?>" 
                               class="btn btn-sm btn-primary"
                               onclick="return confirm('Approve this overtime?')">✓ Approve</a>
                            <a href="/admin/employee/overtime/reject/<?= $rec['id'] ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Reject this overtime?')">✗ Reject</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php if(empty($records)): ?>
                <div class="p-4 text-center text-muted">No pending overtime requests</div>
            <?php endif; ?>
        </div>
    </div>
</div>
