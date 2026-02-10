<h2>Add New Branch</h2>

<div class="card" style="max-width: 600px;">
    <form action="/branches/store" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Branch Name *</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label>Address/Location Name</label>
            <input type="text" name="location" class="form-control">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
            <div>
                <label style="color: #00f3ff;">Latitude</label>
                <input type="text" name="latitude" class="form-control" placeholder="e.g. 12.9716">
            </div>
            <div>
                <label style="color: #00f3ff;">Longitude</label>
                <input type="text" name="longitude" class="form-control" placeholder="e.g. 77.5946">
            </div>
            <div>
                <label style="color: #00f3ff;">Radius (m)</label>
                <input type="number" name="geofence_radius" class="form-control" value="100">
            </div>
        </div>
        <div style="margin-bottom: 1rem;">
             <button type="button" class="btn" style="font-size: 0.8rem;" onclick="getLocation()">📍 Get Current Location</button>
             <span id="geoStatus" style="font-size: 0.8rem; color: #888; margin-left: 10px;"></span>
        </div>

        <script>
        function getLocation() {
            const status = document.getElementById('geoStatus');
            if (!navigator.geolocation) {
                status.textContent = "Geolocation is not supported by your browser";
                return;
            }
            status.textContent = "Locating...";
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    document.getElementsByName('latitude')[0].value = position.coords.latitude;
                    document.getElementsByName('longitude')[0].value = position.coords.longitude;
                    status.textContent = "Location set!";
                },
                () => { status.textContent = "Unable to retrieve your location"; }
            );
        }
        </script>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; color: #00f3ff;">Branch Manager</label>
            <select name="manager_id" 
                    style="width: 100%; padding: 0.75rem; background: rgba(0,243,255,0.05); border: 1px solid rgba(0,243,255,0.3); border-radius: 4px; color: #fff;">
                <option value="">-- Select Manager (Optional) --</option>
                <?php foreach ($managers as $manager): ?>
                    <option value="<?= $manager['id'] ?>">
                        <?= htmlspecialchars($manager['full_name']) ?> (<?= htmlspecialchars($manager['username']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; color: #00f3ff;">Phone</label>
            <input type="text" name="phone" placeholder="e.g., 9876543210" 
                   style="width: 100%; padding: 0.75rem; background: rgba(0,243,255,0.05); border: 1px solid rgba(0,243,255,0.3); border-radius: 4px; color: #fff;">
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">Create Branch</button>
            <a href="/branches" class="btn" style="flex: 1; text-align: center;">Cancel</a>
        </div>
    </form>
</div>
