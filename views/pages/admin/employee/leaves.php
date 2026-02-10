<h1>Leave Requests</h1>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Type</th>
                <th>Dates</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($leaves as $leave): ?>
            <tr>
                <td>
                    <?= htmlspecialchars($leave['full_name']) ?><br>
                    <span style="font-size: 0.8rem; color: #8b949e;">@<?= $leave['username'] ?></span>
                </td>
                <td><?= ucfirst($leave['type']) ?></td>
                <td>
                    <?= date('d M', strtotime($leave['start_date'])) ?> - <?= date('d M', strtotime($leave['end_date'])) ?><br>
                    <span style="font-size: 0.8rem; color: #8b949e;"><?= $leave['days'] ?> Days</span>
                </td>
                <td><?= htmlspecialchars($leave['reason']) ?></td>
                <td>
                    <span style="padding: 2px 8px; border-radius: 4px; 
                        background: <?= $leave['status'] == 'approved' ? 'rgba(46, 160, 67, 0.2)' : ($leave['status'] == 'rejected' ? 'rgba(218, 54, 51, 0.2)' : 'rgba(227, 179, 65, 0.2)') ?>; 
                        color: <?= $leave['status'] == 'approved' ? '#2ea043' : ($leave['status'] == 'rejected' ? '#da3633' : '#e3b341') ?>">
                        <?= ucfirst($leave['status']) ?>
                    </span>
                </td>
                <td>
                    <?php if($leave['status'] == 'pending'): ?>
                    <form action="/admin/employee/leaves/update/<?= $leave['id'] ?>" method="POST" style="display: inline-block;">
                        <input type="hidden" name="status" value="approved">
                        <button class="btn" style="background: #238636; padding: 4px 8px; font-size: 0.8rem;">✓</button>
                    </form>
                    <form action="/admin/employee/leaves/update/<?= $leave['id'] ?>" method="POST" style="display: inline-block;">
                        <input type="hidden" name="status" value="rejected">
                        <button class="btn" style="background: #da3633; padding: 4px 8px; font-size: 0.8rem;">✗</button>
                    </form>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
