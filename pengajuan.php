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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_pengajuan']) && $is_admin) {
    insertData('Pengajuan', [
        'id_client' => $_POST['id_client'],
        'jenis_pengajuan' => $_POST['jenis_pengajuan'],
        'id_status' => $_POST['id_status'],
        'deskripsi' => $_POST['deskripsi'],
        'tanggal_pengajuan' => $_POST['tanggal_pengajuan']
    ]);
    header("Location: pengajuan.php?added=1");
    exit;
}

// Ambil data pengajuan
$pengajuan_list = getData('Pengajuan', '*', '1=1', 'created_at DESC');

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
<link href="styles/dashboard.css" rel="stylesheet">
<link href="styles/pengajuan.css" rel="stylesheet">
</head>
<body class="dashboard-body">

<!-- Navbar -->
<?php include 'components/navbar.php'; ?>

<div class="dashboard-container d-flex">

    <!-- Sidebar -->
    <?php include 'components/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content p-4" id="mainContent">
        <h2 id="pageTitle" class="mb-4"><?= htmlspecialchars($page_title) ?></h2>

        <!-- Alerts -->
        <?php if(isset($_GET['added'])): ?>
            <div class="alert alert-success">Pengajuan berhasil ditambahkan!</div>
        <?php endif; ?>
        <?php if(isset($_GET['deleted'])): ?>
            <div class="alert alert-success">Pengajuan berhasil dihapus!</div>
        <?php endif; ?>

        <!-- Form Tambah Pengajuan -->
        <?php if($is_admin): ?>
        <form method="POST" class="mb-3 row g-2 align-items-end">
            <input type="hidden" name="add_pengajuan" value="1">
            <div class="col-md-2">
                <input type="text" name="jenis_pengajuan" class="form-control" placeholder="Jenis Pengajuan" required>
            </div>
            <div class="col-md-2">
                <input type="text" name="id_client" class="form-control" placeholder="ID Client" required>
            </div>
            <div class="col-md-2">
                <input type="text" name="id_status" class="form-control" placeholder="ID Status">
            </div>
            <div class="col-md-3">
                <input type="text" name="deskripsi" class="form-control" placeholder="Deskripsi">
            </div>
            <div class="col-md-2">
                <input type="date" name="tanggal_pengajuan" class="form-control" required>
            </div>
            <div class="col-md-1">
                <button class="btn btn-primary w-100">Tambah</button>
            </div>
        </form>
        <?php endif; ?>

        <!-- Table Pengajuan -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>ID Client</th>
                        <th>Jenis</th>
                        <th>Status</th>
                        <th>Deskripsi</th>
                        <th>Tanggal</th>
                        <?php if($is_admin) echo "<th>Delete</th>"; ?>
                    </tr>
                </thead>
                <tbody>
                <?php if(empty($pengajuan_list)): ?>
                    <tr><td colspan="<?= $is_admin ? 7 : 6 ?>" class="text-center">Tidak ada data</td></tr>
                <?php else: ?>
                    <?php foreach($pengajuan_list as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['id_pengajuan']) ?></td>
                        <td><?= htmlspecialchars($p['id_client']) ?></td>
                        <td><?= htmlspecialchars($p['jenis_pengajuan']) ?></td>
                        <td><?= htmlspecialchars($p['id_status']) ?></td>
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
