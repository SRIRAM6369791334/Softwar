<div class="hud-container">
    <h1 style="color: var(--hud-neon-blue); margin-bottom: 2rem;">V-OS // ANALYTICS HUD</h1>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
        <!-- Financial Speedo -->
        <div class="hud-card">
            <h3 style="margin-top: 0; font-size: 0.9rem; color: var(--text-dim); text-transform: uppercase;">Collection Efficiency</h3>
            <div style="display: flex; flex-direction: column; align-items: center; padding: 1rem 0;">
                <?php 
                    $ratio = $summary['total_owed'] > 0 ? ($summary['total_paid'] / $summary['total_owed']) * 100 : 0;
                    $dash = 283; // 2 * pi * 45
                    $offset = $dash - ($ratio / 100) * $dash;
                ?>
                <svg class="gauge" viewBox="20 20 160 160">
                    <circle class="bg" cx="100" cy="100" r="45" />
                    <circle class="meter" cx="100" cy="100" r="45" 
                            style="stroke: <?= $ratio > 80 ? 'var(--hud-neon-green)' : 'var(--hud-neon-orange)' ?>; 
                                   stroke-dasharray: <?= $dash ?>; 
                                   stroke-dashoffset: <?= $offset ?>;
                                   transform: rotate(-90deg); transform-origin: center;" />
                    <text x="100" y="105" text-anchor="middle" fill="#fff" font-size="1.2rem" font-weight="600"><?= round($ratio) ?>%</text>
                </svg>
                <div style="margin-top: 1rem; text-align: center;">
                    <div style="font-size: 0.8rem; color: var(--text-dim);">Paid vs Outstanding</div>
                    <div style="color: var(--hud-neon-green); font-weight: 600;">₹<?= number_format($summary['total_paid'], 0) ?> Received</div>
                </div>
            </div>
        </div>

        <!-- Delivery Heatmap -->
        <div class="hud-card">
            <h3 style="margin-top: 0; font-size: 0.9rem; color: var(--text-dim); text-transform: uppercase;">Delivery Activity (Last 30 Days)</h3>
            <div style="display: flex; flex-wrap: wrap; gap: 5px; margin-top: 2rem;">
                <?php 
                $dates = [];
                foreach($heatmap as $h) $dates[$h['date']] = $h['count'];
                
                for($i=29; $i>=0; $i--):
                    $d = date('Y-m-d', strtotime("-$i days"));
                    $count = $dates[$d] ?? 0;
                    $level = min(4, $count);
                ?>
                <div class="heatmap-cell heatmap-<?= $level ?>" title="<?= $d ?>: <?= $count ?> deliveries"></div>
                <?php endfor; ?>
            </div>
            <div style="margin-top: 1.5rem; font-size: 0.8rem; color: var(--text-dim); display: flex; gap: 10px; align-items: center;">
                Less <div class="heatmap-cell heatmap-0"></div><div class="heatmap-cell heatmap-1"></div><div class="heatmap-cell heatmap-2"></div><div class="heatmap-cell heatmap-3"></div><div class="heatmap-cell heatmap-4"></div> More
            </div>
        </div>
    </div>

    <div style="margin-top: 2rem; display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem;">
        <!-- Run-out Gauges -->
        <?php foreach ($gauges as $g): 
            $days = $g['avg_daily_sales'] > 0 ? $g['current_stock'] / $g['avg_daily_sales'] : 99;
            $healthPct = min(100, ($days / 14) * 100); // 14 days is 100% health
            $color = $days < 3 ? 'var(--hud-neon-red)' : ($days < 7 ? 'var(--hud-neon-orange)' : 'var(--hud-neon-green)');
        ?>
        <div class="hud-card" style="text-align: center;">
            <div style="font-size: 0.75rem; color: var(--text-dim); margin-bottom: 10px;"><?= htmlspecialchars($g['name']) ?></div>
            <div style="font-size: 1.5rem; font-weight: 600; color: <?= $color ?>;"><?= floor($days) ?> <small style="font-size: 0.8rem;">DAYS</small></div>
            <div style="width: 100%; height: 4px; background: rgba(255,255,255,0.05); margin-top: 15px; border-radius: 2px;">
                <div style="width: <?= $healthPct ?>%; height: 100%; background: <?= $color ?>; border-radius: 2px; box-shadow: 0 0 10px <?= $color ?>;"></div>
            </div>
            <div style="font-size: 0.7rem; color: var(--text-dim); margin-top: 8px;">Stock: <?= (float)$g['current_stock'] ?> units</div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
