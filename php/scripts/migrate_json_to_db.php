<?php
// CLI script: migrate_json_to_db.php
// Usage: php migrate_json_to_db.php [--backup] [--dry-run] [--truncate] [--create-table] [--verbose]
// - --backup : create a timestamped backup of data/transaksi.json before migrating
// - --dry-run: simulate inserts/updates without writing to DB
// - --truncate: truncate `transaksi` table before import
// - --create-table: create minimal `transaksi` table if missing
// - --verbose : show detailed per-row output

require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/migrate.php';
require_once __DIR__ . '/../config.php'; // for DATA_FILE constant

$opts = getopt('', ['backup', 'dry-run', 'truncate', 'create-table', 'verbose']);
$options = [
    'backup' => isset($opts['backup']),
    'dry_run' => isset($opts['dry-run']),
    'truncate' => isset($opts['truncate']),
    'create_table' => isset($opts['create-table']),
    'verbose' => isset($opts['verbose']),
];
echo "Starting migration: JSON -> MySQL (db_biyai)\n";
if ($options['dry_run']) echo "Mode: DRY RUN (no DB changes)\n";

$result = migrate_json_to_db($options);
foreach ($result['logs'] as $line) echo $line . "\n";
if (!$result['ok']) exit(1);
exit(0);

$jsonFile = DATA_FILE; // from config.php
if (!file_exists($jsonFile)) {
    fwrite(STDERR, "Error: data file not found: $jsonFile\n");
    exit(2);
}

// Backup
$backupFile = null;
if ($doBackup) {
    $backupDir = dirname($jsonFile) . '/backups';
    if (!is_dir($backupDir)) mkdir($backupDir, 0755, true);
    $ts = date('Ymd_His');
    $backupFile = "$backupDir/transaksi_backup_$ts.json";
    if (!copy($jsonFile, $backupFile)) {
        fwrite(STDERR, "Warning: failed to create backup to $backupFile\n");
    } else {
        echo "Backup created: $backupFile\n";
    }
}

// Read data
$data = json_decode(file_get_contents($jsonFile), true);
if (!is_array($data)) {
    fwrite(STDERR, "Error: failed to parse JSON or JSON is not an array\n");
    exit(3);
}

// DB
$pdo = get_db();

// Optionally create table if missing
$check = $pdo->query("SHOW TABLES LIKE 'transaksi'")->fetch();
if (!$check) {
    if ($createTable) {
        echo "transaksi table not found, creating minimal table...\n";
        $sqlCreate = <<<SQL
CREATE TABLE IF NOT EXISTS `transaksi` (
  `id` VARCHAR(64) NOT NULL PRIMARY KEY,
  `tanggal` DATE NOT NULL,
  `jenis` VARCHAR(64) DEFAULT NULL,
  `kategori` VARCHAR(64) DEFAULT NULL,
  `nominal` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `deskripsi` TEXT,
  `unit` VARCHAR(128) DEFAULT NULL,
  `jasa` VARCHAR(128) DEFAULT NULL,
  `detail_pekerjaan` TEXT,
  `createdAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;
        if (!$dryRun) $pdo->exec($sqlCreate);
        echo "created transaksi table.\n";
    } else {
        fwrite(STDERR, "Error: transaksi table does not exist. Run with --create-table to create it or create schema first.\n");
        exit(4);
    }
}

if ($truncate) {
    echo ($dryRun ? "(dry-run) Would truncate transaksi table\n" : "Truncating transaksi table...\n");
    if (!$dryRun) $pdo->exec('TRUNCATE TABLE transaksi');
}

// Prepared statement
$insertSql = "INSERT INTO transaksi (id,tanggal,jenis,kategori,nominal,deskripsi,unit,jasa,detail_pekerjaan,createdAt) VALUES (:id,:tanggal,:jenis,:kategori,:nominal,:deskripsi,:unit,:jasa,:detail,:createdAt) ON DUPLICATE KEY UPDATE tanggal=VALUES(tanggal), jenis=VALUES(jenis), kategori=VALUES(kategori), nominal=VALUES(nominal), deskripsi=VALUES(deskripsi), unit=VALUES(unit), jasa=VALUES(jasa), detail_pekerjaan=VALUES(detail_pekerjaan), createdAt=VALUES(createdAt)";
$stmt = $pdo->prepare($insertSql);

$inserted = 0;
$updated = 0;
$skipped = 0;
$errors = 0;

$pdo->beginTransaction();
try {
    foreach ($data as $row) {
        // Normalize
        $id = $row['id'] ?? uniqid('tx_', true);
        $tanggal = $row['tanggal'] ?? (isset($row['createdAt']) ? substr($row['createdAt'], 0, 10) : date('Y-m-d'));
        // validate date format
        $t = DateTime::createFromFormat('Y-m-d', $tanggal) ?: DateTime::createFromFormat('Y-m-d H:i:s', $tanggal);
        if (!$t) {
            if ($verbose) echo "Skipping record with invalid date for id: $id\n";
            $skipped++;
            continue;
        }
        $tanggal = $t->format('Y-m-d');
        $createdAt = $row['createdAt'] ?? date('Y-m-d H:i:s');

        // Data mapping
        $params = [
            ':id' => $id,
            ':tanggal' => $tanggal,
            ':jenis' => $row['jenis'] ?? null,
            ':kategori' => $row['kategori'] ?? null,
            ':nominal' => floatval($row['nominal'] ?? 0),
            ':deskripsi' => $row['deskripsi'] ?? null,
            ':unit' => $row['unit'] ?? null,
            ':jasa' => $row['jasa'] ?? null,
            ':detail' => $row['detail_pekerjaan'] ?? null,
            ':createdAt' => $createdAt,
        ];

        if ($dryRun) {
            if ($verbose) echo "[dry] upsert id=$id tanggal={$params[':tanggal']} nominal={$params[':nominal']}\n";
            $inserted++;
            continue;
        }

        try {
            $stmt->execute($params);
            // Determine if insert or update: use rowCount on UPDATE part is tricky; simpler: check if the row existed before
            // For robust check, attempt to select by id before insert
            // But since we used ON DUPLICATE KEY UPDATE, we can check affectedRows
            $affected = $stmt->rowCount();
            if ($affected === 1) $inserted++; // inserted
            else $updated++; // either updated or 0 when values equal
        } catch (Exception $e) {
            $errors++;
            fwrite(STDERR, "Error inserting id=$id: " . $e->getMessage() . "\n");
        }
    }

    if (!$dryRun) $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    fwrite(STDERR, "Transaction failed: " . $e->getMessage() . "\n");
    exit(5);
}

echo "Done. Inserted: $inserted, Updated: $updated, Skipped: $skipped, Errors: $errors\n";
if ($doBackup && $backupFile) echo "Backup location: $backupFile\n";

exit(0);
