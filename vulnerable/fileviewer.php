<?php
session_start();

// ❌ VULNERABLE: Local File Inclusion (LFI)
// ❌ VULNERABLE: Path traversal attack
// ❌ VULNERABLE: Tidak ada validasi path

$file_content = '';
$error = '';
$filepath = $_GET['file'] ?? '';

if (!empty($filepath)) {
    // ❌ VULNERABLE: Langsung baca file tanpa validasi
    if (file_exists($filepath)) {
        $file_content = file_get_contents($filepath);
    } else {
        // Simulasi untuk demo
        if (strpos($filepath, '..') !== false || strpos($filepath, '/etc/passwd') !== false) {
            $file_content = "root:x:0:0:root:/root:/bin/bash
daemon:x:1:1:daemon:/usr/sbin:/usr/sbin/nologin
bin:x:2:2:bin:/bin:/usr/sbin/nologin
sys:x:3:3:sys:/dev:/usr/sbin/nologin
admin:x:1000:1000:Admin User:/home/admin:/bin/bash
database:x:1001:1001:Database User:/var/lib/mysql:/bin/false
⚠️ VULNERABLE! File sistem berhasil diakses via Path Traversal!";
        } else {
            $error = 'File tidak ditemukan atau path tidak valid';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Viewer - Vulnerable Version</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 900px;
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
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            font-family: monospace;
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
        .file-output {
            margin-top: 30px;
            background: #2c3e50;
            color: #ecf0f1;
            padding: 20px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            white-space: pre-wrap;
            word-wrap: break-word;
            max-height: 500px;
            overflow-y: auto;
            box-shadow: inset 0 2px 5px rgba(0,0,0,0.3);
        }
        .file-output.error {
            background: #c0392b;
            color: white;
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
        .examples {
            background: #ffe5e5;
            border: 2px dashed #e74c3c;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .examples h4 {
            color: #c0392b;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .examples code {
            display: block;
            background: white;
            padding: 8px;
            margin: 5px 0;
            border-radius: 3px;
            font-family: monospace;
            font-size: 13px;
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📁 File Viewer Module</h1>
            <span class="badge">VULNERABLE VERSION</span>
        </div>

        <div class="vulnerability-info">
            <h3>⚠️ KERENTANAN YANG ADA:</h3>
            <ul>
                <li>Path Traversal Attack (../../../)</li>
                <li>Local File Inclusion (LFI)</li>
                <li>Tidak ada validasi path</li>
                <li>Bisa akses file sistem sensitif</li>
            </ul>
        </div>

        <div class="examples">
            <h4>💀 Contoh LFI Attack:</h4>
            <code>../../../etc/passwd</code>
            <code>....//....//....//etc/passwd</code>
            <code>/etc/shadow</code>
            <code>../../config/database.php</code>
        </div>

        <form method="GET" action="">
            <div class="form-group">
                <label for="file">File Path (Vulnerable Input)</label>
                <input 
                    type="text" 
                    id="file" 
                    name="file" 
                    placeholder="../../../etc/passwd" 
                    value="<?= htmlspecialchars($filepath) ?>"
                    required
                >
            </div>

            <button type="submit" class="btn">View File</button>
        </form>

        <?php if (!empty($file_content)): ?>
            <div class="file-output">
                <strong>📄 File Content:</strong>
                <hr style="border: 1px solid #34495e; margin: 10px 0;">
                <?= htmlspecialchars($file_content) ?>
            </div>
        <?php elseif (!empty($error)): ?>
            <div class="file-output error">
                ❌ <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="hint">
            💡 <strong>Untuk Testing LFI:</strong><br>
            Coba input: ../../../etc/passwd<br>
            Atau: ....//....//etc/passwd<br>
            File sistem akan bisa diakses!
        </div>

        <div class="nav-link">
            <a href="../index.php">← Kembali ke Menu</a> | 
            <a href="../secure/fileviewer.php">Lihat Versi Secure →</a>
        </div>
    </div>
</body>
</html>