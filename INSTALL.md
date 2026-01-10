# Panduan Instalasi Lengkap - UAS Pak Shandy

## Overview
Project ini adalah aplikasi pelanggaran berbasis web menggunakan framework CodeIgniter 4. Project ini sudah include folder `vendor` sehingga bisa langsung dijalankan tanpa perlu `composer install`.

## Persyaratan Sistem

- **PHP**: 8.1 atau lebih tinggi
- **Database**: MySQL 5.7+ atau MariaDB 10.2+
- **Web Server**: Apache, Nginx, atau XAMPP
- **Composer**: (Opsional) Jika perlu update dependencies

## Metode 1: Instalasi Cepat (Dengan Vendor Folder)

Project ini sudah include folder `vendor` untuk kemudahan deployment.

### ⚡ Cara Paling Mudah - Gunakan Script Setup Otomatis

**Windows:**
1. Download atau clone repository
2. Double-click file `setup.bat`
3. Selesai! Script akan mengatur semuanya

**Linux/Mac:**
```bash
chmod +x setup.sh
./setup.sh
```

### Manual Installation

Jika script tidak berjalan, ikuti langkah manual di bawah ini:

### 1. Clone atau Download Repository

```bash
# Via HTTPS
git clone https://github.com/jikipadila1/uas-jiki-pakshandy.git

# Atau download ZIP dari GitHub dan extract
```

### 2. Masuk ke Folder Project

```bash
cd uas-jiki-pakshandy
```

### 3. Buat Database MySQL

Buka phpMyAdmin atau terminal MySQL:

```sql
CREATE DATABASE census_db;
```

### 4. Import Database

**Via phpMyAdmin:**
1. Buka phpMyAdmin (http://localhost/phpmyadmin)
2. Pilih database `census_db`
3. Klik tab "Import"
4. Pilih file `census_db.sql`
5. Klik "Go"

**Via Terminal:**
```bash
mysql -u root -p census_db < census_db.sql
```

### 5. Konfigurasi Environment

File `.env` sudah tersedia. Pastikan konfigurasi database sudah benar:

```env
database.default.hostname = localhost
database.default.database = census_db
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306
```

**Jika password MySQL tidak kosong:**
- Buka file `.env`
- Cari baris: `database.default.password =`
- Isi password setelah `=`, contoh: `database.default.password = password_anda`

### 6. Set Permission Folder Writable

**Windows (PowerShell - Run as Administrator):**
```powershell
# Method 1: Using icacls (recommended)
icacls "writable" /grant "Users:(OI)(CI)F" /T

# Method 2: Usingicacls for each subfolder
icacls "writable\cache" /grant "Users:(OI)(CI)F" /T
icacls "writable\logs" /grant "Users:(OI)(CI)F" /T
icacls "writable\session" /grant "Users:(OI)(CI)F" /T
icacls "writable\uploads" /grant "Users:(OI)(CI)F" /T
```

**Windows (Git Bash):**
```bash
chmod -R 777 writable
```

**Linux/Mac:**
```bash
chmod -R 777 writable
chmod -R 777 public/uploads
```

### 7. Pastikan Folder Cache Ada

**Windows (PowerShell/CMD):**
```powershell
mkdir writable\cache
mkdir writable\logs
mkdir writable\session
mkdir writable\uploads
```

**Windows (Git Bash) / Linux / Mac:**
```bash
mkdir -p writable/cache writable/logs writable/session writable/uploads
chmod 777 writable/cache writable/logs writable/session writable/uploads
```

### 8. Jalankan Development Server

```bash
php spark serve
```

Server akan berjalan di: **http://localhost:8080**

### 9. Buka di Browser

```
http://localhost:8080
```

---

## Metode 2: Instalasi dengan Composer Update

Jika ingin mendapatkan update dependencies terbaru atau ada masalah dengan vendor folder.

### 1. Install Composer (Jika belum ada)

**Windows:**
- Download dan install dari: https://getcomposer.org/Composer-Setup.exe
- Verify: `composer --version`

**Linux/Mac:**
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 2. Clone Repository

```bash
git clone https://github.com/jikipadila1/uas-jiki-pakshandy.git
cd uas-jiki-pakshandy
```

### 3. Install/Update Dependencies

```bash
# Remove vendor folder dulu (opsional)
rm -rf vendor composer.lock

# Install dependencies
composer install
```

### 4. Lanjutkan ke Langkah 3-9 dari Metode 1

---

## Konfigurasi Web Server

### XAMPP

1. Copy folder project ke: `C:\xampp\htdocs\uas-jiki-pakshandy`
2. Buat virtual host di `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:
```apache
<VirtualHost *:80>
    ServerName uas-jiki.local
    DocumentRoot "C:/xampp/htdocs/uas-jiki-pakshandy/public"
    <Directory "C:/xampp/htdocs/uas-jiki-pakshandy/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```
3. Tambahkan ke `C:\Windows\System32\drivers\etc\hosts`:
```
127.0.0.1 uas-jiki.local
```
4. Restart Apache
5. Akses: http://uas-jiki.local

### Laragon (Windows Otomatis)

1. Copy project ke: `C:\laragon\www\uas-jiki-pakshandy`
2. Klik kanan Laragon → Quick App → Create Apache alias
3. Otomatis bisa diakses di: http://uas-jiki-pakshandy.test

### Linux (Apache)

```bash
# Enable mod_rewrite
sudo a2enmod rewrite

# Create virtual host
sudo nano /etc/apache2/sites-available/uas-jiki.conf
```

Isi dengan:
```apache
<VirtualHost *:80>
    ServerName uas-jiki.local
    DocumentRoot /var/www/uas-jiki-pakshandy/public

    <Directory /var/www/uas-jiki-pakshandy/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/uas-jiki_error.log
    CustomLog ${APACHE_LOG_DIR}/uas-jiki_access.log combined
</VirtualHost>
```

```bash
# Enable site dan restart Apache
sudo a2ensite uas-jiki.conf
sudo systemctl restart apache2

# Add to hosts
echo "127.0.0.1 uas-jiki.local" | sudo tee -a /etc/hosts
```

---

## Troubleshooting

### Error Khusus Windows

#### PowerShell Error: "Missing argument in parameter list"

**Problem:** Command seperti `mkdir -p writable/{cache,logs,session}` tidak jalan di PowerShell

**Solution:** Gunakan perintah Windows yang benar:
```powershell
# Salah (tidak jalan di PowerShell):
mkdir -p writable/{cache,logs,session,uploads}

# Benar untuk PowerShell:
mkdir writable\cache
mkdir writable\logs
mkdir writable\session
mkdir writable\uploads

# Atau gunakan setup.bat otomatis
setup.bat
```

#### Git Bash: "chmod: command not found" atau Permission Denied

**Problem:** Command `chmod` tidak jalan di Windows PowerShell

**Solution:**
1. Gunakan Git Bash (bukan PowerShell)
2. Atau gunakan PowerShell command yang sesuai:
```powershell
# PowerShell (Run as Administrator)
icacls "writable" /grant "Users:(OI)(CI)F" /T
```

#### Error: "php is not recognized as an internal or external command"

**Problem:** PHP tidak ada di PATH

**Solution:**
1. Install XAMPP dari https://www.apachefriends.org/
2. Tambahkan PHP ke PATH:
   - Buka System Properties → Environment Variables
   - Edit PATH
   - Add: `C:\xampp\php`
   - Restart PowerShell/Command Prompt

Atau gunakan full path:
```powershell
C:\xampp\php\php.exe spark serve
```

---

### Error 1: "Cache unable to write"

**Penyebab:** Folder writable/cache tidak ada atau tidak punya permission

**Solusi:**
```bash
# Buat folder cache
mkdir -p writable/cache writable/logs writable/session writable/uploads

# Set permission
chmod -R 777 writable
```

### Error 2: "Failed opening required 'Boot.php'"

**Penyebab:** Dependencies CodeIgniter belum terinstall (vendor folder tidak ada atau tidak lengkap)

**Solusi:**

**Opsi 1: Check apakah vendor folder ada**
```powershell
# Windows
dir vendor

# Linux/Mac
ls -la vendor
```

Jika tidak ada:
```bash
# Install dependencies dengan composer
composer install
```

**Opsi 2: Download ulang dari GitHub**
- Pastikan download dengan "Download ZIP" dari GitHub
- Extract dan pastikan folder `vendor` ada
- Atau gunakan git clone (bukan download zip)

**Opsi 3: Cek apakah vendor lengkap**
```powershell
# Windows
dir vendor\codeigniter4

# Harus ada folder framework di dalamnya
```

Jika tidak lengkap, jalankan:
```bash
composer install
```

### Error 3: "Unable to connect to database"

**Penyebab:** Koneksi database tidak benar

**Solusi:**
1. Pastikan MySQL server sudah berjalan
2. Cek database `census_db` sudah dibuat
3. Verifikasi username dan password di file `.env`
4. Import database: `mysql -u root -p census_db < census_db.sql`

### Error 4: Blank page / 404 Not Found

**Penyebab:** Base URL tidak sesuai atau mod_rewrite tidak aktif

**Solusi:**
1. Cek `.env`: `app.baseURL = 'http://localhost:8080/'`
2. Enable mod_rewrite (Apache):
```bash
sudo a2enmod rewrite
sudo service apache2 restart
```
3. Pastikan document root mengarah ke folder `public/`

### Error 5: "Class 'Firebase\JWT\JWT' not found"

**Penyebab:** Package firebase/php-jwt belum terinstall

**Solusi:**
```bash
composer require firebase/php-jwt:^6.10
```

### Error 6: Permission denied di Windows

**Solusi PowerShell (Run as Administrator):**
```powershell
# Grant full access ke folder writable
icacls "writable" /grant "Users:(OI)(CI)F" /T
icacls "public/uploads" /grant "Users:(OI)(CI)F" /T
```

---

## Verifikasi Instalasi

Setelah instalasi, pastikan semua berjalan dengan baik:

### 1. Cek PHP Version
```bash
php --version
# Output harus: PHP 8.1.x atau lebih tinggi
```

### 2. Cek Composer (jika digunakan)
```bash
composer --version
# Output harus: Composer version 2.x.x
```

### 3. Test Database Connection
```bash
mysql -u root -p census_db -e "SHOW TABLES;"
# Output harus menampilkan tabel-tabel database
```

### 4. Test Application
```bash
php spark serve
# Output: CodeIgniter development server started on http://localhost:8080
```

### 5. Access di Browser
Buka http://localhost:8080 dan pastikan halaman tampil dengan baik

---

## Struktur Folder Penting

```
uas-jiki-pakshandy/
├── app/                    # Source code aplikasi
│   ├── Config/            # Konfigurasi
│   ├── Controllers/       # Logic controllers
│   ├── Models/           # Database models
│   └── Views/            # HTML templates
├── public/               # Public accessible files
│   ├── index.php        # Entry point
│   └── uploads/         # File uploads
├── writable/            # Writable folder (IMPORTANT!)
│   ├── cache/          # Cache files
│   ├── logs/           # Application logs
│   ├── session/        # Session files
│   └── uploads/        # Upload files
├── vendor/             # Dependencies (composer)
├── .env                # Environment configuration
├── composer.json       # Composer dependencies
└── census_db.sql       # Database dump
```

---

## Tips & Best Practices

### Development
- Gunakan `php spark serve` untuk development server
- Aktifkan error reporting di development: `.env` → `CI_ENVIRONMENT = development`
- Cek logs di `writable/logs/` jika ada error

### Production
- Set `CI_ENVIRONMENT = production` di `.env`
- Gunakan web server yang proper (Apache/Nginx)
- Set permission yang aman untuk writable (755 instead of 777)
- Remove sensitive data dari `.env` sebelum commit ke GitHub
- Regular backup database

### Security
- Ganti JWT secret key di `.env`
- Use strong database password
- Enable CSRF protection di Config/App.php
- Validate and sanitize all user inputs

---

## Command Spark yang Berguna

```bash
# Jalankan development server
php spark serve

# Tampilkan routes
php spark routes

# Clear cache
php spark cache:clear

# Generate encryption key
php spark key:generate

# Cek environment
php spark env

# Help
php spark help
```

---

## Support

Jika mengalami masalah yang tidak tercakup di dokumentasi ini:

1. Cek error logs: `writable/logs/`
2. Enable error logging di `.env`
3. Pastikan semua requirements terpenuhi
4. Coba reinstall dependencies: `composer install`
5. Check issue tracker di GitHub

---

## Quick Start (One-liner)

Untuk yang sudah terbiasa:

```bash
git clone https://github.com/jikipadila1/uas-jiki-pakshandy.git
cd uas-jiki-pakshandy
mysql -u root -p -e "CREATE DATABASE census_db"
mysql -u root -p census_db < census_db.sql
mkdir -p writable/{cache,logs,session,uploads}
chmod -R 777 writable
php spark serve
```

Akses di: http://localhost:8080

---

**Selamat Menggunakan! 🚀**

Dibuat untuk UAS Pemrograman Web - Pak Shandy
Author: Jiki (411222088)
