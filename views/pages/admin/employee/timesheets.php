<h1>Weekly Timesheets (Week of <?= date('M d, Y', strtotime($week)) ?>)</h1>
<div class="no-print" style="margin-bottom: 20px;">
    <button onclick="window.print()" class="btn btn-primary">🖨️ Print Timesheets</button>
</div>

<style>
    @media print {
        .no-print, .sidebar, .top-bar { display: none !important; }
        .content-area { padding: 0 !important; margin: 0 !important; background: white !important; color: black !important; }
        .site-card { border: 1px solid #ccc !important; box-shadow: none !important; break-inside: avoid; page-break-inside: avoid; }
        body { background: white !important; color: black !important; font-size: 10pt; }
        table { border-collapse: collapse; width: 100%; border: 1px solid #000; }
        th, td { border: 1px solid #000; padding: 5px; }
    }
    .sheet-card {
        background: white;
        color: black;
        padding: 20px;
        margin-bottom: 30px;
        border-radius: 8px;
        page-break-inside: avoid;
    }
</style>

<?php foreach($report as $userId => $data): ?>
<div class="sheet-card site-card">
    <div style="display: flex; justify-content: space-between; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px;">
        <div>
            <h2 style="margin: 0;">TIMESHEET</h2>
            <div>Supermarket OS</div>
        </div>
        <div style="text-align: right;">
            <div><strong>Employee:</strong> <?= htmlspecialchars($data['name']) ?></div>
            <div><strong>Week:</strong> <?= $week ?></div>
        </div>
    </div>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <thead>
            <tr style="background: #eee;">
                <th style="border: 1px solid #000; padding: 8px;">Date</th>
                <th style="border: 1px solid #000; padding: 8px;">In</th>
                <th style="border: 1px solid #000; padding: 8px;">Out</th>
                <th style="border: 1px solid #000; padding: 8px;">Hours</th>
                <th style="border: 1px solid #000; padding: 8px;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data['logs'] as $log): ?>
            <tr>
                <td style="border: 1px solid #000; padding: 8px;"><?= date('D, M d', strtotime($log['date'])) ?></td>
                <td style="border: 1px solid #000; padding: 8px;"><?= date('H:i', strtotime($log['clock_in'])) ?></td>
                <td style="border: 1px solid #000; padding: 8px;"><?= $log['clock_out'] ? date('H:i', strtotime($log['clock_out'])) : '-' ?></td>
                <td style="border: 1px solid #000; padding: 8px;"><?= number_format($log['total_hours'], 2) ?></td>
                <td style="border: 1px solid #000; padding: 8px;">
                    <?= ucfirst($log['status']) ?>
                    <?php if($log['is_overtime']): ?>
                        <span style="font-weight: bold;">(OT: <?= round($log['overtime_minutes']) ?>m)</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <tr style="background: #eee; font-weight: bold;">
                <td colspan="3" style="border: 1px solid #000; padding: 8px; text-align: right;">TOTAL:</td>
                <td style="border: 1px solid #000; padding: 8px;"><?= number_format($data['total_hours'], 2) ?></td>
                <td style="border: 1px solid #000; padding: 8px;">OT: <?= round($data['overtime_minutes'] / 60, 2) ?> hrs</td>
            </tr>
        </tbody>
    </table>

    <div style="display: flex; justify-content: space-between; margin-top: 40px;">
        <div style="width: 45%; border-top: 1px solid #000; padding-top: 5px;">Employee Signature</div>
        <div style="width: 45%; border-top: 1px solid #000; padding-top: 5px;">Manager Approval</div>
    </div>
</div>
<?php endforeach; ?>
