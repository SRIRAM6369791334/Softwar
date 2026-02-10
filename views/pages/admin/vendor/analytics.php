<div class="hud-container">
    <h1 style="color: var(--hud-neon-blue); margin-bottom: 2rem;">SUPPLY CHAIN // COMMAND CENTER</h1>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <!-- Reliability Index -->
        <div class="hud-card">
            <h3 style="margin-top: 0; font-size: 0.9rem; color: var(--text-dim); text-transform: uppercase;">Supplier Reliability (Fulfillment %)</h3>
            <div style="margin-top: 1.5rem;">
                <?php foreach ($reliability as $r): 
                    $pct = $r['total_orders'] > 0 ? ($r['delivered_orders'] / $r['total_orders']) * 100 : 0;
                ?>
                <div style="margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 5px;">
                        <span><?= htmlspecialchars($r['vendor_name']) ?></span>
                        <span style="color: var(--hud-neon-blue);"><?= round($pct) ?>%</span>
                    </div>
                    <div style="width: 100%; height: 6px; background: rgba(255,255,255,0.05); border-radius: 3px; overflow: hidden;">
                        <div style="width: <?= $pct ?>%; height: 100%; background: linear-gradient(90deg, #1d4ed8, var(--hud-neon-blue));"></div>
                    </div>
                    <div style="font-size: 0.7rem; color: var(--text-dim); margin-top: 4px;"><?= $r['delivered_orders'] ?> of <?= $r['total_orders'] ?> orders delivered</div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Price Trend HUD -->
        <div class="hud-card">
            <h3 style="margin-top: 0; font-size: 0.9rem; color: var(--text-dim); text-transform: uppercase;">Approved Procurement Volatility</h3>
            <div style="height: 250px; display: flex; align-items: flex-end; gap: 10px; padding-bottom: 2rem; border-bottom: 1px solid var(--hud-border);">
                <?php 
                $maxPrice = !empty($priceTrends) ? max(array_column($priceTrends, 'proposed_price')) : 100;
                foreach ($priceTrends as $p): 
                    $height = ($p['proposed_price'] / $maxPrice) * 100;
                ?>
                <div style="flex: 1; background: var(--hud-neon-blue); opacity: 0.6; height: <?= $height ?>%; border-radius: 2px 2px 0 0;" title="<?= $p['product_name'] ?>: ₹<?= $p['proposed_price'] ?>"></div>
                <?php endforeach; ?>
                <?php if (empty($priceTrends)): ?>
                    <div style="width: 100%; text-align: center; color: var(--text-dim);">No volatility data available.</div>
                <?php endif; ?>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 0.7rem; color: var(--text-dim); margin-top: 10px;">
                <span>Oldest Approvals</span>
                <span>Latest Trends</span>
            </div>
        </div>
    <div style="margin-top: 2rem; display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <!-- Regional Map HUD -->
        <div class="hud-card">
            <h3 style="margin-top: 0; font-size: 0.9rem; color: var(--text-dim); text-transform: uppercase;">Regional Fulfillment Network</h3>
            <div style="display: flex; justify-content: space-around; align-items: flex-end; height: 200px; padding: 20px; border-bottom: 1px solid var(--hud-border);">
                <?php 
                $maxValue = !empty($regionalDist) ? max(array_column($regionalDist, 'total_value')) : 1;
                if ($maxValue <= 0) $maxValue = 1;
                foreach ($regionalDist as $r): 
                    $val = (float)($r['total_value'] ?? 0);
                    $h = ($val / $maxValue) * 100;
                ?>
                <div style="text-align: center; width: 60px;">
                    <div style="font-size: 0.7rem; color: var(--hud-neon-blue); margin-bottom: 8px;">₹<?= number_format($val / 1000, 1) ?>k</div>
                    <div style="height: <?= $h ?>%; background: linear-gradient(to top, var(--hud-neon-blue), transparent); border-top: 2px solid var(--hud-neon-blue); border-radius: 4px 4px 0 0; opacity: 0.8; box-shadow: 0 0 15px var(--accent-dim);"></div>
                    <div style="font-size: 0.8rem; margin-top: 10px; font-weight: 600;"><?= htmlspecialchars($r['region'] ?? 'Unknown') ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Supply Statistics -->
        <div class="hud-card">
            <h3 style="margin-top: 0; font-size: 0.9rem; color: var(--text-dim); text-transform: uppercase;">Network Load</h3>
            <div style="margin-top: 1rem;">
                <?php 
                $totalVal = array_sum(array_column($regionalDist, 'total_value'));
                foreach ($regionalDist as $r): 
                    $pct = $totalVal > 0 ? ($r['total_value'] / $totalVal) * 100 : 0;
                ?>
                <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                    <span style="font-size: 0.8rem;"><?= htmlspecialchars($r['region']) ?></span>
                    <span style="font-size: 0.8rem; color: var(--hud-neon-orange);"><?= round($pct) ?>%</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
