<div class="central-dashboard">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Centralized Dashboard (Cross-Branch)</h1>
        <div style="background: var(--accent-dim); padding: 10px 20px; border-radius: 8px; border: 1px solid var(--accent-color);">
            <strong style="color: var(--accent-color);">Admin Oversight Active</strong>
        </div>
    </div>

    <!-- Aggregate Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card" style="border-top: 4px solid var(--accent-color);">
            <div style="color: #8b949e; font-size: 0.9rem; text-transform: uppercase;">Active Branches</div>
            <div style="font-size: 2.5rem; font-weight: bold; margin: 10px 0;"><?= $agg_stats['total_active_branches'] ?></div>
            <div style="color: var(--success); font-size: 0.8rem;">● System Wide Online</div>
        </div>

        <div class="card" style="border-top: 4px solid var(--success);">
            <div style="color: #8b949e; font-size: 0.9rem; text-transform: uppercase;">Total Sales Today</div>
            <div style="font-size: 2.5rem; font-weight: bold; margin: 10px 0; color: var(--success);">₹<?= number_format($agg_stats['total_today_sales'], 2) ?></div>
            <div style="color: #8b949e; font-size: 0.8rem;"><?= $agg_stats['total_today_bills'] ?> Bills processed across all branches</div>
        </div>

        <div class="card" style="border-top: 4px solid #f39c12;">
            <div style="color: #8b949e; font-size: 0.9rem; text-transform: uppercase;">MTD Sales (System)</div>
            <div style="font-size: 2.5rem; font-weight: bold; margin: 10px 0;">₹<?= number_format($mtd_sales, 2) ?></div>
            <div style="color: #8b949e; font-size: 0.8rem;">Month-to-Date Performance</div>
        </div>

        <div class="card" style="border-top: 4px solid var(--danger);">
            <div style="color: #8b949e; font-size: 0.9rem; text-transform: uppercase;">Low Stock Alert</div>
            <div style="font-size: 2.5rem; font-weight: bold; margin: 10px 0; color: var(--danger);"><?= $low_stock_total ?></div>
            <div style="color: #8b949e; font-size: 0.8rem;">Products across all branches</div>
        </div>
    </div>

    <!-- Branch Performance Table -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3>Branch Performance (Today)</h3>
            <a href="/central/comparison" class="btn btn-primary" style="text-decoration: none; font-size: 0.8rem;">View Detailed Comparison</a>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Branch Name</th>
                    <th>Bills Issued</th>
                    <th>Total Sales</th>
                    <th>Performance Bar</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $maxSales = (float)max(array_column($branch_performance, 'sales_total') ?: [0]) ?: 1;
                foreach ($branch_performance as $branch): 
                    $percent = ($branch['sales_total'] / $maxSales) * 100;
                ?>
                <tr>
                    <td style="font-weight: 600;"><?= htmlspecialchars($branch['branch_name']) ?></td>
                    <td><?= $branch['bills_count'] ?> Bills</td>
                    <td style="color: var(--accent-color); font-weight: bold;">₹<?= number_format($branch['sales_total'], 2) ?></td>
                    <td style="width: 200px;">
                        <div style="background: #30363d; height: 8px; border-radius: 4px; overflow: hidden;">
                            <div style="background: var(--accent-color); width: <?= $percent ?>%; height: 100%; box-shadow: 0 0 10px var(--accent-color);"></div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .central-dashboard h1 {
        margin: 0;
        background: linear-gradient(90deg, #fff, var(--accent-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        letter-spacing: -1px;
    }
</style>
