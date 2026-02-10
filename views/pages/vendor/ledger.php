<div class="ledger">
    <h1>Supply Chain Ledger</h1>
    <p style="color: var(--text-dim); margin-bottom: 2rem;">Financial transparency: Your dues, payments, and credit balance.</p>

    <div class="stat-grid" style="margin-bottom: 2rem;">
        <div class="card" style="border-left: 4px solid var(--accent);">
            <div style="font-size: 0.85rem; color: var(--text-dim);">Total Value Supplied</div>
            <div style="font-size: 2rem; font-weight: 600; margin: 5px 0;">₹<?= number_format($summary['total_owed'] ?? 0, 2) ?></div>
        </div>
        <div class="card" style="border-left: 4px solid #3fb950;">
            <div style="font-size: 0.85rem; color: var(--text-dim);">Total Payments Received</div>
            <div style="font-size: 2rem; font-weight: 600; margin: 5px 0;">₹<?= number_format($summary['total_paid'] ?? 0, 2) ?></div>
        </div>
        <div class="card" style="border-left: 4px solid #e3b341;">
            <div style="font-size: 0.85rem; color: var(--text-dim);">Outstanding Balance</div>
            <div style="font-size: 2rem; font-weight: 600; margin: 5px 0;">₹<?= number_format(($summary['total_owed'] ?? 0) - ($summary['total_paid'] ?? 0), 2) ?></div>
        </div>
    </div>

    <div class="card">
        <h3>Transaction History</h3>
        <table style="margin-top: 1rem;">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Ref # / PO #</th>
                    <th style="text-align: right;">Credit (Dues)</th>
                    <th style="text-align: right;">Debit (Payment)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($entries as $e): ?>
                <tr>
                    <td style="color: var(--text-dim);"><?= date('d M Y', strtotime($e['created_at'])) ?></td>
                    <td><?= htmlspecialchars($e['description']) ?></td>
                    <td><?= $e['order_no'] ?: $e['reference_no'] ?: '-' ?></td>
                    <td style="text-align: right; color: var(--accent-color);"><?= $e['type'] == 'credit' ? '₹' . number_format($e['amount'], 2) : '-' ?></td>
                    <td style="text-align: right; color: #3fb950;"><?= $e['type'] == 'debit' ? '₹' . number_format($e['amount'], 2) : '-' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
