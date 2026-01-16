<?php
session_start();

// ❌ VULNERABLE: Server-Side Request Forgery (SSRF)
// ❌ VULNERABLE: Tidak ada URL validation
// ❌ VULNERABLE: Bisa akses internal network

$response = '';
$error = '';
$url = $_POST['url'] ?? '';

if (!empty($url)) {
    // ❌ VULNERABLE: Langsung fetch URL tanpa validasi
    // Simulasi SSRF untuk demo
    
    if (strpos($url, 'localhost') !== false || strpos($url, '127.0.0.1') !== false) {
        // Simulasi akses ke internal service
        $response = "HTTP/1.1 200 OK
Content-Type: application/json

{
  \"status\": \"success\",
  \"message\": \"Internal Admin Panel\",
  \"data\": {
    \"admin_access\": true,
    \"database\": \"mysql://localhost:3306\",
    \"redis\": \"redis://127.0.0.1:6379\",
    \"internal_api\": \"http://localhost:8080/api\",
    \"users\": [
      {\"id\": 1, \"username\": \"admin\", \"role\": \"superadmin\"},
      {\"id\": 2, \"username\": \"dbadmin\", \"role\": \"database_admin\"}
    ]
  }
}

⚠️ VULNERABLE! Berhasil akses internal network via SSRF!";
    } elseif (strpos($url, '192.168.') !== false || strpos($url, '10.0.') !== false) {
        $response = "⚠️ SSRF Attack berhasil! Akses ke private IP network: {$url}
Ini bisa digunakan untuk port scanning internal network!";
    } else {
        // Simulasi fetch normal URL
        $response = "Fetching URL: {$url}

HTTP/1.1 200 OK
Content-Type: text/html

<html>
  <body>Content from external URL...</body>
</html>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL Fetch - Vulnerable Version</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
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
        .response-output {
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
        .response-output.error {
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🌐 URL Fetch Module</h1>
            <span class="badge">VULNERABLE VERSION</span>
        </div>

        <div class="vulnerability-info">
            <h3>⚠️ KERENTANAN YANG ADA:</h3>
            <ul>
                <li>Server-Side Request Forgery (SSRF)</li>
                <li>Tidak ada URL validation</li>
                <li>Bisa akses localhost/internal network</li>
                <li>Bisa digunakan untuk port scanning</li>
            </ul>
        </div>

        <div class="examples">
            <h4>💀 Contoh SSRF Attack:</h4>
            <code>http://localhost:8080/admin</code>
            <code>http://127.0.0.1:3306</code>
            <code>http://192.168.1.1/router-config</code>
            <code>http://169.254.169.254/latest/meta-data/</code>
        </div>

        <form method="POST" action="">
            <div class="form-group">
                <label for="url">URL to Fetch (Vulnerable Input)</label>
                <input 
                    type="text" 
                    id="url" 
                    name="url" 
                    placeholder="http://localhost:8080/admin" 
                    value="<?= htmlspecialchars($url) ?>"
                    required
                >
            </div>

            <button type="submit" class="btn">Fetch URL</button>
        </form>

        <?php if (!empty($response)): ?>
            <div class="response-output">
                <strong>📥 Server Response:</strong>
                <hr style="border: 1px solid #34495e; margin: 10px 0;">
                <?= htmlspecialchars($response) ?>
            </div>
        <?php elseif (!empty($error)): ?>
            <div class="response-output error">
                ❌ <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="hint">
            💡 <strong>Untuk Testing SSRF:</strong><br>
            Coba URL: http://localhost:8080/admin<br>
            Atau: http://127.0.0.1:3306<br>
            Internal service akan bisa diakses!
        </div>

        <div class="nav-link">
            <a href="../index.php">← Kembali ke Menu</a> | 
            <a href="../secure/ssrf.php">Lihat Versi Secure →</a>
        </div>
    </div>
</body>
</html>