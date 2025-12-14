<?php
require_once 'common_crud.php';

$current_user = get_logged_in_user();
$is_admin = ($current_user['role'] === 'AdminNotaris');
$page_title = 'Data Client';

// Ambil data client
$clients = getData(
    'Client c 
     LEFT JOIN Pribadi p ON c.id_client=p.id_client
     LEFT JOIN Perusahaan pr ON c.id_client=pr.id_client',
    'c.id_client, COALESCE(p.nama_lengkap, pr.nama_perusahaan) AS nama, c.nomor_telepon, c.alamat, c.jenis_client',
    '1=1',
    'c.id_client ASC'
);

// Insert client
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_client' && $is_admin) {
    $nama = $_POST['nama_lengkap'];
    $nomor = $_POST['nomor_telepon'];
    $alamat = $_POST['alamat'];
    $jenis = $_POST['jenis_client'];

    $id_client = insertData('Client', ['jenis_client' => $jenis]);

    if ($jenis === 'Pribadi') {
        insertData('Pribadi', ['id_client' => $id_client, 'nama_lengkap' => $nama, 'nomor_telepon' => $nomor, 'alamat' => $alamat]);
    } else {
        insertData('Perusahaan', ['id_client' => $id_client, 'nama_perusahaan' => $nama, 'nomor_telepon' => $nomor, 'alamat' => $alamat]);
    }

    header("Location: client.php?added=1");
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']); // Untuk highlight menu aktif
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
<link href="styles/client.css" rel="stylesheet">
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

        <h2 id="clientTitle" class="mb-4"><?= htmlspecialchars($page_title) ?></h2>

        <!-- Alerts -->
        <?php if(isset($_GET['added'])): ?>
            <div class="alert alert-success">Client berhasil ditambahkan!</div>
        <?php endif; ?>
        <?php if(isset($_GET['deleted'])): ?>
            <div class="alert alert-success">Client berhasil dihapus!</div>
        <?php endif; ?>

        <!-- Tombol Tambah Client (Admin Only) -->
        <?php if($is_admin): ?>
            <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addClientModal">
                Tambah Client
            </button>
        <?php endif; ?>

        <!-- Table Client -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>No Telp</th>
                        <th>Alamat</th>
                        <th>Jenis</th>
                        <?php if($is_admin) echo "<th>Delete</th>"; ?>
                    </tr>
                </thead>
                <tbody>
                <?php if(empty($clients)): ?>
                    <tr><td colspan="<?= $is_admin ? 6 : 5 ?>" class="text-center">Tidak ada data</td></tr>
                <?php else: ?>
                    <?php foreach($clients as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['id_client']) ?></td>
                        <td><?= htmlspecialchars($c['nama']) ?></td>
                        <td><?= htmlspecialchars($c['nomor_telepon']) ?></td>
                        <td><?= htmlspecialchars($c['alamat']) ?></td>
                        <td><?= htmlspecialchars($c['jenis_client']) ?></td>
                        <?php if($is_admin): ?>
                        <td>
                            <a href="client.php?delete=<?= $c['id_client'] ?>" 
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

<!-- Modal Tambah Client -->
<?php if($is_admin): ?>
<div class="modal fade" id="addClientModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Client Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="client.php">
                <input type="hidden" name="action" value="add_client">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama_lengkap" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="text" name="nomor_telepon" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <input type="text" name="alamat" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Client</label>
                        <select name="jenis_client" class="form-select" required>
                            <option value="">Pilih Jenis Client</option>
                            <option value="Pribadi">Pribadi</option>
                            <option value="Perusahaan">Perusahaan</option>
                        </select>
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
