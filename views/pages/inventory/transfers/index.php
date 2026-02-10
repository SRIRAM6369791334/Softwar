<div class="stock-transfers">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Stock Transfers</h1>
        <a href="/inventory/transfers/create" class="btn btn-primary" style="text-decoration: none;">+ Move Stock to Another Branch</a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div style="background: var(--success); color: #fff; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem;">
            <?= htmlspecialchars($_GET['success']) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Ref #</th>
                    <th>Product</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Qty</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transfers as $trf): ?>
                <tr>
                    <td><small style="color: #8b949e;"><?= $trf['transfer_no'] ?></small></td>
                    <td>
                        <strong><?= htmlspecialchars($trf['product_name']) ?></strong><br>
                        <small style="color: #8b949e;"><?= $trf['sku'] ?></small>
                    </td>
                    <td><?= htmlspecialchars($trf['from_branch']) ?></td>
                    <td><?= htmlspecialchars($trf['to_branch']) ?></td>
                    <td style="font-weight: bold;"><?= number_format($trf['qty'], 2) ?></td>
                    <td>
                        <?php 
                        $colors = ['pending' => '#f39c12', 'completed' => 'var(--success)', 'cancelled' => 'var(--danger)'];
                        $color = $colors[$trf['status']] ?? '#8b949e';
                        ?>
                        <span style="background: <?= $color ?>22; color: <?= $color ?>; padding: 4px 8px; border-radius: 4px; border: 1px solid <?= $color ?>; text-transform: uppercase; font-size: 0.7rem; font-weight: bold;">
                            <?= $trf['status'] ?>
                        </span>
                    </td>
                    <td><?= date('M d, H:i', strtotime($trf['created_at'])) ?></td>
                    <td>
                        <?php 
                        $currentBranch = \App\Core\Auth::getCurrentBranch();
                        if ($trf['status'] == 'pending' && $trf['to_branch_id'] == $currentBranch): ?>
                            <a href="/inventory/transfers/receive/<?= $trf['id'] ?>" class="btn btn-primary" style="font-size: 0.8rem; padding: 5px 10px; text-decoration: none;" onclick="return confirm('Confirm receipt of these items?')">Receive</a>
                        <?php elseif ($trf['status'] == 'requested' && $trf['from_branch_id'] == $currentBranch): ?>
                            <a href="/inventory/transfers/fulfill/<?= $trf['id'] ?>" class="btn btn-primary" style="font-size: 0.8rem; padding: 5px 10px; text-decoration: none; background: #2ea043; border-color: #2ea043;">Fulfill</a>
                        <?php else: ?>
                            <span style="color: #30363d;">--</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($transfers)): ?>
                <tr><td colspan="8" style="text-align: center; color: #8b949e; padding: 2rem;">No transfers recorded for this branch yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
