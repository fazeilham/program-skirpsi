<?php
session_start();

// Jika sudah login, redirect ke halaman utama
if (isset($_SESSION['user_email'])) {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    // Validasi input
    if (empty($email)) {
        $error = 'Username/Email tidak boleh kosong';
    } elseif (empty($password)) {
        $error = 'Password tidak boleh kosong';
    } else {
        // Validasi login (tanpa database, menggunakan array hardcoded)
        $users = [
            ['email' => 'admin', 'password' => 'admin123'],
            ['email' => 'user', 'password' => 'user123'],
        ];
        
        $user_found = false;
        foreach ($users as $user) {
            if ($user['email'] === $email && $user['password'] === $password) {
                $_SESSION['user_email'] = $email;
                $user_found = true;
                header('Location: index.php');
                exit();
            }
        }
        
        if (!$user_found) {
            $error = 'Email atau password salah';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Biyai Finance Tracker</title>
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
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-container {
        background: white;
        border-radius: 24px;
        padding: 48px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        width: 100%;
        max-width: 400px;
    }

    .login-header {
        text-align: center;
        margin-bottom: 32px;
    }

    .avatar {
        width: 72px;
        height: 72px;
        background: #f0f0ff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 36px;
    }

    .login-header h1 {
        font-size: 28px;
        font-weight: bold;
        color: #4609e7;
        margin-bottom: 8px;
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
    input[type="password"] {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #ddd;
        border-radius: 12px;
        font-size: 16px;
        background: white;
        transition: 0.3s;
    }

    input[type="text"]:focus,
    input[type="password"]:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .error-message {
        background: #ffe0e0;
        color: #d32f2f;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 16px;
        font-size: 14px;
    }

    .success-message {
        background: #e0ffe0;
        color: #00b300;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 16px;
        font-size: 14px;
    }

    .login-button {
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, #bdf1e0, #bdf1e0);
        color: #333;
        border: none;
        border-radius: 12px;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
        margin-top: 24px;
    }

    .login-button:hover {
        opacity: 0.9;
        transform: translateY(-2px);
    }

    .demo-info {
        background: #f5f5f5;
        padding: 16px;
        border-radius: 12px;
        margin-top: 24px;
        font-size: 12px;
        color: #666;
    }

    .demo-info strong {
        display: block;
        margin-bottom: 8px;
        color: #333;
    }

    .demo-account {
        background: white;
        padding: 8px 12px;
        border-radius: 6px;
        margin-bottom: 8px;
        font-family: monospace;
    }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <div class="avatar">🔒</div>
            <h1>Login</h1>
        </div>

        <?php if ($error): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="email">Username/Email</label>
                <input type="text" id="email" name="email" placeholder="Masukkan username atau email" required
                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password" required>
            </div>

            <button type="submit" name="login" class="login-button">Login</button>
        </form>

        <div class="demo-info">
            <strong>Demo Account (Akun Demo):</strong>
            <div class="demo-account">
                Email: admin@example.com<br>
                Password: admin123
            </div>
            <div class="demo-account">
                Email: user@example.com<br>
                Password: user123
            </div>
        </div>
    </div>
</body>

</html>