<?php
/**
 * Informasi Aplikasi
 * Halaman ini menampilkan informasi tentang aplikasi
 */

session_start();

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
    <title>Tentang - Biyai Finance Tracker</title>
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

    .navbar {
        background: rgba(0, 0, 0, 0.1);
        padding: 15px 30px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
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

    /* Sidebar layout */
    .app {
        display: flex;
        gap: 20px;
        align-items: flex-start;
    }

    .sidebar {
        width: 220px;
        background: linear-gradient(180deg, #3b82f6, #6366f1);
        padding: 18px;
        border-radius: 12px;
        color: white;
        display: flex;
        flex-direction: column;
    }

    .sidebar .brand {
        font-weight: bold;
        margin-bottom: 10px;
    }

    .sidebar nav a {
        display: block;
        color: white;
        text-decoration: none;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 8px
    }

    .sidebar nav a.active,
    .sidebar nav a:hover {
        background: rgba(255, 255, 255, 0.08)
    }

    .main {
        flex: 1
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

    .container {
        max-width: 800px;
        margin: 40px auto;
        padding: 20px;
    }

    .info-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .info-card h2 {
        color: #667eea;
        margin-bottom: 15px;
        font-size: 24px;
    }

    .info-card p {
        color: #666;
        line-height: 1.6;
        margin-bottom: 10px;
    }

    .info-section {
        background: #f5f5f5;
        padding: 15px;
        border-radius: 8px;
        margin: 10px 0;
        border-left: 4px solid #667eea;
    }

    .info-section strong {
        color: #333;
    }

    .feature-list {
        list-style: none;
        padding: 0;
    }

    .feature-list li {
        padding: 8px 0;
        color: #666;
    }

    .feature-list li:before {
        content: "✅ ";
        color: #4caf50;
        font-weight: bold;
        margin-right: 8px;
    }

    .tech-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }

    .tech-badge {
        background: #f0f0ff;
        border: 1px solid #667eea;
        border-radius: 8px;
        padding: 12px;
        text-align: center;
        color: #667eea;
        font-weight: bold;
        font-size: 14px;
    }

    .footer-text {
        text-align: center;
        color: #666;
        margin-top: 20px;
        font-size: 12px;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="app">
            <aside class="sidebar" aria-hidden="true">
                <?php $curFile = basename($_SERVER['PHP_SELF']); $curMode = $_GET['mode'] ?? null; ?>
                <div class="brand">Menu Laporan</div>
                <nav>
                    <a href="index.php" class="<?php echo $curFile==='index.php' ? 'active' : ''; ?>">Dashboard</a>
                    <a href="laporan.php?mode=daily"
                        class="<?php echo ($curFile==='laporan.php' && $curMode==='daily') ? 'active' : ''; ?>">Laporan
                        Harian</a>
                    <a href="laporan.php?mode=weekly"
                        class="<?php echo ($curFile==='laporan.php' && $curMode==='weekly') ? 'active' : ''; ?>">Laporan
                        Mingguan</a>
                    <a href="laporan.php?mode=monthly"
                        class="<?php echo ($curFile==='laporan.php' && $curMode==='monthly') ? 'active' : ''; ?>">Laporan
                        Bulanan</a>
                    <a href="about.php" class="<?php echo $curFile==='about.php' ? 'active' : ''; ?>">About</a>
                    <?php if (isset($_SESSION['user_email']) && $_SESSION['user_email'] === 'admin@example.com'): ?>
                    <a href="migrate.php" class="<?php echo $curFile==='migrate.php' ? 'active' : ''; ?>">Migrasi</a>
                    <?php endif; ?>
                </nav>
            </aside>
            <main class="main">
                <button class="hamburger" aria-label="Toggle menu">☰</button>
                <div class="info-card">
                    <h2>💰 Biyai Finance Tracker</h2>
                    <p>Aplikasi Pencatatan Keuangan yang dirancang untuk membantu Anda mengelola transaksi keuangan
                        dengan mudah
                        dan efisien.</p>
                </div>

                <div class="info-card">
                    <h2>👨‍💻 Developer Information</h2>
                    <div class="info-section">
                        <strong>Nama:</strong> Ilham Fahturozi Akbar
                    </div>
                    <div class="info-section">
                        <strong>NIM:</strong> 22101152610489
                    </div>
                    <div class="info-section">
                        <strong>Platform:</strong> PHP Native (No Database)
                    </div>
                    <div class="info-section">
                        <strong>Versi:</strong> 1.0.0
                    </div>
                </div>

                <div class="info-card">
                    <h2>✨ Fitur Aplikasi</h2>
                    <ul class="feature-list">
                        <li>Login dengan autentikasi sederhana</li>
                        <li>Tambah transaksi masuk dan keluar</li>
                        <li>Lihat daftar semua transaksi</li>
                        <li>Edit data transaksi yang sudah ada</li>
                        <li>Hapus transaksi yang tidak diperlukan</li>
                        <li>Penyimpanan data dalam JSON file</li>
                        <li>Interface responsif</li>
                    </ul>
                </div>

                <div class="info-card">
                    <h2>🛠️ Teknologi yang Digunakan</h2>
                    <div class="tech-grid">
                        <div class="tech-badge">PHP 7.4+</div>
                        <div class="tech-badge">HTML5</div>
                        <div class="tech-badge">CSS3</div>
                        <div class="tech-badge">JavaScript</div>
                        <div class="tech-badge">JSON</div>
                        <div class="tech-badge">File Storage</div>
                    </div>
                </div>

                <div class="info-card">
                    <h2>📚 Konversi dari Dart</h2>
                    <p>Aplikasi ini adalah konversi dari aplikasi Flutter (Dart) yang menggunakan Firebase ke PHP Native
                        tanpa
                        database eksternal.</p>
                    <div class="info-section">
                        <strong>File Asli Dart:</strong>
                        <ul style="margin-top: 8px; margin-left: 20px;">
                            <li>auth_form.dart</li>
                            <li>create_form.dart</li>
                            <li>edit_form.dart</li>
                            <li>tampil_form.dart</li>
                            <li>login_page.dart</li>
                            <li>main.dart</li>
                            <li>firebase_options.dart</li>
                        </ul>
                    </div>
                </div>

                <div class="info-card">
                    <h2>🔐 Keamanan</h2>
                    <p>⚠️ <strong>Catatan Penting:</strong></p>
                    <p>Aplikasi ini adalah versi demo/pembelajaran. Untuk penggunaan production, diperlukan:</p>
                    <div class="info-section">
                        ✓ Password hashing (bcrypt/Argon2)<br>
                        ✓ Database yang proper (MySQL/PostgreSQL)<br>
                        ✓ HTTPS connection<br>
                        ✓ Input validation & sanitization<br>
                        ✓ CSRF protection<br>
                        ✓ Session security
                    </div>
                </div>

                <div class="info-card">
                    <h2>📝 Penggunaan Akun Demo</h2>
                    <div class="info-section">
                        <strong>Email:</strong> admin@example.com<br>
                        <strong>Password:</strong> admin123
                    </div>
                    <div class="info-section">
                        <strong>Email:</strong> user@example.com<br>
                        <strong>Password:</strong> user123
                    </div>
                </div>

                <div class="info-card">
                    <h2>👥 Pengguna Sistem</h2>
                    <div class="info-section">
                        <strong>Nama Sistem:</strong> Biyai Finance Tracker
                    </div>
                    <div class="info-section">
                        <strong>Jenis Sistem:</strong> Aplikasi Pencatatan Keuangan (PHP Native, File Storage / Optional
                        DB)
                    </div>
                    <div class="info-section">
                        <strong>Pengguna:</strong>
                        <ul style="margin-top:8px;margin-left:20px;">
                            <li><strong>Admin</strong> — akses penuh: CRUD data, melihat laporan, backup</li>
                            <li><strong>User</strong> — akses terbatas: melihat dan menambah transaksi</li>
                        </ul>
                    </div>
                </div>

                <div class="footer-text">
                    <p>© 2024 Biyai Finance Tracker | All Rights Reserved</p>
                    <p>Made with ❤️ by Ilham Fahturozi Akbar</p>
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
<!-- Migration code removed: use the CLI `php scripts/migrate_json_to_db.php` or the admin page `migrate.php` to migrate data safely. -->