<?php
/**
 * Konfigurasi Database
 * File ini berisi konfigurasi koneksi ke database MySQL
 */

// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'project_notaris');

// Konfigurasi Aplikasi
define('APP_NAME', 'Notaris Pro');
define('APP_URL', 'http://localhost/Tubes_basdat');

// Konfigurasi Session
define('SESSION_LIFETIME', 3600); // 1 jam dalam detik

// Konfigurasi Upload
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB dalam bytes

/**
 * Koneksi ke Database
 */
function getDBConnection() {
    static $conn = null;
    
    if ($conn === null) {
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            // Set charset ke UTF-8
            $conn->set_charset("utf8mb4");
            
            // Cek koneksi
            if ($conn->connect_error) {
                throw new Exception("Koneksi gagal: " . $conn->connect_error);
            }
        } catch (Exception $e) {
            error_log("Database Error: " . $e->getMessage());
            die(json_encode([
                'success' => false,
                'message' => 'Koneksi database gagal. Silakan hubungi administrator.'
            ]));
        }
    }
    
    return $conn;
}

/**
 * Fungsi untuk memulai session dengan konfigurasi yang aman
 */
function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        // Konfigurasi session yang aman
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 0); // Set ke 1 jika menggunakan HTTPS
        ini_set('session.cookie_samesite', 'Strict');
        
        session_start();
        
        // Regenerate session ID secara berkala untuk keamanan
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } else if (time() - $_SESSION['created'] > 1800) {
            // Regenerate setiap 30 menit
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }
    }
}

/**
 * Fungsi untuk mengirim response JSON
 */
function sendJSONResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

/**
 * Fungsi untuk validasi input
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Fungsi untuk validasi email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Fungsi untuk hash password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Fungsi untuk verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Fungsi untuk mendapatkan user yang sedang login
 */
function getCurrentUser() {
    startSecureSession();
    
    if (isset($_SESSION['user_id'])) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT id_user, username, nama_lengkap, role FROM User WHERE id_user = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($user = $result->fetch_assoc()) {
            return $user;
        }
    }
    
    return null;
}

/**
 * Fungsi untuk cek apakah user sudah login
 */
function isLoggedIn() {
    startSecureSession();
    return isset($_SESSION['user_id']);
}

/**
 * Fungsi untuk require login (redirect jika belum login)
 */
function requireLogin() {
    if (!isLoggedIn()) {
        sendJSONResponse([
            'success' => false,
            'message' => 'Anda harus login terlebih dahulu',
            'redirect' => 'auth.html'
        ], 401);
    }
}

/**
 * Fungsi untuk log error
 */
function logError($message, $file = '', $line = '') {
    $logMessage = date('Y-m-d H:i:s') . " - Error: $message";
    if ($file) $logMessage .= " in $file";
    if ($line) $logMessage .= " on line $line";
    $logMessage .= "\n";
    
    error_log($logMessage, 3, __DIR__ . '/logs/error.log');
}

// Pastikan folder uploads dan logs ada
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

if (!file_exists(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0755, true);
}
?>

