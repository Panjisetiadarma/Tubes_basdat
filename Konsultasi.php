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

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: auth.php');
    exit;
}

// Handle actions
$action = $_GET['action'] ?? 'list';
$id_konsultasi = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data client untuk dropdown
$clients = getData(
    'Client c 
     LEFT JOIN Pribadi p ON c.id_client=p.id_client
     LEFT JOIN Perusahaan pr ON c.id_client=pr.id_client',
    'c.id_client, COALESCE(p.nama_lengkap, pr.nama_perusahaan) AS nama',
    '1=1',
    'c.id_client ASC'
);

// Ambil data Notaris dan PPAT untuk dropdown
$notaris_list = getData('Notaris', '*', '1=1', 'nama_notaris ASC');
$ppat_list = getData('Ppat', '*', '1=1', 'nama_ppat ASC');

// Handle tambah konsultasi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_konsultasi') {
    $id_client = (int)$_POST['id_client'];
    $jenis_konsultasi = escape($_POST['jenis_konsultasi']);
    $topik = escape($_POST['topik']);
    $pesan = isset($_POST['pesan']) && trim($_POST['pesan']) !== '' ? escape($_POST['pesan']) : null;
    $tanggal_konsultasi = !empty($_POST['tanggal_konsultasi']) ? escape($_POST['tanggal_konsultasi']) : null;
    $durasi = isset($_POST['durasi']) && !empty($_POST['durasi']) ? (int)$_POST['durasi'] : null;
    $link_meeting = isset($_POST['link_meeting']) && !empty($_POST['link_meeting']) ? escape($_POST['link_meeting']) : null;
    
    // Tentukan penangan berdasarkan jenis
    $id_notaris = null;
    $id_ppat = null;
    $id_user = $current_user['id_user']; // Default ke user yang login
    
    if (isset($_POST['id_notaris']) && !empty($_POST['id_notaris'])) {
        $id_notaris = (int)$_POST['id_notaris'];
    }
    if (isset($_POST['id_ppat']) && !empty($_POST['id_ppat'])) {
        $id_ppat = (int)$_POST['id_ppat'];
    }
    if (isset($_POST['id_user']) && !empty($_POST['id_user'])) {
        $id_user = (int)$_POST['id_user'];
    }
    
    $data = [
        'id_client' => $id_client,
        'jenis_konsultasi' => $jenis_konsultasi,
        'topik' => $topik,
        'status' => 'terjadwal'
    ];
    
    // Tambahkan field yang tidak null
    if ($pesan !== null) $data['pesan'] = $pesan;
    if ($id_notaris !== null) $data['id_notaris'] = $id_notaris;
    if ($id_ppat !== null) $data['id_ppat'] = $id_ppat;
    if ($id_user !== null) $data['id_user'] = $id_user;
    if ($tanggal_konsultasi !== null) $data['tanggal_konsultasi'] = $tanggal_konsultasi;
    if ($durasi !== null) $data['durasi'] = $durasi;
    if ($link_meeting !== null) $data['link_meeting'] = $link_meeting;
    
    $id_konsultasi_new = insertData('Konsultasi', $data);
    
    if ($id_konsultasi_new) {
        header("Location: konsultasi.php?action=detail&id=$id_konsultasi_new&added=1");
        exit;
    } else {
        $error = "Gagal menambahkan konsultasi: " . error();
    }
}

// Handle kirim pesan chat
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_message') {
    $id_konsultasi_msg = (int)$_POST['id_konsultasi'];
    $pesan_text = escape($_POST['pesan_text']);
    
    if (!empty($pesan_text)) {
        $data_msg = [
            'id_konsultasi' => $id_konsultasi_msg,
            'id_pengirim' => $current_user['id_user'],
            'pesan' => $pesan_text,
            'tipe' => 'text'
        ];
        
        if (insertData('Chat_Message', $data_msg)) {
            // Update status konsultasi menjadi 'berlangsung' jika masih 'terjadwal'
            $query_update = "UPDATE Konsultasi SET status = 'berlangsung' WHERE id_konsultasi = $id_konsultasi_msg AND status = 'terjadwal'";
            query($query_update);
            
            header("Location: konsultasi.php?action=detail&id=$id_konsultasi_msg&message_sent=1");
            exit;
        } else {
            $error = "Gagal mengirim pesan: " . error();
        }
    }
}

// Handle update status konsultasi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $id_konsultasi_status = (int)$_POST['id_konsultasi'];
    $status_baru = escape($_POST['status']);
    
    if (updateData('Konsultasi', ['status' => $status_baru], "id_konsultasi = $id_konsultasi_status")) {
        header("Location: konsultasi.php?action=detail&id=$id_konsultasi_status&status_updated=1");
        exit;
    } else {
        $error = "Gagal mengupdate status: " . error();
    }
}

// Ambil daftar konsultasi
if ($action === 'list') {
    $query = "
        SELECT 
            k.*,
            COALESCE(p.nama_lengkap, pr.nama_perusahaan) AS nama_client,
            n.nama_notaris,
            pp.nama_ppat,
            u.nama_lengkap AS staff_nama
        FROM Konsultasi k
        LEFT JOIN Client c ON k.id_client = c.id_client
        LEFT JOIN Pribadi p ON c.id_client = p.id_client AND c.jenis_client = 'pribadi'
        LEFT JOIN Perusahaan pr ON c.id_client = pr.id_client AND c.jenis_client = 'perusahaan'
        LEFT JOIN Notaris n ON k.id_notaris = n.id_notaris
        LEFT JOIN Ppat pp ON k.id_ppat = pp.id_ppat
        LEFT JOIN User u ON k.id_user = u.id_user
        ORDER BY k.created_at DESC
    ";
    
    $result = query($query);
    $konsultasi_list = [];
    if ($result) {
        while ($row = fetch_array($result)) {
            $konsultasi_list[] = $row;
        }
    }
}

// Ambil detail konsultasi dan chat messages
if ($action === 'detail' && $id_konsultasi > 0) {
    $query_detail = "
        SELECT 
            k.*,
            COALESCE(p.nama_lengkap, pr.nama_perusahaan) AS nama_client,
            c.email AS email_client,
            c.nomor_telepon AS telepon_client,
            n.nama_notaris,
            pp.nama_ppat,
            u.nama_lengkap AS staff_nama,
            u.username AS staff_username
        FROM Konsultasi k
        LEFT JOIN Client c ON k.id_client = c.id_client
        LEFT JOIN Pribadi p ON c.id_client = p.id_client AND c.jenis_client = 'pribadi'
        LEFT JOIN Perusahaan pr ON c.id_client = pr.id_client AND c.jenis_client = 'perusahaan'
        LEFT JOIN Notaris n ON k.id_notaris = n.id_notaris
        LEFT JOIN Ppat pp ON k.id_ppat = pp.id_ppat
        LEFT JOIN User u ON k.id_user = u.id_user
        WHERE k.id_konsultasi = $id_konsultasi
    ";
    
    $result_detail = query($query_detail);
    $konsultasi_detail = $result_detail ? fetch_array($result_detail) : null;
    
    // Ambil chat messages
    if ($konsultasi_detail) {
        $query_messages = "
            SELECT 
                cm.*,
                u.nama_lengkap AS nama_pengirim,
                u.username AS username_pengirim,
                u.role AS role_pengirim
            FROM Chat_Message cm
            LEFT JOIN User u ON cm.id_pengirim = u.id_user
            WHERE cm.id_konsultasi = $id_konsultasi
            ORDER BY cm.waktu_kirim ASC
        ";
        
        $result_messages = query($query_messages);
        $messages = [];
        if ($result_messages) {
            while ($row = fetch_array($result_messages)) {
                $messages[] = $row;
            }
        }
    }
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Konsultasi Online | NotarisPro</title>

<!-- Dependencies -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="styles/main.css" rel="stylesheet">
<link href="styles/navbar.css" rel="stylesheet">
<link href="styles/sidebar.css" rel="stylesheet">
<link href="styles/dashboard.css" rel="stylesheet">

<style>
.chat-container {
    max-height: 500px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 1rem;
    background: #f8f9fa;
}
.message-item {
    margin-bottom: 1rem;
    padding: 0.75rem;
    border-radius: 8px;
    background: white;
}
.message-item.own-message {
    background: #e3f2fd;
    margin-left: 20%;
}
.message-item.other-message {
    background: #f5f5f5;
    margin-right: 20%;
}
.message-header {
    font-size: 0.85rem;
    color: #666;
    margin-bottom: 0.25rem;
}
.message-body {
    color: #333;
}
.badge-status {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
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
        
        <?php if ($action === 'list'): ?>
            <!-- Daftar Konsultasi -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-comments me-2"></i>Konsultasi Online</h2>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addKonsultasiModal">
                    <i class="fas fa-plus me-2"></i>Tambah Konsultasi
                </button>
            </div>

            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if(isset($_GET['added'])): ?>
                <div class="alert alert-success">Konsultasi berhasil ditambahkan!</div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Client</th>
                                    <th>Jenis</th>
                                    <th>Topik</th>
                                    <th>Penangan</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if(empty($konsultasi_list)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-inbox fa-2x text-muted mb-2 d-block"></i>
                                        <span class="text-muted">Belum ada konsultasi</span>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($konsultasi_list as $k): ?>
                                <tr>
                                    <td><?= htmlspecialchars($k['id_konsultasi']) ?></td>
                                    <td><?= htmlspecialchars($k['nama_client'] ?? '-') ?></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?php
                                            $jenis = $k['jenis_konsultasi'];
                                            echo $jenis === 'chat' ? 'Chat' : ($jenis === 'video_call' ? 'Video Call' : 'Janji Temu');
                                            ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($k['topik'] ?? '-') ?></td>
                                    <td>
                                        <?php
                                        $penangan = $k['nama_notaris'] ?? $k['nama_ppat'] ?? $k['staff_nama'] ?? '-';
                                        echo htmlspecialchars($penangan);
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($k['tanggal_konsultasi']) {
                                            echo date('d-m-Y H:i', strtotime($k['tanggal_konsultasi']));
                                        } else {
                                            echo date('d-m-Y H:i', strtotime($k['created_at']));
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $status = strtolower($k['status']);
                                        $badge_class = 'bg-secondary';
                                        if ($status === 'berlangsung') $badge_class = 'bg-primary';
                                        elseif ($status === 'selesai') $badge_class = 'bg-success';
                                        elseif ($status === 'dibatalkan') $badge_class = 'bg-danger';
                                        ?>
                                        <span class="badge <?= $badge_class ?>">
                                            <?= ucfirst($status) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="konsultasi.php?action=detail&id=<?= $k['id_konsultasi'] ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        <?php elseif ($action === 'detail' && $konsultasi_detail): ?>
            <!-- Detail Konsultasi -->
            <div class="mb-3">
                <a href="konsultasi.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>

            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if(isset($_GET['message_sent'])): ?>
                <div class="alert alert-success">Pesan berhasil dikirim!</div>
            <?php endif; ?>

            <?php if(isset($_GET['status_updated'])): ?>
                <div class="alert alert-success">Status berhasil diupdate!</div>
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Detail Konsultasi #<?= htmlspecialchars($konsultasi_detail['id_konsultasi']) ?></h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Client:</strong> <?= htmlspecialchars($konsultasi_detail['nama_client'] ?? '-') ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($konsultasi_detail['email_client'] ?? '-') ?></p>
                            <p><strong>Telepon:</strong> <?= htmlspecialchars($konsultasi_detail['telepon_client'] ?? '-') ?></p>
                            <p><strong>Jenis:</strong> 
                                <span class="badge bg-info">
                                    <?php
                                    $jenis = $konsultasi_detail['jenis_konsultasi'];
                                    echo $jenis === 'chat' ? 'Chat' : ($jenis === 'video_call' ? 'Video Call' : 'Janji Temu');
                                    ?>
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Topik:</strong> <?= htmlspecialchars($konsultasi_detail['topik'] ?? '-') ?></p>
                            <p><strong>Penangan:</strong> 
                                <?php
                                $penangan = $konsultasi_detail['nama_notaris'] ?? $konsultasi_detail['nama_ppat'] ?? $konsultasi_detail['staff_nama'] ?? '-';
                                echo htmlspecialchars($penangan);
                                ?>
                            </p>
                            <p><strong>Tanggal:</strong> 
                                <?php
                                if ($konsultasi_detail['tanggal_konsultasi']) {
                                    echo date('d-m-Y H:i', strtotime($konsultasi_detail['tanggal_konsultasi']));
                                } else {
                                    echo date('d-m-Y H:i', strtotime($konsultasi_detail['created_at']));
                                }
                                ?>
                            </p>
                            <p><strong>Status:</strong> 
                                <?php
                                $status = strtolower($konsultasi_detail['status']);
                                $badge_class = 'bg-secondary';
                                if ($status === 'berlangsung') $badge_class = 'bg-primary';
                                elseif ($status === 'selesai') $badge_class = 'bg-success';
                                elseif ($status === 'dibatalkan') $badge_class = 'bg-danger';
                                ?>
                                <span class="badge <?= $badge_class ?>">
                                    <?= ucfirst($status) ?>
                                </span>
                            </p>
                            <?php if ($konsultasi_detail['link_meeting']): ?>
                                <p><strong>Link Meeting:</strong> 
                                    <a href="<?= htmlspecialchars($konsultasi_detail['link_meeting']) ?>" target="_blank">
                                        <?= htmlspecialchars($konsultasi_detail['link_meeting']) ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if ($konsultasi_detail['pesan']): ?>
                        <div class="mt-3">
                            <strong>Pesan Awal:</strong>
                            <p class="text-muted"><?= nl2br(htmlspecialchars($konsultasi_detail['pesan'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Chat Messages -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Pesan Chat</h5>
                </div>
                <div class="card-body">
                    <div class="chat-container" id="chatContainer">
                        <?php if(empty($messages)): ?>
                            <p class="text-center text-muted">Belum ada pesan</p>
                        <?php else: ?>
                            <?php foreach($messages as $msg): ?>
                                <div class="message-item <?= $msg['id_pengirim'] == $current_user['id_user'] ? 'own-message' : 'other-message' ?>">
                                    <div class="message-header">
                                        <strong><?= htmlspecialchars($msg['nama_pengirim'] ?? 'Unknown') ?></strong>
                                        <span class="text-muted ms-2">
                                            <?= date('d-m-Y H:i', strtotime($msg['waktu_kirim'])) ?>
                                        </span>
                                    </div>
                                    <div class="message-body">
                                        <?= nl2br(htmlspecialchars($msg['pesan'])) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Form Kirim Pesan -->
                    <form method="POST" class="mt-3">
                        <input type="hidden" name="action" value="send_message">
                        <input type="hidden" name="id_konsultasi" value="<?= $konsultasi_detail['id_konsultasi'] ?>">
                        <div class="input-group">
                            <textarea name="pesan_text" class="form-control" rows="2" placeholder="Ketik pesan..." required></textarea>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Kirim
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Update Status -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Update Status</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id_konsultasi" value="<?= $konsultasi_detail['id_konsultasi'] ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <select name="status" class="form-select" required>
                                    <option value="terjadwal" <?= $konsultasi_detail['status'] === 'terjadwal' ? 'selected' : '' ?>>Terjadwal</option>
                                    <option value="berlangsung" <?= $konsultasi_detail['status'] === 'berlangsung' ? 'selected' : '' ?>>Berlangsung</option>
                                    <option value="selesai" <?= $konsultasi_detail['status'] === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                    <option value="dibatalkan" <?= $konsultasi_detail['status'] === 'dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Status
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        <?php endif; ?>
    </main>
</div>

<!-- Modal Tambah Konsultasi -->
<div class="modal fade" id="addKonsultasiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Konsultasi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_konsultasi">
                    
                    <div class="mb-3">
                        <label class="form-label">Client <span class="text-danger">*</span></label>
                        <select name="id_client" class="form-select" required>
                            <option value="">Pilih Client</option>
                            <?php foreach($clients as $c): ?>
                                <option value="<?= $c['id_client'] ?>"><?= htmlspecialchars($c['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jenis Konsultasi <span class="text-danger">*</span></label>
                        <select name="jenis_konsultasi" class="form-select" id="jenisKonsultasi" required>
                            <option value="chat">Chat</option>
                            <option value="video_call">Video Call</option>
                            <option value="janji_temu">Janji Temu</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Topik <span class="text-danger">*</span></label>
                        <input type="text" name="topik" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pesan</label>
                        <textarea name="pesan" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Konsultasi</label>
                        <input type="datetime-local" name="tanggal_konsultasi" class="form-control">
                    </div>

                    <div class="mb-3" id="durasiField" style="display:none;">
                        <label class="form-label">Durasi (menit)</label>
                        <input type="number" name="durasi" class="form-control" min="1">
                    </div>

                    <div class="mb-3" id="linkMeetingField" style="display:none;">
                        <label class="form-label">Link Meeting</label>
                        <input type="url" name="link_meeting" class="form-control" placeholder="https://...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Penangan (Notaris)</label>
                        <select name="id_notaris" class="form-select">
                            <option value="">Pilih Notaris (opsional)</option>
                            <?php foreach($notaris_list as $n): ?>
                                <option value="<?= $n['id_notaris'] ?>"><?= htmlspecialchars($n['nama_notaris']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Penangan (PPAT)</label>
                        <select name="id_ppat" class="form-select">
                            <option value="">Pilih PPAT (opsional)</option>
                            <?php foreach($ppat_list as $p): ?>
                                <option value="<?= $p['id_ppat'] ?>"><?= htmlspecialchars($p['nama_ppat']) ?></option>
                            <?php endforeach; ?>
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

// Toggle fields berdasarkan jenis konsultasi
document.getElementById('jenisKonsultasi')?.addEventListener('change', function() {
    const jenis = this.value;
    const durasiField = document.getElementById('durasiField');
    const linkMeetingField = document.getElementById('linkMeetingField');
    
    if (jenis === 'video_call' || jenis === 'janji_temu') {
        durasiField.style.display = 'block';
        if (jenis === 'video_call') {
            linkMeetingField.style.display = 'block';
        } else {
            linkMeetingField.style.display = 'none';
        }
    } else {
        durasiField.style.display = 'none';
        linkMeetingField.style.display = 'none';
    }
});

// Auto scroll chat ke bawah
const chatContainer = document.getElementById('chatContainer');
if (chatContainer) {
    chatContainer.scrollTop = chatContainer.scrollHeight;
}
</script>
</body>
</html>

