# UAS Pak Shandy - Jiki (411222088)

Aplikasi pelanggaran berbasis web menggunakan framework CodeIgniter 4 dengan sistem CRUD lengkap dan autentikasi JWT.

## 📋 Fitur

- **CRUD Lengkap** - Create, Read, Update, Delete untuk data pelanggaran
- **Autentikasi JWT** - Sistem login dengan JSON Web Token
- **RESTful API** - API endpoint untuk integrasi frontend
- **Database MySQL** - Manajemen database yang reliable
- **Responsive Design** - Tampilan modern dan mobile-friendly
- **Easy Deployment** - Include vendor folder untuk instalasi cepat

## 🛠️ Teknologi

- **Backend**: CodeIgniter 4.6.4
- **Database**: MySQL / MariaDB
- **PHP**: 8.1+
- **Authentication**: Firebase JWT
- **Frontend**: HTML, CSS, JavaScript, Bootstrap

## 📦 Persyaratan Sistem

- PHP 8.1 atau lebih tinggi
- MySQL 5.7+ atau MariaDB 10.2+
- Composer (opsional, untuk update dependencies)
- Web Server (Apache/Nginx) atau XAMPP

## 🚀 Quick Start

Project ini sudah include folder `vendor`, jadi bisa langsung dijalankan:

### Windows (PowerShell / CMD)

```powershell
# 1. Clone atau download repository
Download/clone dari GitHub
Double-click setup.bat
Import database census_db.sql via phpMyAdmin
Jalankan: php spark serve
Buka: http://localhost:8080
# 5. Buka browser
# http://localhost:8080
```

**Atau jalankan setup otomatis:** Double-click `setup.bat`

### Linux/Mac / Git Bash

```bash
# 1. Clone repository
git clone https://github.com/jikipadila1/uas-jiki-pakshandy.git
cd uas-jiki-pakshandy

# 2. Jalankan script setup
chmod +x setup.sh
./setup.sh

# 3. Buat database dan import
mysql -u root -p -e "CREATE DATABASE census_db"
mysql -u root -p census_db < census_db.sql

# 4. Jalankan server
php spark serve

# 5. Buka browser
# http://localhost:8080
```

**🎉 Selesai! Aplikasi siap digunakan.**

---

## 📖 Panduan Instalasi Lengkap

Untuk panduan instalasi yang lebih detail, troubleshooting, dan konfigurasi web server (XAMPP, Laragon, Apache), silakan baca:

**[📘 INSTALL.md](INSTALL.md)** - Panduan instalasi lengkap dengan troubleshooting

---

## 📁 Struktur Project

```
uas-jiki-pakshandy/
├── app/                    # Source code aplikasi
│   ├── Config/            # Konfigurasi (database, jwt, routes)
│   ├── Controllers/       # Logic controllers
│   ├── Models/           # Database models
│   ├── Filters/          # Auth filters
│   └── Views/            # HTML templates
├── public/               # Public accessible files
│   ├── index.php        # Entry point
│   └── uploads/         # File uploads
├── writable/            # Writable folder (cache, logs, session)
├── vendor/              # Composer dependencies (included)
├── .env                 # Environment configuration
├── composer.json        # Composer dependencies config
├── census_db.sql        # Database dump
├── INSTALL.md           # Panduan instalasi lengkap
└── README.md            # File ini
```

---

## ⚙️ Konfigurasi

### Database

Edit file [`.env`](.env) sesuaikan dengan konfigurasi database Anda:

```env
database.default.hostname = localhost
database.default.database = census_db
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306
```

### JWT Secret Key

Edit file [`.env`](.env) untuk mengubah JWT secret key:

```env
jwt.secret_key = rahasia1234567890abcdef
jwt.expire_days = 2
```

**Catatan:** Token JWT akan expired setelah 2 hari. User harus login kembali setelah masa berlaku habis.

---

## 🔧 Troubleshooting

### Error: "Cache unable to write"

```bash
mkdir -p writable/cache writable/logs writable/session writable/uploads
chmod -R 777 writable
```

### Error: "Failed opening required Boot.php"

Ini terjadi jika folder `vendor` tidak ada. Solusi:

**Opsi 1: Download ulang dari GitHub** (Recommended)
- Pastikan folder `vendor` ter-download saat clone

**Opsi 2: Install dengan Composer**
```bash
composer install
```

### Error: "Unable to connect to database"

1. Pastikan MySQL sudah berjalan
2. Database `census_db` sudah dibuat
3. Import database: `mysql -u root -p census_db < census_db.sql`
4. Cek konfigurasi di file `.env`

Untuk troubleshooting lebih lengkap, lihat [INSTALL.md](INSTALL.md#troubleshooting)

---

## 📸 Screenshots

### Halaman Login
<img width="960" height="540" alt="Halaman Login" src="https://github.com/user-attachments/assets/43b2c54f-12d4-4515-b556-3eed1f020669" />

### Dashboard Pelanggaran
<img width="960" height="540" alt="Dashboard" src="https://github.com/user-attachments/assets/2d1730db-95d8-4699-b692-60b0850b0c56" />

### Tambah Data Pelanggaran
<img width="960" height="540" alt="Tambah Data" src="https://github.com/user-attachments/assets/0fc7161f-65c8-4e36-9c11-9b877f55f154" />

### Edit Data Pelanggaran
<img width="960" height="540" alt="Edit Data" src="https://github.com/user-attachments/assets/43e2f627-3934-47c1-bee9-af182e665f3b" />

### Delete Data Pelanggaran
<img width="960" height="540" alt="Delete Data" src="https://github.com/user-attachments/assets/430b9283-ad40-4033-a023-43810541964b" />

---

## 🔐 API Endpoints

### Authentication

#### Login
```http
POST /api/login
Content-Type: application/json

{
  "username": "admin",
  "password": "password"
}
```

Response:
```json
{
  "status": "success",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "id": 1,
    "username": "admin"
  }
}
```

### Pelanggaran CRUD

#### Get All Pelanggaran
```http
GET /api/pelanggaran
Authorization: Bearer {token}
```

#### Get Pelanggaran by ID
```http
GET /api/pelanggaran/{id}
Authorization: Bearer {token}
```

#### Create Pelanggaran
```http
POST /api/pelanggaran
Authorization: Bearer {token}
Content-Type: application/json

{
  "nama": "John Doe",
  "kelas": "XII-RPL",
  "jenis_pelanggaran": "Terlambat",
  "tanggal": "2024-01-15",
  "poin": 5
}
```

#### Update Pelanggaran
```http
PUT /api/pelanggaran/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "nama": "John Doe",
  "kelas": "XII-RPL",
  "jenis_pelanggaran": "Terlambat",
  "tanggal": "2024-01-15",
  "poin": 10
}
```

#### Delete Pelanggaran
```http
DELETE /api/pelanggaran/{id}
Authorization: Bearer {token}
```

---

## 📝 Available Commands

```bash
# Jalankan development server
php spark serve

# Tampilkan semua routes
php spark routes

# Clear cache
php spark cache:clear

# Generate encryption key
php spark key:generate

# Check environment
php spark env

# Show help
php spark help
```

---

## 👤 Author

**Nama:** Jiki
**NIM:** 411222088
**Mata Kuliah:** Pemrograman Web - Pak Shandy
**Tahun:** 2024

---



##
🔗 Repository: [https://github.com/jikipadila1/uas-jiki-pakshandy](https://github.com/jikipadila1/uas-jiki-pakshandy)
