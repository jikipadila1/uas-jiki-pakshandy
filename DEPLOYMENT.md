# Deployment Guide - UAS Pak Shandy

Panduan deployment aplikasi CodeIgniter 4 ke production server.

---

## 📋 Prasyarat Sebelum Deployment

Sebelum melakukan deployment, pastikan:

1. ✅ Aplikasi sudah tested di local environment
2. ✅ Semua dependencies terinstall (`composer install` sudah dijalankan)
3. ✅ Database sudah siap untuk di-export
4. ✅ Environment configuration sudah diset untuk production
5. ✅ Folder `vendor` sudah include atau siap di-install di server

---

## 🚀 Metode Deployment

### Metode 1: GitHub Deployment (Recommended)

Cocok untuk deployment ke hosting yang support Git seperti cPanel, VPS, atau cloud platforms.

#### 1. Finalize Local Changes

```bash
# Test aplikasi terakhir kali
php spark serve

# Clear semua cache
php spark cache:clear

# Set environment ke production
# Edit .env: CI_ENVIRONMENT = production
```

#### 2. Update .gitignore

Pastikan file-file sensitive tidak ter-commit:

```bash
# Check .gitignore
cat .gitignore

# Pastikan termasuk:
.env.production
.env.staging
/writable/logs/*.log
```

#### 3. Commit & Push ke GitHub

```bash
# Add semua perubahan
git add .

# Commit changes
git commit -m "Prepare for production deployment"

# Push ke GitHub
git push origin main
```

#### 4. Clone di Server

```bash
# SSH ke server
ssh user@yourserver.com

# Clone repository
git clone https://github.com/jikipadila1/uas-jiki-pakshandy.git

# Atau jika sudah ada, pull update
cd uas-jiki-pakshandy
git pull origin main
```

#### 5. Install Dependencies di Server

```bash
cd uas-jiki-pakshandy

# Install composer dependencies
composer install --no-dev --optimize-autoloader

# Atau jika vendor sudah include di git, skip ini
```

#### 6. Setup Database di Server

```bash
# Export database dari local
mysqldump -u root -p census_db > census_db_backup.sql

# Import ke production database
mysql -u production_user -p -h production_host census_db < census_db_backup.sql

# Atau upload file SQL via phpMyAdmin/SFTP
```

#### 7. Configure Environment

```bash
# Copy .env.example ke .env (atau edit yang sudah ada)
cp .env .env

# Edit untuk production
nano .env
```

Production .env configuration:
```env
CI_ENVIRONMENT = production
app.baseURL = 'https://yourdomain.com/'

database.default.hostname = production_host
database.default.database = production_db_name
database.default.username = production_user
database.default.password = strong_password_here

jwt.secret_key = change_this_to_very_strong_secret_key
```

#### 8. Set Permissions

```bash
# Set writable permissions
chmod -R 755 writable
chmod -R 755 public/uploads

# Khusus folder di dalam writable
chmod -R 777 writable/cache
chmod -R 777 writable/logs
chmod -R 777 writable/session
chmod -R 777 writable/uploads
```

#### 9. Configure Web Server

**Apache (.htaccess sudah include):**
- Pastikan `document_root` mengarah ke folder `public/`
- Enable `mod_rewrite`

**Nginx:**
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/uas-jiki-pakshandy/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

#### 10. Test Deployment

```bash
# Clear cache di production
php spark cache:clear

# Test di browser
https://yourdomain.com
```

---

### Metode 2: FTP/SFTP Deployment

Cocok untuk shared hosting (cPanel, DirectAdmin, dll).

#### 1. Compress Project

```bash
# Di local, buat file untuk upload
cd /path/to/project

# Hapus folder yang tidak perlu
rm -rf writable/logs/*
rm - writable/cache/*

# Zip file (kecuali vendor jika mau install di server)
zip -r uas-jiki-pakshandy.zip . -x "*.git*" "*.DS_Store" "writable/logs/*"
```

#### 2. Upload via FTP/SFTP

- Gunakan FileZilla, WinSCP, atau cPanel File Manager
- Upload file `.zip` ke server
- Extract di folder public_html atau subdomain

#### 3. Install Dependencies

Jika vendor tidak include:

```bash
# SSH ke server atau gunakan Terminal cPanel
cd public_html
composer install --no-dev
```

#### 4. Setup Database

- Buat database baru di cPanel → MySQL Databases
- Import file `census_db.sql` via phpMyAdmin
- Update `.env` dengan credential database baru

#### 5. Set Permissions

Via File Manager atau SSH:
```bash
chmod -R 755 .
chmod -R 777 writable/cache writable/logs writable/session
```

---

### Metode 3: DirectAdmin/cPanel Quick Install

Untuk shared hosting dengan cPanel/DirectAdmin.

#### 1. Upload Files

1. Compress project menjadi `.zip`
2. Upload via cPanel File Manager
3. Extract di folder `public_html` atau subdomain

#### 2. Create Database

1. Login ke cPanel
2. MySQL Database Wizard
3. Buat database: `username_censusdb`
4. Buat user database dan assign privileges
5. Import `census_db.sql` via phpMyAdmin

#### 3. Configure .env

```env
CI_ENVIRONMENT = production
app.baseURL = 'https://yourdomain.com/'

database.default.hostname = localhost
database.default.database = username_censusdb
database.default.username = username_dbuser
database.default.password = YourStrongPassword123!
```

#### 4. Set Writable Permissions

Via cPanel File Manager:
- Select folder `writable`
- Change Permissions to `755` or `777`
- Apply to subdirectories

#### 5. Test Application

Buka `https://yourdomain.com` dan test semua functionality.

---

## 🔒 Security Best Practices

### 1. Protect .env File

```bash
# Jangan expose .env ke public
chmod 640 .env

# Pastikan .htaccess di root menghapus akses ke .env
<Files .env>
    Order allow,deny
    Deny from all
</Files>
```

### 2. Change Default JWT Secret

```env
# Generate strong random key
jwt.secret_key = very_long_random_string_here_use_password_generator
```

### 3. Database Security

- Use strong password
- Different user for production (not root)
- Limit database user privileges
- Regular backups

### 4. File Permissions

```bash
# Ideal production permissions
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;

# Writable folders
chmod -R 777 writable/cache writable/logs writable/session
```

### 5. Disable Debug Mode in Production

```env
# .env
CI_ENVIRONMENT = production
```

### 6. HTTPS/SSL

- Install SSL certificate (Let's Encrypt free)
- Force HTTPS redirect
- Update app.baseURL to https://

---

## 🔧 Troubleshooting Production Issues

### Issue 1: Blank Page

**Cause:** Error tapi display error off

**Solution:**
```bash
# Temporary enable error untuk debug
# Edit .env: CI_ENVIRONMENT = development

# Check error logs
tail -f writable/logs/*.log
```

### Issue 2: Database Connection Failed

**Solution:**
1. Check database credentials in `.env`
2. Verify database exists
3. Test MySQL connection manually:
```bash
mysql -u username -p -h hostname database_name
```

### Issue 3: Cache Not Writing

**Solution:**
```bash
# Check writable folder permissions
ls -la writable/

# Fix permissions
chmod -R 777 writable/cache writable/logs writable/session

# Clear cache
php spark cache:clear
```

### Issue 4: 404 Not Found

**Cause:** mod_rewrite not enabled or wrong document root

**Solution:**
1. Apache: Enable mod_rewrite
```bash
sudo a2enmod rewrite
sudo service apache2 restart
```

2. Nginx: Check nginx config (see method 1, step 9)

3. cPanel: Make sure domain points to `public/` folder

### Issue 5: JWT Token Invalid

**Solution:**
1. Check jwt.secret_key in `.env` matches
2. Clear browser cache and cookies
3. Regenerate secret key if needed

---

## 📊 Monitoring & Maintenance

### Daily Tasks

```bash
# Check error logs
tail -n 50 writable/logs/log-$(date +%Y-m-d).log

# Monitor disk space
df -h

# Check database size
mysql -u root -p -e "SELECT table_schema AS 'Database', ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)' FROM information_schema.tables GROUP BY table_schema;"
```

### Weekly Tasks

- Backup database
- Check for CodeIgniter updates
- Review security logs
- Monitor traffic

### Monthly Tasks

- Full backup (files + database)
- Review and update dependencies
- Security audit
- Performance optimization

---

## 🔄 Update Deployment

### Update via Git

```bash
# SSH ke server
cd uas-jiki-pakshandy

# Pull latest changes
git pull origin main

# Install new dependencies
composer install

# Clear cache
php spark cache:clear

# Test application
```

### Update via FTP

1. Upload modified files via FTP
2. Overwrite existing files
3. Clear cache via SSH or cPanel Terminal
4. Test functionality

---

## 📱 Deployment Checklist

Sebelum go-live, pastikan:

- [ ] Aplikasi tested 100% di local
- [ ] Database sudah backup
- [ ] `.env` sudah configure untuk production
- [ ] JWT secret key sudah diganti
- [ ] Folder permissions sudah set dengan benar
- [ ] SSL/HTTPS sudah enable
- [ ] Error logging sudah aktif
- [ ] Database backup schedule sudah set
- [ ] Monitoring sudah configure
- [ ] Contact form/error reporting sudah test

---

## 🆘 Support

Jika mengalami masalah saat deployment:

1. Check logs: `writable/logs/`
2. Verify semua requirements terinstall
3. Test database connection
4. Enable error mode untuk debugging
5. Check [INSTALL.md](INSTALL.md) untuk troubleshooting lengkap

---

**Good luck with your deployment! 🚀**

Untuk pertanyaan atau bantuan tambahan, hubungi:
- Author: Jiki (411222088)
- Project: https://github.com/jikipadila1/uas-jiki-pakshandy
