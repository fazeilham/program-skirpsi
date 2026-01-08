<?php
/**
 * File: QUICK_START.md
 * Panduan Cepat Biyai Finance Tracker - PHP Native
 */
?>

# 🚀 Quick Start Guide - Biyai Finance Tracker

Panduan cepat untuk menjalankan aplikasi Biyai Finance Tracker versi PHP Native.

## ⚙️ Persyaratan Sistem

- PHP 7.4 atau lebih tinggi
- Web Server (Apache dengan mod_rewrite atau built-in PHP server)
- Browser modern (Chrome, Firefox, Safari, Edge)
- Minimal 10MB free disk space

## 🔧 Setup Lokal

### Menggunakan PHP Built-in Server

1. **Buka terminal/command prompt**

2. **Navigasi ke folder project:**

```bash
cd "e:\Semester 8 (FINAL DANCE)\Skripsi (END)\Program Paze\tryfirebase\php"
```

3. **Jalankan PHP built-in server:**

```bash
php -S localhost:8000
```

4. **Buka browser dan akses:**

```
http://localhost:8000/login.php
```

### Menggunakan Apache/Xampp

1. **Copy folder `php` ke folder htdocs:**

   - Windows: `C:\xampp\htdocs\`
   - Mac: `/Applications/XAMPP/htdocs/`
   - Linux: `/opt/lampp/htdocs/`

2. **Pastikan Apache berjalan**

3. **Akses di browser:**

```
http://localhost/php/login.php
```

## 📱 Login Pertama Kali

### Akun Demo yang Tersedia:

**Admin Account:**

```
Email: admin@example.com
Password: admin123
```

**User Account:**

```
Email: user@example.com
Password: user123
```

> **Tip:** Anda bisa login dengan salah satu akun di atas untuk mencoba aplikasi

## 🎯 Workflow Umum

### 1️⃣ Login

```
→ Akses http://localhost:8000/login.php
→ Masukkan email dan password
→ Klik tombol "Login"
→ Berhasil login
```

### 2️⃣ Dashboard

```
→ Lihat menu utama dengan 2 pilihan:
   • Input Data (Tambah transaksi)
   • Data Tersimpan (Lihat semua transaksi)
```

### 3️⃣ Tambah Transaksi

```
→ Klik "Input Data"
→ Isi form:
   • Tanggal Transaksi
   • Jenis (Masuk/Keluar)
   • Kategori
   • Nominal
   • Deskripsi (opsional)
→ Klik "Simpan Transaksi"
→ Transaksi berhasil disimpan
```

### 4️⃣ Lihat Transaksi

```
→ Klik "Data Tersimpan"
→ Melihat daftar semua transaksi yang tersimpan
→ Bisa edit atau hapus transaksi
```

### 5️⃣ Edit Transaksi

```
→ Di halaman Data Tersimpan, klik tombol "Edit"
→ Ubah data yang diperlukan
→ Klik "Simpan Perubahan"
```

### 6️⃣ Hapus Transaksi

```
→ Di halaman Data Tersimpan, klik tombol "Hapus"
→ Konfirmasi penghapusan
→ Transaksi dihapus dari sistem
```

### 7️⃣ Logout

```
→ Klik tombol "Logout" di dashboard
→ Kembali ke halaman login
```

## 📁 Struktur Folder

```
php/
├── index.php              ← Dashboard utama
├── login.php              ← Halaman login
├── logout.php             ← Script logout
├── create.php             ← Form tambah transaksi
├── tampil.php             ← Daftar semua transaksi
├── edit.php               ← Edit transaksi
├── about.php              ← Info aplikasi
├── config.php             ← Konfigurasi & fungsi
├── .htaccess              ← Apache rules
├── data/
│   └── transaksi.json     ← Database JSON
├── README.md              ← Dokumentasi lengkap
├── CONVERSION_NOTES.md    ← Catatan konversi
└── QUICK_START.md         ← File ini
```

## 🔄 Data Flow

```
Login Page
    ↓
Authentication (Array-based)
    ↓
Dashboard (index.php)
    ├→ Create Form (create.php)
    │   └→ Save to transaksi.json
    │
    └→ View Data (tampil.php)
        ├→ Edit Form (edit.php)
        │   └→ Update transaksi.json
        │
        └→ Delete
            └→ Remove from transaksi.json
```

## 🗂️ Format Data JSON

File `data/transaksi.json` menyimpan transaksi dalam format:

```json
[
  {
    "id": "unique_id",
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

## ⚠️ Troubleshooting

### ❌ Error: "Call to undefined function json_encode()"

**Solusi:** Install JSON extension atau update PHP

### ❌ Error: "Permission denied"

**Solusi:** Berikan permission write pada folder `data/`

```bash
chmod 755 php/data/
```

### ❌ Transaksi tidak tersimpan

**Solusi:**

1. Cek semua field sudah terisi
2. Cek permission folder `data/`
3. Cek ukuran file `transaksi.json`
4. Cek error log PHP

### ❌ File transaksi.json tidak ada

**Solusi:** File akan dibuat otomatis. Jika tidak, buat manual:

```bash
echo "[]" > data/transaksi.json
```

## 💡 Tips & Tricks

1. **Kategori Custom:** Anda bisa membuat kategori apapun saat input transaksi
2. **Nominal Besar:** Gunakan format angka tanpa titik/koma
3. **Backup Data:** Download file `data/transaksi.json` secara berkala
4. **Multiple Users:** Bisa menambah user baru di `login.php`

## 🔐 Default Security Features

✅ Session management
✅ Login protection
✅ Form validation
✅ File permission control

## 📈 Next Steps

Setelah familiar dengan aplikasi, Anda bisa:

1. **Customize UI** - Edit CSS di setiap file
2. **Tambah User** - Edit array `$users` di `login.php`
3. **Extend Features** - Tambah fitur baru di `config.php`
4. **Migrate to Database** - Upgrade ke MySQL/PostgreSQL

## 📞 Need Help?

- Baca file `README.md` untuk dokumentasi lengkap
- Cek `CONVERSION_NOTES.md` untuk detail teknis
- Review kode dalam setiap file PHP

---

**Selamat menggunakan Biyai Finance Tracker! 🎉**

_Created with ❤️ by Ilham Fahturozi Akbar_
