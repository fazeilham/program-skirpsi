<?php
/**
 * Simple PDO connection helper for db_biyai
 * Usage: require_once __DIR__ . '/db.php'; then use get_db() to get PDO instance
 */

// Configuration (override with env vars in production)
$DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
$DB_NAME = getenv('DB_NAME') ?: 'db_biyai';
$DB_USER = getenv('DB_USER') ?: 'biyai_user';
$DB_PASS = getenv('DB_PASS') ?: 'GantiDenganPasswordKuat';
$DB_PORT = getenv('DB_PORT') ?: '3306';

// Create PDO instance and expose via function
function get_db()
{
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS, $DB_PORT;

    $dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";
    try {
        $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $e) {
        // In production you might want to log this and show a friendly message
        // Rethrow as RuntimeException so callers can handle it (and we avoid an unconditional exit)
        throw new RuntimeException("Database connection failed: " . $e->getMessage(), 0, $e);
    }
    return $pdo;
}

// Convenience: basic health check
function db_is_connected()
{
    try {
        $db = get_db();
        $db->query('SELECT 1');
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Debug helper: safe diagnostics without printing passwords
function db_connect_debug()
{
    global $DB_HOST, $DB_NAME, $DB_USER, $DB_PORT;
    $info = [
        'dsn' => "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME}",
        'host' => $DB_HOST,
        'port' => $DB_PORT,
        'db' => $DB_NAME,
        'user' => $DB_USER,
        'ok' => false,
        'message' => null,
    ];

    try {
        $pdo = get_db();
        $info['ok'] = true;
        $info['server_version'] = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) ?? null;
        $stmt = $pdo->query("SELECT VERSION() as v, CURRENT_USER() as current_user");
        $row = $stmt->fetch();
        if ($row) {
            $info['version'] = $row['v'] ?? null;
            $info['current_user'] = $row['current_user'] ?? null;
        }
        // Try to read auth plugin if privileges allow
        try {
            $stmt = $pdo->prepare("SELECT plugin FROM mysql.user WHERE user = ? LIMIT 1");
            $stmt->execute([$DB_USER]);
            $r = $stmt->fetch();
            if ($r && isset($r['plugin'])) $info['auth_plugin'] = $r['plugin'];
        } catch (Exception $e) {
            // ignore lack of privilege
        }
        $info['message'] = 'Connected successfully';
    } catch (Exception $e) {
        $info['message'] = $e->getMessage();
    }

    return $info;
}

// Example migration helper (commented)
/*
// Example: migrate JSON transactions into DB
require_once __DIR__ . '/../config.php';
$pdo = get_db();
$data = getTransaksi();
$stmt = $pdo->prepare('INSERT INTO transaksi (id,tanggal,jenis,kategori,nominal,deskripsi,unit,jasa,detail_pekerjaan,createdAt) VALUES (:id,:tanggal,:jenis,:kategori,:nominal,:deskripsi,:unit,:jasa,:detail,:createdAt) ON DUPLICATE KEY UPDATE nominal=VALUES(nominal)');
foreach ($data as $tx) {
    $stmt->execute([
        ':id' => $tx['id'],
        ':tanggal' => $tx['tanggal'] ?? substr($tx['createdAt'] ?? '',0,10),
        ':jenis' => $tx['jenis'] ?? null,
        ':kategori' => $tx['kategori'] ?? null,
        ':nominal' => floatval($tx['nominal'] ?? 0),
        ':deskripsi' => $tx['deskripsi'] ?? null,
        ':unit' => $tx['unit'] ?? null,
        ':jasa' => $tx['jasa'] ?? null,
        ':detail' => $tx['detail_pekerjaan'] ?? null,
        ':createdAt' => $tx['createdAt'] ?? date('Y-m-d H:i:s'),
    ]);
}
*/

?>