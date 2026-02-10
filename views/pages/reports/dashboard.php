<h2>Dashboard Overview</h2>

<div id="dashboard-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 2rem;">
    <!-- Widget 1: Today Sales -->
    <div id="widget-today-sales" class="card draggable-widget" draggable="true" style="text-align: center; padding: 2rem; cursor: move;">
        <div style="color: var(--text-dim); font-size: 0.9rem; margin-bottom: 10px;">TODAY'S SALES</div>
        <div style="font-size: 2.5rem; color: var(--hud-neon-green); font-weight: bold;">₹<?= number_format($today_sales, 2) ?></div>
        <a href="/reports/daybook" style="color: var(--hud-neon-blue); font-size: 0.8rem; margin-top: 10px; display: inline-block;">View Day Book →</a>
    </div>
    
    <!-- Widget 2: Month Sales -->
    <div id="widget-month-sales" class="card draggable-widget" draggable="true" style="text-align: center; padding: 2rem; cursor: move;">
        <div style="color: var(--text-dim); font-size: 0.9rem; margin-bottom: 10px;">THIS MONTH</div>
        <div style="font-size: 2.5rem; color: var(--hud-neon-blue); font-weight: bold;">₹<?= number_format($month_sales, 2) ?></div>
        <div style="color: var(--text-dim); font-size: 0.75rem; margin-top: 10px;"><?= date('F Y') ?></div>
    </div>
    
    <!-- Widget 3: Low Stock -->
    <div id="widget-low-stock" class="card draggable-widget" draggable="true" style="text-align: center; padding: 2rem; cursor: move;">
        <div style="color: var(--text-dim); font-size: 0.9rem; margin-bottom: 10px;">LOW STOCK ALERTS</div>
        <div style="font-size: 2.5rem; color: <?= $low_stock_items > 0 ? 'var(--hud-neon-orange)' : 'var(--hud-neon-green)' ?>; font-weight: bold;">
            <?= $low_stock_items ?>
        </div>
        <a href="/products" style="color: var(--hud-neon-blue); font-size: 0.8rem; margin-top: 10px; display: inline-block;">Check Products →</a>
    </div>

    <!-- Widget 4: Quick Actions -->
    <div id="widget-quick-actions" class="card draggable-widget" draggable="true" style="cursor: move;">
        <h3>Quick Actions</h3>
        <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 1rem;">
            <a href="/pos" class="btn btn-primary" style="text-decoration: none; text-align: center;">🛒 Open POS Terminal</a>
            <a href="/inventory/inward" class="btn" style="text-decoration: none; text-align: center; background: rgba(255,255,255,0.05);">📦 Inward Stock</a>
            <a href="/products/create" class="btn" style="text-decoration: none; text-align: center; background: rgba(255,255,255,0.05);">➕ Add New Product</a>
        </div>
    </div>
    
    <!-- Widget 5: System Status -->
    <div id="widget-system-status" class="card draggable-widget" draggable="true" style="cursor: move;">
        <h3>System Status</h3>
        <div style="margin-top: 1rem;">
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border-color);">
                <span>Database</span>
                <span style="color: var(--hud-neon-green);">● Connected</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border-color);">
                <span>Server Time</span>
                <span><?= date('h:i:s A') ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 10px 0;">
                <span>Logged in as</span>
                <span style="color: var(--hud-neon-blue);"><?= $_SESSION['full_name'] ?></span>
            </div>
        </div>
    </div>
</div>

<script src="/js/drag_widgets.js"></script>
