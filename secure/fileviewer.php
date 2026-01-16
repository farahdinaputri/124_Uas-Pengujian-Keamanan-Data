<?php
session_start();

// ✅ SECURE: Whitelist file validation
// ✅ SECURE: Path validation dengan basename
// ✅ SECURE: File disimpan di direktori aman

$file_content = '';
$error = '';
$success = '';
$filepath = $_GET['file'] ?? '';

// ✅ SECURE: Whitelist file yang diizinkan
$allowed_files = [
    'document.txt' => 'Dokumen teks berisi informasi umum...',
    'report.pdf' => '[PDF Content] Laporan Bulanan Q4 2024...',
    'notes.md' => '# Catatan Penting

## Meeting Notes
- Diskusi project security
- Review vulnerability assessment
- Plan mitigasi keamanan'
];

if (!empty($filepath)) {
    // ✅ SECURE: Extract filename only (hindari path traversal)
    $filename = basename($filepath);
    
    // ✅ SECURE: Validasi dengan whitelist
    if (array_key_exists($filename, $allowed_files)) {
        $file_content = $allowed_files[$filename];
        $success = "File berhasil dimuat: {$filename}";
    } else {
        $error = 'File tidak ditemukan dalam whitelist! Hanya file tertentu yang diizinkan.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Viewer - Secure Version</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
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
        .whitelist-box {
            background: #d4edda;
            border: 2px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .whitelist-box h4 {
            color: #155724;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .whitelist-box ul {
            list-style: none;
            padding: 0;
        }
        .whitelist-box li {
            background: white;
            padding: 8px 12px;
            margin: 5px 0;
            border-radius: 3px;
            font-family: monospace;
            font-size: 13px;
            color: #155724;
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
        .file-output.success {
            border: 3px solid #27ae60;
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
            <h1>📁 File Viewer Module</h1>
            <span class="badge">SECURE VERSION</span>
        </div>

        <div class="security-info">
            <h3>✅ PROTEKSI YANG DITERAPKAN:</h3>
            <ul>
                <li>File whitelist (hanya file tertentu)</li>
                <li>Path validation dengan basename()</li>
                <li>Tidak ada direct file system access</li>
                <li>File content dari database/array aman</li>
            </ul>
        </div>

        <div class="whitelist-box">
            <h4>✅ File yang Diizinkan (Whitelist):</h4>
            <ul>
                <?php foreach (array_keys($allowed_files) as $file): ?>
                    <li>📄 <?= htmlspecialchars($file) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="GET" action="">
            <div class="form-group">
                <label for="file">File Name (Secure Input)</label>
                <input 
                    type="text" 
                    id="file" 
                    name="file" 
                    placeholder="document.txt" 
                    value="<?= htmlspecialchars($filepath) ?>"
                    required
                >
            </div>

            <button type="submit" class="btn">View File</button>
        </form>

        <?php if (!empty($file_content)): ?>
            <div class="file-output success">
                <strong>📄 File Content (Safe):</strong>
                <hr style="border: 1px solid #34495e; margin: 10px 0;">
                <?= htmlspecialchars($file_content) ?>
            </div>
        <?php endif; ?>

        <div class="hint">
            💡 <strong>Untuk Testing:</strong><br>
            Coba file: document.txt, report.pdf, notes.md<br>
            Coba path traversal: ../../../etc/passwd (akan ditolak!)<br>
            Hanya file dalam whitelist yang bisa diakses.
        </div>

        <div class="nav-link">
            <a href="../index.php">← Kembali ke Menu</a> | 
            <a href="../vulnerable/fileviewer.php">Lihat Versi Vulnerable →</a>
        </div>
    </div>
</body>
</html>