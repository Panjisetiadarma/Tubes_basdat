<?php
require_once 'common_crud.php';

$current_user = get_logged_in_user();
$is_admin = ($current_user['role'] === 'AdminNotaris');
$page_title = 'Data Jadwal';

// Delete jadwal
if (isset($_GET['delete']) && $is_admin) {
    deleteData('Jadwal', "id_jadwal=" . (int)$_GET['delete']);
    header("Location: jadwal.php?deleted=1");
    exit;
}

// Insert jadwal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_jadwal' && $is_admin) {
    insertData('Jadwal', [
        'id_pengajuan' => $_POST['id_pengajuan'],
        'tanggal_jadwal' => $_POST['tanggal_jadwal'],
        'kegiatan' => $_POST['kegiatan'],
        'keterangan' => $_POST['keterangan']
    ]);
    header("Location: jadwal.php?added=1");
    exit;
}

// Ambil data jadwal dengan info pengajuan
$jadwal_list = getData(
    'Jadwal j LEFT JOIN Pengajuan p ON j.id_pengajuan=p.id_pengajuan',
    'j.id_jadwal, j.id_pengajuan, j.tanggal_jadwal, j.kegiatan, j.keterangan, p.jenis_pengajuan',
    '1=1',
    'j.tanggal_jadwal ASC'
);

// Ambil list pengajuan untuk modal
$pengajuan_list = getData('Pengajuan', '*', '1=1', 'id_pengajuan ASC');

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
<link href="styles/main.css" rel="stylesheet">
<link href="styles/navbar.css" rel="stylesheet">
<link href="styles/sidebar.css" rel="stylesheet">
<link href="styles/jadwal.css" rel="stylesheet">
<link href="styles/dashboard.css" rel="stylesheet">
</head>
<body class="dashboard-body">

<!-- Navbar -->
<?php include 'components/navbar.php'; ?>

<div class="dashboard-container d-flex">

    <!-- Sidebar -->
    <?php include 'components/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content p-4" id="mainContent">
        <h2 id="jadwalTitle" class="mb-4"><?= htmlspecialchars($page_title) ?></h2>

        <!-- Alerts -->
        <?php if(isset($_GET['added'])): ?>
            <div class="alert alert-success">Jadwal berhasil ditambahkan!</div>
        <?php endif; ?>
        <?php if(isset($_GET['deleted'])): ?>
            <div class="alert alert-success">Jadwal berhasil dihapus!</div>
        <?php endif; ?>

        <!-- Tombol Tambah Jadwal (Admin Only) -->
        <?php if($is_admin): ?>
            <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addJadwalModal">
                Tambah Jadwal
            </button>
        <?php endif; ?>

        <!-- Table Jadwal -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>ID Pengajuan</th>
                        <th>Jenis Pengajuan</th>
                        <th>Tanggal Jadwal</th>
                        <th>Kegiatan</th>
                        <th>Keterangan</th>
                        <?php if($is_admin) echo "<th>Delete</th>"; ?>
                    </tr>
                </thead>
                <tbody>
                <?php if(empty($jadwal_list)): ?>
                    <tr><td colspan="<?= $is_admin ? 7 : 6 ?>" class="text-center">Tidak ada data</td></tr>
                <?php else: ?>
                    <?php foreach($jadwal_list as $j): ?>
                    <tr>
                        <td><?= htmlspecialchars($j['id_jadwal']) ?></td>
                        <td><?= htmlspecialchars($j['id_pengajuan']) ?></td>
                        <td><?= htmlspecialchars($j['jenis_pengajuan']) ?></td>
                        <td><?= htmlspecialchars($j['tanggal_jadwal']) ?></td>
                        <td><?= htmlspecialchars($j['kegiatan']) ?></td>
                        <td><?= htmlspecialchars($j['keterangan']) ?></td>
                        <?php if($is_admin): ?>
                        <td>
                            <a href="jadwal.php?delete=<?= $j['id_jadwal'] ?>" 
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

<!-- Modal Add Jadwal (Admin Only) -->
<?php if ($is_admin): ?>
<div class="modal fade" id="addJadwalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Jadwal Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="jadwal.php">
                <input type="hidden" name="action" value="add_jadwal">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pengajuan</label>
                        <select name="id_pengajuan" class="form-select" required>
                            <option value="">Pilih Pengajuan</option>
                            <?php foreach ($pengajuan_list as $p): ?>
                                <option value="<?= $p['id_pengajuan']; ?>">
                                    <?= htmlspecialchars($p['jenis_pengajuan']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Jadwal</label>
                        <input type="datetime-local" name="tanggal_jadwal" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kegiatan</label>
                        <input type="text" name="kegiatan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3"></textarea>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Sidebar toggle
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
