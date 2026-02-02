<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit();
}
require_once __DIR__ . '/inc/reports.php';

$mode = $_GET['mode'] ?? 'daily'; // daily, weekly, monthly
$valid = ['daily','weekly','monthly'];
if (!in_array($mode, $valid)) $mode = 'daily';

$start = $_GET['start'] ?? null;
$end = $_GET['end'] ?? null;

$transactions = load_transactions();
$groups = summarize_transactions($transactions, $mode, $start, $end);

// export CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    csv_download_response("laporan_{$mode}.csv", $groups);
}

function mode_label($m) {
    return $m === 'daily' ? 'Harian' : ($m === 'weekly' ? 'Mingguan' : 'Bulanan');
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Laporan - <?php echo mode_label($mode); ?> - Biyai Finance Tracker</title>
    <style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        color: #222
    }

    .container {
        max-width: 1100px;
        margin: 30px auto;
        padding: 20px;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 12px
    }

    .app {
        display: flex;
        gap: 20px;
        align-items: flex-start;
    }

    .sidebar {
        width: 220px;
        background: linear-gradient(180deg, #3b82f6, #6366f1);
        padding: 20px;
        border-radius: 12px;
        color: white;
        display: flex;
        flex-direction: column;
        min-height: 320px;
    }

    .sidebar .brand {
        font-weight: bold;
        margin-bottom: 12px;
    }

    .sidebar nav a {
        display: block;
        color: white;
        text-decoration: none;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 8px;
        background: transparent;
    }

    .sidebar nav a.active,
    .sidebar nav a:hover {
        background: rgba(255, 255, 255, 0.12);
    }

    .main {
        flex: 1;
    }

    .hamburger {
        display: none;
        background: transparent;
        border: 2px solid rgba(0, 0, 0, 0.08);
        padding: 8px 10px;
        border-radius: 8px;
        cursor: pointer
    }

    @media (max-width:900px) {
        .hamburger {
            display: inline-block
        }

        .sidebar {
            position: fixed;
            left: -260px;
            top: 0;
            height: 100vh;
            z-index: 900;
            transition: left .25s ease
        }

        body.sidebar-open .sidebar {
            left: 0
        }
    }

    .controls {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-bottom: 15px
    }

    table {
        width: 100%;
        border-collapse: collapse
    }

    th,
    td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: left
    }

    th {
        background: #f4f6fb
    }

    .period-row td {
        background: #f9fbff;
        font-weight: 600;
    }

    .sub-header th {
        background: #eef3ff;
        font-weight: 600;
    }

    .btn {
        padding: 8px 12px;
        border-radius: 8px;
        border: none;
        cursor: pointer
    }

    .btn-primary {
        background: #667eea;
        color: white
    }

    .btn-muted {
        background: #f0f0f0
    }

    /* print styles */
    @media print {

        .controls,
        .nav-links,
        .back-link {
            display: none;
        }

        body {
            background: white
        }

        .container {
            box-shadow: none;
            background: white
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="app">
            <aside class="sidebar" aria-hidden="true">
                <div class="brand">Biyai Finance Tracker</div>
                <nav>
                    <a href="index.php"
                        class="<?php echo basename($_SERVER['PHP_SELF'])==='index.php' ? 'active' : ''; ?>">Dashboard</a>
                    <a href="laporan.php?mode=daily" class="<?php echo $mode==='daily'?'active':''; ?>">Laporan
                        Harian</a>
                    <a href="laporan.php?mode=weekly" class="<?php echo $mode==='weekly'?'active':''; ?>">Laporan
                        Mingguan</a>
                    <a href="laporan.php?mode=monthly" class="<?php echo $mode==='monthly'?'active':''; ?>">Laporan
                        Bulanan</a>
                    <a href="about.php"
                        class="<?php echo basename($_SERVER['PHP_SELF'])==='about.php' ? 'active' : ''; ?>">About</a>
                    <?php if (isset($_SESSION['user_email']) && $_SESSION['user_email'] === 'admin@example.com'): ?>
                        <a href="migrate.php" class="<?php echo basename($_SERVER['PHP_SELF'])==='migrate.php' ? 'active' : ''; ?>">Migrasi</a>
                    <?php endif; ?>
                </nav>
            </aside>
            <main class="main">
                <button class="hamburger" aria-label="Toggle menu">☰</button>

                <h2>Laporan <?php echo mode_label($mode); ?></h2>
                <div class="controls">
                    <form method="GET" style="display:flex;gap:8px;align-items:center;">
                        <input type="hidden" name="mode" value="<?php echo htmlspecialchars($mode); ?>">
                        <label>Start: <input type="date" name="start"
                                value="<?php echo htmlspecialchars($start); ?>"></label>
                        <label>End: <input type="date" name="end" value="<?php echo htmlspecialchars($end); ?>"></label>
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </form>

                    <div style="margin-left:auto;display:flex;gap:8px;">
                        <a href="laporan.php?mode=<?php echo $mode;?>&export=csv<?php echo $start?('&start='.urlencode($start)):'';?><?php echo $end?('&end='.urlencode($end)):'';?>"
                            class="btn btn-muted">Export CSV</a>
                        <button class="btn btn-primary" onclick="window.print()">Cetak / Save as PDF</button>
                    </div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Periode / Detail</th>
                            <th>Jumlah Transaksi</th>
                            <th>Total Nominal (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($groups)): ?>
                        <tr>
                            <td colspan="6">Tidak ada data untuk periode ini.</td>
                        </tr>
                        <?php else: ?>

                        <?php if ($mode === 'daily'): ?>

                        <?php foreach ($groups as $g): ?>
                        <tr class="period-row">
                            <td colspan="6"><strong><?php echo htmlspecialchars($g['label']); ?></strong> —
                                <?php echo $g['count']; ?> transaksi — Total:
                                <?php echo number_format($g['total'], 0, ',', '.'); ?></td>
                        </tr>

                        <tr class="sub-header">
                            <th>Tanggal</th>
                            <th>Unit</th>
                            <th>Deskripsi</th>
                            <th>Jenis</th>
                            <th>Kategori</th>
                            <th>Nominal (Rp)</th>
                        </tr>

                        <?php foreach ($g['items'] as $tx): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($tx['tanggal'] ?? substr($tx['createdAt'] ?? '',0,10)); ?>
                            </td>
                            <td><?php echo htmlspecialchars($tx['unit'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($tx['deskripsi'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($tx['jenis'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($tx['kategori'] ?? '-'); ?></td>
                            <td><?php echo number_format(floatval($tx['nominal'] ?? 0), 0, ',', '.'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endforeach; ?>

                        <?php else: ?>

                        <?php foreach ($groups as $g): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($g['label']); ?></td>
                            <td><?php echo $g['count']; ?></td>
                            <td><?php echo number_format($g['total'], 0, ',', '.'); ?></td>
                        </tr>
                        <?php endforeach; ?>

                        <?php endif; ?>

                        <?php endif; ?>
                    </tbody>
                </table>

                <div style="margin-top:20px;color:#555;font-size:13px">Tip: Gunakan tombol <strong>"Cetak / Save as
                        PDF"</strong> untuk menyimpan laporan ke PDF menggunakan fitur print browser.</div>

            </main>
        </div>
    </div>

    <script>
    (function() {
        var btn = document.querySelector('.hamburger');
        var sidebar = document.querySelector('.sidebar');
        if (!btn || !sidebar) return;
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            document.body.classList.toggle('sidebar-open');
            sidebar.setAttribute('aria-hidden', !document.body.classList.contains('sidebar-open'));
        });
        document.addEventListener('click', function(e) {
            if (!sidebar.contains(e.target) && !btn.contains(e.target) && document.body.classList.contains(
                    'sidebar-open')) {
                document.body.classList.remove('sidebar-open');
                sidebar.setAttribute('aria-hidden', 'true');
            }
        });
    })();
    </script>
</body>

</html>