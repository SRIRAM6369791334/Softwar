<h1>Welcome, <?= $_SESSION['username'] ?>!</h1>

<div class="card">
    <h2 style="margin-top: 0;">⏱️ Attendance</h2>
    <div style="text-align: center; padding: 20px 0;">
        <div style="font-size: 2.5rem; font-weight: bold; margin-bottom: 20px;">
            <?= date('h:i A') ?>
        </div>
        <div style="font-size: 0.9rem; color: #8b949e; margin-bottom: 20px;">
            <?= date('l, d M Y') ?>
        </div>

        <?php if(!$todayLog): ?>
            <button onclick="attemptClockIn()" class="btn" style="background: #238636;">▶ CLOCK IN</button>
            <div id="locStatus" style="font-size: 0.8rem; margin-top: 10px; color: #8b949e;"></div>
            
            <script>
            function attemptClockIn() {
                const status = document.getElementById('locStatus');
                if (!navigator.geolocation) {
                    alert("Geolocation is not supported by your browser");
                    return;
                }
                status.textContent = "Acquiring location...";
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lon = position.coords.longitude;
                        window.location.href = `/employee/clock-in?lat=${lat}&lon=${lon}`;
                    },
                    (error) => {
                        let msg = "Unable to retrieve your location";
                        if (error.code == 1) msg = "Location permission denied. Please allow access.";
                        status.textContent = msg;
                        status.style.color = '#da3633';
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            }
            </script>
        <?php elseif(empty($todayLog['clock_out'])): ?>
            <div style="margin-bottom: 15px; color: #2ea043;">
                Started at <?= date('h:i A', strtotime($todayLog['clock_in'])) ?>
            </div>
            <a href="/employee/clock-out" class="btn" style="background: #da3633;">⏹ CLOCK OUT</a>
        <?php else: ?>
            <div style="color: #8b949e;">
                Shift Completed: <?= $todayLog['total_hours'] ?> hrs
            </div>
            <div style="margin-top: 10px; color: #2ea043;">See you tomorrow! 👋</div>
        <?php endif; ?>
    </div>
</div>

<div class="stat-grid">
    <div class="stat-box">
        <div style="font-size: 1.5rem; font-weight: bold;">0</div>
        <div style="font-size: 0.8rem; color: #8b949e;">Leaves Avail</div>
    </div>
    <div class="stat-box">
        <div style="font-size: 1.5rem; font-weight: bold;">0</div>
        <div style="font-size: 0.8rem; color: #8b949e;">Unread Msgs</div>
    </div>
</div>

<?php if($nextShift): ?>
<div class="card">
    <h3 style="margin-top: 0;">📅 Next Shift</h3>
    <div>
        <?= date('D, M d', strtotime($nextShift['start_time'])) ?><br>
        <span style="color: var(--accent-color);">
            <?= date('h:i A', strtotime($nextShift['start_time'])) ?> - 
            <?= date('h:i A', strtotime($nextShift['end_time'])) ?>
        </span>
    </div>
</div>
<?php endif; ?>
