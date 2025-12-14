<?php
require_once 'koneksi.php';
require_once 'common_crud.php';

// Cek login
start_session();
if (!is_logged_in()) {
    header('Location: auth.php');
    exit;
}

$current_user = get_logged_in_user();
$is_admin = ($current_user['role'] === 'AdminNotaris');

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: auth.php');
    exit;
}

$error = '';
$success = '';

// Handle update profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profil') {
    $nama_lengkap = escape($_POST['nama_lengkap']);
    $nomor_telepon = !empty($_POST['nomor_telepon']) ? escape($_POST['nomor_telepon']) : null;
    $alamat = !empty($_POST['alamat']) ? escape($_POST['alamat']) : null;
    $bio = !empty($_POST['bio']) ? escape($_POST['bio']) : null;
    
    $data = [
        'nama_lengkap' => $nama_lengkap
    ];
    
    if ($nomor_telepon !== null) $data['nomor_telepon'] = $nomor_telepon;
    if ($alamat !== null) $data['alamat'] = $alamat;
    if ($bio !== null) $data['bio'] = $bio;
    
    // Handle upload foto profil
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/profil/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_name = time() . '_' . basename($_FILES['foto_profil']['name']);
        $file_path = $upload_dir . $file_name;
        
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
        $file_type = $_FILES['foto_profil']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $file_path)) {
                // Hapus foto lama jika ada
                if (!empty($current_user['foto_profil']) && file_exists($current_user['foto_profil'])) {
                    @unlink($current_user['foto_profil']);
                }
                $data['foto_profil'] = $file_path;
            } else {
                $error = 'Gagal mengupload foto profil';
            }
        } else {
            $error = 'Format file tidak didukung. Gunakan JPG, PNG, atau GIF';
        }
    }
    
    if (empty($error)) {
        if (updateData('User', $data, "id_user = " . (int)$current_user['id_user'])) {
            $success = 'Profil berhasil diupdate!';
            // Refresh user data
            $current_user = get_logged_in_user();
        } else {
            $error = 'Gagal mengupdate profil: ' . error();
        }
    }
}

// Handle update password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_password') {
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $password_konfirmasi = $_POST['password_konfirmasi'];
    
    // Verifikasi password lama (tanpa enkripsi)
    if ($password_lama === $current_user['password']) {
        if ($password_baru === $password_konfirmasi) {
            if (strlen($password_baru) >= 3) {
                // Password tidak dienkripsi - langsung simpan
                $password_escaped = escape($password_baru);
                if (updateData('User', ['password' => $password_escaped], "id_user = " . (int)$current_user['id_user'])) {
                    $success = 'Password berhasil diubah!';
                } else {
                    $error = 'Gagal mengubah password: ' . error();
                }
            } else {
                $error = 'Password baru minimal 3 karakter';
            }
        } else {
            $error = 'Password baru dan konfirmasi tidak cocok';
        }
    } else {
        $error = 'Password lama salah';
    }
}

// Update last_login saat membuka profil
if (empty($current_user['last_login']) || (time() - strtotime($current_user['last_login'])) > 300) {
    $query_update_login = "UPDATE User SET last_login = NOW() WHERE id_user = " . (int)$current_user['id_user'];
    query($query_update_login);
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profil Akun | NotarisPro</title>

<!-- Dependencies -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="styles/main.css" rel="stylesheet">
<link href="styles/navbar.css" rel="stylesheet">
<link href="styles/sidebar.css" rel="stylesheet">
<link href="styles/dashboard.css" rel="stylesheet">

<style>
.profile-header {
    background: linear-gradient(135deg, #6A85FF 0%, #7DE2F2 100%);
    border-radius: 16px;
    padding: 2rem;
    color: white;
    margin-bottom: 2rem;
}
.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 4px solid white;
    object-fit: cover;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: #6A85FF;
}
.profile-card {
    border: none;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-bottom: 1.5rem;
}
.profile-card .card-header {
    background: rgba(106, 133, 255, 0.1);
    border-bottom: 1px solid rgba(106, 133, 255, 0.2);
    border-radius: 16px 16px 0 0;
    padding: 1rem 1.5rem;
}
.info-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid #f0f0f0;
}
.info-item:last-child {
    border-bottom: none;
}
.info-label {
    font-weight: 600;
    color: #666;
    font-size: 0.9rem;
}
.info-value {
    color: #333;
    font-size: 1rem;
}
</style>
</head>
<body class="dashboard-body">

<!-- Navbar -->
<?php include 'components/navbar.php'; ?>

<div class="dashboard-container">
    <!-- Sidebar -->
    <?php include 'components/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="row align-items-center">
                <div class="col-auto">
                    <?php if (!empty($current_user['foto_profil']) && file_exists($current_user['foto_profil'])): ?>
                        <img src="<?= htmlspecialchars($current_user['foto_profil']) ?>" alt="Foto Profil" class="profile-avatar">
                    <?php else: ?>
                        <div class="profile-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col">
                    <h2 class="mb-1"><?= htmlspecialchars($current_user['nama_lengkap']) ?></h2>
                    <p class="mb-1 opacity-75">
                        <i class="fas fa-user-tag me-2"></i>
                        <?= htmlspecialchars(ucfirst($current_user['role'])) ?>
                    </p>
                    <p class="mb-0 opacity-75">
                        <i class="fas fa-at me-2"></i>
                        <?= htmlspecialchars($current_user['username']) ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        <?php if(!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Informasi Profil -->
            <div class="col-lg-8">
                <div class="profile-card card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user-circle me-2"></i>Informasi Profil</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="update_profil">
                            
                            <div class="mb-3">
                                <label class="form-label">Foto Profil</label>
                                <div class="d-flex align-items-center gap-3">
                                    <?php if (!empty($current_user['foto_profil']) && file_exists($current_user['foto_profil'])): ?>
                                        <img src="<?= htmlspecialchars($current_user['foto_profil']) ?>" 
                                             alt="Foto Profil" 
                                             style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">
                                    <?php else: ?>
                                        <div style="width: 80px; height: 80px; border-radius: 50%; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-user fa-2x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="foto_profil" class="form-control" accept="image/*">
                                </div>
                                <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 2MB</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama_lengkap" class="form-control" 
                                       value="<?= htmlspecialchars($current_user['nama_lengkap']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" 
                                       value="<?= htmlspecialchars($current_user['username']) ?>" disabled>
                                <small class="text-muted">Username tidak dapat diubah</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Role</label>
                                <input type="text" class="form-control" 
                                       value="<?= htmlspecialchars(ucfirst($current_user['role'])) ?>" disabled>
                                <small class="text-muted">Role tidak dapat diubah</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nomor Telepon</label>
                                <input type="text" name="nomor_telepon" class="form-control" 
                                       value="<?= htmlspecialchars($current_user['nomor_telepon'] ?? '') ?>" 
                                       placeholder="08xxxxxxxxxx">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea name="alamat" class="form-control" rows="3" 
                                          placeholder="Masukkan alamat lengkap"><?= htmlspecialchars($current_user['alamat'] ?? '') ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Bio</label>
                                <textarea name="bio" class="form-control" rows="3" 
                                          placeholder="Tuliskan bio singkat tentang Anda"><?= htmlspecialchars($current_user['bio'] ?? '') ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Informasi Akun & Ubah Password -->
            <div class="col-lg-4">
                <!-- Informasi Akun -->
                <div class="profile-card card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Akun</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-item">
                            <div class="info-label">ID User</div>
                            <div class="info-value">#<?= htmlspecialchars($current_user['id_user']) ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Tanggal Bergabung</div>
                            <div class="info-value">
                                <?= date('d M Y', strtotime($current_user['created_at'])) ?>
                            </div>
                        </div>
                        <?php if (!empty($current_user['last_login'])): ?>
                        <div class="info-item">
                            <div class="info-label">Login Terakhir</div>
                            <div class="info-value">
                                <?= date('d M Y H:i', strtotime($current_user['last_login'])) ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Ubah Password -->
                <div class="profile-card card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Ubah Password</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="update_password">
                            
                            <div class="mb-3">
                                <label class="form-label">Password Lama <span class="text-danger">*</span></label>
                                <input type="password" name="password_lama" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password Baru <span class="text-danger">*</span></label>
                                <input type="password" name="password_baru" class="form-control" 
                                       minlength="3" required>
                                <small class="text-muted">Minimal 3 karakter</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                                <input type="password" name="password_konfirmasi" class="form-control" 
                                       minlength="3" required>
                            </div>

                            <button type="submit" class="btn btn-warning w-100">
                                <i class="fas fa-key me-2"></i>Ubah Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Sidebar toggle
const sidebarToggle = document.getElementById('sidebarToggle');
const sidebar = document.getElementById('sidebar');
const mainContent = document.getElementById('mainContent');

if (sidebarToggle && sidebar && mainContent) {
    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('show');
        mainContent.classList.toggle('blur');
    });
}
</script>
</body>
</html>

