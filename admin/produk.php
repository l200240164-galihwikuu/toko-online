<?php
require_once '../config.php';
requireAdmin();
$db = getDB();
$page_title = 'Kelola Produk';
$active_menu = 'produk';
$msg = '';

// Hapus
if (isset($_GET['hapus'])) {
    $hid = (int)$_GET['hapus'];
    $row = $db->query("SELECT foto FROM produk WHERE id=$hid")->fetch_assoc();
    if ($row && $row['foto'] && file_exists(UPLOAD_DIR . $row['foto'])) unlink(UPLOAD_DIR . $row['foto']);
    $db->query("DELETE FROM produk WHERE id=$hid");
    header('Location: produk.php?msg=hapus'); exit;
}

// Toggle status
if (isset($_GET['toggle'])) {
    $tid = (int)$_GET['toggle'];
    $db->query("UPDATE produk SET status = IF(status='aktif','nonaktif','aktif') WHERE id=$tid");
    header('Location: produk.php'); exit;
}

// Simpan (tambah/edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $edit_id     = (int)($_POST['edit_id'] ?? 0);
    $nama        = sanitize($_POST['nama'] ?? '');
    $kategori_id = (int)($_POST['kategori_id'] ?? 0);
    $harga       = (float)str_replace(['.', ','], ['', '.'], $_POST['harga'] ?? 0);
    $stok        = (int)($_POST['stok'] ?? 0);
    $deskripsi   = sanitize($_POST['deskripsi'] ?? '');
    $status      = $_POST['status'] === 'aktif' ? 'aktif' : 'nonaktif';
    $slug        = strtolower(preg_replace('/[^a-z0-9]+/', '-', $nama)) . '-' . time();

    // Upload foto
    $foto_name = null;
    if (!empty($_FILES['foto']['name'])) {
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
            $msg = '<div class="alert alert-danger">Format foto tidak valid.</div>';
        } else {
            $foto_name = uniqid() . '.' . $ext;
            if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
            move_uploaded_file($_FILES['foto']['tmp_name'], UPLOAD_DIR . $foto_name);
        }
    }

    if (!$msg) {
        if ($edit_id) {
            $old = $db->query("SELECT foto FROM produk WHERE id=$edit_id")->fetch_assoc();
            $foto_sql = $foto_name ? ", foto='$foto_name'" : '';
            if ($foto_name && $old['foto'] && file_exists(UPLOAD_DIR . $old['foto'])) unlink(UPLOAD_DIR . $old['foto']);
            $stmt = $db->prepare("UPDATE produk SET nama=?, kategori_id=?, harga=?, stok=?, deskripsi=?, status=?$foto_sql WHERE id=?");
            $stmt->bind_param("sidssssi"[0] . ($kategori_id ? 'i' : 's') . "dsssi", $nama, $kategori_id, $harga, $stok, $deskripsi, $status, $edit_id);
            // Rebuild binding properly
            if ($foto_name) {
                $st = $db->prepare("UPDATE produk SET nama=?, kategori_id=?, harga=?, stok=?, deskripsi=?, status=?, foto=? WHERE id=?");
                $st->bind_param("siidsssi", $nama, $kategori_id, $harga, $stok, $deskripsi, $status, $foto_name, $edit_id);
            } else {
                $st = $db->prepare("UPDATE produk SET nama=?, kategori_id=?, harga=?, stok=?, deskripsi=?, status=? WHERE id=?");
                $st->bind_param("siidssi", $nama, $kategori_id, $harga, $stok, $deskripsi, $status, $edit_id);
            }
            $st->execute();
            $msg = '<div class="alert alert-success">Produk berhasil diupdate.</div>';
        } else {
            $st = $db->prepare("INSERT INTO produk (nama, slug, kategori_id, harga, stok, deskripsi, status, foto) VALUES (?,?,?,?,?,?,?,?)");
            $st->bind_param("ssiidsss", $nama, $slug, $kategori_id, $harga, $stok, $deskripsi, $status, $foto_name);
            $st->execute();
            $msg = '<div class="alert alert-success">Produk berhasil ditambahkan.</div>';
        }
    }
}

// Edit mode
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_data = $db->query("SELECT * FROM produk WHERE id=" . (int)$_GET['edit'])->fetch_assoc();
}

// Daftar produk
$search = sanitize($_GET['q'] ?? '');
$where = $search ? "WHERE nama LIKE '%$search%'" : '';
$produk_list = $db->query("SELECT p.*, k.nama as kat FROM produk p LEFT JOIN kategori k ON p.kategori_id=k.id $where ORDER BY p.created_at DESC")->fetch_all(MYSQLI_ASSOC);
$kategori_list = $db->query("SELECT * FROM kategori ORDER BY nama")->fetch_all(MYSQLI_ASSOC);

if (isset($_GET['msg']) && $_GET['msg'] === 'hapus')
    $msg = '<div class="alert alert-success">Produk berhasil dihapus.</div>';

require_once 'sidebar.php';
?>

<?= $msg ?>

<div class="box">
    <div class="box-title"><?= $edit_data ? 'Edit Produk' : 'Tambah Produk Baru' ?></div>
    <div class="box-body">
        <form method="POST" enctype="multipart/form-data">
            <?php if ($edit_data): ?><input type="hidden" name="edit_id" value="<?= $edit_data['id'] ?>"><?php endif; ?>
            <div style="display:flex; gap:15px; flex-wrap:wrap;">
                <div style="flex:1; min-width:250px;">
                    <div class="form-group">
                        <label>Nama Produk *</label>
                        <input type="text" name="nama" class="form-control" value="<?= $edit_data['nama'] ?? '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="kategori_id" class="form-control">
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($kategori_list as $k): ?>
                                <option value="<?= $k['id'] ?>" <?= ($edit_data['kategori_id'] ?? '') == $k['id'] ? 'selected' : '' ?>><?= $k['nama'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="display:flex; gap:10px;">
                        <div class="form-group" style="flex:1;">
                            <label>Harga (Rp) *</label>
                            <input type="number" name="harga" class="form-control" value="<?= $edit_data['harga'] ?? '' ?>" required>
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label>Stok</label>
                            <input type="number" name="stok" class="form-control" value="<?= $edit_data['stok'] ?? 0 ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="aktif" <?= ($edit_data['status'] ?? 'aktif') === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="nonaktif" <?= ($edit_data['status'] ?? '') === 'nonaktif' ? 'selected' : '' ?>>Non-aktif</option>
                        </select>
                    </div>
                </div>
                <div style="flex:1; min-width:250px;">
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" style="min-height:100px;"><?= $edit_data['deskripsi'] ?? '' ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Foto Produk</label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
                        <?php if (!empty($edit_data['foto']) && file_exists(UPLOAD_DIR . $edit_data['foto'])): ?>
                            <div style="margin-top:6px;"><img src="<?= BASE_URL ?>uploads/products/<?= $edit_data['foto'] ?>" class="img-product"> <small>Foto saat ini</small></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><?= $edit_data ? 'Update Produk' : 'Simpan Produk' ?></button>
            <?php if ($edit_data): ?> <a href="produk.php" class="btn">Batal</a><?php endif; ?>
        </form>
    </div>
</div>

<!-- Daftar Produk -->
<div class="box">
    <div class="box-title">Daftar Produk</div>
    <div class="box-body" style="padding-bottom:0;">
        <form method="GET" class="search-bar">
            <input type="text" name="q" placeholder="Cari produk..." value="<?= $search ?>">
            <button type="submit" class="btn btn-primary">Cari</button>
            <?php if ($search): ?><a href="produk.php" class="btn">Reset</a><?php endif; ?>
        </form>
    </div>
    <div class="box-body" style="padding:0;"><div class="table-responsive"><table class="table">
            <thead>
                <tr><th>Foto</th><th>Nama Produk</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                <?php if (empty($produk_list)): ?>
                    <tr><td colspan="7" class="text-center">Belum ada produk.</td></tr>
                <?php else: ?>
                <?php foreach ($produk_list as $p): ?>
                <tr>
                    <td>
                        <?php if ($p['foto'] && file_exists(UPLOAD_DIR . $p['foto'])): ?>
                            <img src="<?= BASE_URL ?>uploads/products/<?= $p['foto'] ?>" class="img-product">
                        <?php else: ?>
                            <div class="no-img">No img</div>
                        <?php endif; ?>
                    </td>
                    <td><?= $p['nama'] ?></td>
                    <td><?= $p['kat'] ?? '-' ?></td>
                    <td><?= formatRupiah($p['harga']) ?></td>
                    <td><?= $p['stok'] ?></td>
                    <td><span class="status status-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
                    <td>
                        <a href="produk.php?edit=<?= $p['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="produk.php?toggle=<?= $p['id'] ?>" class="btn btn-sm" onclick="return confirm('Toggle status produk ini?')">Toggle</a>
                        <a href="produk.php?hapus=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus produk ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table></div>
    </div>
</div>

<?php require_once 'footer_admin.php'; ?>