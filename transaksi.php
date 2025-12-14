<?php
require_once 'common_crud.php';

$current_user = get_logged_in_user();
$is_admin = ($current_user['role'] === 'AdminNotaris');
$page_title = 'Data Transaksi';

// Delete transaksi
if (isset($_GET['delete']) && $is_admin) {
    deleteData('Transaksi', "id_transaksi=" . (int)$_GET['delete']);
    header("Location: transaksi.php?deleted=1");
    exit;
}

// Insert transaksi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_transaksi']) && $is_admin) {
    insertData('Transaksi', [
        'id_pengajuan' => $_POST['id_pengajuan'],
        'jumlah' => $_POST['jumlah'],
        'metode_pembayaran' => $_POST['metode_pembayaran'],
        'tanggal_transaksi' => $_POST['tanggal_transaksi'],
        'status_pembayaran' => $_POST['status_pembayaran'],
        'keterangan' => $_POST['keterangan']
    ]);
    header("Location: transaksi.php?added=1");
    exit;
}

// Ambil data transaksi (JOIN dengan pengajuan untuk info pengajuan)
// Admin lihat semua, User hanya lihat transaksi dari pengajuan mereka
$where_transaksi = "1=1";
if (!$is_admin) {
    $where_transaksi = "p.id_user = " . (int)$current_user['id_user'];
}

$transaksi_list = getData(
    'Transaksi t LEFT JOIN Pengajuan p ON t.id_pengajuan = p.id_pengajuan',
    't.*, p.jenis_pengajuan, p.id_client, p.id_user',
    $where_transaksi,
    't.created_at DESC'
);

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($page_title) ?></title>

<!-- Dependencies -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="styles/main.css" rel="stylesheet">
<link href="styles/navbar.css" rel="stylesheet">
<link href="styles/sidebar.css" rel="stylesheet">
<link href="styles/transaksi.css" rel="stylesheet">
<link href="styles/dashboard.css" rel="stylesheet">
</head>
<body class="dashboard-body">

<!-- Navbar -->
<?php include 'components/navbar.php'; ?>

<div class="dashboard-container">

    <!-- Sidebar -->
    <?php include 'components/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content p-4" id="mainContent">
        <h2 id="tTitle" class="mb-4"><?= htmlspecialchars($page_title) ?></h2>

        <!-- Alerts -->
        <?php if(isset($_GET['added'])): ?>
            <div class="alert alert-success">Transaksi berhasil ditambahkan!</div>
        <?php endif; ?>
        <?php if(isset($_GET['deleted'])): ?>
            <div class="alert alert-success">Transaksi berhasil dihapus!</div>
        <?php endif; ?>

        <!-- Form Tambah Transaksi -->
        <?php if($is_admin): ?>
        <form method="POST" class="mb-3 row g-2 align-items-end">
            <input type="hidden" name="add_transaksi" value="1">
            <div class="col-md-2">
                <input type="text" name="id_pengajuan" class="form-control" placeholder="ID Pengajuan" required>
            </div>
            <div class="col-md-2">
                <input type="number" step="0.01" name="jumlah" class="form-control" placeholder="Jumlah" required>
            </div>
            <div class="col-md-2">
                <input type="text" name="metode_pembayaran" class="form-control" placeholder="Metode Pembayaran">
            </div>
            <div class="col-md-2">
                <input type="date" name="tanggal_transaksi" class="form-control">
            </div>
            <div class="col-md-2">
                <select name="status_pembayaran" class="form-control">
                    <option value="pending">Pending</option>
                    <option value="lunas">Lunas</option>
                    <option value="gagal">Gagal</option>
                </select>
            </div>
            <div class="col-md-1">
                <button class="btn btn-primary w-100">Tambah</button>
            </div>
            <div class="col-md-3 mt-2">
                <input type="text" name="keterangan" class="form-control" placeholder="Keterangan">
            </div>
        </form>
        <?php endif; ?>

        <!-- Table Transaksi -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>ID Pengajuan</th>
                        <th>Jenis Pengajuan</th>
                        <th>Jumlah</th>
                        <th>Metode</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <?php if($is_admin) echo "<th>Delete</th>"; ?>
                    </tr>
                </thead>
                <tbody>
                <?php if(empty($transaksi_list)): ?>
                    <tr><td colspan="<?= $is_admin ? 9 : 8 ?>" class="text-center">Tidak ada data</td></tr>
                <?php else: ?>
                    <?php foreach($transaksi_list as $t): ?>
                    <tr>
                        <td><?= htmlspecialchars($t['id_transaksi']) ?></td>
                        <td><?= htmlspecialchars($t['id_pengajuan']) ?></td>
                        <td><?= htmlspecialchars($t['jenis_pengajuan']) ?></td>
                        <td><?= htmlspecialchars($t['jumlah']) ?></td>
                        <td><?= htmlspecialchars($t['metode_pembayaran']) ?></td>
                        <td><?= htmlspecialchars($t['tanggal_transaksi']) ?></td>
                        <td><?= htmlspecialchars($t['status_pembayaran']) ?></td>
                        <td><?= htmlspecialchars($t['keterangan']) ?></td>
                        <?php if($is_admin): ?>
                        <td>
                            <a href="transaksi.php?delete=<?= $t['id_transaksi'] ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Yakin hapus?')">Hapus</a>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const sidebarToggle = document.getElementById('sidebarToggle');
const sidebar = document.getElementById('sidebar');
const mainContent = document.getElementById('mainContent');
if(sidebarToggle){
    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('show');
        mainContent.classList.toggle('blur');
    });
}
</script>
</body>
</html>
