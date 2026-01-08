<?php
/**
 * API Test & Data Management
 * File ini untuk testing dan management data aplikasi
 * 
 * HANYA UNTUK DEVELOPMENT, HAPUS DI PRODUCTION!
 */

session_start();

// Simple security - check if already logged in
if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit();
}

require_once 'config.php';

$action = $_GET['action'] ?? '';
$message = '';
$data = [];

// Handle berbagai actions
switch ($action) {
    case 'view_json':
        $data = getTransaksi();
        $message = 'JSON Data (Total: ' . count($data) . ' transaksi)';
        break;
        
    case 'clear_all':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
                saveTransaksi([]);
                $message = '✅ Semua data telah dihapus!';
                header('Refresh: 2; url=test.php');
            }
        }
        break;
        
    case 'add_sample':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $samples = [
                [
                    'tanggal' => date('Y-m-d'),
                    'jenis' => 'Masuk',
                    'kategori' => 'Gaji',
                    'nominal' => 5000000,
                    'deskripsi' => 'Gaji bulanan'
                ],
                [
                    'tanggal' => date('Y-m-d'),
                    'jenis' => 'Keluar',
                    'kategori' => 'Makan',
                    'nominal' => 50000,
                    'deskripsi' => 'Makan siang'
                ],
                [
                    'tanggal' => date('Y-m-d'),
                    'jenis' => 'Keluar',
                    'kategori' => 'Transportasi',
                    'nominal' => 20000,
                    'deskripsi' => 'Bensin'
                ]
            ];
            
            foreach ($samples as $sample) {
                tambahTransaksi($sample);
            }
            $message = '✅ ' . count($samples) . ' data sample telah ditambahkan!';
            header('Refresh: 2; url=test.php?action=view_json');
        }
        break;
        
    case 'stats':
        $transaksi_list = getTransaksi();
        $data = [
            'total_transaksi' => count($transaksi_list),
            'total_masuk' => 0,
            'total_keluar' => 0,
            'saldo' => 0,
        ];
        
        foreach ($transaksi_list as $t) {
            if ($t['jenis'] === 'Masuk') {
                $data['total_masuk'] += $t['nominal'];
            } else {
                $data['total_keluar'] += $t['nominal'];
            }
        }
        $data['saldo'] = $data['total_masuk'] - $data['total_keluar'];
        $message = 'Statistik Transaksi';
        break;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Development Test - Biyai Finance Tracker</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
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
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .menu-btn {
            background: #2d2d30;
            border: 1px solid #3e3e42;
            color: #d4d4d4;
            padding: 15px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: block;
            text-align: center;
            transition: 0.3s;
            font-size: 14px;
            font-weight: bold;
        }
        
        .menu-btn:hover {
            background: #3d3d40;
            border-color: #667eea;
        }
        
        .menu-btn.danger {
            border-color: #f44747;
        }
        
        .menu-btn.danger:hover {
            background: #5c3a3a;
        }
        
        .content {
            background: #1e1e1e;
            border: 1px solid #3e3e42;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .content h2 {
            color: #4ec9b0;
            margin-bottom: 15px;
            font-size: 18px;
        }
        
        .message {
            background: #264f78;
            border-left: 4px solid #007acc;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 4px;
            color: #9cdcfe;
        }
        
        .message.success {
            background: #274c2a;
            border-left-color: #4ec9b0;
            color: #4ec9b0;
        }
        
        .json-output {
            background: #1e1e1e;
            border: 1px solid #3e3e42;
            padding: 15px;
            border-radius: 6px;
            overflow-x: auto;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .json-output pre {
            margin: 0;
            color: #9cdcfe;
            font-size: 12px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .stat-card {
            background: #2d2d30;
            border: 1px solid #3e3e42;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
        }
        
        .stat-label {
            color: #858585;
            font-size: 12px;
            margin-bottom: 8px;
        }
        
        .stat-value {
            color: #4ec9b0;
            font-size: 24px;
            font-weight: bold;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #9cdcfe;
            font-size: 14px;
        }
        
        .confirm-form {
            background: #2d2d30;
            border: 1px solid #f44747;
            padding: 15px;
            border-radius: 6px;
            margin-top: 15px;
        }
        
        .confirm-form p {
            color: #f44747;
            margin-bottom: 15px;
            font-weight: bold;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
        }
        
        button {
            padding: 10px 20px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }
        
        button.yes {
            background: #f44747;
            color: white;
        }
        
        button.yes:hover {
            background: #d13438;
        }
        
        button.no {
            background: #3e3e42;
            color: #d4d4d4;
        }
        
        button.no:hover {
            background: #555;
        }
        
        button.add {
            background: #4ec9b0;
            color: #1e1e1e;
        }
        
        button.add:hover {
            background: #6dd3ca;
        }
        
        .warning {
            background: #5c4a2c;
            border: 1px solid #c9a86d;
            color: #e8d4a6;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        
        .file-info {
            background: #2d2d30;
            border-left: 4px solid #ce9178;
            padding: 12px;
            margin-top: 15px;
            border-radius: 4px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-title">🔧 Development Test Panel</div>
        <a href="index.php" class="back-link">← Back to App</a>
    </div>
    
    <div class="container">
        <div class="warning">
            ⚠️ <strong>WARNING:</strong> Ini adalah panel development. HAPUS file ini sebelum production!
        </div>
        
        <div class="menu-grid">
            <a href="test.php?action=view_json" class="menu-btn">View JSON Data</a>
            <a href="test.php?action=stats" class="menu-btn">View Statistics</a>
            <form method="POST" action="test.php?action=add_sample" style="display: inline; width: 100%;">
                <button type="submit" class="menu-btn" style="width: 100%; border: none; cursor: pointer;">Add Sample Data</button>
            </form>
            <button class="menu-btn danger" onclick="showClearConfirm()">Clear All Data</button>
        </div>
        
        <?php if ($message): ?>
            <div class="content">
                <h2><?php echo htmlspecialchars($message); ?></h2>
                
                <?php if ($action === 'view_json'): ?>
                    <div class="json-output">
                        <pre><?php echo json_encode(getTransaksi(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?></pre>
                    </div>
                    <div class="file-info">
                        📁 File: <?php echo DATA_FILE; ?><br>
                        📊 Total Records: <?php echo count(getTransaksi()); ?><br>
                        💾 File Size: <?php echo file_exists(DATA_FILE) ? round(filesize(DATA_FILE) / 1024, 2) . ' KB' : 'N/A'; ?>
                    </div>
                <?php elseif ($action === 'stats'): ?>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-label">Total Transaksi</div>
                            <div class="stat-value"><?php echo $data['total_transaksi']; ?></div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Total Masuk</div>
                            <div class="stat-value">Rp<?php echo number_format($data['total_masuk'], 0, ',', '.'); ?></div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Total Keluar</div>
                            <div class="stat-value">Rp<?php echo number_format($data['total_keluar'], 0, ',', '.'); ?></div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Saldo</div>
                            <div class="stat-value" style="color: <?php echo $data['saldo'] >= 0 ? '#4ec9b0' : '#f44747'; ?>">
                                Rp<?php echo number_format($data['saldo'], 0, ',', '.'); ?>
                            </div>
                        </div>
                    </div>
                <?php elseif ($action === 'clear_all'): ?>
                    <div class="confirm-form">
                        <p>❓ Apakah Anda yakin ingin menghapus SEMUA data transaksi?</p>
                        <form method="POST">
                            <div class="button-group">
                                <button type="submit" name="confirm" value="yes" class="yes">✓ Yes, Delete All</button>
                                <button type="button" class="no" onclick="window.location='test.php'">✕ No, Cancel</button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="content">
                <h2>Welcome to Development Test Panel</h2>
                <p>Gunakan menu di atas untuk manage data aplikasi:</p>
                <ul style="margin-left: 20px; margin-top: 10px;">
                    <li>View JSON Data - Lihat semua data dalam format JSON</li>
                    <li>View Statistics - Lihat statistik transaksi</li>
                    <li>Add Sample Data - Tambah data sample untuk testing</li>
                    <li>Clear All Data - Hapus semua data (hati-hati!)</li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        function showClearConfirm() {
            window.location = 'test.php?action=clear_all';
        }
    </script>
</body>
</html>
