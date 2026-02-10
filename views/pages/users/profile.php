<h1>My Profile</h1>

<div class="card" style="margin-bottom: 2rem;">
    <h2>User Details</h2>
    <div style="display: grid; grid-template-columns: 100px 1fr; gap: 10px; align-items: center;">
        <div style="color: #8b949e;">Name:</div>
        <div><?= htmlspecialchars($user['full_name']) ?></div>
        
        <div style="color: #8b949e;">Username:</div>
        <div><?= htmlspecialchars($user['username']) ?></div>
        
        <div style="color: #8b949e;">Role:</div>
        <div><?= $user['role_id'] == 1 ? 'Administrator' : ($user['role_id'] == 2 ? 'Manager' : 'Cashier') ?></div>
    </div>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h2>Biometric Security</h2>
        <button onclick="registerBiometric()" class="btn btn-primary">
            ➕ Register this Device
        </button>
    </div>

    <table>
        <thead>
            <tr>
                <th>Device Label</th>
                <th>Registered On</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($devices)): ?>
                <tr><td colspan="3" style="text-align: center; color: #8b949e;">No devices registered</td></tr>
            <?php else: ?>
                <?php foreach($devices as $device): ?>
                <tr>
                    <td><?= htmlspecialchars($device['label']) ?></td>
                    <td><?= $device['created_at'] ?></td>
                    <td style="color: var(--success);">Active</td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
async function registerBiometric() {
    try {
        // 1. Get Options
        const res = await fetch('/auth/biometric/register-options');
        const options = await res.json();
        
        // 2. Decode Binary Data
        options.challenge = Uint8Array.from(atob(options.challenge), c => c.charCodeAt(0));
        options.user.id = Uint8Array.from(atob(options.user.id), c => c.charCodeAt(0));

        // 3. Create Credential
        const credential = await navigator.credentials.create({ publicKey: options });

        // 4. Send to Backend
        const verifyRes = await fetch('/auth/biometric/register', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                id: credential.id,
                rawId: btoa(String.fromCharCode(...new Uint8Array(credential.rawId))),
                type: credential.type,
                response: {
                    attestationObject: btoa(String.fromCharCode(...new Uint8Array(credential.response.attestationObject))),
                    clientDataJSON: btoa(String.fromCharCode(...new Uint8Array(credential.response.clientDataJSON)))
                }
            })
        });

        const result = await verifyRes.json();
        if (result.success) {
            alert('Device Registered Successfully!');
            window.location.reload();
        } else {
            alert('Registration Failed: ' + result.message);
        }
    } catch (e) {
        console.error(e);
        alert('Biometric registration failed definition.');
    }
}
</script>
