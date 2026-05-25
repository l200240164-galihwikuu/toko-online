-- ============================================
-- DATABASE SETUP - Toko Online
-- Jalankan script ini di phpMyAdmin atau MySQL
-- ============================================

CREATE DATABASE IF NOT EXISTS toko_online CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE toko_online;

-- Tabel Users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    telepon VARCHAR(20),
    alamat TEXT,
    foto VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Kategori
CREATE TABLE IF NOT EXISTS kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Produk
CREATE TABLE IF NOT EXISTS produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kategori_id INT,
    nama VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(15,2) NOT NULL,
    stok INT DEFAULT 0,
    foto VARCHAR(255),
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE SET NULL
);

-- Tabel Keranjang
CREATE TABLE IF NOT EXISTS keranjang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    produk_id INT NOT NULL,
    jumlah INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart (user_id, produk_id)
);

-- Tabel Pesanan
CREATE TABLE IF NOT EXISTS pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    kode_pesanan VARCHAR(50) UNIQUE NOT NULL,
    total DECIMAL(15,2) NOT NULL,
    status ENUM('pending', 'dikonfirmasi', 'diproses', 'dikirim', 'selesai', 'dibatalkan') DEFAULT 'pending',
    alamat_pengiriman TEXT NOT NULL,
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Tabel Detail Pesanan
CREATE TABLE IF NOT EXISTS detail_pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pesanan_id INT NOT NULL,
    produk_id INT NOT NULL,
    nama_produk VARCHAR(200) NOT NULL,
    harga DECIMAL(15,2) NOT NULL,
    jumlah INT NOT NULL,
    subtotal DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE,
    FOREIGN KEY (produk_id) REFERENCES produk(id)
);

-- ============================================
-- DATA AWAL (SEED DATA)
-- ============================================

-- Admin default (password: admin123)
INSERT INTO users (nama, email, password, role) VALUES 
('Administrator', 'admin@toko.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- User default (password: user123)
INSERT INTO users (nama, email, password, role, telepon, alamat) VALUES 
('Budi Santoso', 'budi@email.com', '$2y$10$TKh8H1.PFbuSpgvguEe3DO/X4rlD4s0iRenZBEjoSDZFelNBiVGvS', 'user', '08123456789', 'Jl. Merdeka No. 10, Semarang');

-- Kategori
INSERT INTO kategori (nama, slug, deskripsi) VALUES 
('Elektronik', 'elektronik', 'Produk elektronik dan gadget'),
('Fashion', 'fashion', 'Pakaian dan aksesoris fashion'),
('Makanan & Minuman', 'makanan-minuman', 'Produk makanan dan minuman'),
('Olahraga', 'olahraga', 'Peralatan dan perlengkapan olahraga'),
('Rumah Tangga', 'rumah-tangga', 'Peralatan rumah tangga');

-- Produk contoh
INSERT INTO produk (kategori_id, nama, slug, deskripsi, harga, stok, status) VALUES
(1, 'Smartphone Samsung Galaxy A54', 'smartphone-samsung-galaxy-a54', 'Smartphone terbaru dengan kamera 50MP, RAM 8GB, Storage 256GB', 4500000, 25, 'aktif'),
(1, 'Laptop ASUS VivoBook 15', 'laptop-asus-vivobook-15', 'Laptop ringan dengan prosesor Intel Core i5, RAM 16GB, SSD 512GB', 8750000, 10, 'aktif'),
(1, 'TWS Earbuds Bluetooth', 'tws-earbuds-bluetooth', 'Earbuds wireless dengan noise cancelling, baterai tahan 8 jam', 350000, 50, 'aktif'),
(2, 'Kemeja Batik Pria Premium', 'kemeja-batik-pria-premium', 'Kemeja batik motif Parang, bahan katun premium, tersedia berbagai ukuran', 185000, 100, 'aktif'),
(2, 'Dress Casual Wanita', 'dress-casual-wanita', 'Dress casual modern dengan bahan jersey nyaman, cocok untuk berbagai acara', 220000, 75, 'aktif'),
(3, 'Kopi Arabika Gayo 500gr', 'kopi-arabika-gayo-500gr', 'Kopi arabika asli Gayo, Aceh. Biji kopi pilihan, sangat harum', 95000, 200, 'aktif'),
(3, 'Madu Murni Hutan 350ml', 'madu-murni-hutan-350ml', 'Madu hutan murni tanpa campuran, kaya antioksidan dan mineral', 135000, 80, 'aktif'),
(4, 'Sepatu Lari Nike Air Max', 'sepatu-lari-nike-air-max', 'Sepatu lari ringan dengan teknologi Air Max untuk kenyamanan maksimal', 950000, 30, 'aktif'),
(5, 'Rice Cooker Digital 1.8L', 'rice-cooker-digital-1-8l', 'Rice cooker dengan teknologi digital, kapasitas 1.8 liter, 8 menu masak', 450000, 40, 'aktif'),
(5, 'Blender Portable Juicer', 'blender-portable-juicer', 'Blender portable USB rechargeable, mudah dibawa kemana saja', 175000, 60, 'aktif');

-- ============================================
-- PASSWORD NOTES:
-- admin@toko.com  -> password: password
-- budi@email.com  -> password: password
-- Ganti dengan hash baru menggunakan: password_hash('yourpassword', PASSWORD_DEFAULT)
-- ============================================
