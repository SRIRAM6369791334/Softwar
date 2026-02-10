<h1>My Roster (This Week)</h1>

<?php if(empty($shifts)): ?>
    <div class="card" style="text-align: center; color: #8b949e;">
        No shifts assigned for this week.
    </div>
<?php else: ?>
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Day</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($shifts as $shift): ?>
                <tr>
                    <td><?= date('l', strtotime($shift['start_time'])) ?></td>
                    <td><?= date('d M', strtotime($shift['start_time'])) ?></td>
                    <td>
                        <span style="color: var(--accent-color);">
                            <?= date('h:i A', strtotime($shift['start_time'])) ?>
                        </span>
                        - 
                        <?= date('h:i A', strtotime($shift['end_time'])) ?>
                    </td>
                    <td><?= htmlspecialchars($shift['notes'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<h2 style="margin-top: 30px;">Open Shifts Marketplace 🛒</h2>
<p style="color: #8b949e; font-size: 0.9rem;">Tap 'Claim' to add these shifts to your schedule.</p>

<?php if(empty($openShifts)): ?>
    <div class="card" style="text-align: center; color: #8b949e;">
        No open shifts available right now.
    </div>
<?php else: ?>
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($openShifts as $shift): ?>
                <tr>
                    <td><?= date('D, M d', strtotime($shift['start_time'])) ?></td>
                    <td>
                        <?= date('h:i A', strtotime($shift['start_time'])) ?> - 
                        <?= date('h:i A', strtotime($shift['end_time'])) ?>
                    </td>
                    <td>
                        <a href="/employee/shifts/claim/<?= $shift['id'] ?>" class="btn" style="padding: 5px 10px; font-size: 0.8rem; background: #2f81f7;">Claim</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
