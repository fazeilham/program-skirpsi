# Biyai Finance Tracker - PHP Native Edition

Aplikasi Pencatatan Keuangan berbasis PHP Native tanpa menggunakan database. Data disimpan dalam format JSON.

## 📋 Fitur

✅ **Autentikasi Login** - Login dengan username/email dan password
✅ **Tambah Transaksi** - Menambahkan transaksi masuk atau keluar
✅ **Lihat Data Transaksi** - Menampilkan semua transaksi yang tersimpan
✅ **Edit Transaksi** - Mengubah data transaksi yang sudah ada
✅ **Hapus Transaksi** - Menghapus transaksi yang tidak diperlukan
✅ **Responsive Design** - Bisa diakses dari berbagai ukuran layar
✅ **Tanpa Database** - Data disimpan dalam file JSON

## 📁 Struktur File

```
php/
├── index.php           # Halaman dashboard utama
├── login.php           # Halaman login
├── logout.php          # Script logout
├── create.php          # Halaman tambah transaksi
├── tampil.php          # Halaman lihat semua transaksi
├── edit.php            # Halaman edit transaksi
├── config.php          # Konfigurasi dan fungsi utama
├── data/               # Folder untuk menyimpan data JSON
│   └── transaksi.json  # File penyimpanan data transaksi
└── README.md           # File dokumentasi (ini)
```

## 🚀 Cara Menggunakan

### 1. Setup Awal

1. Copy semua file PHP ke server atau localhost Anda
2. Pastikan folder `php/data/` ada dan memiliki permission write
3. Akses aplikasi melalui browser: `http://localhost/php/login.php`

### 2. Login

Gunakan akun demo yang sudah tersedia:

**Akun Admin:**

- Email: `admin@example.com`
- Password: `admin123`

**Akun User:**

- Email: `user@example.com`
- Password: `user123`

> **Catatan:** Anda bisa menambahkan user baru dengan mengedit array `$users` di dalam `login.php`

### 3. Menambah Transaksi

1. Dari dashboard, klik **"Input Data"** atau buka `create.php`
2. Isi form dengan data transaksi:
   - **Tanggal Transaksi**: Pilih tanggal
   - **Jenis Transaksi**: Masuk atau Keluar
   - **Kategori**: Nama kategori (misal: Makan, Transportasi, Gaji, dll)
   - **Nominal**: Jumlah uang (angka saja)
   - **Deskripsi**: Keterangan tambahan (opsional)
3. Klik tombol **"Simpan Transaksi"**

### 4. Melihat Semua Transaksi

1. Dari dashboard, klik **"Data Tersimpan"** atau buka `tampil.php`
2. Lihat daftar semua transaksi yang tersimpan
3. Untuk setiap transaksi Anda bisa:
   - **Edit** (✏️): Mengubah data transaksi
   - **Hapus** (🗑️): Menghapus transaksi

### 5. Edit Transaksi

1. Di halaman "Data Tersimpan", klik tombol **"Edit"** pada transaksi yang ingin diubah
2. Ubah data yang diperlukan
3. Klik tombol **"Simpan Perubahan"**

### 6. Logout

Klik tombol **"Logout"** di bagian atas dashboard untuk keluar dari aplikasi

## 🔧 Konfigurasi

### Menambah User Baru

Edit file `login.php` dan tambahkan user baru di array `$users`:

```php
$users = [
    ['email' => 'admin@example.com', 'password' => 'admin123'],
    ['email' => 'user@example.com', 'password' => 'user123'],
    ['email' => 'newuser@example.com', 'password' => 'password123'], // User baru
];
```

### Mengubah Informasi Aplikasi

Edit file `config.php` untuk mengubah nama aplikasi atau informasi lainnya:

```php
define('APP_NAME', 'Biyai Finance Tracker');
define('APP_DESC', 'Aplikasi Pencatatan Keuangan');
define('APP_AUTHOR', 'Ilham Fahturozi Akbar');
define('APP_NIM', '22101152610489');
```

## 📊 Struktur Data JSON

File `php/data/transaksi.json` menyimpan data dalam format berikut:

```json
[
  {
    "id": "64f9a1b2c3d4e",
    "tanggal": "2024-01-08",
    "jenis": "Masuk",
    "kategori": "Gaji",
    "nominal": 5000000,
    "deskripsi": "Gaji bulanan",
    "createdAt": "2024-01-08 10:30:45",
    "updatedAt": "2024-01-08 11:00:00"
  }
]
```

## 🛡️ Keamanan

**Catatan Penting:**

- Aplikasi ini adalah versi demo/pembelajaran, tidak cocok untuk production
- Password disimpan dalam plain text (tidak di-encrypt)
- Untuk production, gunakan:
  - Password hashing (bcrypt, Argon2)
  - Database yang proper (MySQL, PostgreSQL, dll)
  - HTTPS
  - Input validation yang lebih ketat
  - CSRF protection
  - Session security

## 📝 Fitur yang Dapat Ditambahkan

- [ ] Pencarian transaksi
- [ ] Filter berdasarkan tanggal/kategori/jenis
- [ ] Export data ke CSV atau PDF
- [ ] Laporan/statistik keuangan
- [ ] Multi-user dengan akses kontrol
- [ ] Backup otomatis data
- [ ] Dark mode
- [ ] Notifikasi

## 🐛 Troubleshooting

### Error: "Folder data tidak bisa ditulis"

Pastikan folder `php/data/` memiliki permission write:

```bash
chmod 755 php/data/
```

### Error: "File transaksi.json tidak ada"

File akan dibuat otomatis saat pertama kali menjalankan aplikasi. Jika tidak, pastikan folder `php/data/` ada.

### Transaksi tidak tersimpan

1. Cek apakah semua field sudah terisi
2. Cek permission folder `php/data/`
3. Cek ukuran file `transaksi.json` tidak terlalu besar

## 👨‍💻 Konversi dari Dart

Aplikasi ini adalah konversi dari aplikasi Flutter (Dart) ke PHP Native:

**File Dart Original:**

- `auth_form.dart` → `login.php` + `config.php` (authentication)
- `create_form.dart` → `create.php` (form input)
- `tampil_form.dart` → `tampil.php` (display data)
- `edit_form.dart` → `edit.php` (edit functionality)
- `login_page.dart` → `login.php`
- `main.dart` → `index.php`

**Perubahan:**

- Tidak lagi menggunakan Firebase
- Data disimpan dalam JSON file
- UI diubah menggunakan HTML/CSS/JavaScript
- Session management menggunakan PHP native

## 📞 Support

Jika ada pertanyaan atau bug, silakan hubungi developer.

---

**Dibuat oleh:** Ilham Fahturozi Akbar  
**NIM:** 22101152610489  
**Tanggal:** 2024  
**Platform:** PHP Native (no database)
