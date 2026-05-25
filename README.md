# Toko Online - Panduan Instalasi

## Persyaratan
- PHP 7.4 atau lebih baru
- MySQL 5.7 atau lebih baru
- Web server (Apache/Nginx) dengan XAMPP/Laragon/Wamp

---

## Langkah Instalasi

### 1. Copy File
Ekstrak dan letakkan folder `toko-online` di dalam:
- XAMPP: `C:/xampp/htdocs/toko-online`
- Laragon: `C:/laragon/www/toko-online`

### 2. Buat Database
- Buka phpMyAdmin: `http://localhost/phpmyadmin`
- Buat database baru bernama `toko_online`
- Import file `database.sql` ke database tersebut

### 3. Sesuaikan Konfigurasi
Edit file `config.php` sesuai pengaturan lokal Anda:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // username MySQL Anda
define('DB_PASS', '');           // password MySQL Anda
define('DB_NAME', 'toko_online');
define('BASE_URL', 'http://localhost/toko-online/');
```

### 4. Buat Folder Upload
Pastikan folder ini ada dan writable:
```
toko-online/uploads/products/
```

### 5. Akses Website
Buka browser: `http://localhost/toko-online/`

---

## Akun Default

| Role  | Email              | Password |
|-------|--------------------|----------|
| Admin | admin@toko.com     | password |
| User  | budi@email.com     | password |

> **Catatan:** Ganti password setelah instalasi!
> Untuk membuat hash password baru: `password_hash('passwordbaru', PASSWORD_DEFAULT)`

---

## Fitur

### Admin
- Dashboard dengan statistik
- CRUD Produk (tambah, edit, hapus, upload foto)
- CRUD Kategori
- Kelola Pesanan (lihat detail, ubah status)
- Kelola Users (edit, hapus)

### User
- Register & Login
- Browse produk dengan filter kategori & pencarian
- Halaman detail produk
- Keranjang belanja (tambah, update jumlah, hapus)
- Checkout & buat pesanan
- Riwayat pesanan & batalkan pesanan
- Edit profil & ganti password

---

## Struktur File
```
toko-online/
├── config.php          # Konfigurasi DB & fungsi helper
├── database.sql        # Script SQL setup database
├── index.php           # Halaman utama (daftar produk)
├── login.php           # Halaman login
├── register.php        # Halaman registrasi
├── logout.php          # Proses logout
├── produk.php          # Detail produk
├── assets/
│   └── css/style.css   # Stylesheet utama
├── includes/
│   ├── header.php      # Header & navbar
│   └── footer.php      # Footer
├── admin/
│   ├── dashboard.php   # Dashboard admin
│   ├── produk.php      # CRUD produk
│   ├── kategori.php    # CRUD kategori
│   ├── pesanan.php     # Kelola pesanan
│   ├── users.php       # Kelola users
│   ├── sidebar.php     # Sidebar admin
│   └── footer_admin.php
├── user/
│   ├── keranjang.php   # Keranjang & checkout
│   ├── pesanan.php     # Riwayat pesanan user
│   └── profil.php      # Edit profil user
└── uploads/
    └── products/       # Folder upload foto produk
```
