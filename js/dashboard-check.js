// js/dashboard-check.js
document.addEventListener('DOMContentLoaded', function() {
    // Cek apakah user sudah login
    const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
    
    if (!isLoggedIn && !window.location.href.includes('auth.html')) {
        // Redirect ke halaman login jika belum login
        window.location.href = 'auth.html?redirect=' + encodeURIComponent(window.location.href);
    }
});

if (!localStorage.getItem("isLoggedIn")) {
    window.location.href = "auth.html";
}
