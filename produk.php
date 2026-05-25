<?php
require_once 'config.php';

$page_title = 'Detail Produk';

$db = getDB();

$id = (int)($_GET['id'] ?? 0);

if (!$id) {
    header("Location: index.php");
    exit;
}

// Ambil data produk
$stmt = $db->prepare("SELECT p.*, k.nama as kategori_nama
                      FROM produk p
                      LEFT JOIN kategori k ON p.kategori_id = k.id
                      WHERE p.id = ? AND p.status='aktif'");

$stmt->bind_param("i", $id);
$stmt->execute();

$produk = $stmt->get_result()->fetch_assoc();

if (!$produk) {
    header("Location: index.php");
    exit;
}

// Produk terkait
$related_stmt = $db->prepare("SELECT * FROM produk
                              WHERE kategori_id = ?
                              AND id != ?
                              AND status='aktif'
                              LIMIT 4");

$related_stmt->bind_param("ii", $produk['kategori_id'], $produk['id']);
$related_stmt->execute();

$related_products = $related_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

require_once 'includes/header.php';
?>

<div class="container main-content">

    <div class="box">
        <div class="box-body">

            <div style="display:flex; gap:30px; flex-wrap:wrap;">

                <!-- Foto Produk -->
                <div style="flex:1; min-width:300px;">

                    <?php if ($produk['foto'] && file_exists(UPLOAD_DIR . $produk['foto'])): ?>
                        <img 
                            src="<?= BASE_URL ?>uploads/products/<?= $produk['foto'] ?>" 
                            alt="<?= $produk['nama'] ?>"
                            style="width:100%; border-radius:10px;"
                        >
                    <?php else: ?>
                        <div style="
                            height:350px;
                            background:#eee;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            color:#999;
                            border-radius:10px;
                        ">
                            Tidak ada foto
                        </div>
                    <?php endif; ?>

                </div>

                <!-- Detail Produk -->
                <div style="flex:1; min-width:300px;">

                    <div style="
                        font-size:13px;
                        color:#777;
                        margin-bottom:8px;
                    ">
                        <?= $produk['kategori_nama'] ?? 'Tanpa kategori' ?>
                    </div>

                    <h1 style="margin-bottom:15px;">
                        <?= $produk['nama'] ?>
                    </h1>

                    <div style="
                        font-size:28px;
                        font-weight:bold;
                        color:#e63946;
                        margin-bottom:15px;
                    ">
                        <?= formatRupiah($produk['harga']) ?>
                    </div>

                    <div style="margin-bottom:15px;">
                        <strong>Stok:</strong>

                        <?php if ($produk['stok'] > 0): ?>
                            <span style="color:green;">
                                <?= $produk['stok'] ?> tersedia
                            </span>
                        <?php else: ?>
                            <span style="color:red;">
                                Stok habis
                            </span>
                        <?php endif; ?>
                    </div>

                    <div style="
                        line-height:1.8;
                        color:#444;
                        margin-bottom:20px;
                    ">
                        <?= nl2br($produk['deskripsi']) ?>
                    </div>

                    <?php if (isLoggedIn() && !isAdmin()): ?>

                        <?php if ($produk['stok'] > 0): ?>
                            <a 
                                href="user/keranjang.php?aksi=tambah&id=<?= $produk['id'] ?>" 
                                class="btn btn-danger"
                            >
                                + Tambah ke Keranjang
                            </a>
                        <?php else: ?>
                            <button class="btn" disabled>
                                Stok Habis
                            </button>
                        <?php endif; ?>

                    <?php else: ?>

                        <a href="login.php" class="btn btn-primary">
                            Login untuk membeli
                        </a>

                    <?php endif; ?>

                </div>

            </div>

        </div>
    </div>

    <!-- Produk Terkait -->
    <?php if (!empty($related_products)): ?>

    <div class="box">
        <div class="box-title">Produk Terkait</div>

        <div class="box-body">

            <div class="product-grid">

                <?php foreach ($related_products as $p): ?>

                <div class="product-card">

                    <a href="produk.php?id=<?= $p['id'] ?>">

                        <?php if ($p['foto'] && file_exists(UPLOAD_DIR . $p['foto'])): ?>

                            <img 
                                src="<?= BASE_URL ?>uploads/products/<?= $p['foto'] ?>" 
                                alt="<?= $p['nama'] ?>"
                            >

                        <?php else: ?>

                            <div style="
                                height:160px;
                                background:#eee;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                color:#aaa;
                            ">
                                Tidak ada foto
                            </div>

                        <?php endif; ?>

                    </a>

                    <div class="product-card-body">

                        <div class="product-card-title">
                            <a href="produk.php?id=<?= $p['id'] ?>">
                                <?= $p['nama'] ?>
                            </a>
                        </div>

                        <div class="product-card-price">
                            <?= formatRupiah($p['harga']) ?>
                        </div>

                        <a 
                            href="produk.php?id=<?= $p['id'] ?>" 
                            class="btn btn-sm btn-block"
                        >
                            Lihat Detail
                        </a>

                    </div>

                </div>

                <?php endforeach; ?>

            </div>

        </div>
    </div>

    <?php endif; ?>

</div>

<?php require_once 'includes/footer.php'; ?>