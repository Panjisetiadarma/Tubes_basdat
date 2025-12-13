<?php
require_once 'common_crud.php'; // ini otomatis memanggil koneksi juga

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


?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>

    <!-- Load dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/dashboard.css">
    <link rel="stylesheet" href="styles/client.css">

    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="dashboard-body">

    <!-- Navbar -->
    <?php include 'components/navbar.php'; ?>

    <!-- Dashboard Container -->
    <div class="dashboard-container d-flex">

        <!-- Sidebar -->
        <?php include 'components/sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="main-content p-4" id="mainContent">
            <h2 id="clientTitle" class="mb-4">Data Client</h2>
        <!-- Alerts -->
        <?php if(isset($_GET['added'])): ?>
            <div class="alert alert-success">Client berhasil ditambahkan!</div>
        <?php endif; ?>
        <?php if(isset($_GET['deleted'])): ?>
            <div class="alert alert-success">Client berhasil dihapus!</div>
        <?php endif; ?>

        <!-- form tambah -->
        <?php if($is_admin): ?>
        <form method="POST" class="mb-3 row g-2 align-items-end">
            <input type="hidden" name="add_client" value="1">
            <div class="col-md-2">
                <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama Lengkap" required>
            </div>
            <div class="col-md-2">
                <input type="text" name="nomor_telepon" class="form-control" placeholder="Nomor Telepon" required>
            </div>
            <div class="col-md-3">
                <input type="text" name="alamat" class="form-control" placeholder="Alamat" required>
            </div>
            <div class="col-md-2">
                <select name="jenis_client" class="form-control" required>
                    <option value="">Pilih Jenis Client</option>
                    <option value="Pribadi">Pribadi</option>
                    <option value="Perusahaan">Perusahaan</option>
                </select>
            </div>
            <div class="col-md-1">
                <button class="btn btn-primary w-100">Tambah</button>
            </div>
        </form>
        <?php endif; ?>
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
                <?php if (empty($clients)): ?>
                    <tr><td colspan="5" class="text-center">Tidak ada data</td></tr>
                <?php else: ?>
                    <?php foreach ($clients as $c): ?>
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
        </main>

    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('show');
                mainContent.classList.toggle('blur');
            });
        }
    </script>
</body>
</html>
