<?php
session_start();
require_once 'config.php';

// Cek status login
if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit();
}

$success = '';
$error = '';
$jenis_list = ['Transaksi Masuk', 'Transaksi Keluar'];

// Inisialisasi variable form
$tanggal = '';
$jenis = '';
$kategori = '';
$nominal = '';
$deskripsi = '';

// Proses penyimpanan transaksi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal'] ?? '';
    $jenis = $_POST['jenis'] ?? '';
    $kategori = $_POST['kategori'] ?? '';
    $nominal = $_POST['nominal'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    
    // Validasi input
    if (empty($tanggal)) {
        $error = 'Tanggal transaksi harus dipilih';
    } elseif (empty($jenis)) {
        $error = 'Jenis transaksi harus dipilih';
    } elseif (empty($kategori)) {
        $error = 'Kategori tidak boleh kosong';
    } elseif (empty($nominal) || !is_numeric($nominal)) {
        $error = 'Nominal harus diisi dengan angka';
    } else {
        // Simpan transaksi
        $transaksi = [
            'tanggal' => $tanggal,
            'jenis' => $jenis,
            'kategori' => $kategori,
            'nominal' => (int)$nominal,
            'deskripsi' => $deskripsi
        ];
        
        tambahTransaksi($transaksi);
        $success = 'Transaksi berhasil disimpan!';
        
        // Reset form
        $tanggal = '';
        $jenis = '';
        $kategori = '';
        $nominal = '';
        $deskripsi = '';
    }
}

$transaksi_list = getTransaksi();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Transaksi - Biyai Finance Tracker</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        background: #f5f5f5;
    }

    .navbar {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .navbar-title {
        font-size: 18px;
        font-weight: bold;
    }

    .back-link {
        color: white;
        text-decoration: none;
        padding: 8px 16px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        transition: 0.3s;
    }

    .back-link:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .container {
        max-width: 900px;
        margin: 0 auto;
        padding: 20px;
    }

    .form-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 30px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .form-card h2 {
        margin-bottom: 20px;
        color: #333;
        font-size: 22px;
    }

    .form-group {
        margin-bottom: 16px;
    }

    label {
        display: block;
        margin-bottom: 8px;
        color: #333;
        font-weight: 500;
    }

    input[type="date"],
    input[type="text"],
    input[type="number"],
    select,
    textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        transition: 0.3s;
    }

    input[type="date"]:focus,
    input[type="text"]:focus,
    input[type="number"]:focus,
    select:focus,
    textarea:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    textarea {
        resize: vertical;
        min-height: 100px;
    }

    .submit-button {
        background: #4169e1;
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
        width: 100%;
    }

    .submit-button:hover {
        background: #315ac1;
    }

    .message {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-weight: 500;
    }

    .error {
        background: #ffe0e0;
        color: #d32f2f;
        border: 1px solid #ffb3b3;
    }

    .success {
        background: #e0ffe0;
        color: #00b300;
        border: 1px solid #b3ffb3;
    }

    .transaction-list {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .transaction-list h2 {
        margin-bottom: 20px;
        color: #333;
        font-size: 20px;
    }

    .transaction-card {
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fafafa;
    }

    .transaction-info {
        flex: 1;
    }

    .transaction-title {
        font-weight: bold;
        color: #333;
    }

    .transaction-detail {
        font-size: 12px;
        color: #666;
        margin-top: 4px;
    }

    .badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: bold;
        margin-right: 8px;
    }

    .badge-masuk {
        background: #e0ffe0;
        color: #00b300;
    }

    .badge-keluar {
        background: #ffe0e0;
        color: #d32f2f;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #999;
    }
    </style>
</head>

<body>
    <div class="navbar">
        <div class="navbar-title">Tambah Transaksi</div>
        <a href="index.php" class="back-link">← Kembali</a>
    </div>

    <div class="container">
        <?php if ($error): ?>
        <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" class="form-card">
            <h2>Form Tambah Transaksi</h2>

            <div class="form-group">
                <label for="tanggal">Tanggal Transaksi</label>
                <input type="date" id="tanggal" name="tanggal" required
                    value="<?php echo htmlspecialchars($tanggal); ?>">
            </div>

            <div class="form-group">
                <label for="jenis">Jenis Transaksi</label>
                <select id="jenis" name="jenis" required>
                    <option value="">-- Pilih Jenis --</option>
                    <?php foreach ($jenis_list as $j): ?>
                    <option value="<?php echo $j; ?>" <?php echo $jenis === $j ? 'selected' : ''; ?>>
                        <?php echo $j; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="kategori">Kategori Transaksi</label>
                <input type="text" id="kategori" name="kategori" placeholder="Contoh: Makan, Transportasi, Gaji, dll"
                    required value="<?php echo htmlspecialchars($kategori); ?>">
            </div>

            <div class="form-group">
                <label for="nominal">Nominal (Jumlah Uang)</label>
                <input type="number" id="nominal" name="nominal" placeholder="Masukkan jumlah uang" required min="0"
                    value="<?php echo htmlspecialchars($nominal); ?>">
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi / Keterangan</label>
                <textarea id="deskripsi" name="deskripsi"
                    placeholder="Masukkan deskripsi atau keterangan transaksi (opsional)"><?php echo htmlspecialchars($deskripsi); ?></textarea>
            </div>

            <button type="submit" class="submit-button">💾 Simpan Transaksi</button>
        </form>

        <div class="transaction-list">
            <h2>Daftar Transaksi Terakhir</h2>

            <?php if (empty($transaksi_list)): ?>
            <div class="empty-state">
                <p>📭 Belum ada transaksi. Mulai tambahkan transaksi Anda!</p>
            </div>
            <?php else: ?>
            <?php $count = 0; ?>
            <?php foreach ($transaksi_list as $t): ?>
            <?php if ($count >= 5) break; ?>
            <div class="transaction-card">
                <div class="transaction-info">
                    <div class="transaction-title">
                        <span
                            class="badge <?php echo strpos($t['jenis'], 'Masuk') !== false ? 'badge-masuk' : 'badge-keluar'; ?>">
                            <?php echo strpos($t['jenis'], 'Masuk') !== false ? '⬇️' : '⬆️'; ?>
                            <?php echo $t['jenis']; ?>
                        </span>
                        <?php echo htmlspecialchars($t['kategori']); ?>
                    </div>
                    <div class="transaction-detail">
                        📅 <?php echo htmlspecialchars($t['tanggal']); ?> |
                        💵 Rp<?php echo number_format($t['nominal'], 0, ',', '.'); ?>
                        <?php if ($t['deskripsi']): ?>
                        | 📝 <?php echo htmlspecialchars($t['deskripsi']); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php $count++; ?>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>