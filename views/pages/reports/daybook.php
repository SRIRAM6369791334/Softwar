<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Sales Day Book</h2>
    <div>
        <input type="date" id="dateFilter" value="<?= $date ?>" 
               style="padding: 8px; background: #0d1117; color: #fff; border: 1px solid var(--accent-color);">
        <button class="btn btn-primary" onclick="filterByDate()" style="padding: 8px 16px; margin-left: 10px;">Load</button>
    </div>
</div>

<!-- Summary Cards -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 2rem;">
    <div class="card" style="text-align: center;">
        <div style="color: #888; font-size: 0.8rem;">Total Bills</div>
        <div style="font-size: 2rem; color: var(--accent-color); font-weight: bold;"><?= $totals['total_bills'] ?></div>
    </div>
    <div class="card" style="text-align: center;">
        <div style="color: #888; font-size: 0.8rem;">Total Sales</div>
        <div style="font-size: 2rem; color: var(--success); font-weight: bold;"><?= \App\Core\Currency::format($totals['total_sales'] ?? 0) ?></div>
    </div>
    <div class="card" style="text-align: center;">
        <div style="color: #888; font-size: 0.8rem;">Tax Collected</div>
        <div style="font-size: 1.5rem; font-weight: bold;"><?= \App\Core\Currency::format($totals['total_tax'] ?? 0) ?></div>
    </div>
    <div class="card" style="text-align: center;">
        <div style="color: #888; font-size: 0.8rem;">Discounts</div>
        <div style="font-size: 1.5rem; color: #d29922; font-weight: bold;"><?= \App\Core\Currency::format($totals['total_discount'] ?? 0) ?></div>
    </div>
</div>

<!-- Invoice List -->
<div class="card">
    <table>
        <thead>
            <tr>
                <th>Invoice No</th>
                <th>Time</th>
                <th>Cashier</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($invoices as $inv): ?>
            <tr>
                <td style="font-family: monospace; color: var(--accent-color);"><?= $inv['invoice_no'] ?></td>
                <td><?= date('h:i A', strtotime($inv['created_at'])) ?></td>
                <td><?= $inv['cashier_name'] ?></td>
                <td style="font-weight: bold;"><?= \App\Core\Currency::format($inv['grand_total']) ?></td>
                <td>
                    <span style="padding: 2px 8px; background: <?= $inv['status'] == 'paid' ? 'var(--success)' : '#999' ?>; border-radius: 3px; font-size: 0.75rem;">
                        <?= strtoupper($inv['status']) ?>
                    </span>
                </td>
                <td>
                    <a href="/reports/invoice/<?= $inv['id'] ?>" class="btn" style="padding: 4px 10px; font-size: 0.75rem;">View</a>
                </td>
            </tr>
            <?php endforeach; ?>
            
            <?php if (empty($invoices)): ?>
                <tr><td colspan="6" style="text-align:center; padding: 2rem; color: #888;">No invoices for this date.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    function filterByDate() {
        const date = document.getElementById('dateFilter').value;
        window.location.href = '/reports/daybook?date=' + date;
    }
</script>
