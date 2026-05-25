<?php
require_once '../config.php';
requireLogin();
if (isAdmin()) { header('Location: ' . BASE_URL . 'admin/dashboard.php'); exit; }

$db  = getDB();
$uid = $_SESSION['user_id'];
$page_title = 'Profil Saya';
$msg = '';

$user = $db->query("SELECT * FROM users WHERE id=$uid")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama    = sanitize($_POST['nama'] ?? '');
    $telepon = sanitize($_POST['telepon'] ?? '');
    $alamat  = sanitize($_POST['alamat'] ?? '');

    if (empty($nama)) {
        $msg = '<div class="alert alert-danger">Nama wajib diisi.</div>';
    } else {
        if (!empty($_POST['password_baru'])) {
            if (!password_verify($_POST['password_lama'], $user['password'])) {
                $msg = '<div class="alert alert-danger">Password lama salah.</div>';
            } elseif (strlen($_POST['password_baru']) < 6) {
                $msg = '<div class="alert alert-danger">Password baru minimal 6 karakter.</div>';
            } else {
                $hash = password_hash($_POST['password_baru'], PASSWORD_DEFAULT);
                $st = $db->prepare("UPDATE users SET nama=?, telepon=?, alamat=?, password=? WHERE id=?");
                $st->bind_param("ssssi", $nama, $telepon, $alamat, $hash, $uid);
                $st->execute();
                $_SESSION['nama'] = $nama;
                $msg = '<div class="alert alert-success">Profil dan password berhasil diupdate.</div>';
            }
        } else {
            $st = $db->prepare("UPDATE users SET nama=?, telepon=?, alamat=? WHERE id=?");
            $st->bind_param("sssi", $nama, $telepon, $alamat, $uid);
            $st->execute();
            $_SESSION['nama'] = $nama;
            $msg = '<div class="alert alert-success">Profil berhasil diupdate.</div>';
        }
        $user = $db->query("SELECT * FROM users WHERE id=$uid")->fetch_assoc();
    }
}

require_once '../includes/header.php';
?>

<div class="container main-content">
    <div class="breadcrumb"><a href="<?= BASE_URL ?>index.php">Beranda</a> <span>&raquo;</span> Profil Saya</div>

    <?= $msg ?>

    <div style="display:flex; gap:15px; flex-wrap:wrap;">
        <div style="flex:1; min-width:280px;">
            <div class="box">
                <div class="box-title">Data Profil</div>
                <div class="box-body">
                    <form method="POST">
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" value="<?= $user['nama'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email (tidak bisa diubah)</label>
                            <input type="text" class="form-control" value="<?= $user['email'] ?>" disabled style="background:#f5f5f5;">
                        </div>
                        <div class="form-group">
                            <label>No. Telepon</label>
                            <input type="text" name="telepon" class="form-control" value="<?= $user['telepon'] ?>">
                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea name="alamat" class="form-control"><?= $user['alamat'] ?></textarea>
                        </div>
                        <hr style="margin:12px 0; border:none; border-top:1px solid #ddd;">
                        <p style="font-weight:bold; margin-bottom:8px; font-size:13px;">Ubah Password (opsional)</p>
                        <div class="form-group">
                            <label>Password Lama</label>
                            <input type="password" name="password_lama" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Password Baru</label>
                            <input type="password" name="password_baru" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
        <div style="flex:1; min-width:220px;">
            <div class="box">
                <div class="box-title">Ringkasan Akun</div>
                <div class="box-body">
                    <?php
                    $jml_pesanan = $db->query("SELECT COUNT(*) as c FROM pesanan WHERE user_id=$uid")->fetch_assoc()['c'];
                    $jml_selesai = $db->query("SELECT COUNT(*) as c FROM pesanan WHERE user_id=$uid AND status='selesai'")->fetch_assoc()['c'];
                    ?>
                    <p>Total Pesanan: <strong><?= $jml_pesanan ?></strong></p>
                    <p>Pesanan Selesai: <strong><?= $jml_selesai ?></strong></p>
                    <p>Bergabung: <strong><?= date('d/m/Y', strtotime($user['created_at'])) ?></strong></p>
                    <hr style="margin:10px 0; border:none; border-top:1px solid #ddd;">
                    <a href="pesanan.php" class="btn btn-sm btn-block">Lihat Pesanan Saya</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
