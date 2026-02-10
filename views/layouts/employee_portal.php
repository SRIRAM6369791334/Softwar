<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Portal</title>
    <link rel="stylesheet" href="/css/hud.css">
    <style>
        :root {
            --bg-color: #0d1117;
            --text-color: #c9d1d9;
            --accent-color: #2f81f7;
            --panel-bg: #161b22;
        }
        body {
            margin: 0;
            background: var(--bg-color);
            color: var(--text-color);
            font-family: 'Segoe UI', sans-serif;
            padding-bottom: 60px; /* For bottom nav */
        }
        .header {
            background: var(--panel-bg);
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #30363d;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .container {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        .card {
            background: var(--panel-bg);
            border: 1px solid #30363d;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .btn {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            width: 100%;
        }
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: var(--panel-bg);
            border-top: 1px solid #30363d;
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            z-index: 100;
        }
        .nav-item {
            text-align: center;
            color: #8b949e;
            text-decoration: none;
            font-size: 0.8rem;
        }
        .nav-item.active {
            color: var(--accent-color);
        }
        .nav-icon {
            display: block;
            font-size: 1.5rem;
            margin-bottom: 2px;
        }
        .stat-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-box {
            background: rgba(255,255,255,0.05);
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="header">
    <div style="font-weight: bold; font-size: 1.1rem;">Employee Portal</div>
    <div>
        <span style="font-size: 0.9rem; margin-right: 10px;"><?= $_SESSION['username'] ?? 'User' ?></span>
        <a href="/employee/logout" style="color: #da3633; text-decoration: none;">Logout</a>
    </div>
</div>

<div class="container">
    <?= $content ?>
</div>

<div class="bottom-nav">
    <a href="/employee/dashboard" class="nav-item <?= $active_tab == 'dashboard' ? 'active' : '' ?>">
        <span class="nav-icon">🏠</span>
        Home
    </a>
    <a href="/employee/roster" class="nav-item <?= $active_tab == 'roster' ? 'active' : '' ?>">
        <span class="nav-icon">📅</span>
        Roster
    </a>
    <a href="/employee/messages" class="nav-item <?= $active_tab == 'messages' ? 'active' : '' ?>">
        <span class="nav-icon">💬</span>
        Inbox
    </a>
    <a href="/employee/leaves" class="nav-item <?= $active_tab == 'leaves' ? 'active' : '' ?>">
        <span class="nav-icon">🏖️</span>
        Leaves
    </a>
</div>

</body>
</html>
