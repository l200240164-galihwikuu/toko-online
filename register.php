<?php
require_once 'config.php';

if (isLoggedIn()) { header('Location: ' . BASE_URL . 'index.php'); exit; }

$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = sanitize($_POST['nama'] ?? '');
    $email    = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirm  = $_POST['konfirm'] ?? '';
    $telepon  = sanitize($_POST['telepon'] ?? '');
    $alamat   = sanitize($_POST['alamat'] ?? '');

    if (empty($nama) || empty($email) || empty($password)) {
        $error = 'Nama, email, dan password wajib diisi.';
    } elseif ($password !== $konfirm) {
        $error = 'Konfirmasi password tidak cocok.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } else {
        $db   = getDB();
        $cek  = $db->prepare("SELECT id FROM users WHERE email = ?");
        $cek->bind_param("s", $email);
        $cek->execute();
        if ($cek->get_result()->num_rows > 0) {
            $error = 'Email sudah terdaftar.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (nama, email, password, telepon, alamat, role) VALUES (?, ?, ?, ?, ?, 'user')");
            $stmt->bind_param("sssss", $nama, $email, $hash, $telepon, $alamat);
            if ($stmt->execute()) {
                header('Location: login.php?registered=1');
                exit;
            } else {
                $error = 'Terjadi kesalahan, coba lagi.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar - Toko Online</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body style="background:#f0f0f0;">

<div class="login-wrapper register-wrapper"l>
    <div class="login-header">
        <h2>Daftar Akun Baru</h2>
    </div>
    <div class="login-body">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Nama Lengkap *</label>
                <input type="text" name="nama" class="form-control" value="<?= sanitize($_POST['nama'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" class="form-control" value="<?= sanitize($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Password * (min. 6 karakter)</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Konfirmasi Password *</label>
                <input type="password" name="konfirm" class="form-control" required>
            </div>
            <div class="form-group">
                <label>No. Telepon</label>
                <input type="text" name="telepon" class="form-control" value="<?= sanitize($_POST['telepon'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control"><?= sanitize($_POST['alamat'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn btn-danger btn-block">Daftar</button>
        </form>
        <p class="text-center mt-10" style="font-size:13px;">
            Sudah punya akun? <a href="login.php">Masuk</a>
        </p>
    </div>
</div>
</body>
</html>
