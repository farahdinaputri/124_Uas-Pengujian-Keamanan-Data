<?php
session_start();

// ✅ SECURE: Rate limiting implemented
// ✅ SECURE: Password di-hash dengan bcrypt
// ✅ SECURE: Account lockout setelah 3 percobaan

$error = '';
$success = '';

// Rate limiting: max 3 percobaan dalam 15 menit
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['lockout_time'] = null;
}

// Cek apakah akun terkunci
if ($_SESSION['lockout_time'] && time() < $_SESSION['lockout_time']) {
    $remaining = $_SESSION['lockout_time'] - time();
    $minutes = floor($remaining / 60);
    $seconds = $remaining % 60;
    $error = "Akun terkunci! Coba lagi dalam {$minutes}m {$seconds}s";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // ✅ SECURE: Password kuat dan di-hash
    $valid_username = 'admin';
    // Password asli: Admin@2024!SecurePass
    $valid_password_hash = password_hash('Admin@2024!SecurePass', PASSWORD_BCRYPT);
    
    // ✅ SECURE: Validasi password kuat
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        $error = 'Password harus minimal 8 karakter dengan huruf besar, kecil, angka, dan simbol!';
    } elseif ($username === $valid_username && password_verify($password, $valid_password_hash)) {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['login_attempts'] = 0;
        $_SESSION['lockout_time'] = null;
        $success = 'Login berhasil dengan password yang kuat!';
    } else {
        $_SESSION['login_attempts']++;
        
        // ✅ SECURE: Lock account setelah 3 percobaan gagal
        if ($_SESSION['login_attempts'] >= 3) {
            $_SESSION['lockout_time'] = time() + (15 * 60); // 15 menit
            $error = 'Terlalu banyak percobaan gagal! Akun terkunci selama 15 menit.';
        } else {
            $remaining = 3 - $_SESSION['login_attempts'];
            $error = "Login gagal! Sisa percobaan: {$remaining}";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Secure Version</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
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
            color: #27ae60;
            font-size: 28px;
            margin-bottom: 5px;
        }
        .badge {
            display: inline-block;
            background: #27ae60;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .security-info {
            background: #d1ecf1;
            border-left: 4px solid #17a2b8;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 5px;
        }
        .security-info h3 {
            color: #0c5460;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .security-info ul {
            color: #0c5460;
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
            border-color: #27ae60;
        }
        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #27ae60, #219a52);
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
        .btn:disabled {
            background: #95a5a6;
            cursor: not-allowed;
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
            color: #27ae60;
        }
        .nav-link {
            text-align: center;
            margin-top: 20px;
        }
        .nav-link a {
            color: #27ae60;
            text-decoration: none;
            font-weight: 600;
        }
        .attempts-indicator {
            text-align: center;
            margin-top: 15px;
            padding: 10px;
            background: #fff3cd;
            border-radius: 5px;
            font-size: 13px;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔒 Login Module</h1>
            <span class="badge">SECURE VERSION</span>
        </div>

        <div class="security-info">
            <h3>✅ PROTEKSI YANG DITERAPKAN:</h3>
            <ul>
                <li>Rate limiting: maksimal 3 percobaan</li>
                <li>Password kuat wajib (min 8 char, huruf besar/kecil, angka, simbol)</li>
                <li>Account lockout 15 menit setelah 3x gagal</li>
                <li>Password di-hash dengan bcrypt</li>
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
                <input type="password" id="password" name="password" placeholder="Password kuat" required>
            </div>

            <button type="submit" class="btn" <?= ($_SESSION['lockout_time'] && time() < $_SESSION['lockout_time']) ? 'disabled' : '' ?>>
                Login
            </button>
        </form>

        <?php if ($_SESSION['login_attempts'] > 0 && !$success && ($_SESSION['login_attempts'] < 3 || !$_SESSION['lockout_time'])): ?>
            <div class="attempts-indicator">
                ⚠️ Percobaan login: <?= $_SESSION['login_attempts'] ?>/3
            </div>
        <?php endif; ?>

        <div class="hint">
            💡 <strong>Untuk Testing:</strong><br>
            Username: admin<br>
            Password: Admin@2024!SecurePass<br>
            Max 3 percobaan, lalu terkunci 15 menit!
        </div>

        <div class="nav-link">
            <a href="../index.php">← Kembali ke Menu</a> | 
            <a href="../vulnerable/login.php">Lihat Versi Vulnerable →</a>
        </div>
    </div>
</body>
</html>