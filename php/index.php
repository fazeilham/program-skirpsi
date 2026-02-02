<?php
session_start();

// Konfigurasi aplikasi
define('APP_NAME', 'Biyai Finance Tracker');
define('APP_DESC', 'Aplikasi Pencatatan Keuangan');
define('APP_AUTHOR', 'Ilham Fahturozi Akbar');
define('APP_NIM', '22101152610489');

// Cek status login
if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .navbar {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        padding: 15px 30px;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        color: white;
    }

    .navbar-brand {
        font-size: 24px;
        font-weight: bold;
    }

    .nav-links {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-left: 20px;
    }

    .nav-links a {
        color: white;
        text-decoration: none;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 14px;
        background: rgba(255, 255, 255, 0.06);
    }

    .nav-links a:hover,
    .nav-links a.active {
        background: rgba(255, 255, 255, 0.14);
    }

    .logout-btn {
        background: #ff6b6b;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        transition: 0.3s;
    }

    .logout-btn:hover {
        background: #ff5252;
    }

    /* Layout: sidebar + main */
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

    .sidebar .navbar-brand {
        font-size: 20px;
        margin-bottom: 12px;
    }

    .sidebar nav a {
        display: block;
        color: white;
        text-decoration: none;
        padding: 10px 12px;
        border-radius: 8px;
        margin-bottom: 8px;
        background: transparent;
    }

    .sidebar nav a:hover,
    .sidebar nav a.active {
        background: rgba(255, 255, 255, 0.12);
    }

    .main {
        flex: 1;
    }

    /* Hamburger & responsive sidebar */
    .hamburger {
        display: none;
        background: transparent;
        border: 2px solid rgba(255, 255, 255, 0.12);
        color: white;
        padding: 8px 10px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 18px;
    }

    .header {
        position: relative
    }

    @media (max-width: 900px) {
        .sidebar {
            position: fixed;
            left: -260px;
            top: 0;
            height: 100vh;
            z-index: 900;
            box-shadow: 2px 0 20px rgba(0, 0, 0, 0.4);
            transition: left .25s ease
        }

        body.sidebar-open .sidebar {
            left: 0
        }

        .hamburger {
            display: block
        }

        .container {
            padding: 12px
        }

        .app {
            align-items: stretch
        }

        .main {
            padding-top: 20px
        }
    }

    .header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 40px 30px;
        border-radius: 24px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .header-content {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 20px;
    }

    .avatar {
        width: 64px;
        height: 64px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
    }

    .header h1 {
        font-size: 28px;
        margin-bottom: 5px;
    }

    .header p {
        font-size: 14px;
        opacity: 0.9;
    }

    .header-subtitle {
        margin-top: 20px;
        font-size: 16px;
        font-weight: 500;
    }

    .credit {
        margin-top: 10px;
        font-size: 14px;
        color: #ffd700;
    }

    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .menu-card {
        background: white;
        padding: 30px;
        border-radius: 16px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .menu-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .menu-icon {
        font-size: 48px;
        margin-bottom: 15px;
    }

    .menu-title {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 8px;
        color: #333;
    }

    .menu-desc {
        font-size: 14px;
        color: #666;
    }

    .menu-card.input-data {
        border-top: 4px solid #3667c1;
    }

    .menu-card.data-stored {
        border-top: 4px solid #127bd2;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="app">
            <aside class="sidebar" aria-hidden="true">
                    <?php $curFile = basename($_SERVER['PHP_SELF']); $curMode = $_GET['mode'] ?? null; ?>
                    <div class="brand"><?php echo APP_NAME; ?></div>
                    <nav>
                        <a href="index.php" class="<?php echo $curFile==='index.php' ? 'active' : ''; ?>">Dashboard</a>
                        <a href="laporan.php?mode=daily" class="<?php echo ($curFile==='laporan.php' && $curMode==='daily') ? 'active' : ''; ?>">Laporan Harian</a>
                        <a href="laporan.php?mode=weekly" class="<?php echo ($curFile==='laporan.php' && $curMode==='weekly') ? 'active' : ''; ?>">Laporan Mingguan</a>
                        <a href="laporan.php?mode=monthly" class="<?php echo ($curFile==='laporan.php' && $curMode==='monthly') ? 'active' : ''; ?>">Laporan Bulanan</a>
                        <a href="about.php" class="<?php echo $curFile==='about.php' ? 'active' : ''; ?>">About</a>
                        <?php if (isset($_SESSION['user_email']) && $_SESSION['user_email'] === 'admin@example.com'): ?>
                            <a href="migrate.php" class="<?php echo $curFile==='migrate.php' ? 'active' : ''; ?>">Migrasi</a>
                        <?php endif; ?>
                    </nav>
                </aside>
            <main class="main">
                <div class="header">
                    <button class="hamburger" aria-label="Toggle menu">☰</button>
                    <form method="POST" action="logout.php" class="header-logout"
                        style="position:absolute;right:20px;top:18px;">
                        <button type="submit" class="logout-btn">Logout</button>
                    </form>
                    <div class="header-content">
                        <div class="avatar">💰</div>
                        <div>
                            <h1><?php echo APP_NAME; ?></h1>
                            <p><?php echo APP_DESC; ?></p>
                        </div>
                    </div>
                    <div class="header-subtitle">Kelola Keuangan Untuk Manajemen Yang Lebih Baik</div>
                    <div class="credit">
                        by <?php echo APP_AUTHOR; ?><br>
                        (<?php echo APP_NIM; ?>)
                    </div>
                </div>

                <div class="menu-grid">
                    <a href="create.php" class="menu-card input-data">
                        <div class="menu-icon">➕</div>
                        <div class="menu-title">Input Data</div>
                        <div class="menu-desc">Tambah data transaksi baru</div>
                    </a>

                    <a href="tampil.php" class="menu-card data-stored">
                        <div class="menu-icon">📋</div>
                        <div class="menu-title">Data Tersimpan</div>
                        <div class="menu-desc">Lihat dan kelola data transaksi</div>
                    </a>
                </div>
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
            var open = document.body.classList.contains('sidebar-open');
            sidebar.setAttribute('aria-hidden', !open);
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