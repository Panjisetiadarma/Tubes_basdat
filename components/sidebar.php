<link rel="stylesheet" href="styles/sidebar.css">
<link rel="stylesheet" href="styles/dashboard.css">


<!-- sidebar.php -->
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

    <nav class="sidebar-nav">
        <ul class="nav flex-column">
            <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
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
            <!-- Tambahkan menu lainnya sesuai sidebar asli -->
        </ul>
    </nav>

    <div class="sidebar-footer">
        <a href="index.php" class="btn btn-outline-primary w-100">
            <i class="fas fa-globe me-2"></i>Home
        </a>
    </div>
</aside>
