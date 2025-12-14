<?php
require_once 'koneksi.php';
require_once 'common_crud.php';

// Cek login
// start_session();
// if (!is_logged_in()) {
//     header('Location: auth.php');
//     exit;
// }

$current_user = get_logged_in_user();
$is_admin = ($current_user['role'] === 'AdminNotaris');

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: auth.php');
    exit;
}

// Handle Delete
if (isset($_GET['delete']) && $is_admin) {
    $id = (int)$_GET['delete'];
    $delete_query = "DELETE FROM Pengajuan WHERE id_pengajuan = $id";
    if (query($delete_query)) {
        header('Location: dashboard.php?deleted=1');
        exit;
    }
}

// Handle Add Pengajuan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_pengajuan' && $is_admin) {
    $id_client = (int)$_POST['id_client'];
    $id_notaris = isset($_POST['id_notaris']) && !empty($_POST['id_notaris']) ? (int)$_POST['id_notaris'] : null;
    $id_status = (int)$_POST['id_status'];
    $jenis_pengajuan = escape($_POST['jenis_pengajuan']);
    $deskripsi = isset($_POST['deskripsi']) && !empty($_POST['deskripsi']) ? escape($_POST['deskripsi']) : null;
    $tanggal_pengajuan = escape($_POST['tanggal_pengajuan']);
    
    $data_pengajuan = [
        'id_client' => $id_client,
        'id_status' => $id_status,
        'jenis_pengajuan' => $jenis_pengajuan,
        'tanggal_pengajuan' => $tanggal_pengajuan,
        'id_user' => $current_user['id_user']
    ];
    
    if ($id_notaris !== null) $data_pengajuan['id_notaris'] = $id_notaris;
    if ($deskripsi !== null) $data_pengajuan['deskripsi'] = $deskripsi;
    
    if (insertData('Pengajuan', $data_pengajuan)) {
        header('Location: dashboard.php?added=1');
        exit;
    }
}

// Handle search
$search_term = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$pengajuan_list = [];

// Query untuk mendapatkan pengajuan
// Admin lihat semua, User hanya lihat pengajuan mereka
$where_user = "";
if (!$is_admin) {
    $where_user = " AND (p.id_user = " . (int)$current_user['id_user'] . " OR p.id_user IS NULL)";
}

$where_clause = "";
if (!empty($search_term)) {
    $search_escaped = escape($search_term);
    $where_clause = " AND (
        p.jenis_pengajuan LIKE '%$search_escaped%' OR
        p.deskripsi LIKE '%$search_escaped%' OR
        sp.nama_status LIKE '%$search_escaped%' OR
        n.nama_notaris LIKE '%$search_escaped%' OR
        pr.nama_lengkap LIKE '%$search_escaped%' OR
        pe.nama_perusahaan LIKE '%$search_escaped%' OR
        c.email LIKE '%$search_escaped%' OR
        c.nomor_telepon LIKE '%$search_escaped%'
    )";
}

$query = "
    SELECT p.*, 
           c.jenis_client, c.nomor_telepon, c.email, c.alamat,
           sp.nama_status,
           n.nama_notaris,
           pp.nama_ppat,
           pr.nama_lengkap as nama_client_pribadi,
           pr.nik,
           pe.nama_perusahaan as nama_client_perusahaan,
           pe.npwp
    FROM Pengajuan p
    LEFT JOIN Client c ON p.id_client = c.id_client
    LEFT JOIN Status_Pengajuan sp ON p.id_status = sp.id_status
    LEFT JOIN Notaris n ON p.id_notaris = n.id_notaris
    LEFT JOIN Ppat pp ON p.id_ppat = pp.id_ppat
    LEFT JOIN Pribadi pr ON c.id_client = pr.id_client AND c.jenis_client = 'pribadi'
    LEFT JOIN Perusahaan pe ON c.id_client = pe.id_client AND c.jenis_client = 'perusahaan'
    WHERE 1=1 $where_user $where_clause
    ORDER BY p.created_at DESC
";

$result = query($query);
if ($result) {
    while ($row = fetch_array($result)) {
        $pengajuan_list[] = $row;
    }
}

// Get statistics
$stats = [];

// Jumlah dokumen diproses
$query_doc = "SELECT COUNT(*) as count FROM Pengajuan WHERE id_status IN (SELECT id_status FROM Status_Pengajuan WHERE nama_status LIKE '%proses%' OR nama_status LIKE '%diproses%')";
$result_doc = query($query_doc);
$stats['doc_count'] = $result_doc ? fetch_array($result_doc)['count'] : 0;

// Jumlah janji mendatang
$query_appt = "SELECT COUNT(*) as count FROM Jadwal WHERE tanggal_jadwal >= CURDATE()";
$result_appt = query($query_appt);
$stats['appointment_count'] = $result_appt ? fetch_array($result_appt)['count'] : 0;

// Jumlah verifikasi selesai
$query_verified = "SELECT COUNT(*) as count FROM Pengajuan p JOIN Status_Pengajuan sp ON p.id_status = sp.id_status WHERE sp.nama_status LIKE '%selesai%'";
$result_verified = query($query_verified);
$stats['verified_count'] = $result_verified ? fetch_array($result_verified)['count'] : 0;

// Jumlah konsultasi aktif
$query_consult = "SELECT COUNT(*) as count FROM Pengajuan WHERE jenis_pengajuan LIKE '%konsultasi%' AND id_status IN (SELECT id_status FROM Status_Pengajuan WHERE nama_status NOT LIKE '%selesai%')";
$result_consult = query($query_consult);
$stats['consultation_count'] = $result_consult ? fetch_array($result_consult)['count'] : 0;

// Recent activities
$query_activities = "
    SELECT 
        p.id_pengajuan,
        p.jenis_pengajuan,
        p.created_at,
        sp.nama_status
    FROM Pengajuan p
    JOIN Status_Pengajuan sp ON p.id_status = sp.id_status
    ORDER BY p.created_at DESC
    LIMIT 5
";
$result_activities = query($query_activities);
$activities = [];
if ($result_activities) {
    while ($row = fetch_array($result_activities)) {
        $activities[] = $row;
    }
}

// Get status list for dropdown
$query_status = "SELECT * FROM Status_Pengajuan ORDER BY id_status ASC";
$result_status = query($query_status);
$status_list = [];
if ($result_status) {
    while ($row = fetch_array($result_status)) {
        $status_list[] = $row;
    }
}

// Get notaris list
$query_notaris = "SELECT * FROM Notaris ORDER BY nama_notaris ASC";
$result_notaris = query($query_notaris);
$notaris_list = [];
if ($result_notaris) {
    while ($row = fetch_array($result_notaris)) {
        $notaris_list[] = $row;
    }
}

// Get client list
$query_client = "
    SELECT c.*, 
           p.nama_lengkap, p.nik,
           pe.nama_perusahaan, pe.npwp
    FROM Client c
    LEFT JOIN Pribadi p ON c.id_client = p.id_client
    LEFT JOIN Perusahaan pe ON c.id_client = pe.id_client
    ORDER BY c.created_at DESC
";
$result_client = query($query_client);
$client_list = [];
if ($result_client) {
    while ($row = fetch_array($result_client)) {
        $client_list[] = $row;
    }
}

// Format date helper
function formatDate($date) {
    if (!$date) return 'N/A';
    return date('d M Y', strtotime($date));
}

// Get status badge class
function getStatusClass($status) {
    if (!$status) return 'bg-secondary';
    $statusLower = strtolower($status);
    if (strpos($statusLower, 'selesai') !== false || strpos($statusLower, 'lunas') !== false) {
        return 'bg-success';
    } else if (strpos($statusLower, 'proses') !== false || strpos($statusLower, 'diproses') !== false) {
        return 'bg-info';
    } else if (strpos($statusLower, 'menunggu') !== false || strpos($statusLower, 'pending') !== false) {
        return 'bg-warning';
    } else if (strpos($statusLower, 'tolak') !== false || strpos($statusLower, 'batal') !== false) {
        return 'bg-danger';
    }
    return 'bg-secondary';
}

// Get client name
function getClientName($pengajuan) {
    if ($pengajuan['nama_client_pribadi']) {
        return $pengajuan['nama_client_pribadi'];
    } else if ($pengajuan['nama_client_perusahaan']) {
        return $pengajuan['nama_client_perusahaan'];
    }
    return 'N/A';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Notaris Profesional</title>
    
    <!-- Load dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/sidebar.css">
    <link rel="stylesheet" href="styles/dashboard.css">

    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</head>
<body class="dashboard-body">
    <!-- Dashboard Navbar -->
     <?php include 'components/navbar.php'; ?>

    <!-- Dashboard Container -->
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php include 'components/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content" id="mainContent">
            <!-- Welcome Section -->
            <div class="welcome-section glass-effect">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="welcome-title">Selamat Datang, <span id="welcomeUserName"><?php echo htmlspecialchars(explode(' ', $current_user['nama_lengkap'])[0]); ?></span>!</h1>
                        <p class="welcome-text">Berikut adalah ringkasan aktivitas dan layanan notaris Anda.</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="date-display">
                            <i class="fas fa-calendar-alt me-2"></i>
                            <span id="currentDate"><?php echo date('l, d F Y', strtotime('now')); ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Search Form -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <form method="GET" action="dashboard.php" class="d-flex gap-2">
                            <div class="flex-grow-1">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Cari pengajuan, client, notaris, status..." 
                                       value="<?= htmlspecialchars($search_term) ?>">
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Cari
                            </button>
                            <?php if (!empty($search_term)): ?>
                                <a href="dashboard.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Reset
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row g-3 mt-4">
                <div class="col-md-3">
                    <div class="stat-card glass-effect">
                        <div class="stat-icon bg-primary">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number"><?php echo $stats['doc_count']; ?></h3>
                            <p class="stat-label">Dokumen Diproses</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card glass-effect">
                        <div class="stat-icon bg-success">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number"><?php echo $stats['appointment_count']; ?></h3>
                            <p class="stat-label">Janji Mendatang</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card glass-effect">
                        <div class="stat-icon bg-info">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number"><?php echo $stats['verified_count']; ?></h3>
                            <p class="stat-label">Verifikasi Selesai</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card glass-effect">
                        <div class="stat-icon bg-warning">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number"><?php echo $stats['consultation_count']; ?></h3>
                            <p class="stat-label">Konsultasi Aktif</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Calendar -->
            <div class="row g-4 mt-3">
                <!-- Document Status Chart -->
                <div class="col-lg-8">
                    <div class="activities-card glass-effect">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-history me-2"></i>Aktivitas Terbaru
                            </h5>
                            <a href="pengajuan.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                        </div>
                        <div class="card-body">
                            <div class="activity-list">
                                <?php if (empty($activities)): ?>
                                    <p class="text-muted text-center">Tidak ada aktivitas</p>
                                <?php else: ?>
                                    <?php foreach ($activities as $activity): ?>
                                        <div class="activity-item">
                                            <div class="activity-icon">
                                                <i class="fas fa-file-contract"></i>
                                            </div>
                                            <div class="activity-content">
                                                <div class="activity-title"><?php echo htmlspecialchars($activity['jenis_pengajuan']); ?></div>
                                                <div class="activity-time"><?php echo formatDate($activity['created_at']); ?></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Calendar -->
                <div class="col-lg-4">
                    <div class="quick-actions-card glass-effect">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-bolt me-2"></i>Aksi Cepat
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <?php if ($is_admin): ?>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPengajuanModal">
                                        <i class="fas fa-plus me-2"></i>Tambah Pengajuan
                                    </button>
                                    <a href="management_client.php" class="btn btn-outline-primary">
                                        <i class="fas fa-users-cog me-2"></i>Management Client
                                    </a>
                                <?php endif; ?>
                                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#uploadDokumenModal">
                                    <i class="fas fa-upload me-2"></i>Upload Dokumen
                                </button>
                                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#buatJanjiModal">
                                    <i class="fas fa-calendar-plus me-2"></i>Buat Janji
                                </button>
                                <a href="konsultasi.php?action=list" class="btn btn-outline-primary">
                                    <i class="fas fa-comment-dots me-2"></i>Konsultasi
                                </a>
                                <a href="riwayat.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-history me-2"></i>Riwayat Transaksi
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Pengajuan -->
            <?php if (!empty($pengajuan_list)): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card glass-effect">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i>Daftar Pengajuan
                                <?php if (!empty($search_term)): ?>
                                    <small class="text-muted">- Hasil pencarian: "<?= htmlspecialchars($search_term) ?>"</small>
                                <?php endif; ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Jenis Pengajuan</th>
                                            <th>Client</th>
                                            <th>Notaris</th>
                                            <th>Status</th>
                                            <th>Tanggal</th>
                                            <?php if($is_admin): ?>
                                                <th>Aksi</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($pengajuan_list as $p): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($p['id_pengajuan']) ?></td>
                                            <td><?= htmlspecialchars($p['jenis_pengajuan']) ?></td>
                                            <td><?= htmlspecialchars(getClientName($p)) ?></td>
                                            <td><?= htmlspecialchars($p['nama_notaris'] ?? '-') ?></td>
                                            <td>
                                                <span class="badge <?= getStatusClass($p['nama_status']) ?>">
                                                    <?= htmlspecialchars($p['nama_status'] ?? '-') ?>
                                                </span>
                                            </td>
                                            <td><?= formatDate($p['tanggal_pengajuan']) ?></td>
                                            <?php if($is_admin): ?>
                                                <td>
                                                    <a href="dashboard.php?delete=<?= $p['id_pengajuan'] ?>" 
                                                       class="btn btn-sm btn-danger"
                                                       onclick="return confirm('Yakin hapus?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

    <!-- Modal Add Pengajuan (Admin Only) -->
    <?php if ($is_admin): ?>
    <div class="modal fade" id="addPengajuanModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pengajuan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="dashboard.php">
                    <input type="hidden" name="action" value="add_pengajuan">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Client</label>
                            <select name="id_client" class="form-select" required>
                                <option value="">Pilih Client</option>
                                <?php foreach ($client_list as $client): ?>
                                    <option value="<?php echo $client['id_client']; ?>">
                                        <?php echo htmlspecialchars($client['nama_lengkap'] ?: $client['nama_perusahaan']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jenis Pengajuan</label>
                            <input type="text" name="jenis_pengajuan" class="form-control" required 
                                   placeholder="Contoh: Akta Jual Beli, Legalisasi, dll">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notaris</label>
                            <select name="id_notaris" class="form-select">
                                <option value="">Pilih Notaris (Opsional)</option>
                                <?php foreach ($notaris_list as $notaris): ?>
                                    <option value="<?php echo $notaris['id_notaris']; ?>">
                                        <?php echo htmlspecialchars($notaris['nama_notaris']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="id_status" class="form-select" required>
                                <?php foreach ($status_list as $status): ?>
                                    <option value="<?php echo $status['id_status']; ?>">
                                        <?php echo htmlspecialchars($status['nama_status']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Pengajuan</label>
                            <input type="date" name="tanggal_pengajuan" class="form-control" 
                                   value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Modal Upload Dokumen -->
    <div class="modal fade" id="uploadDokumenModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Dokumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="arsip.php" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Pilih Pengajuan</label>
                            <select name="id_pengajuan" class="form-select" required>
                                <option value="">Pilih Pengajuan</option>
                                <?php 
                                $pengajuan_for_upload = getData('Pengajuan', '*', '1=1', 'id_pengajuan DESC');
                                foreach ($pengajuan_for_upload as $p): 
                                ?>
                                    <option value="<?= $p['id_pengajuan'] ?>">
                                        #<?= $p['id_pengajuan'] ?> - <?= htmlspecialchars($p['jenis_pengajuan']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pilih File</label>
                            <input type="file" name="file_dokumen" class="form-control" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG. Maksimal 10MB</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama File</label>
                            <input type="text" name="nama_file" class="form-control" placeholder="Nama file (opsional)">
                            <small class="text-muted">Jika kosong, akan menggunakan nama file asli</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <a href="arsip.php" class="btn btn-primary">
                            <i class="fas fa-upload me-2"></i>Upload ke Halaman Arsip
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Buat Janji -->
    <div class="modal fade" id="buatJanjiModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Buat Janji Temu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="jadwal.php">
                    <input type="hidden" name="action" value="add_jadwal">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Pilih Pengajuan</label>
                            <select name="id_pengajuan" class="form-select" required>
                                <option value="">Pilih Pengajuan</option>
                                <?php 
                                $pengajuan_for_jadwal = getData('Pengajuan', '*', '1=1', 'id_pengajuan DESC');
                                foreach ($pengajuan_for_jadwal as $p): 
                                ?>
                                    <option value="<?= $p['id_pengajuan'] ?>">
                                        #<?= $p['id_pengajuan'] ?> - <?= htmlspecialchars($p['jenis_pengajuan']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal & Waktu</label>
                            <input type="datetime-local" name="tanggal_jadwal" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kegiatan</label>
                            <input type="text" name="kegiatan" class="form-control" 
                                   placeholder="Contoh: Konsultasi, Penandatanganan, dll" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3" 
                                      placeholder="Keterangan tambahan (opsional)"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-calendar-plus me-2"></i>Buat Janji
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Initialize calendar
        function initCalendar() {
            let currentDate = new Date();
            const prevMonthBtn = document.getElementById('prevMonth');
            const nextMonthBtn = document.getElementById('nextMonth');
            const todayBtn = document.getElementById('todayBtn');
            const currentMonthYear = document.getElementById('currentMonthYear');
            const calendarGrid = document.getElementById('calendarGrid');
            
            if (!calendarGrid) return;
            
            function renderCalendar() {
                const year = currentDate.getFullYear();
                const month = currentDate.getMonth();
                
                const monthNames = [
                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ];
                
                if (currentMonthYear) {
                    currentMonthYear.textContent = `${monthNames[month]} ${year}`;
                }
                
                const firstDay = new Date(year, month, 1);
                const lastDay = new Date(year, month + 1, 0);
                const totalDays = lastDay.getDate();
                const firstDayIndex = firstDay.getDay();
                
                calendarGrid.innerHTML = '';
                
                const dayNames = ['M', 'S', 'S', 'R', 'K', 'J', 'S'];
                dayNames.forEach(day => {
                    const dayElement = document.createElement('div');
                    dayElement.className = 'calendar-day fw-bold';
                    dayElement.textContent = day;
                    calendarGrid.appendChild(dayElement);
                });
                
                for (let i = 0; i < firstDayIndex; i++) {
                    const emptyDay = document.createElement('div');
                    emptyDay.className = 'calendar-day other-month';
                    calendarGrid.appendChild(emptyDay);
                }
                
                const today = new Date();
                for (let day = 1; day <= totalDays; day++) {
                    const dayElement = document.createElement('div');
                    dayElement.className = 'calendar-day';
                    dayElement.textContent = day;
                    
                    if (day === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
                        dayElement.classList.add('active');
                    }
                    
                    const dayOfWeek = new Date(year, month, day).getDay();
                    if (dayOfWeek === 0 || dayOfWeek === 6) {
                        dayElement.classList.add('weekend');
                    }
                    
                    calendarGrid.appendChild(dayElement);
                }
            }
            
            if (prevMonthBtn) {
                prevMonthBtn.addEventListener('click', () => {
                    currentDate.setMonth(currentDate.getMonth() - 1);
                    renderCalendar();
                });
            }
            
            if (nextMonthBtn) {
                nextMonthBtn.addEventListener('click', () => {
                    currentDate.setMonth(currentDate.getMonth() + 1);
                    renderCalendar();
                });
            }
            
            if (todayBtn) {
                todayBtn.addEventListener('click', () => {
                    currentDate = new Date();
                    renderCalendar();
                });
            }
            
            renderCalendar();
        }

        // Initialize charts
        function initCharts() {
            const ctx = document.getElementById('documentChart');
            if (!ctx) return;
            
            const chartCtx = ctx.getContext('2d');
            
            const data = {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                datasets: [
                    {
                        label: 'Dokumen Selesai',
                        data: [12, 19, 15, 25, 22, 30],
                        borderColor: '#6A85FF',
                        backgroundColor: 'rgba(106, 133, 255, 0.1)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Dokumen Diproses',
                        data: [8, 12, 10, 18, 15, 22],
                        borderColor: '#7DE2F2',
                        backgroundColor: 'rgba(125, 226, 242, 0.1)',
                        fill: true,
                        tension: 0.4
                    }
                ]
            };
            
            new Chart(chartCtx, {
                type: 'line',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Sidebar toggle
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', () => {
                    sidebar.classList.toggle('show');
                    if (mainContent) {
                        mainContent.classList.toggle('blur');
                    }
                });
            }
            
            initCalendar();
            initCharts();
            
            // Show success message
            <?php if (isset($_GET['added'])): ?>
                alert('Pengajuan berhasil ditambahkan!');
            <?php endif; ?>
            
            <?php if (isset($_GET['deleted'])): ?>
                alert('Pengajuan berhasil dihapus!');
            <?php endif; ?>
        });
    </script>
</body>
</html>
