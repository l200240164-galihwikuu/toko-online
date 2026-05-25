<?php
require_once '../config.php';
requireAdmin();
$db = getDB();
$page_title = 'Kelola Users';
$active_menu = 'users';
$msg = '';

// Hapus
if (isset($_GET['hapus'])) {
    $hid = (int)$_GET['hapus'];
    if ($hid == $_SESSION['user_id']) {
        $msg = '<div class="alert alert-danger">Tidak bisa menghapus akun sendiri.</div>';
    } else {
        $db->query("DELETE FROM users WHERE id=$hid AND role='user'");
        header('Location: users.php?msg=hapus'); exit;
    }
}

// Simpan edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid   = (int)$_POST['user_id'];
    $nama  = sanitize($_POST['nama'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $role  = in_array($_POST['role'], ['admin','user']) ? $_POST['role'] : 'user';
    $telepon = sanitize($_POST['telepon'] ?? '');
    $alamat  = sanitize($_POST['alamat'] ?? '');

    if (!empty($_POST['password'])) {
        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $st = $db->prepare("UPDATE users SET nama=?, email=?, role=?, telepon=?, alamat=?, password=? WHERE id=?");
        $st->bind_param("ssssssi", $nama, $email, $role, $telepon, $alamat, $hash, $uid);
    } else {
        $st = $db->prepare("UPDATE users SET nama=?, email=?, role=?, telepon=?, alamat=? WHERE id=?");
        $st->bind_param("sssssi", $nama, $email, $role, $telepon, $alamat, $uid);
    }
    $st->execute();
    $msg = '<div class="alert alert-success">Data user berhasil diupdate.</div>';
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_data = $db->query("SELECT * FROM users WHERE id=" . (int)$_GET['edit'])->fetch_assoc();
}

$list = $db->query("SELECT u.*, (SELECT COUNT(*) FROM pesanan WHERE user_id=u.id) as jml_pesanan FROM users u ORDER BY u.created_at DESC")->fetch_all(MYSQLI_ASSOC);

if (isset($_GET['msg']) && $_GET['msg'] === 'hapus')
    $msg = '<div class="alert alert-success">User berhasil dihapus.</div>';

require_once 'sidebar.php';
?>

<?= $msg ?>

<?php if ($edit_data): ?>
<div class="box">
    <div class="box-title">Edit User: <?= $edit_data['nama'] ?></div>
    <div class="box-body">
        <form method="POST" style="max-width:500px;">
            <input type="hidden" name="user_id" value="<?= $edit_data['id'] ?>">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" value="<?= $edit_data['nama'] ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?= $edit_data['email'] ?>" required>
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role" class="form-control">
                    <option value="user" <?= $edit_data['role']==='user'?'selected':'' ?>>User</option>
                    <option value="admin" <?= $edit_data['role']==='admin'?'selected':'' ?>>Admin</option>
                </select>
            </div>
            <div class="form-group">
                <label>No. Telepon</label>
                <input type="text" name="telepon" class="form-control" value="<?= $edit_data['telepon'] ?>">
            </div>
            <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control"><?= $edit_data['alamat'] ?></textarea>
            </div>
            <div class="form-group">
                <label>Password Baru (kosongkan jika tidak diubah)</label>
                <input type="password" name="password" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Update User</button>
            <a href="users.php" class="btn">Batal</a>
        </form>
    </div>
</div>
<?php endif; ?>

<div class="box">
    <div class="box-title">Daftar Users</div>
    <div class="box-body" style="padding:0;">
        <table class="table">
            <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Telepon</th><th>Pesanan</th><th>Terdaftar</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php foreach ($list as $u): ?>
                <tr>
                    <td><?= $u['nama'] ?></td>
                    <td><?= $u['email'] ?></td>
                    <td><span class="status <?= $u['role']==='admin'?'status-dikonfirmasi':'status-aktif' ?>"><?= ucfirst($u['role']) ?></span></td>
                    <td><?= $u['telepon'] ?: '-' ?></td>
                    <td><?= $u['jml_pesanan'] ?></td>
                    <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                    <td>
                        <a href="users.php?edit=<?= $u['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <?php if ($u['id'] != $_SESSION['user_id'] && $u['role'] === 'user'): ?>
                            <a href="users.php?hapus=<?= $u['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus user ini?')">Hapus</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'footer_admin.php'; ?>
