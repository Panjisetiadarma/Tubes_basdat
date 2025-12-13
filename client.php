<?php
require_once 'koneksi.php';
$current_user = get_logged_in_user();
$is_admin = ($current_user['role'] === 'AdminNotaris');

$page_title = 'Data Client';

// Ambil data client
$query = "SELECT Client.id_client, Pribadi.nama_lengkap,
          Client.nomor_telepon, Client.alamat, Client.jenis_client
          FROM Client
          LEFT JOIN Pribadi ON Client.id_client = Pribadi.id_client";
$result = query($query);

$clients = [];
while ($row = fetch_array($result)) {
    $clients[] = $row;
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>

    <!-- Load dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/dashboard.css">

    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="dashboard-body">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg dashboard-navbar">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <button class="btn sidebar-toggle me-3" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <a class="navbar-brand" href="dashboard.php">Notaris<span style="color: #6A85FF;">Pro</span></a>
            </div>

            <!-- User Menu -->
            <div class="dropdown">
                <button class="btn user-menu dropdown-toggle d-flex align-items-center" 
                        type="button" id="userDropdown" data-bs-toggle="dropdown">
                    <div class="user-avatar me-2">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-info d-none d-md-block">
                        <span id="userName"><?php echo htmlspecialchars($current_user['nama_lengkap']); ?></span>
                        <small class="text-muted d-block"><?php echo $is_admin ? 'Admin Notaris' : 'User'; ?></small>
                    </div>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profil</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Pengaturan</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="dashboard.php?logout=1"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Dashboard Container -->
    <div class="dashboard-container d-flex">

        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="user-profile">
                    <div class="avatar-lg">
                        <i class="fas fa-user"></i>
                    </div>
                    <h5 id="sidebarUserName"><?php echo htmlspecialchars($current_user['nama_lengkap']); ?></h5>
                    <p class="text-muted"><?php echo htmlspecialchars($current_user['username']); ?></p>
                </div>
            </div>
            
            <?php
            $current_page = basename($_SERVER['PHP_SELF']);
            ?>
           <nav class="sidebar-nav">
            <ul class="nav flex-column">

                <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>" href="dashboard.php">
                        <i class="fas fa-chart-line me-2"></i>Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'client.php') ? 'active' : '' ?>" href="client.php">
                        <i class="fas fa-user-tie me-2"></i>Client
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'pengajuan.php') ? 'active' : '' ?>" href="pengajuan.php">
                        <i class="fas fa-file-upload me-2"></i>Pengajuan
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'transaksi.php') ? 'active' : '' ?>" href="transaksi.php">
                        <i class="fas fa-money-check-alt me-2"></i>Transaksi
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'jadwal.php') ? 'active' : '' ?>" href="jadwal.php">
                        <i class="fas fa-calendar-alt me-2"></i>Jadwal
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'arsip.php') ? 'active' : '' ?>" href="arsip.php">
                        <i class="fas fa-folder-open me-2"></i>Arsip File
                    </a>
                </li>

                <?php if ($is_admin): ?>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-users-cog me-2"></i>Manage Client
                    </a>
                </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'riwayat.php') ? 'active' : '' ?>" href="riwayat.php">
                        <i class="fas fa-history me-2"></i>Riwayat Transaksi
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'konsultasi.php') ? 'active' : '' ?>" href="konsultasi.php">
                        <i class="fas fa-comments me-2"></i>Konsultasi Online
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'profil.php') ? 'active' : '' ?>" href="profil.php">
                        <i class="fas fa-user-circle me-2"></i>Profil Akun
                    </a>
                </li>
            </ul>
        </nav>  
                <div class="sidebar-footer">
                    <a href="index.php" class="btn btn-outline-primary w-100">
                        <i class="fas fa-globe me-2"></i>Home
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content p-4" id="mainContent">
            <h2 class="mb-4">Data Client</h2>

            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>No Telp</th>
                        <th>Alamat</th>
                        <th>Jenis</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($clients)): ?>
                    <tr><td colspan="5" class="text-center">Tidak ada data</td></tr>
                <?php else: ?>
                    <?php foreach ($clients as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['id_client']) ?></td>
                        <td><?= htmlspecialchars($c['nama_lengkap']) ?></td>
                        <td><?= htmlspecialchars($c['nomor_telepon']) ?></td>
                        <td><?= htmlspecialchars($c['alamat']) ?></td>
                        <td><?= htmlspecialchars($c['jenis_client']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </main>

    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('show');
                mainContent.classList.toggle('blur');
            });
        }
    </script>
</body>
</html>
