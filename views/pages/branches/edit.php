<h2>Edit Branch</h2>

<div class="card">
    <form action="/branches/update/<?= $branch['id'] ?>" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Branch Name *</label>
            <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($branch['name']) ?>">
        </div>

        <div class="form-group">
            <label>Location</label>
            <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($branch['location']) ?>">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
            <div>
                <label style="color: #00f3ff;">Latitude</label>
                <input type="text" name="latitude" class="form-control" placeholder="e.g. 12.9716" value="<?= htmlspecialchars($branch['latitude'] ?? '') ?>">
            </div>
            <div>
                <label style="color: #00f3ff;">Longitude</label>
                <input type="text" name="longitude" class="form-control" placeholder="e.g. 77.5946" value="<?= htmlspecialchars($branch['longitude'] ?? '') ?>">
            </div>
            <div>
                <label style="color: #00f3ff;">Radius (meters)</label>
                <input type="number" name="geofence_radius" class="form-control" value="<?= htmlspecialchars($branch['geofence_radius'] ?? '100') ?>">
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

        <div class="form-group">
            <label>Dashboard Background Image (Optional)</label>
            <input type="file" name="background" class="form-control" accept="image/*">
            <small style="color: #666;">Leave empty to keep current background. Recommended: 1920x1080px JPG/PNG</small>
            <?php if (!empty($branch['background_url'])): ?>
                <div style="margin-top: 10px;">
                    <p style="font-size: 0.8rem; color: #888;">Current Background:</p>
                    <img src="<?= $branch['background_url'] ?>" style="max-height: 100px; border-radius: 4px; border: 1px solid #333;">
                </div>
            <?php endif; ?>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; color: #00f3ff;">Branch Manager</label>
            <select name="manager_id" 
                    style="width: 100%; padding: 0.75rem; background: rgba(0,243,255,0.05); border: 1px solid rgba(0,243,255,0.3); border-radius: 4px; color: #fff;">
                <option value="">-- No Manager --</option>
                <?php foreach ($managers as $manager): ?>
                    <option value="<?= $manager['id'] ?>" <?= $branch['manager_id'] == $manager['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($manager['full_name']) ?> (<?= htmlspecialchars($manager['username']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; color: #00f3ff;">Phone</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($branch['phone']) ?>" 
                   style="width: 100%; padding: 0.75rem; background: rgba(0,243,255,0.05); border: 1px solid rgba(0,243,255,0.3); border-radius: 4px; color: #fff;">
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="is_active" value="1" <?= $branch['is_active'] ? 'checked' : '' ?>>
                <span style="color: #00f3ff;">Branch is Active</span>
            </label>
            <small style="color: #999; display: block; margin-top: 0.25rem;">
                Inactive branches cannot be selected or used for transactions
            </small>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">Update Branch</button>
            <a href="/branches" class="btn" style="flex: 1; text-align: center;">Cancel</a>
        </div>
    </form>
</div>
