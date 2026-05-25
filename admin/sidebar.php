<?php require_once '../includes/header.php'; ?>
<div class="admin-layout">
<div class="sidebar">
    <div class="sidebar-box">
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
