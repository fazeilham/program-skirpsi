<?php
session_start();
require_once __DIR__ . '/inc/migrate.php';

// Simple admin guard: only allow admin@example.com
if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit();
}
if ($_SESSION['user_email'] !== 'admin@example.com') {
    $is_admin = false;
} else {
    $is_admin = true;
}

$messages = [];
$result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$is_admin) {
        $messages[] = ['type' => 'error', 'text' => 'Hanya admin yang dapat menjalankan migrasi.'];
    } else {
        // CSRF check
        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            $messages[] = ['type' => 'error', 'text' => 'CSRF token invalid.'];
        } else {
            $options = [
                'backup' => !empty($_POST['backup']),
                'dry_run' => !empty($_POST['dry_run']),
                'truncate' => !empty($_POST['truncate']),
                'create_table' => !empty($_POST['create_table']),
                'verbose' => !empty($_POST['verbose']),
            ];
            $result = migrate_json_to_db($options);
            if ($result['ok']) $messages[] = ['type'=>'success','text'=>'Migrasi selesai.'];
            else $messages[] = ['type'=>'error','text'=>'Migrasi gagal. Lihat log.'];
        }
    }
}

// generate CSRF token
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
$csrf = $_SESSION['csrf_token'];

?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Migrasi JSON → MySQL - Admin</title>
<style>
body{font-family:Arial,Helvetica,sans-serif;background:#f6f8fb;padding:24px}
.card{background:white;padding:18px;border-radius:10px;box-shadow:0 6px 20px rgba(0,0,0,0.06);max-width:900px;margin:12px auto}
.row{display:flex;gap:8px;align-items:center}
.btn{padding:8px 12px;border-radius:8px;border:0;cursor:pointer}
.btn-primary{background:#3b82f6;color:white}
.btn-danger{background:#ff6b6b;color:white}
.log{background:#0f172a;color:#e2e8f0;padding:12px;border-radius:8px;margin-top:12px;font-family:monospace;white-space:pre-wrap}
.alert{padding:8px;border-radius:6px;margin-bottom:8px}
.alert.success{background:#e6ffed;color:#0f5132}
.alert.error{background:#ffe6e6;color:#7b1a1a}
.note{font-size:13px;color:#444}
</style>
</head>
<body>
<div class="card">
    <h2>Admin - Migrasi data JSON → MySQL</h2>
    <?php foreach($messages as $m): ?>
        <div class="alert <?php echo $m['type']==='success' ? 'success' : 'error'; ?>"><?php echo htmlspecialchars($m['text']); ?></div>
    <?php endforeach; ?>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
        <div style="display:flex;gap:12px;flex-wrap:wrap">
            <label><input type="checkbox" name="backup" value="1"> Buat backup JSON sebelum migrasi</label>
            <label><input type="checkbox" name="truncate" value="1"> Kosongkan tabel `transaksi` sebelum import</label>
            <label><input type="checkbox" name="create_table" value="1"> Buat tabel jika belum ada</label>
            <label><input type="checkbox" name="verbose" value="1"> Tampilkan log rinci</label>
            <label><input type="checkbox" name="dry_run" value="1"> Dry run (tidak menulis DB)</label>
        </div>
        <div style="margin-top:12px">
            <?php if ($is_admin): ?>
                <button type="submit" class="btn btn-primary">Jalankan Migrasi</button>
            <?php else: ?>
                <button type="button" class="btn btn-danger" disabled>Hanya admin</button>
            <?php endif; ?>
            <a href="index.php" style="margin-left:12px;color:#667eea;text-decoration:none;">← Kembali</a>
        </div>
    </form>

    <?php if ($result): ?>
        <h3 style="margin-top:18px">Hasil & Log</h3>
        <div class="log"><?php echo htmlspecialchars(implode("\n", $result['logs'] ?? [])); ?></div>
    <?php endif; ?>

    <p class="note">Catatan: hanya user <strong>admin@example.com</strong> yang dapat menjalankan migrasi lewat halaman ini.</p>
</div>
</body>
</html>