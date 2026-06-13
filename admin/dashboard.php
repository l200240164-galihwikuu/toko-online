<?php
require_once '../config.php';
requireAdmin();
$page_title = 'Dashboard Admin';
$db = getDB();

$total_produk   = $db->query("SELECT COUNT(*) as c FROM produk")->fetch_assoc()['c'];
$total_user     = $db->query("SELECT COUNT(*) as c FROM users WHERE role='user'")->fetch_assoc()['c'];
$total_pesanan  = $db->query("SELECT COUNT(*) as c FROM pesanan")->fetch_assoc()['c'];
$total_pendapatan = $db->query("SELECT SUM(total) as s FROM pesanan WHERE status='selesai'")->fetch_assoc()['s'] ?? 0;

$pesanan_terbaru = $db->query("SELECT p.*, u.nama as user_nama FROM pesanan p LEFT JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC LIMIT 8")->fetch_all(MYSQLI_ASSOC);
$produk_stok_rendah = $db->query("SELECT * FROM produk WHERE stok <= 5 AND status='aktif' ORDER BY stok ASC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

$active_menu = 'dashboard';
require_once 'sidebar.php';
?>

<div class="stat-grid">
    <div class="stat-box">
        <div class="stat-num"><?= $total_produk ?></div>
        <div class="stat-label">Total Produk</div>
    </div>

    <div class="stat-box">
        <div class="stat-num"><?= $total_user ?></div>
        <div class="stat-label">Total User</div>
    </div>

    <div class="stat-box">
        <div class="stat-num"><?= $total_pesanan ?></div>
        <div class="stat-label">Total Pesanan</div>
    </div>
</div>

<div class="stat-grid-bottom">
    <div class="stat-box">
        <div class="stat-num" style="font-size:16px;">
            <?= formatRupiah($total_pendapatan) ?>
        </div>
        <div class="stat-label">Pendapatan (Selesai)</div>
    </div>
</div>

<div class="dahsboard-row">
    <div class="dashboard-left">
        <div class="box">
            <div class="box-title">Pesanan Terbaru</div>
            <div class="box-body" style="padding:0;"><div class="table-responsive"><table class="table">
                    <thead>
                        <tr><th>Kode</th><th>Pembeli</th><th>Total</th><th>Status</th><th>Tanggal</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pesanan_terbaru)): ?>
                            <tr><td colspan="5" class="text-center">Belum ada pesanan</td></tr>
                        <?php else: ?>
                        <?php foreach ($pesanan_terbaru as $p): ?>
                        <tr>
                            <td><a href="pesanan.php?id=<?= $p['id'] ?>"><?= $p['kode_pesanan'] ?></a></td>
                            <td><?= $p['user_nama'] ?></td>
                            <td><?= formatRupiah($p['total']) ?></td>
                            <td><span class="status status-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
                            <td><?= date('d/m/Y', strtotime($p['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table></div>
            </div>
        </div>
    </div>
    <div class="dashboard-right">
        <div class="box">
            <div class="box-title" style="background:#dd9900; color:#fff;">⚠ Stok Hampir Habis</div>
            <div class="box-body" style="padding:0;"><div class="table-responsive"><table class="table">
                    <thead><tr><th>Produk</th><th>Stok</th></tr></thead>
                    <tbody>
                        <?php if (empty($produk_stok_rendah)): ?>
                            <tr><td colspan="2" class="text-center">Stok aman</td></tr>
                        <?php else: ?>
                        <?php foreach ($produk_stok_rendah as $p): ?>
                            <tr>
                                <td><?= $p['nama'] ?></td>
                                <td class="text-red"><strong><?= $p['stok'] ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table></div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer_admin.php'; ?>