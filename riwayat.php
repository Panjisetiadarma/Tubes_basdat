<?php
require_once 'koneksi.php';

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

// Filter berdasarkan user (jika bukan admin, hanya tampilkan transaksi miliknya)
// Untuk sekarang, semua user dapat melihat semua transaksi
// Filter dapat ditambahkan kemudian jika diperlukan berdasarkan relasi User-Client
$where_clause = "1=1";

// Ambil data transaksi dengan JOIN ke Pengajuan untuk mendapatkan jenis_pengajuan
$query = "
    SELECT 
        t.id_transaksi,
        t.id_pengajuan,
        t.jumlah,
        t.metode_pembayaran,
        t.tanggal_transaksi,
        t.status_pembayaran,
        t.keterangan,
        t.created_at,
        p.jenis_pengajuan,
        p.deskripsi AS deskripsi_pengajuan
    FROM Transaksi t
    LEFT JOIN Pengajuan p ON t.id_pengajuan = p.id_pengajuan
    WHERE $where_clause
    ORDER BY t.tanggal_transaksi DESC, t.created_at DESC
";

$result = query($query);
$transaksi_list = [];
if ($result) {
    while ($row = fetch_array($result)) {
        $transaksi_list[] = $row;
    }
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Riwayat Transaksi | NotarisPro</title>

<!-- Dependencies -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="styles/main.css" rel="stylesheet">
<link href="styles/navbar.css" rel="stylesheet">
<link href="styles/sidebar.css" rel="stylesheet">
<link href="styles/dashboard.css" rel="stylesheet">

<style>
.badge-pending {
    background-color: #ffc107;
    color: #000;
}
.badge-lunas {
    background-color: #198754;
    color: #fff;
}
.badge-gagal {
    background-color: #dc3545;
    color: #fff;
}
.table thead {
    background: #f1f3f7;
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
    <main class="main-content p-4" id="mainContent">
        <h2 class="mb-4">📑 Riwayat Transaksi</h2>
        <p class="text-muted mb-4">Riwayat pembayaran layanan notaris</p>

        <!-- Table Transaksi -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>ID Transaksi</th>
                                <th>ID Pengajuan</th>
                                <th>Jenis Pengajuan</th>
                                <th>Jumlah</th>
                                <th>Metode Pembayaran</th>
                                <th>Tanggal Transaksi</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if(empty($transaksi_list)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-inbox fa-2x text-muted mb-2 d-block"></i>
                                    <span class="text-muted">Tidak ada data transaksi</span>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach($transaksi_list as $t): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($t['id_transaksi']) ?></td>
                                <td><?= htmlspecialchars($t['id_pengajuan']) ?></td>
                                <td><?= htmlspecialchars($t['jenis_pengajuan'] ?? '-') ?></td>
                                <td>Rp <?= number_format((float)$t['jumlah'], 0, ',', '.') ?></td>
                                <td><?= htmlspecialchars($t['metode_pembayaran'] ?? '-') ?></td>
                                <td>
                                    <?php 
                                    if ($t['tanggal_transaksi']) {
                                        echo date('d-m-Y', strtotime($t['tanggal_transaksi']));
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    $status = strtolower($t['status_pembayaran'] ?? 'pending');
                                    $badge_class = 'badge-pending';
                                    if ($status === 'lunas') {
                                        $badge_class = 'badge-lunas';
                                    } elseif ($status === 'gagal') {
                                        $badge_class = 'badge-gagal';
                                    }
                                    ?>
                                    <span class="badge <?= $badge_class ?>">
                                        <?= ucfirst($status) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($t['keterangan'] ?? '-') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Sidebar toggle functionality
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
