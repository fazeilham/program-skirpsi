<?php
/**
 * Reusable migration helper: migrate JSON -> transaksi table
 * Returns array with summary and logs
 */
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../config.php';

function migrate_json_to_db(array $options = []) {
    $doBackup = !empty($options['backup']);
    $dryRun = !empty($options['dry_run']);
    $truncate = !empty($options['truncate']);
    $createTable = !empty($options['create_table']);
    $verbose = !empty($options['verbose']);

    $logs = [];
    $jsonFile = DATA_FILE;
    if (!file_exists($jsonFile)) {
        $logs[] = "Error: data file not found: $jsonFile";
        return ['ok' => false, 'logs' => $logs];
    }

    // Backup
    $backupFile = null;
    if ($doBackup) {
        $backupDir = dirname($jsonFile) . '/backups';
        if (!is_dir($backupDir)) mkdir($backupDir, 0755, true);
        $ts = date('Ymd_His');
        $backupFile = "$backupDir/transaksi_backup_$ts.json";
        if (!copy($jsonFile, $backupFile)) {
            $logs[] = "Warning: failed to create backup to $backupFile";
        } else {
            $logs[] = "Backup created: $backupFile";
        }
    }

    $data = json_decode(file_get_contents($jsonFile), true);
    if (!is_array($data)) {
        $logs[] = "Error: failed to parse JSON or JSON is not an array";
        return ['ok' => false, 'logs' => $logs];
    }

    $pdo = get_db();

    // Optionally create table if missing
    $check = $pdo->query("SHOW TABLES LIKE 'transaksi'")->fetch();
    if (!$check) {
        if ($createTable) {
            $logs[] = "transaksi table not found, creating minimal table...";
            $sqlCreate = "CREATE TABLE IF NOT EXISTS `transaksi` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            if (!$dryRun) $pdo->exec($sqlCreate);
            $logs[] = "created transaksi table.";
        } else {
            $logs[] = "Error: transaksi table does not exist. Use create_table option or create schema first.";
            return ['ok' => false, 'logs' => $logs];
        }
    }

    if ($truncate) {
        $logs[] = $dryRun ? "(dry-run) Would truncate transaksi table" : "Truncating transaksi table...";
        if (!$dryRun) $pdo->exec('TRUNCATE TABLE transaksi');
    }

    $insertSql = "INSERT INTO transaksi (id,tanggal,jenis,kategori,nominal,deskripsi,unit,jasa,detail_pekerjaan,createdAt) VALUES (:id,:tanggal,:jenis,:kategori,:nominal,:deskripsi,:unit,:jasa,:detail,:createdAt) ON DUPLICATE KEY UPDATE tanggal=VALUES(tanggal), jenis=VALUES(jenis), kategori=VALUES(kategori), nominal=VALUES(nominal), deskripsi=VALUES(deskripsi), unit=VALUES(unit), jasa=VALUES(jasa), detail_pekerjaan=VALUES(detail_pekerjaan), createdAt=VALUES(createdAt)";
    $stmt = $pdo->prepare($insertSql);

    $inserted = $updated = $skipped = $errors = 0;

    // Start transaction
    if (!$dryRun) $pdo->beginTransaction();
    try {
        foreach ($data as $row) {
            $id = $row['id'] ?? uniqid('tx_', true);
            $tanggal = $row['tanggal'] ?? (isset($row['createdAt']) ? substr($row['createdAt'], 0, 10) : date('Y-m-d'));
            $t = DateTime::createFromFormat('Y-m-d', $tanggal) ?: DateTime::createFromFormat('Y-m-d H:i:s', $tanggal);
            if (!$t) { $logs[] = "Skipping record with invalid date for id: $id"; $skipped++; continue; }
            $tanggal = $t->format('Y-m-d');
            $createdAt = $row['createdAt'] ?? date('Y-m-d H:i:s');

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
                if ($verbose) $logs[] = "[dry] upsert id=$id tanggal={$params[':tanggal']} nominal={$params[':nominal']}";
                $inserted++; continue;
            }

            try {
                $stmt->execute($params);
                $affected = $stmt->rowCount();
                if ($affected === 1) $inserted++; else $updated++;
            } catch (Exception $e) {
                $errors++; $logs[] = "Error inserting id=$id: " . $e->getMessage();
            }
        }

        if (!$dryRun) $pdo->commit();
    } catch (Exception $e) {
        if (!$dryRun) $pdo->rollBack();
        $logs[] = "Transaction failed: " . $e->getMessage();
        return ['ok' => false, 'logs' => $logs];
    }

    $logs[] = "Done. Inserted: $inserted, Updated: $updated, Skipped: $skipped, Errors: $errors";
    if ($doBackup && $backupFile) $logs[] = "Backup location: $backupFile";

    return ['ok' => true, 'logs' => $logs, 'inserted'=>$inserted,'updated'=>$updated,'skipped'=>$skipped,'errors'=>$errors, 'backup'=>$backupFile];
}

?>