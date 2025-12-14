<?php
require_once 'common_crud.php';

$current_user = get_logged_in_user();
$is_admin = ($current_user['role'] === 'AdminNotaris');
$page_title = 'Data Pengajuan';

// Delete pengajuan
if (isset($_GET['delete']) && $is_admin) {
    deleteData('Pengajuan', "id_pengajuan=" . (int)$_GET['delete']);
    header("Location: pengajuan.php?deleted=1");
    exit;
}

// Insert pengajuan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_pengajuan' && $is_admin) {
    insertData('Pengajuan', [
        'id_client' => $_POST['id_client'],
        'jenis_pengajuan' => $_POST['jenis_pengajuan'],
        'id_notaris' => $_POST['id_notaris'] ?: null,
        'id_status' => $_POST['id_status'],
        'deskripsi' => $_POST['deskripsi'],
        'tanggal_pengajuan' => $_POST['tanggal_pengajuan']
    ]);
    header("Location: pengajuan.php?added=1");
    exit;
}

$pengajuan_list = getData(
    'Pengajuan pj
     LEFT JOIN Client c ON pj.id_client = c.id_client
     LEFT JOIN Pribadi p ON c.id_client = p.id_client
     LEFT JOIN Perusahaan pr ON c.id_client = pr.id_client
     LEFT JOIN Notaris n ON pj.id_notaris = n.id_notaris
     LEFT JOIN Status_Pengajuan sp ON pj.id_status = sp.id_status',
     
    'pj.id_pengajuan,
     CASE
        WHEN c.jenis_client = "pribadi" THEN p.nama_lengkap
        WHEN c.jenis_client = "perusahaan" THEN pr.nama_perusahaan
     END AS nama_client,
     pj.jenis_pengajuan,
     n.nama_notaris,
     sp.nama_status,
     pj.deskripsi,
     pj.tanggal_pengajuan',
     
    '1=1',
    'pj.created_at DESC'
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
<link href="styles/pengajuan.css" rel="stylesheet">
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
        <h2 id="pTitle" class="mb-4"><?= htmlspecialchars($page_title) ?></h2>

        <!-- Alerts -->
        <?php if(isset($_GET['added'])): ?>
            <div class="alert alert-success">Pengajuan berhasil ditambahkan!</div>
        <?php endif; ?>
        <?php if(isset($_GET['deleted'])): ?>
            <div class="alert alert-success">Pengajuan berhasil dihapus!</div>
        <?php endif; ?>

        <!-- Tombol Tambah Pengajuan (Admin Only) -->
        <?php if($is_admin): ?>
            <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addPengajuanModal">
                Tambah Pengajuan
            </button>
        <?php endif; ?>

        <!-- Table Pengajuan -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nama Client</th>
                        <th>Jenis</th>
                        <th>Notaris</th>
                        <th>Status</th>
                        <th>Deskripsi</th>
                        <th>Tanggal</th>
                        <?php if($is_admin) echo "<th>Delete</th>"; ?>
                    </tr>
                </thead>
                <tbody>
                <?php if(empty($pengajuan_list)): ?>
                    <tr><td colspan="<?= $is_admin ? 8 : 7 ?>" class="text-center">Tidak ada data</td></tr>
                <?php else: ?>
                    <?php foreach($pengajuan_list as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['id_pengajuan']) ?></td>
                        <td><?= htmlspecialchars($p['nama_client']) ?></td>
                        <td><?= htmlspecialchars($p['jenis_pengajuan']) ?></td>
                        <td><?= htmlspecialchars($p['nama_notaris']) ?></td>
                        <td><?= htmlspecialchars($p['nama_status']) ?></td>
                        <td><?= htmlspecialchars($p['deskripsi']) ?></td>
                        <td><?= htmlspecialchars($p['tanggal_pengajuan']) ?></td>
                        <?php if($is_admin): ?>
                        <td>
                            <a href="pengajuan.php?delete=<?= $p['id_pengajuan'] ?>" 
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

<!-- Modal Add Pengajuan (Admin Only) -->
<?php if ($is_admin): ?>
<div class="modal fade" id="addPengajuanModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Pengajuan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="pengajuan.php">
                <input type="hidden" name="action" value="add_pengajuan">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Client</label>
                        <select name="id_client" class="form-select" required>
                            <option value="">Pilih Client</option>
                            <?php foreach ($client_list as $client): ?>
                                <option value="<?= $client['id_client']; ?>">
                                    <?= htmlspecialchars($client['nama_lengkap'] ?: $client['nama_perusahaan']); ?>
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
                                <option value="<?= $notaris['id_notaris']; ?>">
                                    <?= htmlspecialchars($notaris['nama_notaris']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="id_status" class="form-select" required>
                            <?php foreach ($status_list as $status): ?>
                                <option value="<?= $status['id_status']; ?>">
                                    <?= htmlspecialchars($status['nama_status']); ?>
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
                               value="<?= date('Y-m-d'); ?>" required>
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
