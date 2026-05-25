<?php
require_once '../config.php';
requireAdmin();
$db = getDB();
$page_title = 'Kelola Kategori';
$active_menu = 'kategori';
$msg = '';

// Hapus
if (isset($_GET['hapus'])) {
    $hid = (int)$_GET['hapus'];
    $cek = $db->query("SELECT COUNT(*) as c FROM produk WHERE kategori_id=$hid")->fetch_assoc()['c'];
    if ($cek > 0) {
        $msg = '<div class="alert alert-danger">Kategori tidak bisa dihapus karena masih memiliki produk.</div>';
    } else {
        $db->query("DELETE FROM kategori WHERE id=$hid");
        header('Location: kategori.php?msg=hapus'); exit;
    }
}

// Simpan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $edit_id   = (int)($_POST['edit_id'] ?? 0);
    $nama      = sanitize($_POST['nama'] ?? '');
    $deskripsi = sanitize($_POST['deskripsi'] ?? '');
    $slug      = strtolower(preg_replace('/[^a-z0-9]+/', '-', $nama));

    if (empty($nama)) {
        $msg = '<div class="alert alert-danger">Nama kategori wajib diisi.</div>';
    } else {
        if ($edit_id) {
            $st = $db->prepare("UPDATE kategori SET nama=?, slug=?, deskripsi=? WHERE id=?");
            $st->bind_param("sssi", $nama, $slug, $deskripsi, $edit_id);
            $st->execute();
            $msg = '<div class="alert alert-success">Kategori berhasil diupdate.</div>';
        } else {
            $st = $db->prepare("INSERT INTO kategori (nama, slug, deskripsi) VALUES (?,?,?)");
            $st->bind_param("sss", $nama, $slug, $deskripsi);
            $st->execute();
            $msg = '<div class="alert alert-success">Kategori berhasil ditambahkan.</div>';
        }
    }
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_data = $db->query("SELECT * FROM kategori WHERE id=" . (int)$_GET['edit'])->fetch_assoc();
}

$list = $db->query("SELECT k.*, (SELECT COUNT(*) FROM produk WHERE kategori_id=k.id) as jml_produk FROM kategori k ORDER BY k.nama")->fetch_all(MYSQLI_ASSOC);

if (isset($_GET['msg']) && $_GET['msg'] === 'hapus')
    $msg = '<div class="alert alert-success">Kategori berhasil dihapus.</div>';

require_once 'sidebar.php';
?>

<?= $msg ?>

<div class="box">
    <div class="box-title"><?= $edit_data ? 'Edit Kategori' : 'Tambah Kategori' ?></div>
    <div class="box-body">
        <form method="POST" style="max-width:500px;">
            <?php if ($edit_data): ?><input type="hidden" name="edit_id" value="<?= $edit_data['id'] ?>"><?php endif; ?>
            <div class="form-group">
                <label>Nama Kategori *</label>
                <input type="text" name="nama" class="form-control" value="<?= $edit_data['nama'] ?? '' ?>" required>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" class="form-control"><?= $edit_data['deskripsi'] ?? '' ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><?= $edit_data ? 'Update' : 'Simpan' ?></button>
            <?php if ($edit_data): ?><a href="kategori.php" class="btn">Batal</a><?php endif; ?>
        </form>
    </div>
</div>

<div class="box">
    <div class="box-title">Daftar Kategori</div>
    <div class="box-body" style="padding:0;">
        <table class="table">
            <thead><tr><th>No</th><th>Nama Kategori</th><th>Slug</th><th>Deskripsi</th><th>Jml Produk</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php if (empty($list)): ?>
                    <tr><td colspan="6" class="text-center">Belum ada kategori.</td></tr>
                <?php else: ?>
                <?php foreach ($list as $i => $k): ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td><?= $k['nama'] ?></td>
                    <td><code><?= $k['slug'] ?></code></td>
                    <td><?= $k['deskripsi'] ?: '-' ?></td>
                    <td><?= $k['jml_produk'] ?></td>
                    <td>
                        <a href="kategori.php?edit=<?= $k['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="kategori.php?hapus=<?= $k['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus kategori ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'footer_admin.php'; ?>
