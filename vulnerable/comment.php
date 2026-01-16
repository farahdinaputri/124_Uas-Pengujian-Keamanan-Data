<?php
session_start();

// ❌ VULNERABLE: Tidak ada CSRF protection
// ❌ VULNERABLE: Input tidak disanitasi (XSS)

if (!isset($_SESSION['comments'])) {
    $_SESSION['comments'] = [];
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment = $_POST['comment'] ?? '';
    
    // ❌ VULNERABLE: Tidak ada sanitasi input (XSS)
    // ❌ VULNERABLE: Tidak ada CSRF token validation
    
    if (!empty($comment)) {
        $_SESSION['comments'][] = [
            'text' => $comment, // BAHAYA: HTML/Script langsung disimpan!
            'time' => date('H:i:s'),
            'date' => date('d/m/Y')
        ];
        $message = 'Komentar ditambahkan tanpa sanitasi! Rentan XSS';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comments - Vulnerable Version</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffc107;
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
            /* BAHAYA: Ini akan render HTML/Script! */
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
            color: #e74c3c;
            text-decoration: none;
            font-weight: 600;
        }
        .xss-demo {
            background: #ffe5e5;
            border: 2px dashed #e74c3c;
            padding: 10px;
            margin: 15px 0;
            border-radius: 5px;
            font-family: monospace;
            font-size: 13px;
            color: #c0392b;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💬 Comment Module</h1>
            <span class="badge">VULNERABLE VERSION</span>
        </div>

        <div class="vulnerability-info">
            <h3>⚠️ KERENTANAN YANG ADA:</h3>
            <ul>
                <li>Tidak ada CSRF token validation</li>
                <li>Input tidak disanitasi (XSS)</li>
                <li>HTML & JavaScript langsung dirender</li>
                <li>Tidak ada content security policy</li>
            </ul>
        </div>

        <?php if ($message): ?>
            <div class="alert">⚠️ <?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="comment">Tulis Komentar</label>
                <textarea id="comment" name="comment" placeholder="Tulis komentar Anda..." required></textarea>
            </div>

            <button type="submit" class="btn">Kirim Komentar</button>
        </form>

        <div class="xss-demo">
            💀 <strong>Coba XSS Attack:</strong> &lt;script&gt;alert('XSS Attack!')&lt;/script&gt;<br>
            💀 <strong>Atau HTML Injection:</strong> &lt;h1 style="color:red"&gt;HACKED!&lt;/h1&gt;
        </div>

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
                            <!-- ❌ BAHAYA: Render HTML/Script tanpa sanitasi! -->
                            <?= $c['text'] ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="hint">
            💡 <strong>Untuk Testing XSS:</strong><br>
            Coba input: &lt;script&gt;alert('XSS')&lt;/script&gt;<br>
            Atau: &lt;img src=x onerror="alert('XSS')"&gt;<br>
            Script akan langsung dieksekusi!
        </div>

        <div class="nav-link">
            <a href="../index.php">← Kembali ke Menu</a> | 
            <a href="../secure/comment.php">Lihat Versi Secure →</a>
        </div>
    </div>
</body>
</html>