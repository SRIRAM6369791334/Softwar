<div class="hsn-summary">
    <div style="margin-bottom: 2rem;">
        <a href="/reports/gst?month=<?= $month ?>&year=<?= $year ?>" style="color: var(--text-dim); text-decoration: none;">← Back to GST Dashboard</a>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem;">
            <h1>HSN-wise Summary (Table 12 GSTR-1)</h1>
            <a href="/reports/gst/export?type=hsn&month=<?= $month ?>&year=<?= $year ?>" class="btn btn-primary">↓ Export CA Spreadsheet</a>
        </div>
        <p style="color: var(--text-dim);"><?= date('F Y', mktime(0,0,0,$month, 1, $year)) ?> summary for GSTR-1 filing.</p>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>HSN Code</th>
                    <th>Unit</th>
                    <th style="text-align: right;">Total Qty</th>
                    <th style="text-align: right;">Total Value</th>
                    <th style="text-align: right;">Taxable Value</th>
                    <th style="text-align: right;">Tax Rate</th>
                    <th style="text-align: right;">Tax Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($hsnData as $row): ?>
                <tr>
                    <td style="font-family: monospace; font-weight: 600;"><?= $row['hsn_code'] ?></td>
                    <td><?= $row['unit'] ?></td>
                    <td style="text-align: right;"><?= (float)$row['total_qty'] ?></td>
                    <td style="text-align: right;">₹<?= number_format($row['total_value'], 2) ?></td>
                    <td style="text-align: right;">₹<?= number_format($row['taxable_value'], 2) ?></td>
                    <td style="text-align: right;"><?= (float)$row['tax_percent'] ?>%</td>
                    <td style="text-align: right; font-weight: 600;">₹<?= number_format($row['total_tax'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($hsnData)): ?>
                    <tr><td colspan="7" style="text-align: center; color: var(--text-dim); padding: 40px;">No transaction data found for this period.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
