// auth-form-fixed.js
document.addEventListener('DOMContentLoaded', function() {
    console.log('Auth form initialized...');
    initAuthPage();
});

function initAuthPage() {
    console.log('Looking for forms...');
    
    // Cari form dengan cara yang lebih fleksibel
    const loginForm = document.querySelector('#loginForm, form[data-type="login"]');
    const registerForm = document.querySelector('#registerForm, form[data-type="register"]');
    
    console.log('Login form found:', !!loginForm);
    console.log('Register form found:', !!registerForm);
    
    if (!loginForm && !registerForm) {
        console.warn('No auth forms found on this page');
        return;
    }
    
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.previousElementSibling;
            if (input && input.type === 'password') {
                input.type = 'text';
                this.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else if (input) {
                input.type = 'password';
                this.innerHTML = '<i class="fas fa-eye"></i>';
            }
        });
    });
    
    // Handle form submissions
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Login form submitted');
            
            // Validasi sederhana
            const email = document.getElementById('email')?.value;
            const password = document.getElementById('password')?.value;
            
            if (!email || !password) {
                alert('Email dan password harus diisi');
                return;
            }
            
            // Simulasi login (ganti dengan API call sebenarnya)
            showNotification('Login diproses...', 'info');
            
            // Redirect ke dashboard (simulasi)
            setTimeout(() => {
                window.location.href = 'dashboard.html';
            }, 1000);
        });
    }
    
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Register form submitted');
            
            // Validasi
            const password = document.getElementById('regPassword')?.value;
            const confirmPassword = document.getElementById('confirmPassword')?.value;
            
            if (password !== confirmPassword) {
                alert('Password tidak cocok');
                return;
            }
            
            showNotification('Pendaftaran diproses...', 'info');
            
            // Redirect ke login dengan parameter
            setTimeout(() => {
                window.location.href = 'index.html?registered=true';
            }, 1000);
        });
    }
}

// Fungsi notifikasi yang lebih sederhana
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#4CAF50' : type === 'error' ? '#f44336' : '#2196F3'};
        color: white;
        border-radius: 5px;
        z-index: 1000;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Tambahkan style untuk animasi
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);