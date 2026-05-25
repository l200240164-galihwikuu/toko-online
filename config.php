<?php
// ============================================
// KONFIGURASI DATABASE
// ============================================
define('DB_HOST', $_ENV['MYSQLHOST']);
define('DB_PORT', $_ENV['MYSQLPORT']);
define('DB_USER', $_ENV['MYSQLUSER']);
define('DB_PASS', $_ENV['MYSQLPASSWORD']);
define('DB_NAME', $_ENV['MYSQLDATABASE']);

define('BASE_URL', '/');
define('UPLOAD_DIR', __DIR__ . '/uploads/products/');

function getDB() {
    static $conn = null;

    if ($conn === null) {
        $conn = new mysqli(
            DB_HOST,
            DB_USER,
            DB_PASS,
            DB_NAME,
            DB_PORT
        );

        if ($conn->connect_error) {
            die("Koneksi gagal: " . $conn->connect_error);
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
