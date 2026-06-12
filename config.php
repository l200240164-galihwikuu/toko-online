<?php
// ============================================
// KONFIGURASI DATABASE
// ============================================
define('DB_HOST', 'mysql.railway.internal');
define('DB_PORT', 3306);
define('DB_USER', 'root');
define('DB_PASS', 'smNSQPuXRdWUKyaeIcJngyeJHgmFqtMw');
define('DB_NAME', 'toko_online');

define('BASE_URL', '/');
define('UPLOAD_DIR', __DIR__ . '/uploads/products/');

// Koneksi Database
function getDB() {
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die(json_encode(['error' => 'Koneksi database gagal: ' . $conn->connect_error]));
        }
        $conn->set_charset('utf8mb4');
    }
    return $conn;
}

// Session helper
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . 'login.php');
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: ' . BASE_URL . 'index.php');
        exit;
    }
}

function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

session_start();
?>
