<?php
require_once 'config.php';
$db = getDB();

$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare("SELECT p.*, k.nama as kategori_nama FROM produk p LEFT JOIN kategori k ON p.kategori_id = k.id WHERE p.id = ? AND p.status = 'aktif'");
$stmt->bind_param("i", $id);
$stmt->execute();
$produk = $stmt->get_result()->fetch_assoc();

if (!$produk) {
    header('Location: index.php'); exit;
}

$page_title = $produk['nama'];
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn() && !isAdmin()) {
    $jumlah = max(1, (int)($_POST['jumlah'] ?? 1));
    if ($jumlah > $produk['stok']) {
        $msg = '<div class="alert alert-danger">Jumlah melebihi stok tersedia.</div>';
    } else {
        $uid = $_SESSION['user_id'];
        $stmt2 = $db->prepare("INSERT INTO keranjang (user_id, produk_id, jumlah) VALUES (?, ?, ?)
                               ON DUPLICATE KEY UPDATE jumlah = jumlah + VALUES(jumlah)");
        $stmt2->bind_param("iii", $uid, $id, $jumlah);
        $stmt2->execute();
        $msg = '<div class="alert alert-success">Produk berhasil ditambahkan ke keranjang!</div>';
    }
}

require_once 'includes/header.php';
?>
<div class="container main-content">
    <div class="breadcrumb">
        <a href="index.php">Beranda</a> <span>&raquo;</span> <?= $produk['nama'] ?>
    </div>

    <?= $msg ?>

    <div class="box">
        <div class="box-body">
            <div style="display:flex; gap:20px; flex-wrap:wrap;">
                <!-- Foto -->
                <div style="width:280px; flex-shrink:0;">
                    <?php if ($produk['foto'] && file_exists(UPLOAD_DIR . $produk['foto'])): ?>
                        <img src="<?= BASE_URL ?>uploads/products/<?= $produk['foto'] ?>" style="width:100%; border:1px solid #ccc;">
                    <?php else: ?>
                        <div style="width:100%; height:260px; background:#eee; border:1px solid #ccc; display:flex; align-items:center; justify-content:center; color:#aaa;">Tidak ada foto</div>
                    <?php endif; ?>
                </div>
                <!-- Detail -->
                <div style="flex:1;">
                    <h2 style="font-size:18px; margin-bottom:8px;"><?= $produk['nama'] ?></h2>
                    <p style="color:#888; font-size:13px; margin-bottom:8px;">Kategori: <?= $produk['kategori_nama'] ?? '-' ?></p>
                    <p style="font-size:22px; color:#cc0000; font-weight:bold; margin-bottom:10px;"><?= formatRupiah($produk['harga']) ?></p>
                    <p style="margin-bottom:10px;">Stok: <strong><?= $produk['stok'] ?> pcs</strong></p>
                    <p style="margin-bottom:15px; color:#555; line-height:1.6;"><?= nl2br($produk['deskripsi']) ?></p>

                    <?php if (isLoggedIn() && !isAdmin()): ?>
                        <?php if ($produk['stok'] > 0): ?>
                        <form method="POST" style="display:flex; gap:8px; align-items:center;">
                            <input type="number" name="jumlah" value="1" min="1" max="<?= $produk['stok'] ?>" style="width:70px; padding:6px; border:1px solid #ccc; border-radius:3px;">
                            <button type="submit" class="btn btn-danger">+ Tambah ke Keranjang</button>
                        </form>
                        <?php else: ?>
                            <p class="text-red"><strong>Stok habis</strong></p>
                        <?php endif; ?>
                    <?php elseif (!isLoggedIn()): ?>
                        <a href="login.php" class="btn btn-danger">Login untuk Beli</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
