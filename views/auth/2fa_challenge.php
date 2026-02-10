<?php
/**
 * 2FA Challenge View
 * Shown during login when 2FA is enabled
 */
?>
<div class="min-h-screen flex items-center justify-center bg-slate-50 dark:bg-slate-900 p-6">
    <div class="max-w-md w-full">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-8 text-center bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700">
                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Two-Step Verification</h1>
                <p class="text-slate-500 dark:text-slate-400 mt-1">Enter the 6-digit code from your app</p>
            </div>

            <div class="p-8">
                <?php if (isset($data['error'])): ?>
                    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 rounded-xl text-sm text-center">
                        <?php echo htmlspecialchars($data['error']); ?>
                    </div>
                <?php endif; ?>

                <form action="/auth/2fa/login" method="POST" class="space-y-6">
                    <input type="hidden" name="csrf_token" value="<?php echo \App\Core\Security::getCSRFToken(); ?>">
                    
                    <div>
                        <input type="text" name="code" placeholder="000000" maxlength="6" autofocus
                               class="w-full text-center text-4xl tracking-[0.4em] font-mono p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all outline-none" required>
                    </div>

                    <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-500/20 transform transition-transform active:scale-95">
                        Verify & Sign In
                    </button>
                    
                    <p class="text-center text-sm text-slate-500 dark:text-slate-400">
                        Lost access? Use a <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">backup code</a>
                    </p>
                </form>
            </div>
            
            <div class="p-6 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-200 dark:border-slate-700 text-center">
                <a href="/logout" class="text-sm text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">Cancel and sign out</a>
            </div>
        </div>
    </div>
</div>
