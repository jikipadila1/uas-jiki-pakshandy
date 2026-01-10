# Cara Menjalankan Project di VS Code

## Langkah 1: Persiapan Database

### Buka phpMyAdmin (http://localhost/phpmyadmin)

1. Buat database baru dengan nama: `census_db`
2. Import file SQL: `census_db.sql`
   - Klik tab "Import"
   - Pilih file `census_db.sql` dari project folder
   - Klik "Go"

## Langkah 2: Konfigurasi Environment

File `.env` sudah terkonfigurasi, pastikan settingannya:

```env
database.default.hostname = localhost
database.default.database = census_db
database.default.username = root
database.default.password =
database.default.port = 3306
```

Jika password MySQL Anda ada, isi di bagian `database.default.password`

## Langkah 3: Install Dependencies (WAJIB!)

### Buka Terminal di VS Code:
- **Ctrl + ` ** (backtick) atau
- Menu: Terminal → New Terminal

### Jalankan perintah:
```bash
composer install
```

**TUNGGU sampai selesai!** Ini akan download semua library yang dibutuhkan.

## Langkah 4: Set Permission untuk Windows

Buat folder cache jika belum ada:
```bash
if not exist "writable\cache" mkdir writablecache
```

## Langkah 5: Jalankan Development Server

### Cara 1: Menggunakan Terminal (Recommended)

Di terminal VS Code, jalankan:
```bash
php spark serve
```

Akan muncul output:
```
CodeIgniter v4.x.x development server started on http://localhost:8080
```

**Buka browser:** http://localhost:8080

### Cara 2: Menggunakan XAMPP Apache

1. Copy folder project ke: `C:\xampp\htdocs\`
2. Pastikan folder name: `uas-tes-jiki`
3. **Buka browser:** http://localhost/uas-tes-jiki/public

## Langkah 6: Testing Aplikasi

### Login Page:
- URL: http://localhost:8080/login.html (jika pakai spark serve)
- URL: http://localhost/uas-tes-jiki/public/login.html (jika pakai XAMPP)

### Default Login:
- **Username:** admin
- **Password:** admin123

Atau register akun baru di halaman login.

## Troubleshooting

### Error "Failed opening required Boot.php"
**Solusi:** Jalankan `composer install` (langkah 3)

### Error "Database Connection Failed"
**Solusi:**
1. Pastikan database `census_db` sudah dibuat
2. Pastikan file `census_db.sql` sudah di-import
3. Cek konfigurasi di file `.env`

### Error 404 Not Found
**Solusi:** Pastikan URL mengarah ke folder `public/`

### Port 8080 sudah dipakai
**Solusi:** Gunakan port lain
```bash
php spark serve --port=8081
```

### Folder writable tidak bisa ditulis
**Solusi (Windows):**
1. Klik kanan folder `writable`
2. Properties → Security
3. Edit → Full Control untuk Users

## Struktur Project

```
uas-tes-jiki/
├── app/                    # Code backend (Controller, Model, Config)
├── public/                 # Folder public (index.php, assets)
│   ├── login.html         # Halaman login
│   ├── dashboard.html     # Dashboard
│   ├── pelanggaran.html   # CRUD Pelanggaran
│   └── objek-melintas.html # CRUD Objek Melintas
├── writable/              # Folder writable (logs, cache, session)
├── vendor/                # Dependencies dari composer
├── .env                   # Konfigurasi environment
├── composer.json          # Dependencies list
└── census_db.sql          # Database SQL
```

## API Endpoints

### Authentication
- POST `/api/auth/login` - Login user
- POST `/api/auth/register` - Register user baru

### Pelanggaran
- GET `/api/pelanggaran` - Ambil semua data pelanggaran
- GET `/api/pelanggaran/{id}` - Ambil detail pelanggaran
- POST `/api/pelanggaran` - Tambah pelanggaran baru
- PUT `/api/pelanggaran/{id}` - Update pelanggaran
- DELETE `/api/pelanggaran/{id}` - Hapus pelanggaran

### Objek Melintas
- GET `/api/objek-melintas` - Ambil semua data objek
- GET `/api/objek-melintas/{id}` - Ambil detail objek
- POST `/api/objek-melintas` - Tambah objek baru
- PUT `/api/objek-melintas/{id}` - Update objek
- DELETE `/api/objek-melintas/{id}` - Hapus objek

## Tips VS Code

### Extension yang direkomendasikan:
1. **PHP Intelephense** - PHP autocomplete
2. **PHP Debug** - Debugging PHP
3. **Thunder Client** - Testing API (seperti Postman)

### Run di VS Code dengan Thunder Client:
1. Install Thunder Client extension
2. Buat request baru
3. Method: POST
4. URL: http://localhost:8080/api/auth/login
5. Body: JSON
```json
{
  "username": "admin",
  "password": "admin123"
}
```

## Stop Server

Di terminal, tekan: **Ctrl + C**

## Catatan Penting

- Jangan edit file di dalam folder `vendor/`
- Folder `writable/` harus bisa ditulis (writable)
- File `.env` sudah di-include, tidak perlu dibuat lagi
- Selalu jalankan `composer install` setelah clone dari GitHub

---

**Selamat Mengerjakan! 🚀**

Created by: Jiki (411222088)
