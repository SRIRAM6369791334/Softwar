<div class="login-container" style="display: flex; justify-content: center; align-items: center; height: 100vh; background: #0d1117;">
    <div style="background: #161b22; padding: 2.5rem; border-radius: 16px; border: 1px solid #30363d; width: 100%; max-width: 400px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
        <h1 style="text-align: center; color: #58a6ff; margin-bottom: 1rem;">Supplier Portal</h1>
        <p style="text-align: center; color: #8b949e; margin-bottom: 2rem;">Access your purchase orders and payments</p>

        <?php if (isset($error)): ?>
            <div style="background: rgba(248, 81, 73, 0.1); color: #f85149; padding: 12px; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; border: 1px solid rgba(248, 81, 73, 0.4);">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="/vendor/login" method="POST">
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; color: #8b949e; font-size: 0.85rem; margin-bottom: 8px;">Email Address</label>
                <input type="email" name="email" required style="width: 100%; background: #0d1117; border: 1px solid #30363d; padding: 12px; border-radius: 8px; color: #fff; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: block; color: #8b949e; font-size: 0.85rem; margin-bottom: 8px;">Access Key / Password</label>
                <input type="password" name="password" required style="width: 100%; background: #0d1117; border: 1px solid #30363d; padding: 12px; border-radius: 8px; color: #fff; box-sizing: border-box;">
            </div>

            <button type="submit" style="width: 100%; background: #58a6ff; color: #fff; border: none; padding: 14px; border-radius: 8px; font-weight: 600; cursor: pointer;">Sign In</button>
        </form>
        
        <div style="margin-top: 2rem; border-top: 1px solid #30363d; padding-top: 1.5rem; text-align: center; color: #8b949e; font-size: 0.8rem;">
            Supermarket OS Supplier Network
        </div>
    </div>
</div>

<style>
    body { font-family: 'Outfit', sans-serif; margin: 0; }
    input:focus { border-color: #58a6ff !important; outline: none; }
</style>
