<div class="gst-dashboard">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1>GST Compliance Dashboard</h1>
            <p style="color: var(--text-dim);">GSTR-1 Preparation & B2C Aggregation</p>
        </div>
        <form method="GET" style="display: flex; gap: 10px;">
            <select name="month" class="btn" style="background: var(--panel-bg); color: #fff; border: 1px solid var(--border-color);">
                <?php for($i=1;$i<=12;$i++): ?>
                    <option value="<?= sprintf('%02d', $i) ?>" <?= $month == $i ? 'selected' : '' ?>><?= date('F', mktime(0,0,0,$i,1)) ?></option>
                <?php endfor; ?>
            </select>
            <select name="year" class="btn" style="background: var(--panel-bg); color: #fff; border: 1px solid var(--border-color);">
                <?php for($y=date('Y');$y>=2023;$y--): ?>
                    <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>

    <div class="stat-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card" style="border-left: 4px solid var(--accent-color);">
            <div style="font-size: 0.85rem; color: var(--text-dim);">TAXABLE VALUE</div>
            <div style="font-size: 1.8rem; font-weight: 600; margin: 5px 0;">₹<?= number_format($totals['taxable'], 2) ?></div>
        </div>
        <div class="card" style="border-left: 4px solid #3fb950;">
            <div style="font-size: 0.85rem; color: var(--text-dim);">TOTAL GST</div>
            <div style="font-size: 1.8rem; font-weight: 600; margin: 5px 0;">₹<?= number_format($totals['tax'], 2) ?></div>
        </div>
        <div class="card" style="border-left: 4px solid #e3b341;">
            <div style="font-size: 0.85rem; color: var(--text-dim);">CGST (EST)</div>
            <div style="font-size: 1.8rem; font-weight: 600; margin: 5px 0;">₹<?= number_format($totals['tax']/2, 2) ?></div>
        </div>
        <div class="card" style="border-left: 4px solid #e3b341;">
            <div style="font-size: 0.85rem; color: var(--text-dim);">SGST (EST)</div>
            <div style="font-size: 1.8rem; font-weight: 600; margin: 5px 0;">₹<?= number_format($totals['tax']/2, 2) ?></div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3>B2C Aggregation (Table 7 GSTR-1)</h3>
                <a href="/reports/gst/export?type=b2c&month=<?= $month ?>&year=<?= $year ?>" class="btn" style="padding: 5px 10px; font-size: 0.8rem;">↓ Export CSV</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Tax Rate (%)</th>
                        <th style="text-align: right;">Taxable Value</th>
                        <th style="text-align: right;">CGST</th>
                        <th style="text-align: right;">SGST</th>
                        <th style="text-align: right;">Total Tax</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($b2c as $row): ?>
                    <tr>
                        <td><strong><?= (float)$row['tax_percent'] ?>%</strong></td>
                        <td style="text-align: right;">₹<?= number_format($row['taxable_value'], 2) ?></td>
                        <td style="text-align: right; color: var(--text-dim);">₹<?= number_format($row['total_tax']/2, 2) ?></td>
                        <td style="text-align: right; color: var(--text-dim);">₹<?= number_format($row['total_tax']/2, 2) ?></td>
                        <td style="text-align: right; font-weight: 600;">₹<?= number_format($row['total_tax'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($b2c)): ?>
                        <tr><td colspan="5" style="text-align: center; color: var(--text-dim); padding: 20px;">No sales data for this period.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h3>Compliance Quick Links</h3>
            <div style="margin-top: 1rem;">
                <a href="/reports/gst/hsn?month=<?= $month ?>&year=<?= $year ?>" class="btn" style="display: block; width: 100%; margin-bottom: 10px; text-align: left;">📦 HSN-wise Summary</a>
                <p style="font-size: 0.8rem; color: var(--text-dim); margin-top: 2rem;">
                    <strong>Note for CA:</strong> This report includes all 'paid' invoices for the selected month. Cancelled invoices are excluded from turnover.
                </p>
            </div>
        </div>
    </div>
</div>
