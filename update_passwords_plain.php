<?php
/**
 * Script untuk mengupdate password yang terenkripsi menjadi plain text
 * Hanya untuk development - JANGAN gunakan di production!
 */

require_once 'koneksi.php';

// Cek apakah sudah login sebagai admin (opsional, bisa dihapus untuk kemudahan)
// start_session();
// if (!is_logged_in() || get_logged_in_user()['role'] !== 'AdminNotaris') {
//     die('Akses ditolak');
// }

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Password - Hapus Enkripsi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 2rem;
            background: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header bg-warning">
                <h4 class="mb-0">⚠️ Update Password - Hapus Enkripsi</h4>
            </div>
            <div class="card-body">
                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
                    $username = escape($_POST['username']);
                    $new_password = escape($_POST['new_password']);
                    
                    $query = "UPDATE User SET password = '$new_password' WHERE username = '$username'";
                    if (query($query)) {
                        echo '<div class="alert alert-success">Password berhasil diupdate untuk user: ' . htmlspecialchars($username) . '</div>';
                    } else {
                        echo '<div class="alert alert-danger">Gagal update password: ' . error() . '</div>';
                    }
                }
                
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_all'])) {
                    // Update semua password yang terenkripsi
                    $query = "SELECT id_user, username, password FROM User WHERE password LIKE '$2y$%' OR LENGTH(password) > 20";
                    $result = query($query);
                    
                    $updated = 0;
                    $default_password = 'password123';
                    
                    if ($result) {
                        while ($row = fetch_array($result)) {
                            $id = (int)$row['id_user'];
                            $username_escaped = escape($row['username']);
                            
                            // Set password default untuk semua user yang terenkripsi
                            $update_query = "UPDATE User SET password = '$default_password' WHERE id_user = $id";
                            if (query($update_query)) {
                                $updated++;
                            }
                        }
                    }
                    
                    echo '<div class="alert alert-success">Berhasil mengupdate ' . $updated . ' password menjadi: ' . $default_password . '</div>';
                }
                ?>
                
                <h5>Update Password Individual</h5>
                <form method="POST" class="mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="username" class="form-control" placeholder="Username" required>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="new_password" class="form-control" placeholder="Password Baru" required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" name="update" class="btn btn-primary w-100">Update</button>
                        </div>
                    </div>
                </form>
                
                <hr>
                
                <h5>Update Semua Password Terenkripsi</h5>
                <p class="text-muted">Akan mengupdate semua password yang terenkripsi menjadi: <code>password123</code></p>
                <form method="POST" onsubmit="return confirm('Yakin ingin mengupdate semua password?')">
                    <button type="submit" name="update_all" class="btn btn-warning">Update Semua Password</button>
                </form>
                
                <hr>
                
                <h5>Daftar User dan Status Password</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Nama</th>
                            <th>Role</th>
                            <th>Status Password</th>
                            <th>Password (Preview)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT id_user, username, nama_lengkap, role, password FROM User ORDER BY id_user";
                        $result = query($query);
                        
                        if ($result) {
                            while ($row = fetch_array($result)) {
                                $is_encrypted = (strpos($row['password'], '$2y$') === 0 || strlen($row['password']) > 20);
                                $status = $is_encrypted ? '<span class="badge bg-danger">TERENKRIPSI</span>' : '<span class="badge bg-success">PLAIN TEXT</span>';
                                $password_preview = strlen($row['password']) > 20 ? substr($row['password'], 0, 20) . '...' : $row['password'];
                                
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($row['id_user']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['username']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['nama_lengkap']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['role']) . '</td>';
                                echo '<td>' . $status . '</td>';
                                echo '<td><code>' . htmlspecialchars($password_preview) . '</code></td>';
                                echo '</tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
                
                <div class="alert alert-info mt-3">
                    <strong>Catatan:</strong><br>
                    - Setelah mengupdate password, gunakan password baru untuk login<br>
                    - Password default untuk semua user setelah update: <code>password123</code><br>
                    - Script ini hanya untuk development, jangan gunakan di production!
                </div>
            </div>
        </div>
    </div>
</body>
</html>

