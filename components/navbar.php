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
