<?php
require_once 'koneksi.php';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = isset($_POST['username']) ? sanitize($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (empty($username) || empty($password)) {
        $login_error = 'Username dan password harus diisi';
    } else {
        $username_escaped = escape($username);
        $query = "SELECT id_user, username, password, nama_lengkap, role FROM User WHERE username = '$username_escaped'";
        $result = query($query);
        
        if ($result && num_rows($result) > 0) {
            $user = fetch_array($result);
            
            // Verifikasi password tanpa enkripsi
            if (verify_password($password, $user['password'])) {
                start_session();
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['login_time'] = time();
                
                header('Location: dashboard.php');
                exit;
            } else {
                $login_error = 'Username atau password salah.';
            }
        } else {
            $login_error = 'Username tidak ditemukan';
        }
    }
}

// Handle register
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $nama_lengkap = isset($_POST['nama_lengkap']) ? sanitize($_POST['nama_lengkap']) : '';
    $username = isset($_POST['username']) ? sanitize($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    
    $register_errors = [];
    
    if (empty($nama_lengkap)) {
        $register_errors[] = 'Nama lengkap harus diisi';
    }
    if (empty($username)) {
        $register_errors[] = 'Username harus diisi';
    } else if (strlen($username) < 3) {
        $register_errors[] = 'Username minimal 3 karakter';
    } else if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $register_errors[] = 'Username hanya boleh mengandung huruf, angka, dan underscore';
    }
    if (empty($password)) {
        $register_errors[] = 'Password harus diisi';
    } else if (strlen($password) < 3) {
        $register_errors[] = 'Password minimal 3 karakter';
    }
    if ($password !== $confirm_password) {
        $register_errors[] = 'Password dan konfirmasi password tidak cocok';
    }
    
    if (empty($register_errors)) {
        $username_escaped = escape($username);
        $check_query = "SELECT id_user FROM User WHERE username = '$username_escaped'";
        $check_result = query($check_query);
        
        if ($check_result && num_rows($check_result) > 0) {
            $register_error = 'Username sudah terdaftar';
        } else {
            // Password tidak dienkripsi - langsung simpan
            $password_escaped = escape($password);
            $nama_escaped = escape($nama_lengkap);
            $role = 'user';
            
            $insert_query = "INSERT INTO User (username, password, nama_lengkap, role) VALUES ('$username_escaped', '$password_escaped', '$nama_escaped', '$role')";
            
            if (query($insert_query)) {
                $user_id = insert_id();
                start_session();
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                $_SESSION['nama_lengkap'] = $nama_lengkap;
                $_SESSION['role'] = $role;
                $_SESSION['login_time'] = time();
                
                header('Location: dashboard.php');
                exit;
            } else {
                $register_error = 'Gagal menyimpan data user: ' . error();
            }
        }
    } else {
        $register_error = implode(', ', $register_errors);
    }
}

// Check if already logged in
// start_session();
// if (is_logged_in()) {
//     header('Location: dashboard.php');
//     exit;
// }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register - Notaris Pro</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles/auth.css">

    
</head>
<body>
    <div class="container-auth">
        <!-- Logo -->
        <div class="logo">
            <h1>Notaris<span style="color: #6A85FF;">Pro</span></h1>
            <p>Layanan Notaris Profesional</p>
        </div>

        <!-- Tab Switcher -->
        <div class="tab-switcher glass-effect">
            <button class="tab-btn active" id="loginTabBtn">Login</button>
            <button class="tab-btn" id="registerTabBtn">Daftar</button>
        </div>

        <!-- Login Form -->
        <div class="auth-card glass-effect" id="loginForm">
            <h2 class="form-title">Masuk ke Akun</h2>
            <p class="form-subtitle">Masukkan kredensial Anda untuk melanjutkan</p>
            
            <?php if (isset($login_error)): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($login_error); ?>
                    <br><br>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <input type="hidden" name="action" value="login">
                <div class="form-group">
                    <label class="form-label" for="loginUsername">
                        <i class="fas fa-user me-2"></i>Username
                    </label>
                    <input type="text" id="loginUsername" name="username" class="form-control" 
                           placeholder="Masukkan username" required 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="loginPassword">
                        <i class="fas fa-lock me-2"></i>Password
                    </label>
                    <div class="input-group">
                        <input type="password" id="loginPassword" name="password" class="form-control" 
                               placeholder="Masukkan password" required>
                        <button type="button" class="toggle-password" data-target="loginPassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-group" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <input type="checkbox" id="rememberMe" name="remember_me">
                        <label for="rememberMe" style="font-size: 0.9rem; margin-left: 5px;">Ingat saya</label>
                    </div>
                    <a href="#" id="forgotPassword" style="font-size: 0.9rem; color: var(--primary-blue);">Lupa password?</a>
                </div>
                
                <button type="submit" class="btn-primary" id="loginBtn">
                    <i class="fas fa-sign-in-alt me-2"></i>Masuk
                </button>
            </form>
            
            <div class="form-footer">
                <p>Belum punya akun? <a href="#" id="showRegister">Daftar di sini</a></p>
            </div>
        </div>

        <!-- Register Form -->
        <div class="auth-card glass-effect" id="registerForm">
            <h2 class="form-title">Buat Akun Baru</h2>
            <p class="form-subtitle">Daftar untuk mengakses layanan notaris</p>
            
            <?php if (isset($register_error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($register_error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <input type="hidden" name="action" value="register">
                <div class="form-group">
                    <label class="form-label" for="fullName">
                        <i class="fas fa-user me-2"></i>Nama Lengkap
                    </label>
                    <input type="text" id="fullName" name="nama_lengkap" class="form-control" 
                           placeholder="Nama lengkap" required 
                           value="<?php echo isset($_POST['nama_lengkap']) ? htmlspecialchars($_POST['nama_lengkap']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="registerUsername">
                        <i class="fas fa-user me-2"></i>Username
                    </label>
                    <input type="text" id="registerUsername" name="username" class="form-control" 
                           placeholder="Masukkan username" required 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                    <small class="text-muted">Minimal 3 karakter, hanya huruf, angka, dan underscore</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="registerPassword">
                        <i class="fas fa-lock me-2"></i>Password
                    </label>
                    <div class="input-group">
                        <input type="password" id="registerPassword" name="password" class="form-control" 
                               placeholder="Minimal 3 karakter" required>
                        <button type="button" class="toggle-password" data-target="registerPassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="confirmPassword">
                        <i class="fas fa-lock me-2"></i>Konfirmasi Password
                    </label>
                    <div class="input-group">
                        <input type="password" id="confirmPassword" name="confirm_password" class="form-control" 
                               placeholder="Ulangi password" required>
                        <button type="button" class="toggle-password" data-target="confirmPassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-group">
                    <input type="checkbox" id="agreeTerms" required>
                    <label for="agreeTerms" style="font-size: 0.9rem; margin-left: 5px;">
                        Saya setuju dengan <a href="#" style="color: var(--primary-blue);">syarat & ketentuan</a>
                    </label>
                </div>
                
                <button type="submit" class="btn-primary" id="registerBtn">
                    <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                </button>
            </form>
            
            <div class="form-footer">
                <p>Sudah punya akun? <a href="#" id="showLogin">Masuk di sini</a></p>
            </div>
        </div>

        <!-- Back to Home -->
        <div class="back-home">
            <a href="index.html">
                <i class="fas fa-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>
        
        <?php if (isset($_GET['logout'])): ?>
            <script>
                alert('Logout berhasil');
            </script>
        <?php endif; ?>
    </div>

    <!-- JavaScript -->
    <script>
        // DOM Elements
        const loginTabBtn = document.getElementById('loginTabBtn');
        const registerTabBtn = document.getElementById('registerTabBtn');
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        const showRegister = document.getElementById('showRegister');
        const showLogin = document.getElementById('showLogin');
        const togglePasswordBtns = document.querySelectorAll('.toggle-password');
        const forgotPassword = document.getElementById('forgotPassword');

        // Tab Switching
        loginTabBtn.addEventListener('click', () => {
            loginTabBtn.classList.add('active');
            registerTabBtn.classList.remove('active');
            loginForm.style.display = 'block';
            registerForm.style.display = 'none';
        });

        registerTabBtn.addEventListener('click', () => {
            registerTabBtn.classList.add('active');
            loginTabBtn.classList.remove('active');
            registerForm.style.display = 'block';
            loginForm.style.display = 'none';
        });

        showRegister.addEventListener('click', (e) => {
            e.preventDefault();
            registerTabBtn.click();
        });

        showLogin.addEventListener('click', (e) => {
            e.preventDefault();
            loginTabBtn.click();
        });

        // Toggle Password Visibility
        togglePasswordBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const targetId = btn.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = btn.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.className = 'fas fa-eye-slash';
                } else {
                    input.type = 'password';
                    icon.className = 'fas fa-eye';
                }
            });
        });

        // Forgot Password
        forgotPassword.addEventListener('click', function(e) {
            e.preventDefault();
            alert('Fitur reset password akan segera tersedia. Silakan hubungi administrator.');
        });

        // Load remembered username
        document.addEventListener('DOMContentLoaded', function() {
            const rememberedUsername = localStorage.getItem('remember_username');
            if (rememberedUsername) {
                document.getElementById('loginUsername').value = rememberedUsername;
                document.getElementById('rememberMe').checked = true;
            }
            
            // Auto focus on first input
            document.getElementById('loginUsername').focus();
            
            // Check URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('tab') === 'register') {
                registerTabBtn.click();
            }
        });
    </script>
</body>
</html>

