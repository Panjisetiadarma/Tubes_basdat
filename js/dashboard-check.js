/**
 * Dashboard Check - Verifikasi authentication sebelum load dashboard
 */

document.addEventListener('DOMContentLoaded', async function() {
    // Cek apakah user sudah login
    const isAuthenticated = await authManager.checkAuth();
    
    if (!isAuthenticated) {
        // Redirect ke halaman login
        window.location.href = 'auth.html?redirect=' + encodeURIComponent(window.location.href);
        return;
    }
    
    // User sudah authenticated, lanjutkan load dashboard
    console.log('User authenticated, loading dashboard...');
});

