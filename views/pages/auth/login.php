<div class="auth-container">
    <h1>System Access</h1>
    
    <?php if (isset($error)): ?>
        <div class="alert">
    <?php if (isset($error)): ?>
        <div class="alert" style="background: rgba(218, 54, 51, 0.2); border: 1px solid #da3633; color: #ffadad; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
            ⚠️ <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
        </div>
    <?php endif; ?>

    <form action="/login" method="POST">
        <div class="form-group">
            <label>Operator ID</label>
            <input type="text" name="username" required autofocus placeholder="admin" autocomplete="off">
        </div>
        
        <div class="form-group">
            <label>Passkey</label>
            <input type="password" name="password" required placeholder="••••••">
        </div>

        <button type="submit">Initialize >></button>
    </form>

    <div class="footer-text">
        SUPERMARKET OPERATING SYSTEM v1.0<br>
        SECURE CONNECTION ESTABLISHED
    </div>
    
    <!-- Biometric Login -->
    <div style="text-align: center; margin-top: 20px;">
        <button type="button" onclick="startBiometricLogin()" class="btn-mode" style="padding: 10px 20px; border-radius: 20px;">
            🆔 Login with Device
        </button>
    </div>

    <script>
    async function startBiometricLogin() {
        try {
            // 1. Get Challenge
            const res = await fetch('/auth/biometric/login-options');
            const options = await res.json();
            
            // 2. Decode Challenge
            options.challenge = Uint8Array.from(atob(options.challenge), c => c.charCodeAt(0));
            if (options.allowCredentials) {
                options.allowCredentials = options.allowCredentials.map(c => {
                    c.id = Uint8Array.from(atob(c.id), c => c.charCodeAt(0));
                    return c;
                });
            }

            // 3. Prompt User
            const credential = await navigator.credentials.get({ publicKey: options });
            
            // 4. Verify
            const verifyRes = await fetch('/auth/biometric/verify', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    id: credential.id,
                    rawId: btoa(String.fromCharCode(...new Uint8Array(credential.rawId))),
                    type: credential.type
                })
            });

            const result = await verifyRes.json();
            if (result.success) {
                window.location.href = result.redirect;
            } else {
                alert('Biometric Login Failed: ' + result.message);
            }
        } catch (e) {
            console.error(e);
            alert('Biometric authentication failed or cancelled.');
        }
    }
    
    // Add Base64 Polyfill if needed (not needed for basic btoa/atob in modern browsers)
    </script>
</div>
