<div class="branch-comparison">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1>Branch Sales Comparison</h1>
            <p style="color: #8b949e;">Performance analysis over the last 7 days</p>
        </div>
        <a href="/central/dashboard" class="btn btn-primary" style="text-decoration: none;">← Back to Dashboard</a>
    </div>

    <!-- 7 Day Analytics Grid -->
    <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
        <div class="card">
            <h3>Sales Trend (Last 7 Days)</h3>
            <div style="margin-top: 1.5rem; overflow-x: auto;">
                <table style="text-align: center;">
                    <thead>
                        <tr>
                            <th style="text-align: left;">Branch</th>
                            <?php foreach ($labels as $label): ?>
                                <th><?= date('M d', strtotime($label)) ?></th>
                            <?php endforeach; ?>
                            <th>Total (7d)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($comparison_data as $branch): 
                            $branchTotal = array_sum($branch['data']);
                        ?>
                        <tr>
                            <td style="text-align: left; font-weight: 600; color: var(--accent-color);">
                                <?= htmlspecialchars($branch['name']) ?>
                            </td>
                            <?php foreach ($branch['data'] as $sale): ?>
                                <td>₹<?= number_format($sale, 0) ?></td>
                            <?php endforeach; ?>
                            <td style="background: var(--accent-dim); font-weight: bold;">
                                ₹<?= number_format($branchTotal, 2) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <h3>Key Insights</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-top: 1rem;">
                <?php 
                // Simple logic to find top performing branch in 7 days
                usort($comparison_data, function($a, $b) {
                    return array_sum($b['data']) <=> array_sum($a['data']);
                });
                $topBranch = $comparison_data[0] ?? null;
                ?>
                
                <?php if ($topBranch): ?>
                <div style="background: var(--accent-dim); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--accent-color);">
                    <div style="color: var(--accent-color); font-size: 0.8rem; text-transform: uppercase;">Top Performing Branch (7d)</div>
                    <div style="font-size: 1.4rem; font-weight: bold; margin-top: 5px;"><?= htmlspecialchars($topBranch['name']) ?></div>
                    <div style="color: #8b949e; font-size: 0.9rem; margin-top: 5px;">
                        Processed ₹<?= number_format(array_sum($topBranch['data']), 2) ?> in sales
                    </div>
                </div>
                <?php endif; ?>

                <div style="background: rgba(46, 160, 67, 0.1); padding: 1.5rem; border-radius: 8px; border: 1px solid #2ea043;">
                    <div style="color: #2ea043; font-size: 0.8rem; text-transform: uppercase;">System Health</div>
                    <div style="font-size: 1.4rem; font-weight: bold; margin-top: 5px;">Optimization Required</div>
                    <div style="color: #8b949e; font-size: 0.9rem; margin-top: 5px;">
                        Some branches show low volume in the last 48 hours.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .branch-comparison h1 {
        margin: 0;
        background: linear-gradient(90deg, #fff, var(--accent-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        letter-spacing: -1px;
    }
    
    table th { padding: 15px; background: #0d1117; }
    table td { padding: 15px; border-bottom: 1px solid var(--border-color); }
</style>
