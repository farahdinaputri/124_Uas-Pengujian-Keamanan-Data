<?php
session_start();

// ✅ SECURE: CSRF protection enabled
// ✅ SECURE: Input disanitasi (XSS prevention)

if (!isset($_SESSION['comments'])) {
    $_SESSION['comments'] = [];
}

// ✅ Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment = $_POST['comment'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // ✅ SECURE: Validasi CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        $error = 'CSRF token tidak valid! Request ditolak.';
    } elseif (empty($comment)) {
        $error = 'Komentar tidak boleh kosong!';
    } else {
        // ✅ SECURE: Sanitasi input (XSS prevention)
        $safe_comment = htmlspecialchars($comment, ENT_QUOTES, 'UTF-8');
        
        // ✅ SECURE: Filtering karakter berbahaya
        $safe_comment = strip_tags($safe_comment);
        
        $_SESSION['comments'][] = [
            'text' => $safe_comment,
            'time' => date('H:i:s'),
            'date' => date('d/m/Y')
        ];
        
        $message = 'Komentar ditambahkan dengan aman (sudah disanitasi)!';
        
        // ✅ Regenerate CSRF token setelah submit
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comments - Secure Version</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
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
        .csrf-token-display {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 12px;
            word-break: break-all;
        }
        .csrf-token-display strong {
            color: #856404;
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
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            resize: vertical;
            min-height: 100px;
        }
        .btn {
            padding: 12px 30px;
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
        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .comments-section {
            margin-top: 30px;
        }
        .comments-section h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 22px;
        }
        .comment-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .comment-header {
            font-size: 12px;
            color: #999;
            margin-bottom: 10px;
        }
        .comment-text {
            color: #333;
            font-size: 15px;
            line-height: 1.6;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💬 Comment Module</h1>
            <span class="badge">SECURE VERSION</span>
        </div>

        <div class="security-info">
            <h3>✅ PROTEKSI YANG DITERAPKAN:</h3>
            <ul>
                <li>CSRF token validation</li>
                <li>Input sanitization dengan htmlspecialchars()</li>
                <li>Strip HTML tags dengan strip_tags()</li>
                <li>XSS prevention dengan ENT_QUOTES</li>
            </ul>
        </div>

        <div class="csrf-token-display">
            🔐 <strong>CSRF Token Active:</strong> <?= substr($_SESSION['csrf_token'], 0, 32) ?>...
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success">✅ <?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <div class="form-group">
                <label for="comment">Tulis Komentar</label>
                <textarea id="comment" name="comment" placeholder="Tulis komentar Anda..." required></textarea>
            </div>

            <button type="submit" class="btn">Kirim Komentar</button>
        </form>

        <div class="comments-section">
            <h2>📋 Daftar Komentar (<?= count($_SESSION['comments']) ?>)</h2>
            
            <?php if (empty($_SESSION['comments'])): ?>
                <p style="text-align: center; color: #999;">Belum ada komentar. Jadilah yang pertama!</p>
            <?php else: ?>
                <?php foreach (array_reverse($_SESSION['comments']) as $c): ?>
                    <div class="comment-card">
                        <div class="comment-header">
                            📅 <?= htmlspecialchars($c['date']) ?> • 🕐 <?= htmlspecialchars($c['time']) ?>
                        </div>
                        <div class="comment-text">
                            <!-- ✅ AMAN: Sudah disanitasi, tidak render HTML/Script -->
                            <?= htmlspecialchars($c['text']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="hint">
            💡 <strong>Untuk Testing:</strong><br>
            Coba input XSS: &lt;script&gt;alert('XSS')&lt;/script&gt;<br>
            Script akan di-escape dan tidak dieksekusi!<br>
            CSRF token akan divalidasi setiap submit.
        </div>

        <div class="nav-link">
            <a href="../index.php">← Kembali ke Menu</a> | 
            <a href="../vulnerable/comment.php">Lihat Versi Vulnerable →</a>
        </div>
    </div>
</body>
</html>