<h1>Welcome, <?= $_SESSION['username'] ?>!</h1>

<?php if (!empty($_GET['error'])): ?>
    <div class="card" style="border-color:#da3633;color:#ffb4b4;"><?= htmlspecialchars($_GET['error']) ?></div>
<?php endif; ?>
<?php if (!empty($_GET['success'])): ?>
    <div class="card" style="border-color:#2ea043;color:#9be9a8;"><?= htmlspecialchars($_GET['success']) ?></div>
<?php endif; ?>

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
            <button onclick="startAttendanceFlow('clock-in')" class="btn" style="background: #238636;">📸 ▶ CLOCK IN</button>
            <div id="locStatus" style="font-size: 0.8rem; margin-top: 10px; color: #8b949e;"></div>
        <?php elseif(empty($todayLog['clock_out'])): ?>
            <div style="margin-bottom: 15px; color: #2ea043;">
                Started at <?= date('h:i A', strtotime($todayLog['clock_in'])) ?>
            </div>
            <button onclick="startAttendanceFlow('clock-out')" class="btn" style="background: #da3633;">📸 ⏹ CLOCK OUT</button>
            <div id="locStatus" style="font-size: 0.8rem; margin-top: 10px; color: #8b949e;"></div>
        <?php else: ?>
            <div style="color: #8b949e;">
                Shift Completed: <?= $todayLog['total_hours'] ?> hrs
            </div>
            <div style="margin-top: 10px; color: #2ea043;">See you tomorrow! 👋</div>
        <?php endif; ?>
    </div>
</div>

<div id="cameraModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.8); z-index:2000;">
    <div style="max-width:460px; margin:5vh auto; background:#161b22; border:1px solid #30363d; border-radius:10px; padding:16px;">
        <h3 style="margin-top:0;">Capture Attendance Selfie</h3>
        <video id="cameraVideo" autoplay playsinline style="width:100%; border-radius:8px; background:#000;"></video>
        <canvas id="cameraCanvas" width="640" height="480" style="display:none;"></canvas>
        <img id="cameraPreview" alt="Selfie preview" style="display:none; width:100%; margin-top:10px; border-radius:8px; border:1px solid #30363d;"/>

        <div style="display:flex; gap:8px; margin-top:12px;">
            <button class="btn" type="button" onclick="captureSelfie()">Capture</button>
            <button class="btn" type="button" style="background:#1f6feb;" onclick="submitAttendance()">Submit</button>
            <button class="btn" type="button" style="background:#6e7681;" onclick="closeCameraModal()">Cancel</button>
        </div>
        <div id="cameraStatus" style="font-size:.82rem; color:#8b949e; margin-top:8px;"></div>
    </div>
</div>

<form id="attendanceForm" method="POST" style="display:none;">
    <input type="hidden" name="lat" id="latField">
    <input type="hidden" name="lon" id="lonField">
    <input type="hidden" name="photo_data" id="photoField">
    <input type="hidden" name="attendance_csrf_token" value="<?= htmlspecialchars($attendanceCsrfToken ?? '') ?>">
</form>

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

<script>
let currentMode = null;
let currentStream = null;

async function startAttendanceFlow(mode) {
    const status = document.getElementById('locStatus');
    currentMode = mode;

    if (!navigator.geolocation) {
        status.textContent = 'Geolocation is not supported by your browser.';
        status.style.color = '#da3633';
        return;
    }

    status.textContent = 'Acquiring location...';

    navigator.geolocation.getCurrentPosition(async (position) => {
        document.getElementById('latField').value = position.coords.latitude;
        document.getElementById('lonField').value = position.coords.longitude;
        status.textContent = 'Location captured. Opening camera...';

        const modal = document.getElementById('cameraModal');
        const video = document.getElementById('cameraVideo');
        const cameraStatus = document.getElementById('cameraStatus');
        modal.style.display = 'block';

        try {
            currentStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
            video.srcObject = currentStream;
            cameraStatus.textContent = 'Camera ready. Capture then submit.';
        } catch (e) {
            cameraStatus.textContent = 'Camera unavailable. You can still submit without selfie.';
            cameraStatus.style.color = '#f2cc60';
        }
    }, (error) => {
        let msg = 'Unable to retrieve your location.';
        if (error.code === 1) msg = 'Location permission denied. Please allow access.';
        status.textContent = msg;
        status.style.color = '#da3633';
    }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
}

function captureSelfie() {
    const video = document.getElementById('cameraVideo');
    const canvas = document.getElementById('cameraCanvas');
    const preview = document.getElementById('cameraPreview');
    const ctx = canvas.getContext('2d');

    if (!video.srcObject) {
        document.getElementById('cameraStatus').textContent = 'No camera stream. Submit without selfie or re-open camera.';
        return;
    }

    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    const dataUrl = canvas.toDataURL('image/jpeg', 0.82);
    document.getElementById('photoField').value = dataUrl;

    preview.src = dataUrl;
    preview.style.display = 'block';
    document.getElementById('cameraStatus').textContent = 'Selfie captured successfully.';
}

function submitAttendance() {
    if (!currentMode) return;

    if (!document.getElementById('photoField').value) {
        document.getElementById('cameraStatus').textContent = 'Please capture selfie before submitting attendance.';
        document.getElementById('cameraStatus').style.color = '#da3633';
        return;
    }

    const form = document.getElementById('attendanceForm');
    form.action = currentMode === 'clock-in' ? '/employee/clock-in' : '/employee/clock-out';
    closeCameraModal(false);
    form.submit();
}

function closeCameraModal(clear = true) {
    document.getElementById('cameraModal').style.display = 'none';
    if (currentStream) {
        currentStream.getTracks().forEach(track => track.stop());
        currentStream = null;
    }
    document.getElementById('cameraVideo').srcObject = null;

    if (clear) {
        document.getElementById('photoField').value = '';
        document.getElementById('cameraPreview').style.display = 'none';
        document.getElementById('cameraStatus').textContent = '';
    }
}
</script>
