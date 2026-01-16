# 🔐 UAS Keamanan Aplikasi Web - Security Lab Demo

**Nama:** Farahdina Nayla Putri 
**NIM:** C2C023124
**Mata Kuliah:** Keamanan Aplikasi Web  
**Dosen:** [Dr. Dhendra Marutho, S.Kom., M.Kom]

---

## 📋 Deskripsi Project

Project ini merupakan aplikasi web simulasi untuk demonstrasi kerentanan keamanan dan teknik mitigasinya. Terdiri dari dua versi:
- **Vulnerable Version** (`/vulnerable`) - Versi dengan kerentanan keamanan
- **Secure Version** (`/secure`) - Versi yang sudah diamankan

---

## 📁 Struktur Folder

```
uas-keamanan-nama_nim/
├── vulnerable/
│   ├── login.php          # Login rentan brute force
│   ├── comment.php        # Comment rentan XSS & CSRF
│   ├── fileviewer.php     # File viewer rentan LFI
│   └── ssrf.php           # URL fetch rentan SSRF
├── secure/
│   ├── login.php          # Login dengan rate limiting
│   ├── comment.php        # Comment dengan sanitasi & CSRF token
│   ├── fileviewer.php     # File viewer dengan whitelist
│   └── ssrf.php           # URL fetch dengan domain whitelist
├── screenshots/
│   ├── vulnerable_login.png
│   ├── secure_login.png
│   └── ...
├── index.php              # Halaman utama
└── README.md              # Dokumentasi ini
```

---

## 🎯 SOAL 1: Modul Rentan (30 Poin)

### 1. Login Module - Brute Force Attack

**Jenis Kerentanan:**
- ❌ Tidak ada rate limiting
- ❌ Password lemah (123456)
- ❌ Tidak ada account lockout
- ❌ Password tidak di-hash (plaintext)

**Parameter/Flow Rentan:**
```php
// File: vulnerable/login.php (baris 17-31)
if ($username === $valid_username && $password === $valid_password) {
    // Langsung login tanpa validasi kekuatan password
    $_SESSION['logged_in'] = true;
} else {
    // Hanya menghitung percobaan, TIDAK ada rate limiting
    $_SESSION['login_attempts']++;
}
```

**Screenshot Testing:**
- [Screenshot: Berhasil login dengan password lemah '123456']
- [Screenshot: Bisa mencoba berkali-kali tanpa limit]

---

### 2. Comment Module - XSS & CSRF

**Jenis Kerentanan:**
- ❌ Cross-Site Scripting (XSS) - Input tidak disanitasi
- ❌ Tidak ada CSRF token validation
- ❌ HTML/JavaScript langsung dirender

**Parameter/Flow Rentan:**
```php
// File: vulnerable/comment.php (baris 14-22)
$comment = $_POST['comment'] ?? '';

// BAHAYA: Tidak ada sanitasi, langsung simpan
$_SESSION['comments'][] = ['text' => $comment];

// BAHAYA: Render HTML mentah (baris 152)
<?= $c['text'] ?>  // Script akan dieksekusi!
```

**Screenshot Testing:**
- [Screenshot: Input `<script>alert('XSS')</script>`]
- [Screenshot: Script berhasil dieksekusi]

---

### 3. File Viewer - Local File Inclusion (LFI)

**Jenis Kerentanan:**
- ❌ Path traversal attack
- ❌ Tidak ada validasi path
- ❌ Bisa akses file sistem sensitif

**Parameter/Flow Rentan:**
```php
// File: vulnerable/fileviewer.php (baris 11-17)
$filepath = $_GET['file'] ?? '';

// BAHAYA: Langsung baca file tanpa validasi
if (file_exists($filepath)) {
    $content = file_get_contents($filepath);
}
```

**Screenshot Testing:**
- [Screenshot: Input `../../../etc/passwd`]
- [Screenshot: Berhasil membaca file sistem]

---

### 4. URL Fetch - Server-Side Request Forgery (SSRF)

**Jenis Kerentanan:**
- ❌ SSRF - Bisa fetch internal network
- ❌ Tidak ada whitelist URL
- ❌ Bisa akses localhost/127.0.0.1

**Parameter/Flow Rentan:**
```php
// File: vulnerable/ssrf.php (baris 11-16)
$url = $_POST['url'] ?? '';

// BAHAYA: Fetch URL apapun tanpa validasi
$response = file_get_contents($url);
```

---

## 🛡️ SOAL 2: Modul Aman (40 Poin)

### 1. Login Module - Rate Limiting & Strong Password

**Kontrol Keamanan yang Diterapkan:**
1. ✅ Rate limiting: maksimal 3 percobaan
2. ✅ Strong password policy (min 8 char, huruf besar/kecil, angka, simbol)
3. ✅ Account lockout 15 menit setelah 3x gagal
4. ✅ Password di-hash dengan bcrypt

**Perbedaan Kode:**

**VULNERABLE:**
```php
// Tidak ada rate limiting
if ($username === 'admin' && $password === '123456') {
    $_SESSION['logged_in'] = true;
}
```

**SECURE:**
```php
// Rate limiting check
if ($_SESSION['login_attempts'] >= 3) {
    $_SESSION['lockout_time'] = time() + (15 * 60);
    die('Akun terkunci 15 menit!');
}

// Strong password validation
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])/', $password)) {
    die('Password tidak memenuhi kriteria kuat!');
}

// Password hashing
$valid_password_hash = password_hash('Admin@2024!SecurePass', PASSWORD_BCRYPT);
if (password_verify($password, $valid_password_hash)) {
    // Login berhasil
}
```

**Cara Mitigasi Mencegah Eksploitasi:**
- Rate limiting mencegah brute force attack otomatis
- Strong password policy membuat password sulit ditebak
- Account lockout memberikan cooldown period
- Password hashing melindungi jika database bocor


---

### 2. Comment Module - Input Sanitization & CSRF Token

**Kontrol Keamanan yang Diterapkan:**
1. ✅ CSRF token validation
2. ✅ Input sanitization dengan `htmlspecialchars()`
3. ✅ Strip HTML tags dengan `strip_tags()`
4. ✅ ENT_QUOTES untuk escape quotes

**Perbedaan Kode:**

**VULNERABLE:**
```php
$comment = $_POST['comment'];
$_SESSION['comments'][] = ['text' => $comment]; // Langsung simpan!
```

**SECURE:**
```php
// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Validasi CSRF token
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die('CSRF token tidak valid!');
}

// Sanitasi input
$safe_comment = htmlspecialchars($comment, ENT_QUOTES, 'UTF-8');
$safe_comment = strip_tags($safe_comment);

$_SESSION['comments'][] = ['text' => $safe_comment];
```

**Cara Mitigasi Mencegah Eksploitasi:**
- CSRF token mencegah request dari situs lain
- `htmlspecialchars()` escape karakter HTML berbahaya
- `strip_tags()` hapus semua HTML tags
- `ENT_QUOTES` escape single dan double quotes

---

### 3. File Viewer - Whitelist Validation

**Kontrol Keamanan yang Diterapkan:**
1. ✅ File whitelist (hanya file tertentu)
2. ✅ Path validation
3. ✅ Basename extraction untuk hindari traversal

**Perbedaan Kode:**

**VULNERABLE:**
```php
$filepath = $_GET['file'];
$content = file_get_contents($filepath); // Baca file apapun!
```

**SECURE:**
```php
$allowed_files = ['document.txt', 'report.pdf', 'notes.md'];
$filepath = $_GET['file'] ?? '';

// Extract filename only (hindari path traversal)
$filename = basename($filepath);

// Whitelist validation
if (!in_array($filename, $allowed_files)) {
    die('File tidak diizinkan!');
}

// Baca dari direktori aman
$safe_path = __DIR__ . '/files/' . $filename;
$content = file_get_contents($safe_path);
```

**Cara Mitigasi Mencegah Eksploitasi:**
- Whitelist approach: hanya file tertentu yang boleh
- `basename()` ambil nama file saja, buang path
- Hardcode direktori aman untuk read file

---

### 4. URL Fetch - Domain Whitelist

**Kontrol Keamanan yang Diterapkan:**
1. ✅ Domain whitelist
2. ✅ Block internal IP (localhost, 127.0.0.1)
3. ✅ URL validation

**Perbedaan Kode:**

**VULNERABLE:**
```php
$url = $_POST['url'];
$response = file_get_contents($url); // Fetch URL apapun!
```

**SECURE:**
```php
$allowed_domains = ['example.com', 'api.example.com'];
$url = $_POST['url'] ?? '';

// Parse URL
$parsed = parse_url($url);
$host = $parsed['host'] ?? '';

// Block internal IP
if (in_array($host, ['localhost', '127.0.0.1', '0.0.0.0'])) {
    die('Akses ke internal network ditolak!');
}

// Whitelist validation
$is_allowed = false;
foreach ($allowed_domains as $domain) {
    if (strpos($host, $domain) !== false) {
        $is_allowed = true;
        break;
    }
}

if (!$is_allowed) {
    die('Domain tidak diizinkan!');
}

$response = file_get_contents($url);
```

**Cara Mitigasi Mencegah Eksploitasi:**
- Domain whitelist: hanya domain tertentu boleh di-fetch
- Block internal IP mencegah akses localhost
- URL parsing untuk extract hostname

---

## 🔍 SOAL 3: Pengujian Keamanan (30 Poin)

### Teknik Pengujian

**Tools yang Digunakan:**
- Burp Suite Community Edition
- Browser DevTools (Inspect Element)
- Manual Testing

**Teknik Pengujian (Non-Destructive):**

1. **Login Brute Force Testing:**
   - Coba 5-10 kombinasi password berbeda
   - Cek apakah ada rate limiting
   - Monitor response time

2. **XSS Testing:**
   - Input payload: `<script>alert('XSS')</script>`
   - Input payload: `<img src=x onerror="alert('XSS')">`
   - Cek apakah di-escape atau dirender

3. **LFI Testing:**
   - Input: `../../../etc/passwd`
   - Input: `....//....//etc/passwd`
   - Cek apakah bisa akses file sistem

4. **SSRF Testing:**
   - Input: `http://localhost:8080/admin`
   - Input: `http://127.0.0.1:3306`
   - Cek apakah bisa akses internal network

---

### Hasil Temuan (Findings)

| Modul | Vulnerable | Secure |
|-------|-----------|---------|
| **Login** | ❌ Brute force berhasil | ✅ Terkunci setelah 3x |
| **Comment** | ❌ XSS berhasil dieksekusi | ✅ Script di-escape |
| **File Viewer** | ❌ LFI berhasil baca `/etc/passwd` | ✅ Path traversal ditolak |
| **URL Fetch** | ❌ SSRF berhasil akses localhost | ✅ Localhost diblock |

---

### Analisis Risiko

#### 1. Brute Force Login

**Dampak:** TINGGI
- Akun admin bisa diambil alih
- Akses ke seluruh sistem
- Data sensitif bisa dicuri

**Kemungkinan:** TINGGI
- Password lemah mudah ditebak
- Tidak ada rate limiting
- Tools brute force banyak tersedia

**Prioritas Perbaikan:** CRITICAL
- Implementasi rate limiting segera
- Enforce strong password policy
- Add account lockout mechanism

---

#### 2. Cross-Site Scripting (XSS)

**Dampak:** TINGGI
- Cookie stealing (session hijacking)
- Phishing via injected content
- Malware distribution
- Defacement

**Kemungkinan:** SEDANG
- Input field tersedia untuk user
- Requires user interaction untuk exploit

**Prioritas Perbaikan:** HIGH
- Sanitasi semua input user
- Implementasi Content Security Policy
- Escape output dengan `htmlspecialchars()`

---

#### 3. Local File Inclusion (LFI)

**Dampak:** SANGAT TINGGI
- Baca file konfigurasi (database credentials)
- Baca source code aplikasi
- Possible Remote Code Execution (RCE)

**Kemungkinan:** SEDANG
- Butuh endpoint yang terima file path
- User harus tahu struktur file sistem

**Prioritas Perbaikan:** CRITICAL
- Whitelist file yang boleh diakses
- Validasi path dengan `basename()`
- Simpan file di luar web root

---

#### 4. Server-Side Request Forgery (SSRF)

**Dampak:** TINGGI
- Akses internal service (database, admin panel)
- Port scanning internal network
- Bypass firewall/authentication
- Cloud metadata exploitation

**Kemungkinan:** SEDANG
- Butuh endpoint yang fetch URL
- Internal network harus ada service vulnerable

**Prioritas Perbaikan:** HIGH
- Whitelist domain yang boleh di-fetch
- Block internal IP (localhost, private IP)
- Validasi URL format

---

## 🚀 Cara Instalasi & Testing

### Requirements
- PHP 7.4 atau lebih baru
- Web Server (Apache/Nginx) atau PHP Built-in Server
- Browser modern (Chrome, Firefox)

### Instalasi

1. **Clone/Download project:**
   ```bash
   git clone [repository-url]
   cd uas-keamanan-nama_nim
   ```

2. **Jalankan PHP Server:**
   ```bash
   php -S localhost:8000
   ```

3. **Buka di browser:**
   ```
   http://localhost:8000
   ```

### Testing Guide

**Vulnerable Version:**
- Login: `admin` / `123456`
- Comment XSS: `<script>alert('XSS')</script>`
- LFI: `../../../etc/passwd`
- SSRF: `http://localhost:8080`

**Secure Version:**
- Login: `admin` / `Admin@2024!SecurePass`
- Comment: Input apapun akan di-sanitasi
- File: Hanya `document.txt`, `report.pdf`, `notes.md`
- URL: Hanya domain `example.com`, `api.example.com`

---

## 📸 Screenshots

**Struktur folder screenshots:**
```
screenshots/
├── 01_home.png
├── 02_vulnerable_login.png
├── 03_vulnerable_login_brute_force.png
├── 04_secure_login.png
├── 05_secure_login_locked.png
├── 06_vulnerable_xss_input.png
├── 07_vulnerable_xss_executed.png
├── 08_secure_xss_escaped.png
├── 09_vulnerable_lfi.png
├── 10_secure_lfi_blocked.png
├── 11_vulnerable_ssrf.png
└── 12_secure_ssrf_blocked.png
```

---

## 🎓 Kesimpulan

Project ini berhasil mendemonstrasikan 4 kerentanan keamanan web yang umum dan teknik mitigasinya:

1. **Login Security:** Rate limiting & strong password mencegah brute force
2. **XSS Prevention:** Input sanitization & CSRF token mencegah script injection
3. **LFI Prevention:** File whitelist & path validation mencegah file system access
4. **SSRF Prevention:** Domain whitelist & internal IP blocking mencegah internal network access

**Key Takeaways:**
- ✅ Never trust user input - always validate & sanitize
- ✅ Implement defense in depth (multiple layers)
- ✅ Use whitelist approach whenever possible
- ✅ Regular security testing adalah wajib

---

## 📚 Referensi

1. OWASP Top 10 - https://owasp.org/www-project-top-ten/
2. OWASP Cheat Sheet Series - https://cheatsheetseries.owasp.org/
3. PHP Security Best Practices - https://www.php.net/manual/en/security.php
4. PortSwigger Web Security Academy - https://portswigger.net/web-security


**⚠️ DISCLAIMER:**
Project ini dibuat HANYA untuk tujuan pembelajaran dan simulasi pengujian keamanan di lingkungan localhost. TIDAK BOLEH digunakan untuk eksploitasi sistem nyata atau tujuan ilegal. Penulis tidak bertanggung jawab atas penyalahgunaan kode ini.
