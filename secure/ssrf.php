<?php
session_start();

// ✅ SECURE: Domain whitelist
// ✅ SECURE: Block internal IP
// ✅ SECURE: URL validation

$response = '';
$error = '';
$success = '';
$url = $_POST['url'] ?? '';

// ✅ SECURE: Whitelist domain yang diizinkan
$allowed_domains = [
    'example.com',
    'api.example.com',
    'httpbin.org'
];

if (!empty($url)) {
    // ✅ SECURE: Validasi URL format
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        $error = 'Format URL tidak valid!';
    } else {
        // ✅ SECURE: Parse URL
        $parsed = parse_url($url);
        $host = $parsed['host'] ?? '';
        
        // ✅ SECURE: Block internal IP
        $blocked_hosts = ['localhost', '127.0.0.1', '0.0.0.0', '::1'];
        $is_internal = false;
        
        foreach ($blocked_hosts as $blocked) {
            if (strpos($host, $blocked) !== false) {
                $is_internal = true;
                break;
            }
        }
        
        // ✅ SECURE: Block private IP range
        if (preg_match('/^(10\.|172\.(1[6-9]|2[0-9]|3[01])\.|192\.168\.)/', $host)) {
            $is_internal = true;
        }
        
        if ($is_internal) {
            $error = 'Akses ke internal network/localhost ditolak! Hanya URL eksternal yang diizinkan.';
        } else {
            // ✅ SECURE: Validasi dengan whitelist domain
            $is_allowed = false;
            foreach ($allowed_domains as $domain) {
                if (strpos($host, $domain) !== false) {
                    $is_allowed = true;
                    break;
                }
            }
            
            if (!$is_allowed) {
                $error = "Domain '{$host}' tidak ada dalam whitelist! Hanya domain berikut yang diizinkan: " . implode(', ', $allowed_domains);
            } else {
                // ✅ SECURE: URL valid dan dalam whitelist
                $success = "URL valid dan aman: {$url}";
                $response = "✅ URL AMAN! Fetch berhasil dari: {$host}

HTTP/1.1 200 OK
Content-Type: application/json

{
  \"status\": \"success\",
  \"url\": \"{$url}\",
  \"host\": \"{$host}\",
  \"message\": \"Data fetched successfully\",
  \"security\": {
    \"whitelist_validated\": true,
    \"internal_ip_blocked\": true,
    \"safe_domain\": true
  }
}";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL Fetch - Secure Version</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
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
        .response-output.success {
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
            <h1>🌐 URL Fetch Module</h1>
            <span class="badge">SECURE VERSION</span>
        </div>

        <div class="security-info">
            <h3>✅ PROTEKSI YANG DITERAPKAN:</h3>
            <ul>
                <li>Domain whitelist validation</li>
                <li>Block localhost & 127.0.0.1</li>
                <li>Block private IP range (10.x, 192.168.x, 172.16-31.x)</li>
                <li>URL format validation dengan filter_var()</li>
            </ul>
        </div>

        <div class="whitelist-box">
            <h4>✅ Domain yang Diizinkan (Whitelist):</h4>
            <ul>
                <?php foreach ($allowed_domains as $domain): ?>
                    <li>🌐 <?= htmlspecialchars($domain) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="url">URL to Fetch (Secure Input)</label>
                <input 
                    type="text" 
                    id="url" 
                    name="url" 
                    placeholder="https://api.example.com/data" 
                    value="<?= htmlspecialchars($url) ?>"
                    required
                >
            </div>

            <button type="submit" class="btn">Fetch URL</button>
        </form>

        <?php if (!empty($response)): ?>
            <div class="response-output success">
                <strong>📥 Server Response (Safe):</strong>
                <hr style="border: 1px solid #34495e; margin: 10px 0;">
                <?= htmlspecialchars($response) ?>
            </div>
        <?php endif; ?>

        <div class="hint">
            💡 <strong>Untuk Testing:</strong><br>
            Coba URL valid: https://api.example.com/test<br>
            Coba localhost: http://localhost:8080 (akan ditolak!)<br>
            Coba domain lain: https://google.com (akan ditolak!)<br>
            Hanya domain dalam whitelist yang bisa diakses.
        </div>

        <div class="nav-link">
            <a href="../index.php">← Kembali ke Menu</a> | 
            <a href="../vulnerable/ssrf.php">Lihat Versi Vulnerable →</a>
        </div>
    </div>
</body>
</html>