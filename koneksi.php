<?php

// Konfigurasi Database
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'project_notaris';

// Membuat koneksi
$koneksi = mysqli_connect($host, $username, $password, $database);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset ke UTF-8 untuk mendukung karakter Indonesia
mysqli_set_charset($koneksi, "utf8mb4");

// Fungsi helper untuk query
function query($sql) {
    global $koneksi;
    return mysqli_query($koneksi, $sql);
}

// Fungsi untuk escape string (mencegah SQL injection)
function escape($string) {
    global $koneksi;
    return mysqli_real_escape_string($koneksi, $string);
}

// Fungsi untuk mendapatkan hasil query sebagai array
function fetch_array($result) {
    if ($result) {
        return mysqli_fetch_array($result, MYSQLI_ASSOC);
    }
    return false;
}

// Fungsi untuk mendapatkan jumlah baris
function num_rows($result) {
    return mysqli_num_rows($result);
}

// Fungsi untuk mendapatkan ID terakhir yang di-insert
function insert_id() {
    global $koneksi;
    return mysqli_insert_id($koneksi);
}

// Fungsi untuk mendapatkan error
function error() {
    global $koneksi;
    return mysqli_error($koneksi);
}

// Fungsi untuk menutup koneksi
function close_connection() {
    global $koneksi;
    mysqli_close($koneksi);
}

// Fungsi untuk memulai session
function start_session() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}



// Fungsi untuk cek apakah user sudah login
function is_logged_in() {
    start_session();
    return isset($_SESSION['user_id']);
}

// Fungsi untuk mendapatkan user yang sedang login
function get_logged_in_user() {
    start_session();
    if (isset($_SESSION['user_id'])) {
        global $koneksi;
        $user_id = (int)$_SESSION['user_id'];
        $query = "SELECT * FROM User WHERE id_user = $user_id";
        $result = query($query);
        if ($result && num_rows($result) > 0) {
            $user = fetch_array($result);
            if ($user) {
                return $user;
            }
        }
    }
    return null;
}

// Fungsi untuk require login (redirect jika belum login)
function require_login() {
    if (!is_logged_in()) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Anda harus login terlebih dahulu',
            'redirect' => 'auth.html'
        ]);
        exit;
    }
}

// Fungsi untuk require role tertentu
function require_role($allowed_roles) {
    require_login();
    $user = get_logged_in_user();
    if (!$user || !in_array($user['role'], $allowed_roles)) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.'
        ]);
        exit;
    }
}

// Fungsi untuk mengirim response JSON
function json_response($data, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// Fungsi untuk sanitize input
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Fungsi untuk hash password (TIDAK DIENKRIPSI - untuk development saja)
function hash_password($password) {
    // Langsung return password tanpa enkripsi
    return $password;
}

// Fungsi untuk verify password (TIDAK DIENKRIPSI - untuk development saja)
function verify_password($password, $stored_password) {
    // Bandingkan langsung tanpa enkripsi
    return $password === $stored_password;
}

// Fungsi untuk validasi email
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Pastikan folder uploads dan logs ada
if (!file_exists(__DIR__ . '/uploads')) {
    mkdir(__DIR__ . '/uploads', 0755, true);
}

if (!file_exists(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0755, true);
}
?>

