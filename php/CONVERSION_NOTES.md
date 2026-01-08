# Dokumentasi Konversi Dart ke PHP

## Ringkasan Konversi

Aplikasi manajemen keuangan "Biyai Finance Tracker" telah dikonversi dari Flutter (Dart) ke PHP Native.

## File-File yang Dikonversi

### 1. **main.dart** → **index.php**

- **Fungsi Asli:** Widget utama yang menampilkan dashboard
- **Perubahan:**
  - Flutter MaterialApp → HTML/CSS
  - Navigation drawer → Simple menu cards
  - StatefulWidget → PHP Session management
  - Gradient colors → CSS gradients

### 2. **login_page.dart** → **login.php**

- **Fungsi Asli:** Halaman login dengan validasi form
- **Perubahan:**
  - TextFormField → HTML input elements
  - Form validation → PHP server-side validation
  - Navigator.push → Form submission
  - Callbacks → Session variables

### 3. **auth_form.dart** → **login.php + config.php**

- **Fungsi Asli:** Authentication dengan Firebase
- **Perubahan:**
  - FirebaseAuth.instance → Array-based auth
  - async/await → PHP synchronous operations
  - setState → Session updates
  - Firestore → JSON file storage

### 4. **create_form.dart** → **create.php**

- **Fungsi Asli:** Form untuk menambah transaksi
- **Perubahan:**
  - FormState → POST request handling
  - DatePicker → HTML date input
  - DropdownButtonFormField → HTML select
  - StreamBuilder (Firestore) → JSON file read
  - Firestore.add() → JSON append

### 5. **tampil_form.dart** → **tampil.php**

- **Fungsi Asli:** Display semua transaksi dari Firestore
- **Perubahan:**
  - StreamBuilder → Direct file read
  - DocumentSnapshot → Array elements
  - Card widgets → CSS card layout
  - Edit dialog → Modal dialog dengan JavaScript
  - Delete functionality → POST form

### 6. **edit_form.dart** → **edit.php**

- **Fungsi Asli:** Edit data transaksi
- **Perubahan:**
  - TextEditingController → HTML input values
  - initState/dispose → Direct value assignment
  - Firestore.update() → JSON file update
  - Navigator.pop() → Redirect

### 7. **firebase_options.dart** → **config.php**

- **Fungsi Asli:** Konfigurasi Firebase
- **Perubahan:**
  - FirebaseOptions → PHP constants
  - Platform detection → Server-side config
  - API keys → Removed (tidak perlu)

### 8. **main1.dart** → **Not needed**

- Duplicate dari main.dart, tidak perlu dikonversi

## Teknologi Mapping

| Dart/Flutter          | PHP/HTML/CSS       |
| --------------------- | ------------------ |
| StatefulWidget        | Session + POST/GET |
| setState()            | $\_SESSION updates |
| FirebaseAuth          | Array-based auth   |
| Firestore             | JSON file          |
| TextEditingController | HTML input         |
| Form validation       | PHP validation     |
| Navigation            | href links + forms |
| Widgets               | HTML elements      |
| Material colors       | CSS colors         |
| async/await           | Synchronous PHP    |
| StreamBuilder         | Direct file read   |

## Perbandingan Fitur

### Authentication

**Dart (Firebase):**

```dart
await FirebaseAuth.instance.signInWithEmailAndPassword(
  email: emailcontroller.text,
  password: passcontroller.text,
);
```

**PHP:**

```php
if ($user['email'] === $email && $user['password'] === $password) {
    $_SESSION['user_email'] = $email;
    header('Location: index.php');
}
```

### Data Storage

**Dart (Firestore):**

```dart
await FirebaseFirestore.instance.collection('transaksi').add({
  'tanggal': _tanggal?.toIso8601String(),
  'jenis': _jenis,
  'nominal': int.tryParse(_nominal ?? '0') ?? 0,
});
```

**PHP:**

```php
$transaksi = [
    'tanggal' => $tanggal,
    'jenis' => $jenis,
    'nominal' => (int)$nominal,
];
tambahTransaksi($transaksi);
```

### Display Data

**Dart (StreamBuilder):**

```dart
StreamBuilder<QuerySnapshot>(
  stream: FirebaseFirestore.instance.collection('transaksi').snapshots(),
  builder: (context, snapshot) {
    return ListView.builder(...);
  }
)
```

**PHP:**

```php
$transaksi_list = getTransaksi();
foreach ($transaksi_list as $t) {
    // Display item
}
```

## Keuntungan Konversi ke PHP

✅ **Lebih mudah di-deploy** - Bisa di semua server hosting
✅ **Tidak perlu Firebase account**
✅ **Standalone application** - Tidak tergantung service external
✅ **Data kontrol penuh** - Data tersimpan lokal
✅ **Lebih ringan** - Tidak perlu library Firebase
✅ **Mudah di-maintain** - Plain PHP code

## Kekurangan

❌ **Scalability terbatas** - JSON bukan untuk big data
❌ **Real-time limited** - Tidak ada live updates seperti Firestore
❌ **Security** - Plain text storage (untuk demo saja)
❌ **Multi-user complex** - Hanya basic user management

## Rekomendasi

Untuk production, sebaiknya:

1. Gunakan database proper (MySQL, PostgreSQL)
2. Implement proper authentication (Laravel/Symfony)
3. Add input sanitization & validation
4. Use HTTPS
5. Implement caching
6. Add logging & monitoring

---

**Catatan:** Ini adalah konversi educational untuk demonstrasi fungsi aplikasi. Untuk production use, perlu security enhancements yang lebih baik.
