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
$page_title = 'Data Arsip';

$error = '';
$success = '';

// Handle upload file
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload_file') {
    $id_pengajuan = (int)$_POST['id_pengajuan'];
    
    if (isset($_FILES['file_dokumen']) && $_FILES['file_dokumen']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/arsip/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_name = time() . '_' . basename($_FILES['file_dokumen']['name']);
        $file_path = $upload_dir . $file_name;
        $file_size = $_FILES['file_dokumen']['size'];
        $file_type = $_FILES['file_dokumen']['type'];
        
        $allowed_types = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image/jpeg',
            'image/png',
            'image/jpg'
        ];
        
        if (in_array($file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES['file_dokumen']['tmp_name'], $file_path)) {
                $data_arsip = [
                    'id_pengajuan' => $id_pengajuan,
                    'nama_file' => $_FILES['file_dokumen']['name'],
                    'path_file' => $file_path,
                    'tipe_file' => $file_type,
                    'ukuran_file' => $file_size
                ];
                
                if (insertData('Arsip_File', $data_arsip)) {
                    $success = 'File berhasil diupload!';
                } else {
                    $error = 'Gagal menyimpan data arsip: ' . error();
                    @unlink($file_path);
                }
            } else {
                $error = 'Gagal mengupload file';
            }
        } else {
            $error = 'Format file tidak didukung. Gunakan PDF, DOC, DOCX, JPG, atau PNG';
        }
    } else {
        $error = 'File tidak ditemukan atau terjadi error saat upload';
    }
}

// Delete arsip
if (isset($_GET['delete']) && $is_admin) {
    $id_file = (int)$_GET['delete'];
    
    // Ambil path file untuk dihapus
    $file_data = getData('Arsip_File', '*', "id_file = $id_file");
    if (!empty($file_data)) {
        $file_path = $file_data[0]['path_file'];
        if (file_exists($file_path)) {
            @unlink($file_path);
        }
    }
    
    if (deleteData('Arsip_File', "id_file = $id_file")) {
        $success = 'Arsip berhasil dihapus!';
    } else {
        $error = 'Gagal menghapus arsip: ' . error();
    }
}

// Filter arsip berdasarkan role
// Admin lihat semua, User hanya lihat arsip dari pengajuan mereka
$where_arsip = "1=1";
if (!$is_admin) {
    $where_arsip = "p.id_user = " . (int)$current_user['id_user'];
}

// Ambil data arsip dengan info pengajuan
$query_arsip = "
    SELECT 
        a.*, 
        p.jenis_pengajuan, 
        p.id_client,
        p.id_user,
        COALESCE(pr.nama_lengkap, pe.nama_perusahaan) AS nama_client
    FROM Arsip_File a 
    LEFT JOIN Pengajuan p ON a.id_pengajuan = p.id_pengajuan
    LEFT JOIN Client c ON p.id_client = c.id_client
    LEFT JOIN Pribadi pr ON c.id_client = pr.id_client AND c.jenis_client = 'pribadi'
    LEFT JOIN Perusahaan pe ON c.id_client = pe.id_client AND c.jenis_client = 'perusahaan'
    WHERE $where_arsip
    ORDER BY a.uploaded_at DESC
";

$result_arsip = query($query_arsip);
$arsip_list = [];
if ($result_arsip) {
    while ($row = fetch_array($result_arsip)) {
        $arsip_list[] = $row;
    }
}

// Ambil list pengajuan untuk dropdown
$where_pengajuan = "1=1";
if (!$is_admin) {
    $where_pengajuan = "id_user = " . (int)$current_user['id_user'];
}
$pengajuan_list = getData('Pengajuan', '*', $where_pengajuan, 'id_pengajuan DESC');

// Helper function untuk format ukuran file
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($page_title) ?> | NotarisPro</title>

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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-folder-open me-2"></i><?= htmlspecialchars($page_title) ?></h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadFileModal">
                <i class="fas fa-upload me-2"></i>Upload File
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
                                <th>ID Pengajuan</th>
                                <th>Jenis Pengajuan</th>
                                <?php if($is_admin): ?>
                                    <th>Client</th>
                                <?php endif; ?>
                                <th>Nama File</th>
                                <th>Tipe</th>
                                <th>Ukuran</th>
                                <th>Tanggal Upload</th>
                                <th>Aksi</th>
                                <?php if($is_admin) echo "<th>Delete</th>"; ?>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if(empty($arsip_list)): ?>
                            <tr>
                                <td colspan="<?= $is_admin ? 10 : 9 ?>" class="text-center py-4">
                                    <i class="fas fa-inbox fa-2x text-muted mb-2 d-block"></i>
                                    <span class="text-muted">Belum ada file arsip</span>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($arsip_list as $a): ?>
                            <tr>
                                <td><?= htmlspecialchars($a['id_file']) ?></td>
                                <td><?= htmlspecialchars($a['id_pengajuan']) ?></td>
                                <td><?= htmlspecialchars($a['jenis_pengajuan'] ?? '-') ?></td>
                                <?php if($is_admin): ?>
                                    <td><?= htmlspecialchars($a['nama_client'] ?? '-') ?></td>
                                <?php endif; ?>
                                <td>
                                    <i class="fas fa-file me-2"></i>
                                    <?= htmlspecialchars($a['nama_file']) ?>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <?php
                                        $tipe = $a['tipe_file'] ?? '';
                                        if (strpos($tipe, 'pdf') !== false) echo 'PDF';
                                        elseif (strpos($tipe, 'word') !== false || strpos($tipe, 'document') !== false) echo 'DOC';
                                        elseif (strpos($tipe, 'image') !== false) echo 'IMG';
                                        else echo 'FILE';
                                        ?>
                                    </span>
                                </td>
                                <td><?= formatFileSize((int)($a['ukuran_file'] ?? 0)) ?></td>
                                <td>
                                    <?php 
                                    if ($a['uploaded_at']) {
                                        echo date('d-m-Y H:i', strtotime($a['uploaded_at']));
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if (file_exists($a['path_file'])): ?>
                                        <a href="<?= htmlspecialchars($a['path_file']) ?>" 
                                           target="_blank" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">File tidak ditemukan</span>
                                    <?php endif; ?>
                                </td>
                                <?php if($is_admin): ?>
                                <td>
                                    <a href="arsip.php?delete=<?= $a['id_file'] ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Yakin hapus file ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                                <?php endif; ?>
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

<!-- Modal Upload File -->
<div class="modal fade" id="uploadFileModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload File Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload_file">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih Pengajuan <span class="text-danger">*</span></label>
                        <select name="id_pengajuan" class="form-select" required>
                            <option value="">Pilih Pengajuan</option>
                            <?php foreach($pengajuan_list as $p): ?>
                                <option value="<?= $p['id_pengajuan'] ?>">
                                    #<?= $p['id_pengajuan'] ?> - <?= htmlspecialchars($p['jenis_pengajuan']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pilih File <span class="text-danger">*</span></label>
                        <input type="file" name="file_dokumen" class="form-control" required 
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG. Maksimal 10MB</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
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
