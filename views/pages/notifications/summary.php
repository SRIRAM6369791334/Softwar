<div class="notification-summary">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1>Daily Notification Summary</h1>
            <p style="color: var(--text-dim);">System health report for <?= date('d M Y') ?></p>
        </div>
        <a href="/dashboard" class="btn">← Back to Dashboard</a>
    </div>

    <div class="stat-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
        <?php 
        $types = ['stock' => 'Stock Alerts', 'expiry' => 'Expiry Alerts', 'system' => 'System Updates'];
        $counts = [];
        foreach($summary as $s) $counts[$s['type']] = $s['count'];
        
        foreach($types as $type => $label): ?>
            <div class="card" style="border-top: 3px solid <?= $type === 'stock' ? 'var(--danger)' : ($type === 'expiry' ? '#e3b341' : 'var(--accent-color)') ?>">
                <div style="font-size: 0.85rem; color: var(--text-dim); text-transform: uppercase;"><?= $label ?></div>
                <div style="font-size: 2rem; font-weight: 600; margin-top: 10px;"><?= $counts[$type] ?? 0 ?></div>
                <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 5px;">Generated today</div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="card">
        <h3>Summary Recommendations</h3>
        <ul style="margin-top: 1.5rem; color: var(--text-dim); line-height: 1.8;">
            <?php if(($counts['stock'] ?? 0) > 0): ?>
                <li style="color: var(--danger);">Critical: <?= $counts['stock'] ?> products require immediate reordering.</li>
            <?php endif; ?>
            <?php if(($counts['expiry'] ?? 0) > 0): ?>
                <li style="color: #e3b341;">Warning: <?= $counts['expiry'] ?> product batches are expiring within 30 days.</li>
            <?php endif; ?>
            <?php if(empty($counts)): ?>
                <li style="color: #3fb950;">System Green: No critical alerts generated today.</li>
            <?php endif; ?>
        </ul>
        <div style="margin-top: 2rem; padding: 1.5rem; background: rgba(255,255,255,0.02); border-radius: 8px; border-left: 4px solid var(--accent-color);">
            <p style="margin: 0; font-size: 0.9rem;">
                <strong>Owner Note:</strong> This summary is compiled automatically every 24 hours. Emails are simulated for demonstration.
            </p>
        </div>
    </div>
</div>
