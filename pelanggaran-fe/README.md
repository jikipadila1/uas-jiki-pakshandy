# Pelanggaran FE - Frontend Application

Frontend application untuk sistem pelaporan pelanggaran dan objek melintas dengan menggunakan:
- **HTML5**
- **CSS3** (Bootstrap 5)
- **JavaScript (Vanilla)**
- **REST API Integration** dengan JWT Authentication

## Fitur

1. **Authentication**
   - Halaman Login
   - JWT Token Management
   - Auto-logout saat token expired
   - Session persistence dengan localStorage

2. **Dashboard**
   - Statistik overview
   - Recent activity
   - Quick navigation

3. **Modul Data Pelanggaran**
   - List dengan pagination (20 data per halaman)
   - Create, Read, Update, Delete (CRUD)
   - Filter berdasarkan tipe dan status
   - Search functionality

4. **Modul Objek Melintas**
   - List dengan pagination (10 data per halaman)
   - Create, Read, Update, Delete (CRUD)
   - Filter berdasarkan tipe objek
   - Search functionality

## Persyaratan Sistem

- Web Browser modern (Chrome, Firefox, Edge, Safari)
- Web Server (Apache, Nginx, atau XAMPP)
- Backend API harus berjalan (pelanggaran-api)

## Instalasi

### 1. Clone Repository

```bash
cd c:\xampp\htdocs
```

### 2. Konfigurasi API Endpoint

Edit file [js/config.js](js/config.js) dan sesuaikan `API_BASE_URL` dengan URL API Anda:

```javascript
const CONFIG = {
    API_BASE_URL: 'http://localhost:8080/api', // Sesuaikan dengan URL API
    TOKEN_KEY: 'pelanggaran_token',
    USER_KEY: 'pelanggaran_user',
    // ...
};
```

### 3. Jalankan Application

Cukup buka file HTML di browser atau deploy ke web server:

**Menggunakan XAMPP:**
1. Copy folder `pelanggaran-fe` ke `c:\xampp\htdocs\`
2. Buka browser dan akses: `http://localhost/pelanggaran-fe/login.html`

**Menggunakan Live Server (VSCode):**
1. Install "Live Server" extension di VSCode
2. Klik kanan pada `login.html`
3. Pilih "Open with Live Server"

## Struktur Project

```
pelanggaran-fe/
├── css/
│   └── style.css           # Custom styles
├── js/
│   └── config.js           # Configuration & API helper functions
├── login.html              # Halaman Login
├── dashboard.html          # Halaman Dashboard
├── pelanggaran.html        # Halaman Data Pelanggaran
├── objek-melintas.html     # Halaman Objek Melintas
└── README.md
```

## Fitur Utama

### 1. Login System

- Menggunakan JWT untuk authentication
- Token disimpan di localStorage
- Auto-redirect jika sudah login
- Default user: `admin` / `admin123`

### 2. Token Management

Token akan otomatis:
- Disimpan setelah login berhasil
- Dikirim di setiap API request (Authorization header)
- Dihapus saat logout
- Redirect ke login jika token expired (401 response)

### 3. Pagination

- **Data Pelanggaran**: 20 data per halaman
- **Objek Melintas**: 10 data per halaman
- Navigate antar halaman dengan ease

### 4. Search & Filter

- Real-time search functionality
- Filter berdasarkan kategori
- Reset filter capability

### 5. CRUD Operations

Setiap modul mendukung:
- **Create**: Tambah data baru melalui modal form
- **Read**: View data dengan detail
- **Update**: Edit existing data
- **Delete**: Hapus data dengan confirmation

## Halaman-halaman

### Login (`login.html`)
Halaman autentikasi untuk masuk ke aplikasi.

**Features:**
- Username & password input
- Remember me option
- Error handling
- Redirect ke dashboard setelah login sukses

### Dashboard (`dashboard.html`)
Halaman utama dengan overview statistik.

**Features:**
- Statistik cards (total pelanggaran, objek, pending, resolved)
- Recent pelanggaran table
- Recent objek melintas table
- Quick navigation

### Data Pelanggaran (`pelanggaran.html`)
Manajemen data pelanggaran.

**Features:**
- List pelanggaran dengan pagination (20 per halaman)
- Add/Edit/Delete pelanggaran
- Filter berdasarkan tipe (contraflow, overspeed, traffic jam)
- Filter berdasarkan status (pending, processed, resolved)
- Search functionality

**Tipe Pelanggaran:**
- Contraflow
- Overspeed
- Traffic Jam

### Objek Melintas (`objek-melintas.html`)
Manajemen data objek yang melintas.

**Features:**
- List objek melintas dengan pagination (10 per halaman)
- Add/Edit/Delete objek
- Filter berdasarkan tipe (truk, mobil, motor)
- Search functionality

**Tipe Objek:**
- Truk
- Mobil
- Motor

## API Integration

Semua API call dilakukan melalui `CONFIG.apiCall()` helper function:

```javascript
// Example: Get all pelanggaran data
const response = await CONFIG.apiCall('/pelanggaran?page=1');
if (response.status) {
    console.log(response.data);
}
```

### Authentication Flow

1. User submits login form
2. API validates credentials
3. API returns JWT token
4. Frontend stores token in localStorage
5. All subsequent requests include token in Authorization header
6. On 401 response, redirect to login

## Styling

Menggunakan Bootstrap 5 dengan custom styling di [css/style.css](css/style.css):

- Modern gradient backgrounds
- Responsive sidebar navigation
- Card-based layouts
- Custom badges dan buttons
- Smooth transitions
- Mobile-friendly design

## Browser Compatibility

- Chrome (recommended)
- Firefox
- Edge
- Safari

## Troubleshooting

### CORS Errors
Jika mengalami CORS error saat mengakses API:
1. Pastikan backend API berjalan
2. Check API endpoint configuration di `js/config.js`
3. Ensure CORS headers di-set di backend

### Token Expired
Jika tiba-tiba di-redirect ke login:
- Token mungkin sudah expired (2 days)
- Coba login kembali

### Data Not Loading
Jika data tidak muncul:
1. Check browser console untuk error messages
2. Pastikan API berjalan di `http://localhost:8080`
3. Verify JWT token valid

## Development

Untuk development, disarankan menggunakan:
- **VSCode** sebagai code editor
- **Live Server** extension untuk auto-reload
- **Browser DevTools** untuk debugging

## Future Enhancements

Possible improvements:
- Export data to Excel/PDF
- Advanced chart visualizations
- Role-based access control
- File upload untuk gambar
- Dark mode toggle
- Multi-language support
- Real-time updates dengan WebSocket

## Credits

Project ini dibuat untuk keperluan studi kasus pembelajaran Full Stack Development.
