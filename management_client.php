<?php
require_once 'koneksi.php';
require_once 'common_crud.php';

// Cek login
start_session();
if (!is_logged_in()) {
    header('Location: auth.php');
    exit;
}

$current_user = get_logged_in_user();
$is_admin = ($current_user['role'] === 'AdminNotaris');

// Hanya admin yang bisa akses
if (!$is_admin) {
    header('Location: dashboard.php');
    exit;
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: auth.php');
    exit;
}

$error = '';
$success = '';

// Handle tambah client
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_client') {
    $jenis_client = escape($_POST['jenis_client']);
    $nomor_telepon = !empty($_POST['nomor_telepon']) ? escape($_POST['nomor_telepon']) : null;
    $email = !empty($_POST['email']) ? escape($_POST['email']) : null;
    $alamat = !empty($_POST['alamat']) ? escape($_POST['alamat']) : null;
    
    $data_client = [
        'jenis_client' => $jenis_client
    ];
    
    if ($nomor_telepon !== null) $data_client['nomor_telepon'] = $nomor_telepon;
    if ($email !== null) $data_client['email'] = $email;
    if ($alamat !== null) $data_client['alamat'] = $alamat;
    
    $id_client = insertData('Client', $data_client);
    
    if ($id_client) {
        if ($jenis_client === 'pribadi') {
            $nama_lengkap = escape($_POST['nama_lengkap']);
            $nik = !empty($_POST['nik']) ? escape($_POST['nik']) : null;
            $tempat_lahir = !empty($_POST['tempat_lahir']) ? escape($_POST['tempat_lahir']) : null;
            $tanggal_lahir = !empty($_POST['tanggal_lahir']) ? escape($_POST['tanggal_lahir']) : null;
            
            $data_pribadi = [
                'id_client' => $id_client,
                'nama_lengkap' => $nama_lengkap
            ];
            
            if ($nik !== null) $data_pribadi['nik'] = $nik;
            if ($tempat_lahir !== null) $data_pribadi['tempat_lahir'] = $tempat_lahir;
            if ($tanggal_lahir !== null) $data_pribadi['tanggal_lahir'] = $tanggal_lahir;
            
            if (insertData('Pribadi', $data_pribadi)) {
                $success = 'Client pribadi berhasil ditambahkan!';
            } else {
                $error = 'Gagal menambahkan data pribadi: ' . error();
                deleteData('Client', "id_client = $id_client");
            }
        } else {
            $nama_perusahaan = escape($_POST['nama_perusahaan']);
            $npwp = !empty($_POST['npwp']) ? escape($_POST['npwp']) : null;
            $nama_direktur = !empty($_POST['nama_direktur']) ? escape($_POST['nama_direktur']) : null;
            
            $data_perusahaan = [
                'id_client' => $id_client,
                'nama_perusahaan' => $nama_perusahaan
            ];
            
            if ($npwp !== null) $data_perusahaan['npwp'] = $npwp;
            if ($nama_direktur !== null) $data_perusahaan['nama_direktur'] = $nama_direktur;
            
            if (insertData('Perusahaan', $data_perusahaan)) {
                $success = 'Client perusahaan berhasil ditambahkan!';
            } else {
                $error = 'Gagal menambahkan data perusahaan: ' . error();
                deleteData('Client', "id_client = $id_client");
            }
        }
    } else {
        $error = 'Gagal menambahkan client: ' . error();
    }
}

// Handle update client
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_client') {
    $id_client = (int)$_POST['id_client'];
    $jenis_client = escape($_POST['jenis_client']);
    
    // Update data Client
    $data_client = [];
    if (isset($_POST['nomor_telepon'])) $data_client['nomor_telepon'] = !empty($_POST['nomor_telepon']) ? escape($_POST['nomor_telepon']) : null;
    if (isset($_POST['email'])) $data_client['email'] = !empty($_POST['email']) ? escape($_POST['email']) : null;
    if (isset($_POST['alamat'])) $data_client['alamat'] = !empty($_POST['alamat']) ? escape($_POST['alamat']) : null;
    
    if (!empty($data_client)) {
        updateData('Client', $data_client, "id_client = $id_client");
    }
    
    // Update data detail
    if ($jenis_client === 'pribadi') {
        $data_pribadi = [
            'nama_lengkap' => escape($_POST['nama_lengkap'])
        ];
        if (isset($_POST['nik'])) $data_pribadi['nik'] = !empty($_POST['nik']) ? escape($_POST['nik']) : null;
        if (isset($_POST['tempat_lahir'])) $data_pribadi['tempat_lahir'] = !empty($_POST['tempat_lahir']) ? escape($_POST['tempat_lahir']) : null;
        if (isset($_POST['tanggal_lahir'])) $data_pribadi['tanggal_lahir'] = !empty($_POST['tanggal_lahir']) ? escape($_POST['tanggal_lahir']) : null;
        
        updateData('Pribadi', $data_pribadi, "id_client = $id_client");
    } else {
        $data_perusahaan = [
            'nama_perusahaan' => escape($_POST['nama_perusahaan'])
        ];
        if (isset($_POST['npwp'])) $data_perusahaan['npwp'] = !empty($_POST['npwp']) ? escape($_POST['npwp']) : null;
        if (isset($_POST['nama_direktur'])) $data_perusahaan['nama_direktur'] = !empty($_POST['nama_direktur']) ? escape($_POST['nama_direktur']) : null;
        
        updateData('Perusahaan', $data_perusahaan, "id_client = $id_client");
    }
    
    $success = 'Client berhasil diupdate!';
}

// Handle delete client
if (isset($_GET['delete'])) {
    $id_client = (int)$_GET['delete'];
    if (deleteData('Client', "id_client = $id_client")) {
        $success = 'Client berhasil dihapus!';
    } else {
        $error = 'Gagal menghapus client: ' . error();
    }
}

// Ambil data client dengan detail
$query = "
    SELECT 
        c.*,
        p.nama_lengkap, p.nik, p.tempat_lahir, p.tanggal_lahir,
        pr.nama_perusahaan, pr.npwp, pr.nama_direktur
    FROM Client c
    LEFT JOIN Pribadi p ON c.id_client = p.id_client AND c.jenis_client = 'pribadi'
    LEFT JOIN Perusahaan pr ON c.id_client = pr.id_client AND c.jenis_client = 'perusahaan'
    ORDER BY c.created_at DESC
";

$result = query($query);
$clients = [];
if ($result) {
    while ($row = fetch_array($result)) {
        $clients[] = $row;
    }
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Management Client | NotarisPro</title>

<!-- Dependencies -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="styles/main.css" rel="stylesheet">
<link href="styles/navbar.css" rel="stylesheet">
<link href="styles/sidebar.css" rel="stylesheet">
<link href="styles/dashboard.css" rel="stylesheet">
</head>
<body class="dashboard-body">

<!-- Navbar -->
<?php include 'components/navbar.php'; ?>

<div class="dashboard-container">
    <!-- Sidebar -->
    <?php include 'components/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-users-cog me-2"></i>Management Client</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClientModal">
                <i class="fas fa-plus me-2"></i>Tambah Client
            </button>
        </div>

        <?php if(!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Jenis</th>
                                <th>Nama</th>
                                <th>Kontak</th>
                                <th>Alamat</th>
                                <th>Detail</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if(empty($clients)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-inbox fa-2x text-muted mb-2 d-block"></i>
                                    <span class="text-muted">Belum ada client</span>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($clients as $c): ?>
                            <tr>
                                <td><?= htmlspecialchars($c['id_client']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $c['jenis_client'] === 'pribadi' ? 'info' : 'primary' ?>">
                                        <?= ucfirst($c['jenis_client']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= htmlspecialchars($c['nama_lengkap'] ?? $c['nama_perusahaan'] ?? '-') ?>
                                </td>
                                <td>
                                    <div><i class="fas fa-phone me-1"></i><?= htmlspecialchars($c['nomor_telepon'] ?? '-') ?></div>
                                    <?php if($c['email']): ?>
                                        <div><i class="fas fa-envelope me-1"></i><?= htmlspecialchars($c['email']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($c['alamat'] ?? '-') ?></td>
                                <td>
                                    <?php if($c['jenis_client'] === 'pribadi'): ?>
                                        <small>
                                            <div>NIK: <?= htmlspecialchars($c['nik'] ?? '-') ?></div>
                                            <?php if($c['tempat_lahir'] || $c['tanggal_lahir']): ?>
                                                <div>TTL: <?= htmlspecialchars($c['tempat_lahir'] ?? '') ?>, <?= $c['tanggal_lahir'] ? date('d-m-Y', strtotime($c['tanggal_lahir'])) : '' ?></div>
                                            <?php endif; ?>
                                        </small>
                                    <?php else: ?>
                                        <small>
                                            <div>NPWP: <?= htmlspecialchars($c['npwp'] ?? '-') ?></div>
                                            <?php if($c['nama_direktur']): ?>
                                                <div>Direktur: <?= htmlspecialchars($c['nama_direktur']) ?></div>
                                            <?php endif; ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editClientModal<?= $c['id_client'] ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="management_client.php?delete=<?= $c['id_client'] ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Yakin hapus client ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            
                            <!-- Modal Edit Client -->
                            <div class="modal fade" id="editClientModal<?= $c['id_client'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Client</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST">
                                            <input type="hidden" name="action" value="update_client">
                                            <input type="hidden" name="id_client" value="<?= $c['id_client'] ?>">
                                            <input type="hidden" name="jenis_client" value="<?= $c['jenis_client'] ?>">
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Jenis Client</label>
                                                    <input type="text" class="form-control" value="<?= ucfirst($c['jenis_client']) ?>" disabled>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Nomor Telepon</label>
                                                    <input type="text" name="nomor_telepon" class="form-control" 
                                                           value="<?= htmlspecialchars($c['nomor_telepon'] ?? '') ?>">
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" name="email" class="form-control" 
                                                           value="<?= htmlspecialchars($c['email'] ?? '') ?>">
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Alamat</label>
                                                    <textarea name="alamat" class="form-control" rows="2"><?= htmlspecialchars($c['alamat'] ?? '') ?></textarea>
                                                </div>
                                                
                                                <?php if($c['jenis_client'] === 'pribadi'): ?>
                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                                        <input type="text" name="nama_lengkap" class="form-control" 
                                                               value="<?= htmlspecialchars($c['nama_lengkap'] ?? '') ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">NIK</label>
                                                        <input type="text" name="nik" class="form-control" 
                                                               value="<?= htmlspecialchars($c['nik'] ?? '') ?>">
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">Tempat Lahir</label>
                                                                <input type="text" name="tempat_lahir" class="form-control" 
                                                                       value="<?= htmlspecialchars($c['tempat_lahir'] ?? '') ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">Tanggal Lahir</label>
                                                                <input type="date" name="tanggal_lahir" class="form-control" 
                                                                       value="<?= $c['tanggal_lahir'] ? date('Y-m-d', strtotime($c['tanggal_lahir'])) : '' ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                                                        <input type="text" name="nama_perusahaan" class="form-control" 
                                                               value="<?= htmlspecialchars($c['nama_perusahaan'] ?? '') ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">NPWP</label>
                                                        <input type="text" name="npwp" class="form-control" 
                                                               value="<?= htmlspecialchars($c['npwp'] ?? '') ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Direktur</label>
                                                        <input type="text" name="nama_direktur" class="form-control" 
                                                               value="<?= htmlspecialchars($c['nama_direktur'] ?? '') ?>">
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal Tambah Client -->
<div class="modal fade" id="addClientModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Client Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add_client">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Jenis Client <span class="text-danger">*</span></label>
                        <select name="jenis_client" class="form-select" id="jenisClient" required>
                            <option value="">Pilih Jenis</option>
                            <option value="pribadi">Pribadi</option>
                            <option value="perusahaan">Perusahaan</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="text" name="nomor_telepon" class="form-control" placeholder="08xxxxxxxxxx">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="email@example.com">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat lengkap"></textarea>
                    </div>
                    
                    <!-- Form Pribadi -->
                    <div id="formPribadi" style="display:none;">
                        <hr>
                        <h6>Data Pribadi</h6>
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama lengkap">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">NIK</label>
                            <input type="text" name="nik" class="form-control" placeholder="Nomor Induk Kependudukan">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" class="form-control" placeholder="Tempat lahir">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Perusahaan -->
                    <div id="formPerusahaan" style="display:none;">
                        <hr>
                        <h6>Data Perusahaan</h6>
                        <div class="mb-3">
                            <label class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_perusahaan" class="form-control" placeholder="Nama perusahaan">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">NPWP</label>
                            <input type="text" name="npwp" class="form-control" placeholder="Nomor Pokok Wajib Pajak">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Direktur</label>
                            <input type="text" name="nama_direktur" class="form-control" placeholder="Nama direktur">
                        </div>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Toggle form berdasarkan jenis client
document.getElementById('jenisClient')?.addEventListener('change', function() {
    const jenis = this.value;
    const formPribadi = document.getElementById('formPribadi');
    const formPerusahaan = document.getElementById('formPerusahaan');
    
    formPribadi.style.display = jenis === 'pribadi' ? 'block' : 'none';
    formPerusahaan.style.display = jenis === 'perusahaan' ? 'block' : 'none';
    
    // Set required fields
    const namaLengkap = document.querySelector('input[name="nama_lengkap"]');
    const namaPerusahaan = document.querySelector('input[name="nama_perusahaan"]');
    
    if (namaLengkap) namaLengkap.required = jenis === 'pribadi';
    if (namaPerusahaan) namaPerusahaan.required = jenis === 'perusahaan';
});

// Sidebar toggle
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


