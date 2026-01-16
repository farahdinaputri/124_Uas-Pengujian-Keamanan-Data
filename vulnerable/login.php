<?php
session_start();

// ❌ VULNERABLE: Tidak ada rate limiting
// ❌ VULNERABLE: Password disimpan plain text
// ❌ VULNERABLE: Tidak ada proteksi brute force

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // ❌ VULNERABLE: Password lemah dan plain text
    $valid_username = 'admin';
    $valid_password = '123456';
    
    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;
        $success = 'Login berhasil! (Password sangat lemah: 123456)';
    } else {
        // ❌ VULNERABLE: Tidak ada rate limiting
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
        }
        $_SESSION['login_attempts']++;
        $error = "Login gagal! Percobaan ke-{$_SESSION['login_attempts']} (Tidak ada pembatasan!)";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Vulnerable Version</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 450px;
            width: 100%;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #e74c3c;
            font-size: 28px;
            margin-bottom: 5px;
        }
        .badge {
            display: inline-block;
            background: #e74c3c;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .vulnerability-info {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 5px;
        }
        .vulnerability-info h3 {
            color: #856404;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .vulnerability-info ul {
            color: #856404;
            font-size: 13px;
            margin-left: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            transition: border 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .hint {
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 13px;
            color: #666;
        }
        .hint strong {
            color: #e74c3c;
        }
        .nav-link {
            text-align: center;
            margin-top: 20px;
        }
        .nav-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔓 Login Module</h1>
            <span class="badge">VULNERABLE VERSION</span>
        </div>

        <div class="vulnerability-info">
            <h3>⚠️ KERENTANAN YANG ADA:</h3>
            <ul>
                <li>Tidak ada rate limiting (brute force unlimited)</li>
                <li>Password lemah: 123456</li>
                <li>Tidak ada proteksi account lockout</li>
                <li>Password tidak di-hash</li>
            </ul>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="admin" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="123456" required>
            </div>

            <button type="submit" class="btn">Login</button>
        </form>

        <div class="hint">
            💡 <strong>Untuk Testing:</strong><br>
            Username: admin | Password: 123456<br>
            Coba brute force berkali-kali tanpa batasan!
        </div>

        <div class="nav-link">
            <a href="../index.php">← Kembali ke Menu</a> | 
            <a href="../secure/login.php">Lihat Versi Secure →</a>
        </div>
    </div>
</body>
</html>