<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UAS Keamanan Web - Security Lab</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            color: white;
            margin-bottom: 50px;
            padding: 30px;
        }
        
        .header h1 {
            font-size: 48px;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .header p {
            font-size: 20px;
            opacity: 0.9;
        }
        
        .info-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .info-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .info-card h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 24px;
        }
        
        .info-card ul {
            list-style: none;
            color: #666;
        }
        
        .info-card ul li {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        
        .info-card ul li:last-child {
            border-bottom: none;
        }
        
        .modules-section {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        .section-title {
            text-align: center;
            color: #333;
            font-size: 32px;
            margin-bottom: 40px;
        }
        
        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .module-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .module-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .module-card h3 {
            color: #333;
            font-size: 22px;
            margin-bottom: 10px;
        }
        
        .module-card p {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            transition: transform 0.2s;
            font-size: 14px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn-vulnerable {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }
        
        .btn-secure {
            background: linear-gradient(135deg, #27ae60, #219a52);
            color: white;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            margin-left: 8px;
        }
        
        .badge-red {
            background: #e74c3c;
            color: white;
        }
        
        .badge-green {
            background: #27ae60;
            color: white;
        }
        
        .footer {
            text-align: center;
            color: white;
            margin-top: 40px;
            padding: 20px;
            font-size: 14px;
            opacity: 0.8;
        }
        
        .icon {
            font-size: 40px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Security Lab Demo</h1>
            <p>UAS Keamanan Aplikasi Web - Simulasi Kerentanan & Mitigasi</p>
        </div>

        <div class="info-cards">
            <div class="info-card">
                <h2>🔓 Vulnerable Version</h2>
                <ul>
                    <li>❌ Login tanpa rate limiting</li>
                    <li>❌ Password lemah (123456)</li>
                    <li>❌ XSS pada input komentar</li>
                    <li>❌ Local File Inclusion (LFI)</li>
                    <li>❌ Server-Side Request Forgery (SSRF)</li>
                </ul>
            </div>

            <div class="info-card">
                <h2>🔒 Secure Version</h2>
                <ul>
                    <li>✅ Rate limiting (max 3 attempts)</li>
                    <li>✅ Strong password requirement</li>
                    <li>✅ Input sanitization & CSRF token</li>
                    <li>✅ File whitelist validation</li>
                    <li>✅ URL whitelist untuk fetch</li>
                </ul>
            </div>
        </div>

        <div class="modules-section">
            <h2 class="section-title">📚 Modul Keamanan</h2>
            
            <div class="modules-grid">
                <!-- Login Module -->
                <div class="module-card">
                    <div class="icon">🔑</div>
                    <h3>Login Module</h3>
                    <p>Simulasi Brute Force Attack & Password Security</p>
                    <div class="btn-group">
                        <a href="vulnerable/login.php" class="btn btn-vulnerable">
                            Vulnerable
                        </a>
                        <a href="secure/login.php" class="btn btn-secure">
                            Secure
                        </a>
                    </div>
                </div>

                <!-- Comment Module -->
                <div class="module-card">
                    <div class="icon">💬</div>
                    <h3>Comment Module</h3>
                    <p>Cross-Site Scripting (XSS) & CSRF Protection</p>
                    <div class="btn-group">
                        <a href="vulnerable/comment.php" class="btn btn-vulnerable">
                            Vulnerable
                        </a>
                        <a href="secure/comment.php" class="btn btn-secure">
                            Secure
                        </a>
                    </div>
                </div>

                <!-- File Viewer Module -->
                <div class="module-card">
                    <div class="icon">📁</div>
                    <h3>File Viewer</h3>
                    <p>Local File Inclusion (LFI) Vulnerability</p>
                    <div class="btn-group">
                        <a href="vulnerable/fileviewer.php" class="btn btn-vulnerable">
                            Vulnerable
                        </a>
                        <a href="secure/fileviewer.php" class="btn btn-secure">
                            Secure
                        </a>
                    </div>
                </div>

                <!-- SSRF Module -->
                <div class="module-card">
                    <div class="icon">🌐</div>
                    <h3>URL Fetch</h3>
                    <p>Server-Side Request Forgery (SSRF) Attack</p>
                    <div class="btn-group">
                        <a href="vulnerable/ssrf.php" class="btn btn-vulnerable">
                            Vulnerable
                        </a>
                        <a href="secure/ssrf.php" class="btn btn-secure">
                            Secure
                        </a>
                    </div>
                </div>
            </div>

            <div style="text-align: center; margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 10px;">
                <h3 style="color: #333; margin-bottom: 10px;">📋 Informasi Project</h3>
                <p style="color: #666; font-size: 14px;">
                    Project ini dibuat untuk simulasi pengujian keamanan aplikasi web.<br>
                    Semua kerentanan sengaja dibuat untuk tujuan pembelajaran.<br>
                    <strong style="color: #e74c3c;">⚠️ HANYA untuk testing di localhost!</strong>
                </p>
            </div>
        </div>

        <div class="footer">
            <p>🎓 UAS Keamanan Aplikasi Web</p>
            <p>CPL-041, CPL-081, CPL-082 / CPMK041, CPMK081, CPMK082</p>
        </div>
    </div>
</body>
</html>