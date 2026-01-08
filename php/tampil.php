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
$transaksi_list = getTransaksi();

// Proses hapus
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    if (hapusTransaksi($delete_id)) {
        $success = 'Transaksi berhasil dihapus';
        header('Refresh: 1');
    } else {
        $error = 'Gagal menghapus transaksi';
    }
}

// Proses edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'])) {
    $update_id = $_POST['update_id'];
    $jenis = $_POST['jenis'] ?? '';
    $kategori = $_POST['kategori'] ?? '';
    $nominal = $_POST['nominal'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    
    if (empty($jenis) || empty($kategori) || empty($nominal) || !is_numeric($nominal)) {
        $error = 'Semua field harus diisi dengan benar';
    } else {
        $transaksi = [
            'jenis' => $jenis,
            'kategori' => $kategori,
            'nominal' => (int)$nominal,
            'deskripsi' => $deskripsi
        ];
        
        if (updateTransaksi($update_id, $transaksi)) {
            $success = 'Transaksi berhasil diupdate';
            header('Refresh: 1');
        } else {
            $error = 'Gagal mengupdate transaksi';
        }
    }
}

$transaksi_list = getTransaksi();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Tersimpan - Biyai Finance Tracker</title>
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
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
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
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .transaction-list h2 {
            margin-bottom: 20px;
            color: #333;
            font-size: 22px;
        }
        
        .transaction-card {
            border: 1px solid #eee;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 12px;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 16px;
            align-items: start;
        }
        
        .transaction-info {
            flex: 1;
        }
        
        .transaction-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .badge-masuk {
            background: #e0ffe0;
            color: #00b300;
        }
        
        .badge-keluar {
            background: #ffe0e0;
            color: #d32f2f;
        }
        
        .transaction-title {
            font-weight: bold;
            color: #333;
            font-size: 16px;
        }
        
        .transaction-detail {
            font-size: 13px;
            color: #666;
            margin-top: 8px;
        }
        
        .detail-row {
            margin: 4px 0;
        }
        
        .transaction-actions {
            display: flex;
            gap: 8px;
        }
        
        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: bold;
            transition: 0.3s;
            white-space: nowrap;
        }
        
        .btn-edit {
            background: #ffa500;
            color: white;
        }
        
        .btn-edit:hover {
            background: #ff8c00;
        }
        
        .btn-delete {
            background: #ff6b6b;
            color: white;
        }
        
        .btn-delete:hover {
            background: #ff5252;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }
        
        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background: white;
            padding: 24px;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        
        .modal-header {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 16px;
            color: #333;
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
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
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
            min-height: 80px;
        }
        
        .modal-actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            margin-top: 20px;
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
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-title">Daftar Transaksi</div>
        <a href="index.php" class="back-link">← Kembali</a>
    </div>
    
    <div class="container">
        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <div class="transaction-list">
            <h2>📋 Semua Transaksi</h2>
            
            <?php if (empty($transaksi_list)): ?>
                <div class="empty-state">
                    <p>📭 Belum ada transaksi yang tersimpan.</p>
                    <p style="margin-top: 8px;"><a href="create.php" style="color: #667eea; text-decoration: none;">Tambah transaksi sekarang</a></p>
                </div>
            <?php else: ?>
                <?php foreach ($transaksi_list as $t): ?>
                    <div class="transaction-card">
                        <div class="transaction-info">
                            <div class="transaction-header">
                                <span class="badge <?php echo strpos($t['jenis'], 'Masuk') !== false ? 'badge-masuk' : 'badge-keluar'; ?>">
                                    <?php echo strpos($t['jenis'], 'Masuk') !== false ? '⬇️ Masuk' : '⬆️ Keluar'; ?>
                                </span>
                            </div>
                            <div class="transaction-title"><?php echo htmlspecialchars($t['kategori']); ?></div>
                            <div class="transaction-detail">
                                <div class="detail-row">📅 Tanggal: <?php echo htmlspecialchars($t['tanggal']); ?></div>
                                <div class="detail-row">💵 Nominal: Rp<?php echo number_format($t['nominal'], 0, ',', '.'); ?></div>
                                <?php if (!empty($t['deskripsi'])): ?>
                                    <div class="detail-row">📝 Keterangan: <?php echo htmlspecialchars($t['deskripsi']); ?></div>
                                <?php endif; ?>
                                <div class="detail-row" style="color: #999; font-size: 11px;">
                                    ⏰ Dibuat: <?php echo htmlspecialchars($t['createdAt'] ?? '-'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="transaction-actions">
                            <button class="btn btn-edit" onclick="openEditModal('<?php echo htmlspecialchars($t['id']); ?>', '<?php echo htmlspecialchars($t['jenis']); ?>', '<?php echo htmlspecialchars($t['kategori']); ?>', '<?php echo $t['nominal']; ?>', '<?php echo htmlspecialchars($t['deskripsi']); ?>')">✏️ Edit</button>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?');">
                                <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($t['id']); ?>">
                                <button type="submit" class="btn btn-delete">🗑️ Hapus</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Edit Transaksi</div>
            <form method="POST">
                <input type="hidden" id="update_id" name="update_id" value="">
                
                <div class="form-group">
                    <label for="edit_jenis">Jenis Transaksi</label>
                    <select id="edit_jenis" name="jenis" required>
                        <option value="">-- Pilih Jenis --</option>
                        <?php foreach ($jenis_list as $j): ?>
                            <option value="<?php echo $j; ?>"><?php echo $j; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_kategori">Kategori</label>
                    <input type="text" id="edit_kategori" name="kategori" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_nominal">Nominal</label>
                    <input type="number" id="edit_nominal" name="nominal" required min="0">
                </div>
                
                <div class="form-group">
                    <label for="edit_deskripsi">Deskripsi</label>
                    <textarea id="edit_deskripsi" name="deskripsi"></textarea>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-cancel" onclick="closeEditModal()">Batal</button>
                    <button type="submit" class="btn btn-save">💾 Simpan</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function openEditModal(id, jenis, kategori, nominal, deskripsi) {
            document.getElementById('update_id').value = id;
            document.getElementById('edit_jenis').value = jenis;
            document.getElementById('edit_kategori').value = kategori;
            document.getElementById('edit_nominal').value = nominal;
            document.getElementById('edit_deskripsi').value = deskripsi;
            document.getElementById('editModal').classList.add('active');
        }
        
        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
        }
        
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
    </script>
</body>
</html>
