<?php
session_start();
require_once 'config.php';

// Cek status login
if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit();
}

$id = $_GET['id'] ?? '';
$transaksi = null;
$error = '';
$success = '';
$jenis_list = ['Transaksi Masuk', 'Transaksi Keluar'];

// Ambil data transaksi
if (!empty($id)) {
    $transaksi = getTransaksiById($id);
    if (!$transaksi) {
        $error = 'Transaksi tidak ditemukan';
    }
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $jenis = $_POST['jenis'] ?? '';
    $kategori = $_POST['kategori'] ?? '';
    $nominal = $_POST['nominal'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    
    if (empty($jenis) || empty($kategori) || empty($nominal) || !is_numeric($nominal)) {
        $error = 'Semua field harus diisi dengan benar';
    } else {
        $update_data = [
            'jenis' => $jenis,
            'kategori' => $kategori,
            'nominal' => (int)$nominal,
            'deskripsi' => $deskripsi
        ];
        
        if (updateTransaksi($id, $update_data)) {
            $success = 'Transaksi berhasil diupdate!';
            $transaksi = getTransaksiById($id);
        } else {
            $error = 'Gagal mengupdate transaksi';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Transaksi - Biyai Finance Tracker</title>
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
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .navbar-title {
            font-size: 18px;
            font-weight: bold;
        }
        
        .back-link {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            background: rgba(255,255,255,0.2);
            border-radius: 8px;
            transition: 0.3s;
        }
        
        .back-link:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .form-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
        
        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 24px;
        }
        
        .btn {
            flex: 1;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }
        
        .btn-cancel {
            background: #e0e0e0;
            color: #333;
        }
        
        .btn-cancel:hover {
            background: #d0d0d0;
        }
        
        .btn-save {
            background: #4169e1;
            color: white;
        }
        
        .btn-save:hover {
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
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-title">Edit Transaksi</div>
        <a href="tampil.php" class="back-link">← Kembali</a>
    </div>
    
    <div class="container">
        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if ($transaksi): ?>
            <form method="POST" class="form-card">
                <h2>Ubah Data Transaksi</h2>
                
                <div class="form-group">
                    <label for="jenis">Jenis Transaksi</label>
                    <select id="jenis" name="jenis" required>
                        <option value="">-- Pilih Jenis --</option>
                        <?php foreach ($jenis_list as $j): ?>
                            <option value="<?php echo $j; ?>" <?php echo $transaksi['jenis'] === $j ? 'selected' : ''; ?>>
                                <?php echo $j; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="kategori">Kategori Transaksi</label>
                    <input 
                        type="text" 
                        id="kategori" 
                        name="kategori" 
                        required
                        value="<?php echo htmlspecialchars($transaksi['kategori']); ?>"
                    >
                </div>
                
                <div class="form-group">
                    <label for="nominal">Nominal (Jumlah Uang)</label>
                    <input 
                        type="number" 
                        id="nominal" 
                        name="nominal" 
                        required
                        min="0"
                        value="<?php echo $transaksi['nominal']; ?>"
                    >
                </div>
                
                <div class="form-group">
                    <label for="deskripsi">Deskripsi / Keterangan</label>
                    <textarea 
                        id="deskripsi" 
                        name="deskripsi"
                    ><?php echo htmlspecialchars($transaksi['deskripsi'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-actions">
                    <a href="tampil.php" class="btn btn-cancel">Batal</a>
                    <button type="submit" name="save" class="btn btn-save">💾 Simpan Perubahan</button>
                </div>
            </form>
        <?php else: ?>
            <div style="background: white; padding: 40px; border-radius: 12px; text-align: center;">
                <p style="color: #999; font-size: 16px;">❌ Transaksi tidak ditemukan</p>
                <a href="tampil.php" style="color: #667eea; text-decoration: none; margin-top: 16px; display: inline-block;">Kembali ke daftar transaksi</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
