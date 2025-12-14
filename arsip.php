<?php
require_once 'common_crud.php';

$current_user = get_logged_in_user();
$is_admin = ($current_user['role'] === 'AdminNotaris');
$page_title = 'Data Arsip';

// Delete arsip
if (isset($_GET['delete']) && $is_admin) {
    deleteData('Arsip_File', "id_file=" . (int)$_GET['delete']);
    header("Location: arsip.php?deleted=1");
    exit;
}

// Insert arsip
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_arsip']) && $is_admin) {
    insertData('Arsip_File', [
        'id_pengajuan' => $_POST['id_pengajuan'],
        'nama_file' => $_POST['nama_file'],
        'path_file' => $_POST['path_file'],
        'tipe_file' => $_POST['tipe_file'],
        'ukuran_file' => $_POST['ukuran_file']
    ]);
    header("Location: arsip.php?added=1");
    exit;
}

// Ambil data arsip (JOIN dengan pengajuan untuk info pengajuan)
$arsip_list = getData(
    'Arsip_File a LEFT JOIN Pengajuan p ON a.id_pengajuan = p.id_pengajuan',
    'a.*, p.jenis_pengajuan, p.id_client',
    '1=1',
    'a.uploaded_at DESC'
);

// Ambil list pengajuan untuk dropdown tambah arsip
$pengajuan_list = getData('Pengajuan', '*', '1=1', 'id_pengajuan ASC');

?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($page_title) ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="styles/main.css" rel="stylesheet">
<link href="styles/navbar.css" rel="stylesheet">
<link href="styles/sidebar.css" rel="stylesheet">
<link href="styles/arsip.css" rel="stylesheet">
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
        <h2 id="arsipTitle" class="mb-4"><?= htmlspecialchars($page_title) ?></h2>

        <!-- Alerts -->
        <?php if(isset($_GET['added'])): ?>
            <div class="alert alert-success">Arsip berhasil ditambahkan!</div>
        <?php endif; ?>
        <?php if(isset($_GET['deleted'])): ?>
            <div class="alert alert-success">Arsip berhasil dihapus!</div>
        <?php endif; ?>

        <!-- Form Tambah Arsip (Admin Only) -->
        <?php if($is_admin): ?>
        <form method="POST" class="mb-3 row g-2 align-items-end">
            <input type="hidden" name="add_arsip" value="1">
            <div class="col-md-2">
                <select name="id_pengajuan" class="form-control" required>
                    <option value="">Pilih Pengajuan</option>
                    <?php foreach ($pengajuan_list as $p): ?>
                        <option value="<?= $p['id_pengajuan']; ?>">
                            <?= htmlspecialchars($p['id_pengajuan'] . ' - ' . $p['jenis_pengajuan']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" name="nama_file" class="form-control" placeholder="Nama File" required>
            </div>
            <div class="col-md-3">
                <input type="text" name="path_file" class="form-control" placeholder="Path File" required>
            </div>
            <div class="col-md-2">
                <input type="text" name="tipe_file" class="form-control" placeholder="Tipe File">
            </div>
            <div class="col-md-2">
                <input type="number" name="ukuran_file" class="form-control" placeholder="Ukuran (byte)">
            </div>
            <div class="col-md-1">
                <button class="btn btn-primary w-100">Tambah</button>
            </div>
        </form>
        <?php endif; ?>

        <!-- Table Arsip -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>ID Pengajuan</th>
                        <th>Jenis Pengajuan</th>
                        <th>Nama File</th>
                        <th>Path</th>
                        <th>Tipe</th>
                        <th>Ukuran</th>
                        <th>Uploaded At</th>
                        <?php if($is_admin) echo "<th>Delete</th>"; ?>
                    </tr>
                </thead>
                <tbody>
                <?php if(empty($arsip_list)): ?>
                    <tr><td colspan="<?= $is_admin ? 9 : 8 ?>" class="text-center">Tidak ada data</td></tr>
                <?php else: ?>
                    <?php foreach($arsip_list as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['id_file']) ?></td>
                        <td><?= htmlspecialchars($a['id_pengajuan']) ?></td>
                        <td><?= htmlspecialchars($a['jenis_pengajuan']) ?></td>
                        <td><?= htmlspecialchars($a['nama_file']) ?></td>
                        <td><?= htmlspecialchars($a['path_file']) ?></td>
                        <td><?= htmlspecialchars($a['tipe_file']) ?></td>
                        <td><?= htmlspecialchars($a['ukuran_file']) ?></td>
                        <td><?= htmlspecialchars($a['uploaded_at']) ?></td>
                        <?php if($is_admin): ?>
                        <td>
                            <a href="arsip.php?delete=<?= $a['id_file'] ?>" 
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
