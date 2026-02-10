<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S.O.S. - Dashboard</title>
    <link rel="stylesheet" href="/css/hud.css">
    <link rel="stylesheet" href="/css/dark_mode.css">
    <link rel="stylesheet" href="/css/print.css" media="print">
    <style>
        :root {
            --bg-color: #0d1117;
            --panel-bg: rgba(22, 27, 34, 0.95);
            --sidebar-width: 260px;
            --header-height: 60px;
            --text-color: #e6edf3;
            --accent-color: #00f3ff;
            --accent-dim: rgba(0, 243, 255, 0.1);
            --border-color: #30363d;
            --success: #2ea043;
            --danger: #da3633;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--bg-color);
            color: var(--text-color);
            font-family: 'Segoe UI', 'Roboto', sans-serif;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--panel-bg);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            backdrop-filter: blur(5px);
        }

        .brand {
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--accent-color);
            letter-spacing: 2px;
            border-bottom: 1px solid var(--border-color);
        }

        .nav-menu {
            flex: 1;
            padding: 1rem 0;
            overflow-y: auto;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 0.8rem 1.5rem;
            color: #8b949e;
            text-decoration: none;
            transition: 0.2s;
            border-left: 3px solid transparent;
        }

        .nav-item:hover, .nav-item.active {
            color: var(--text-color);
            background: var(--accent-dim);
            border-left-color: var(--accent-color);
        }

        .user-panel {
            padding: 1rem;
            border-top: 1px solid var(--border-color);
            font-size: 0.9rem;
            color: #8b949e;
        }

        /* Main Content */
        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .top-bar {
            height: var(--header-height);
            background: var(--panel-bg);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
        }

        .content-area {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
            background-image: 
                linear-gradient(var(--border-color) 1px, transparent 1px),
                linear-gradient(90deg, var(--border-color) 1px, transparent 1px);
            background-size: 40px 40px;
            background-color: var(--bg-color); /* Fallback */
            background-blend-mode: overlay;
        }

        /* Utility Classes */
        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            border: 1px solid transparent;
            cursor: pointer;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s;
        }

        .btn-primary {
            background: var(--accent-dim);
            color: var(--accent-color);
            border-color: var(--accent-color);
        }

        .btn-primary:hover {
            background: var(--accent-color);
            color: #000;
        }
        
        .btn-danger {
            background: rgba(218, 54, 51, 0.1);
            color: var(--danger);
            border-color: var(--danger);
        }
        
        .btn-danger:hover {
             background: var(--danger);
             color: #fff;
        }

        .card {
            background: #161b22;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        }

        h1, h2, h3 { margin-top: 0; color: var(--text-color); }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid var(--border-color);
        }
        
        th {
            color: #8b949e;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
        }

        input, select {
            background: #0d1117;
            border: 1px solid var(--border-color);
            color: #fff;
            padding: 8px 12px;
            border-radius: 4px;
            width: 100%;
            margin-top: 5px;
        }
        
        input:focus {
            border-color: var(--accent-color);
            outline: none;
        }

        .form-group { margin-bottom: 1rem; }
        
        .btn-mode {
            background: #161b22;
            border: 1px solid var(--border-color);
            color: #8b949e;
        }
        .btn-mode.active {
            background: var(--accent-dim);
            color: var(--accent-color);
            border-color: var(--accent-color);
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand">S.O.S. SYSTEM</div>
    <div class="nav-menu">
        <a href="/dashboard" class="nav-item">Dashboard</a>
        
        <?php if(\App\Core\Auth::hasRole(1)): ?>
            <a href="/users" class="nav-item">Employees</a>
            <div style="padding-left: 20px;">
                <a href="/admin/employee/roster" class="nav-item" style="font-size: 0.9em;">📅 Roster</a>
                <a href="/admin/employee/leaves" class="nav-item" style="font-size: 0.9em;">🏖️ Leaves</a>
                <a href="/admin/employee/timesheets" class="nav-item" style="font-size: 0.9em;">📝 Timesheets</a>
                <a href="/admin/employee/overtime" class="nav-item" style="font-size: 0.9em;">⏰ Overtime</a>
                <a href="/admin/employee/open-shifts" class="nav-item" style="font-size: 0.9em;">🔄 Open Shifts</a>
                <a href="/admin/employee/messages" class="nav-item" style="font-size: 0.9em;">💬 Msgs</a>
            </div>
        <?php endif; ?>

        <?php if(\App\Core\Auth::hasRole([1, 2])): ?>
            <a href="/products" class="nav-item">Products</a>
        <?php endif; ?>

        <a href="/pos" class="nav-item" style="color: var(--accent-color);">POS Terminal</a>

        <?php if(\App\Core\Auth::hasRole([1, 2])): ?>
            <a href="/inventory" class="nav-item">Inventory</a>
            <a href="/inventory/po" class="nav-item">🛒 Purchase Orders</a>
        <?php endif; ?>

        <a href="/reports/daybook" class="nav-item">Day Book</a>

        <?php if(\App\Core\Auth::hasRole([1, 2])): ?>
            <a href="/reports/top-products" class="nav-item">Top Products</a>
            <a href="/reports" class="nav-item">📈 Performance</a>
            <a href="/reports/price-comparison" class="nav-item">💰 Price Check</a>
        <?php endif; ?>

        <?php if(\App\Core\Auth::hasRole(1)): ?>
            <a href="/reports/gst" class="nav-item" style="color: #e3b341;">⚖️ GST Compliance</a>
        <?php endif; ?>

        <?php if(\App\Core\Auth::hasRole([1, 2])): ?>
            <a href="/reports/reorder" class="nav-item" style="color: var(--danger);">⚠️ Reorder Alerts</a>
        <?php endif; ?>

        <?php if(\App\Core\Auth::hasRole(1)): ?>
            <a href="/branches" class="nav-item" style="color: var(--accent-color);">🏢 Branches</a>
        <?php endif; ?>
        
        <?php if(\App\Core\Auth::hasRole([1, 2])): ?>
            <div style="padding: 0.5rem 1.5rem; font-size: 0.75rem; color: #8b949e; text-transform: uppercase; letter-spacing: 1px;">Supply Chain</div>
            <a href="/admin/vendor/analytics" class="nav-item" style="color: var(--hud-neon-blue);">🚀 Analytics HUD</a>
            <a href="/admin/vendors" class="nav-item">👥 Manage Vendors</a>
            <a href="/admin/vendor/quotations" class="nav-item">💰 Price Requests</a>
            <a href="/admin/vendor/payments" class="nav-item">💸 Payments</a>
            <a href="/admin/vendor/broadcast" class="nav-item">📣 Broadcast</a>
        <?php endif; ?>
        
        <?php if(\App\Core\Auth::hasRole(1)): ?>
            <hr style="border: none; border-top: 1px solid var(--border-color); margin: 0.5rem 1rem;">
            <div style="padding: 0.5rem 1.5rem; font-size: 0.75rem; color: #8b949e; text-transform: uppercase; letter-spacing: 1px;">Central Oversight</div>
            <a href="/central/dashboard" class="nav-item">📈 All Branches</a>
            <a href="/central/comparison" class="nav-item">📊 Comparison</a>
            
            <hr style="border: none; border-top: 1px solid var(--border-color); margin: 0.5rem 1rem;">
            <div style="padding: 0.5rem 1.5rem; font-size: 0.75rem; color: #8b949e; text-transform: uppercase; letter-spacing: 1px;">System</div>
            <a href="/admin/settings" class="nav-item">⚙️ Settings</a>
            <a href="/admin/taxes" class="nav-item">⚖️ Tax Rules</a>
            <a href="/admin/data" class="nav-item">💾 Data Tools</a>
            <a href="/admin/workflows" class="nav-item">⚡ Automation</a>
            <a href="/admin/workflows" class="nav-item">⚡ Automation</a>
            <a href="/admin/data" class="nav-item">💾 Data Tools</a>
        <?php endif; ?>
    </div>
    <div class="user-panel">
        User: <strong><?= $_SESSION['username'] ?? 'Guest' ?></strong><br>
        Role: <?= $_SESSION['role_id'] == 1 ? 'Admin' : 'Staff' ?><br>
        <a href="/profile" style="color: var(--accent-color); text-decoration: none; font-size: 0.8rem; margin-top: 5px; display: inline-block;">→ My Profile</a>
    </div>
</div>

<div class="main-wrapper">
    <div class="top-bar">
        <div>
            <span style="margin-right: 1.5rem;">System Status: <span style="color: var(--success)">● ONLINE</span></span>
            <label style="color: #8b949e; margin-right: 0.5rem;">Branch:</label>
            <select id="branchSelector" onchange="switchBranch(this.value)" style="width: auto; display: inline-block; padding: 5px 10px; background: var(--panel-bg); border-color: var(--accent-color); color: var(--accent-color); cursor: pointer;">
                <?php
                $db = \App\Core\Database::getInstance();
                $branches = $db->query("SELECT id, name FROM branches WHERE is_active = 1 ORDER BY id")->fetchAll();
                $currentBranch = \App\Core\Auth::getCurrentBranch();
                foreach ($branches as $branch): ?>
                    <option value="<?= $branch['id'] ?>" <?= $branch['id'] == $currentBranch ? 'selected' : '' ?>>
                        <?= htmlspecialchars($branch['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="display: flex; align-items: center; gap: 1rem;">
            <!-- Theme Toggle -->
            <button onclick="toggleTheme()" class="btn" style="padding: 5px 10px; background: transparent; border: 1px solid var(--border-color); color: var(--text-color);">
                <span id="themeIcon">🌙</span>
            </button>

            <!-- Notification Bell -->
            <div class="notification-wrapper" style="position: relative;">
                <div id="notiBell" style="cursor: pointer; font-size: 1.2rem; position: relative; padding: 5px; border-radius: 50%; transition: background 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background='transparent'">
                    🔔 <span id="notiBadge" style="display: none; position: absolute; top: -2px; right: -2px; background: var(--danger); color: white; border-radius: 50%; min-width: 14px; height: 14px; font-size: 0.6rem; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 8px var(--danger); border: 2px solid #0d1117;">0</span>
                </div>
                
                <!-- Notification Panel -->
                <div id="notiPanel" style="display: none; position: absolute; top: 45px; right: 0; width: 320px; background: rgba(22, 27, 34, 0.95); border: 1px solid var(--border-color); border-radius: 12px; backdrop-filter: blur(20px); box-shadow: 0 10px 40px rgba(0,0,0,0.6); z-index: 1000; overflow: hidden; border-top: 2px solid var(--accent-color);">
                    <div style="padding: 15px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                        <h4 style="margin: 0; font-size: 0.9rem; letter-spacing: 0.5px;">SYSTEM ALERTS</h4>
                        <a href="/notifications/clear" style="font-size: 0.7rem; color: var(--accent-color); text-decoration: none;">Mark all read</a>
                    </div>
                    <div id="notiList" style="max-height: 380px; overflow-y: auto;">
                        <div style="padding: 20px; text-align: center; color: var(--text-dim); font-size: 0.8rem;">Scanning for alerts...</div>
                    </div>
                    <div style="padding: 10px; border-top: 1px solid var(--border-color); text-align: center; background: rgba(255,255,255,0.02);">
                        <a href="/notifications/summary" style="font-size: 0.75rem; color: var(--text-dim); text-decoration: none; display: block; width: 100%;">View Daily Summary Report</a>
                    </div>
                </div>
            </div>

            <a href="/profile" class="btn" style="text-decoration: none; font-size: 0.8rem; padding: 5px 12px; border-radius: 6px; background: var(--accent-dim); color: var(--accent-color); border: 1px solid var(--accent-color); margin-right: 10px;">👤 My Profile</a>
            <a href="/logout" class="btn btn-danger" style="text-decoration: none; font-size: 0.8rem; padding: 5px 12px; border-radius: 6px;">Logout</a>
        </div>


    <script src="/js/loader.js"></script>
    <script src="/js/pos_utilities.js"></script>
    <script src="/js/gdpr.js"></script>
    <script>
        // Theme Toggle Logic [#96]
        function toggleTheme() {
            const html = document.documentElement;
            const current = html.getAttribute('data-theme');
            const target = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', target);
            localStorage.setItem('theme', target);
            document.getElementById('themeIcon').innerText = target === 'dark' ? '☀️' : '🌙';
        }

        // Apply saved theme on load
        (function() {
            const saved = localStorage.getItem('theme');
            if(saved === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
                document.getElementById('themeIcon').innerText = '☀️';
            }
        })();

        // Custom Branch Background
        <?php 
        $t_branch = \App\Core\Database::getInstance()->query("SELECT background_url FROM branches WHERE id = ?", [\App\Core\Auth::getCurrentBranch()])->fetch();
        if(!empty($t_branch['background_url'])): ?>
            document.documentElement.style.setProperty('--bg-color', 'transparent');
            document.body.style.backgroundImage = "url('<?= $t_branch['background_url'] ?>')";
            document.body.style.backgroundSize = "cover";
            document.body.style.backgroundPosition = "center";
            document.body.style.backgroundAttachment = "fixed";
        <?php endif; ?>

        document.addEventListener('DOMContentLoaded', function() {
            const bell = document.getElementById('notiBell');
            const panel = document.getElementById('notiPanel');
            const badge = document.getElementById('notiBadge');
            const list = document.getElementById('notiList');

            // Toggle Panel
            bell.onclick = function(e) {
                e.stopPropagation();
                panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
                if(panel.style.display === 'block') loadNotifications();
            }

            // Close on click outside
            document.onclick = function() { panel.style.display = 'none'; };
            panel.onclick = function(e) { e.stopPropagation(); };

            // Load Notifications
            async function loadNotifications() {
                // Show Skeleton
                list.innerHTML = `
                    <div style="padding: 15px; border-bottom: 1px solid var(--border-color);">
                        <div class="skeleton skeleton-text" style="width: 60%;"></div>
                        <div class="skeleton skeleton-text" style="width: 40%; height: 0.8em;"></div>
                    </div>
                    <div style="padding: 15px; border-bottom: 1px solid var(--border-color);">
                        <div class="skeleton skeleton-text" style="width: 70%;"></div>
                        <div class="skeleton skeleton-text" style="width: 30%; height: 0.8em;"></div>
                    </div>
                `;

                try {
                    const res = await fetch('/api/v1/notifications/unread');
                    const data = await res.json();
                    
                    if(data.length === 0) {
                        list.innerHTML = '<div style="padding: 20px; text-align: center; color: var(--text-dim);">No new notifications</div>';
                        badge.style.display = 'none';
                        return;
                    }

                    // Update Badge
                    const unread = data.filter(n => !n.is_read).length;
                    badge.innerText = unread;
                    badge.style.display = unread > 0 ? 'block' : 'none';

                    // Render List
                    list.innerHTML = data.map(n => `
                        <div class="notification-item ${n.is_read ? '' : 'unread'}" 
                             onclick="markRead(${n.id}, '${n.link}')"
                             style="padding: 15px; border-bottom: 1px solid var(--border-color); cursor: pointer; transition: background 0.2s; ${n.is_read ? 'opacity: 0.7;' : 'background: rgba(0, 243, 255, 0.05); border-left: 3px solid var(--accent-color);'}">
                            <div style="font-weight: bold; margin-bottom: 5px; color: ${n.type === 'alert' ? 'var(--hud-neon-red)' : 'var(--text-color)'}">
                                ${n.type === 'alert' ? '⚠️ ' : 'ℹ️ '}${n.title}
                            </div>
                            <div style="font-size: 0.9rem; color: var(--text-dim); margin-bottom: 5px;">${n.message}</div>
                            <div style="font-size: 0.75rem; color: var(--text-dim); text-align: right;">${timeAgo(new Date(n.created_at))}</div>
                        </div>
                    `).join('');
                } catch(e) {
                    console.error("Failed to load notifications", e);
                    list.innerHTML = '<div style="padding: 10px; color: var(--hud-neon-red);">Failed to load</div>';
                }
            }
            
            window.markRead = async function(id, link) {
                await fetch('/api/notifications/read/' + id, {method: 'POST'});
                if(link && link !== 'null' && link !== '') window.location.href = link;
                else loadNotifications();
            }

            // Initial check
            loadNotifications();
            // Poll Every 2 mins
            setInterval(loadNotifications, 120000);
        });

        function timeAgo(date) {
            const seconds = Math.floor((new Date() - date) / 1000);
            let interval = seconds / 31536000;
            if (interval > 1) return Math.floor(interval) + " years ago";
            interval = seconds / 2592000;
            if (interval > 1) return Math.floor(interval) + " months ago";
            interval = seconds / 86400;
            if (interval > 1) return Math.floor(interval) + " days ago";
            interval = seconds / 3600;
            if (interval > 1) return Math.floor(interval) + " hours ago";
            interval = seconds / 60;
            if (interval > 1) return Math.floor(interval) + " minutes ago";
            return Math.floor(seconds) + " seconds ago";
        }
        </script>
    <script>
    function switchBranch(branchId) {
        fetch('/branches/switch/' + branchId, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        });
    }
    </script>
    </div><!-- end top-bar -->
    
    <div class="content-area">
        <?= $content ?>
    </div>
</div>

</body>
</html>
