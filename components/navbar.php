<!-- navbar.php -->
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
               <?php if (!empty($current_user['foto_profil']) && file_exists($current_user['foto_profil'])): ?>
                                        <img src="<?= htmlspecialchars($current_user['foto_profil']) ?>" 
                                             alt="Foto Profil" 
                                             style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;">
                                    <?php else: ?>
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-user fa-2x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                <div class="user-info d-none d-md-block">
                    <span id="userName"><?php echo htmlspecialchars($current_user['nama_lengkap']); ?></span>
                    <small class="text-muted d-block"><?php echo $is_admin ? 'Admin Notaris' : 'User'; ?></small>
                </div>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profil</a></li>
                <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Pengaturan</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="dashboard.php?logout=1"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
            </ul>
        </div>
    </div>
</nav>
