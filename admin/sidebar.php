<?php require_once '../includes/header.php'; ?>
<div class="admin-layout">

<!-- Sidebar -->
<div class="sidebar">
    <!-- Toggle button (tampil di mobile saja via CSS) -->
    <button class="sidebar-toggle collapsed" id="sidebarToggle" type="button">
        ⚙ Panel Admin
    </button>
    <div class="sidebar-box" id="sidebarBox">
        <div class="sidebar-title">⚙ Panel Admin</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" <?= ($active_menu??'')==='dashboard'?'class="active"':'' ?>>📊 Dashboard</a></li>
            <li><a href="produk.php" <?= ($active_menu??'')==='produk'?'class="active"':'' ?>>📦 Produk</a></li>
            <li><a href="kategori.php" <?= ($active_menu??'')==='kategori'?'class="active"':'' ?>>📁 Kategori</a></li>
            <li><a href="pesanan.php" <?= ($active_menu??'')==='pesanan'?'class="active"':'' ?>>🧾 Pesanan</a></li>
            <li><a href="users.php" <?= ($active_menu??'')==='users'?'class="active"':'' ?>>👥 Users</a></li>
        </ul>
    </div>
    <div style="margin-top:10px; font-size:12px; color:#888; text-align:center;">
        Login sebagai:<br><strong><?= $_SESSION['nama'] ?></strong>
    </div>
</div>

<div class="admin-main">

<script>
// Sidebar toggle untuk mobile
(function(){
    var btn = document.getElementById('sidebarToggle');
    var box = document.getElementById('sidebarBox');
    if (!btn || !box) return;
    btn.addEventListener('click', function(){
        box.classList.toggle('is-open');
        btn.classList.toggle('collapsed');
    });
})();
</script>