<?php
/**
 * Vendor-specific Layout
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supplier Portal - S.O.S. System</title>
    <link rel="stylesheet" href="/css/hud.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0d1117;
            --card-bg: #161b22;
            --text-main: #c9d1d9;
            --text-dim: #8b949e;
            --accent: #58a6ff;
            --border: #30363d;
        }
        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg-color);
            color: var(--text-main);
            margin: 0;
            display: flex;
        }
        .sidebar {
            width: 260px;
            height: 100vh;
            background: var(--card-bg);
            border-right: 1px solid var(--border);
            padding: 2rem 1rem;
            position: fixed;
        }
        .main-content {
            margin-left: 280px;
            padding: 2rem;
            width: calc(100% - 320px);
        }
        .nav-item {
            display: block;
            padding: 12px 15px;
            color: var(--text-main);
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 5px;
            transition: all 0.2s;
        }
        .nav-item:hover {
            background: rgba(88, 166, 255, 0.1);
            color: var(--accent);
        }
        .nav-item.active {
            background: var(--accent);
            color: #fff;
        }
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid var(--border);
        }
        th { color: var(--text-dim); font-size: 0.85rem; text-transform: uppercase; }
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-pending { background: #382d0b; color: #e3b341; }
        .badge-delivered { background: #112d19; color: #3fb950; }
        .btn {
            background: var(--accent);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2 style="margin-bottom: 2rem; color: var(--accent);">Global Suppliers</h2>
        <nav>
            <a href="/vendor/dashboard" class="nav-item">📊 Dashboard</a>
            <a href="/vendor/analytics" class="nav-item" style="color: var(--hud-neon-blue);">🚀 HUD Analytics</a>
            <a href="/vendor/orders" class="nav-item">📦 Purchase Orders</a>
            <a href="/vendor/forecasting" class="nav-item">📉 Stock Forecasting</a>
            <a href="/vendor/quotations" class="nav-item">💰 Price Quotations</a>
            <a href="/vendor/ledger" class="nav-item">💸 My Ledger</a>
            <a href="/vendor/logout" class="nav-item" style="margin-top: 2rem; color: #f85149;">🚪 Logout</a>
        </nav>
    </div>
    <div class="main-content">
        <?= $content ?>
    </div>
</body>
</html>
