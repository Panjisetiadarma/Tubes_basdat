<?php
/**
 * API Endpoint untuk Login
 * Method: POST
 * 
 * Request Body:
 * {
 *   "email": "user@example.com",
 *   "password": "password123"
 * }
 * 
 * Response:
 * {
 *   "success": true,
 *   "message": "Login berhasil",
 *   "user": { ... }
 * }
 */

require_once '../config.php';

// Set header untuk CORS (jika diperlukan)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Hanya terima POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSONResponse([
        'success' => false,
        'message' => 'Method tidak diizinkan'
    ], 405);
}

// Ambil data dari request
$input = json_decode(file_get_contents('php://input'), true);

// Jika tidak ada JSON, coba ambil dari POST
if (!$input) {
    $input = $_POST;
}

// Bisa login dengan email atau username
$email = isset($input['email']) ? sanitizeInput($input['email']) : (isset($input['username']) ? sanitizeInput($input['username']) : '');
$password = isset($input['password']) ? $input['password'] : '';

// Validasi input
if (empty($email) || empty($password)) {
    sendJSONResponse([
        'success' => false,
        'message' => 'Username/Email dan password harus diisi'
    ], 400);
}

try {
    $conn = getDBConnection();
    
    // Cari user berdasarkan username (karena di database menggunakan username)
    // User bisa login dengan username atau email (jika username sama dengan email)
    $stmt = $conn->prepare("SELECT id_user, username, password, nama_lengkap, role FROM User WHERE username = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        sendJSONResponse([
            'success' => false,
            'message' => 'Username/Email atau password salah'
        ], 401);
    }
    
    $user = $result->fetch_assoc();
    
    // Verifikasi password
    if (!verifyPassword($password, $user['password'])) {
        sendJSONResponse([
            'success' => false,
            'message' => 'Username/Email atau password salah'
        ], 401);
    }
    
    // Start session
    startSecureSession();
    
    // Set session variables
    $_SESSION['user_id'] = $user['id_user'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['login_time'] = time();
    
    // Hapus password dari response
    unset($user['password']);
    
    // Response sukses
    sendJSONResponse([
        'success' => true,
        'message' => 'Login berhasil',
        'user' => [
            'id' => $user['id_user'],
            'username' => $user['username'],
            'nama_lengkap' => $user['nama_lengkap'],
            'role' => $user['role']
        ]
    ], 200);
    
} catch (Exception $e) {
    logError($e->getMessage(), __FILE__, __LINE__);
    sendJSONResponse([
        'success' => false,
        'message' => 'Terjadi kesalahan pada server. Silakan coba lagi.'
    ], 500);
}
?>

