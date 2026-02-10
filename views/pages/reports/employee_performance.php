<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Employee Performance Report</h2>
    <div>
        <input type="date" id="startDate" value="<?= $startDate ?>" style="padding: 8px; background: #0d1117; color: #fff; border: 1px solid var(--border-color);">
        <input type="date" id="endDate" value="<?= $endDate ?>" style="padding: 8px; background: #0d1117; color: #fff; border: 1px solid var(--border-color);">
        <button class="btn btn-primary" onclick="filterReport()" style="padding: 8px 16px; margin-left: 10px;">Load</button>
    </div>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Rank</th>
                <th>Employee</th>
                <th>Total Bills</th>
                <th>Total Sales</th>
                <th>Avg Bill Value</th>
                <th>Performance</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $rank = 1;
            foreach ($performance as $emp): 
                $bills = $emp['total_bills'] ?? 0;
                $sales = $emp['total_sales'] ?? 0;
                
                // Performance indicator
                if ($bills == 0) {
                    $indicator = '<span style="color:#666;">No Activity</span>';
                } elseif ($bills > 50) {
                    $indicator = '<span style="color:var(--success);">⭐ Excellent</span>';
                } elseif ($bills > 20) {
                    $indicator = '<span style="color:var(--accent-color);">✓ Good</span>';
                } else {
                    $indicator = '<span style="color:#ff9e00;">△ Average</span>';
                }
            ?>
            <tr>
                <td style="color: var(--accent-color); font-weight: bold;">#<?= $rank++ ?></td>
                <td>
                    <strong><?= htmlspecialchars($emp['full_name']) ?></strong><br>
                    <small style="color: #666;">@<?= $emp['username'] ?></small>
                </td>
                <td><?= $bills ?></td>
                <td style="font-weight: bold; color: var(--success);">₹<?= number_format($sales, 2) ?></td>
                <td>₹<?= number_format($emp['avg_bill_value'] ?? 0, 2) ?></td>
                <td><?= $indicator ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    function filterReport() {
        const start = document.getElementById('startDate').value;
        const end = document.getElementById('endDate').value;
        window.location.href = `/reports/employee-performance?start=${start}&end=${end}`;
    }
</script>
