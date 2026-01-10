# API Documentation

## Base URL
```
http://localhost:8080/api
```

## Authentication

Semua endpoint kecuali `/api/auth/login` memerlukan JWT token di header:

```
Authorization: Bearer {your-jwt-token}
```

## Response Format

### Success Response
```json
{
  "status": true,
  "message": "Success message",
  "data": {...}
}
```

### Error Response
```json
{
  "status": false,
  "message": "Error message",
  "error": "Detailed error"
}
```

---

## Endpoints

### 1. Authentication

#### 1.1 Login
Melakukan autentikasi user dan mendapatkan JWT token.

**Endpoint:** `POST /api/auth/login`

**Request Body:**
```json
{
  "username": "admin",
  "password": "admin123"
}
```

**Response (200 OK):**
```json
{
  "status": true,
  "message": "Login successful",
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE2...",
    "user": {
      "id": 1,
      "username": "admin",
      "email": "admin@census.com",
      "full_name": "Administrator"
    }
  }
}
```

**Error Response (401 Unauthorized):**
```json
{
  "status": false,
  "message": "Invalid credentials",
  "error": "Username or password is incorrect"
}
```

#### 1.2 Get Current User
Mendapatkan data user yang sedang login.

**Endpoint:** `GET /api/auth/me`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "status": true,
  "message": "User profile retrieved successfully",
  "data": {
    "user": {
      "id": 1,
      "username": "admin",
      "email": "admin@census.com",
      "full_name": "Administrator"
    }
  }
}
```

**Error Response (401 Unauthorized):**
```json
{
  "status": false,
  "message": "Invalid or expired token",
  "error": "Token validation failed"
}
```

---

### 2. Data Pelanggaran

#### 2.1 List Pelanggaran
Menampilkan daftar data pelanggaran dengan pagination.

**Endpoint:** `GET /api/pelanggaran`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` (optional, default: 1) - Nomor halaman
- `per_page` (optional, default: 20) - Jumlah data per halaman (max: 100)
- `search` (optional) - Kata kunci pencarian

**Example:**
```
GET /api/pelanggaran?page=1&per_page=20&search=contraflow
```

**Response (200 OK):**
```json
{
  "status": true,
  "message": "Data retrieved successfully",
  "data": [
    {
      "id": 1,
      "tipe_pelanggaran": "contraflow",
      "lokasi": "Jl. Sudirman Km 10",
      "tanggal": "2025-01-09 08:30:00",
      "deskripsi": "Kendaraan melawan arus di pagi hari",
      "foto": null,
      "created_by": 1,
      "created_at": "2025-01-09 08:30:00",
      "updated_at": "2025-01-09 08:30:00",
      "created_by_name": "Administrator"
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 20,
    "total": 3,
    "total_pages": 1
  }
}
```

#### 2.2 Get Pelanggaran Detail
Menampilkan detail data pelanggaran berdasarkan ID.

**Endpoint:** `GET /api/pelanggaran/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "status": true,
  "message": "Data retrieved successfully",
  "data": {
    "id": 1,
    "tipe_pelanggaran": "contraflow",
    "lokasi": "Jl. Sudirman Km 10",
    "tanggal": "2025-01-09 08:30:00",
    "deskripsi": "Kendaraan melawan arus di pagi hari",
    "foto": null,
    "created_by": 1,
    "created_at": "2025-01-09 08:30:00",
    "updated_at": "2025-01-09 08:30:00",
    "created_by_name": "Administrator"
  }
}
```

**Error Response (404 Not Found):**
```json
{
  "status": false,
  "message": "Data not found",
  "error": "Pelanggaran with ID 999 not found"
}
```

#### 2.3 Create Pelanggaran
Menambahkan data pelanggaran baru.

**Endpoint:** `POST /api/pelanggaran`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "tipe_pelanggaran": "contraflow",
  "lokasi": "Jl. Sudirman Km 10",
  "tanggal": "2025-01-09 08:30:00",
  "deskripsi": "Kendaraan melawan arus",
  "foto": null
}
```

**Field Requirements:**
- `tipe_pelanggaran` (required) - Enum: "contraflow", "overspeed", "traffic_jam"
- `lokasi` (required) - String, max 255 characters
- `tanggal` (required) - DateTime format
- `deskripsi` (optional) - Text
- `foto` (optional) - String (file path/URL)

**Response (201 Created):**
```json
{
  "status": true,
  "message": "Data pelanggaran created successfully",
  "data": {
    "id": 4,
    "tipe_pelanggaran": "contraflow",
    "lokasi": "Jl. Sudirman Km 10",
    "tanggal": "2025-01-09 08:30:00",
    "deskripsi": "Kendaraan melawan arus",
    "foto": null,
    "created_by": 1,
    "created_at": "2025-01-09 10:00:00",
    "updated_at": "2025-01-09 10:00:00",
    "created_by_name": "Administrator"
  }
}
```

**Error Response (400 Bad Request):**
```json
{
  "status": false,
  "message": "Validation failed",
  "errors": {
    "tipe_pelanggaran": "The tipe_pelanggaran field is required."
  }
}
```

#### 2.4 Update Pelanggaran
Mengupdate data pelanggaran yang sudah ada.

**Endpoint:** `PUT /api/pelanggaran/{id}`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "tipe_pelanggaran": "overspeed",
  "lokasi": "Jl. Sudirman Km 10",
  "tanggal": "2025-01-09 08:30:00",
  "deskripsi": "Kendaraan melebihi batas kecepatan"
}
```

**Response (200 OK):**
```json
{
  "status": true,
  "message": "Data pelanggaran updated successfully",
  "data": {
    "id": 1,
    "tipe_pelanggaran": "overspeed",
    "lokasi": "Jl. Sudirman Km 10",
    "tanggal": "2025-01-09 08:30:00",
    "deskripsi": "Kendaraan melebihi batas kecepatan",
    ...
  }
}
```

#### 2.5 Delete Pelanggaran
Menghapus data pelanggaran.

**Endpoint:** `DELETE /api/pelanggaran/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "status": true,
  "message": "Data pelanggaran deleted successfully"
}
```

---

### 3. Data Objek Melintas

#### 3.1 List Objek Melintas
Menampilkan daftar data objek melintas dengan pagination.

**Endpoint:** `GET /api/objek-melintas`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` (optional, default: 1) - Nomor halaman
- `per_page` (optional, default: 10) - Jumlah data per halaman (max: 100)
- `search` (optional) - Kata kunci pencarian

**Example:**
```
GET /api/objek-melintas?page=1&per_page=10&search=motor
```

**Response (200 OK):**
```json
{
  "status": true,
  "message": "Data retrieved successfully",
  "data": [
    {
      "id": 1,
      "tipe_objek": "motor",
      "jumlah": 45,
      "lokasi": "Jl. Sudirman Km 10",
      "tanggal": "2025-01-09 08:00:00",
      "deskripsi": "Dominasi sepeda motor di jam berangkat kerja",
      "created_by": 1,
      "created_at": "2025-01-09 08:00:00",
      "updated_at": "2025-01-09 08:00:00",
      "created_by_name": "Administrator"
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 10,
    "total": 3,
    "total_pages": 1
  }
}
```

#### 3.2 Get Objek Melintas Detail
Menampilkan detail data objek melintas berdasarkan ID.

**Endpoint:** `GET /api/objek-melintas/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "status": true,
  "message": "Data retrieved successfully",
  "data": {
    "id": 1,
    "tipe_objek": "motor",
    "jumlah": 45,
    "lokasi": "Jl. Sudirman Km 10",
    "tanggal": "2025-01-09 08:00:00",
    "deskripsi": "Dominasi sepeda motor di jam berangkat kerja",
    "created_by": 1,
    "created_at": "2025-01-09 08:00:00",
    "updated_at": "2025-01-09 08:00:00",
    "created_by_name": "Administrator"
  }
}
```

#### 3.3 Create Objek Melintas
Menambahkan data objek melintas baru.

**Endpoint:** `POST /api/objek-melintas`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "tipe_objek": "motor",
  "jumlah": 45,
  "lokasi": "Jl. Sudirman Km 10",
  "tanggal": "2025-01-09 08:00:00",
  "deskripsi": "Dominasi sepeda motor"
}
```

**Field Requirements:**
- `tipe_objek` (required) - Enum: "truk", "mobil", "motor"
- `jumlah` (required) - Integer, minimum 1
- `lokasi` (required) - String, max 255 characters
- `tanggal` (required) - DateTime format
- `deskripsi` (optional) - Text

**Response (201 Created):**
```json
{
  "status": true,
  "message": "Data objek melintas created successfully",
  "data": {
    "id": 4,
    "tipe_objek": "motor",
    "jumlah": 45,
    "lokasi": "Jl. Sudirman Km 10",
    "tanggal": "2025-01-09 08:00:00",
    "deskripsi": "Dominasi sepeda motor",
    "created_by": 1,
    "created_at": "2025-01-09 10:00:00",
    "updated_at": "2025-01-09 10:00:00",
    "created_by_name": "Administrator"
  }
}
```

#### 3.4 Update Objek Melintas
Mengupdate data objek melintas yang sudah ada.

**Endpoint:** `PUT /api/objek-melintas/{id}`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "tipe_objek": "mobil",
  "jumlah": 30,
  "lokasi": "Jl. Gatot Subroto Km 5",
  "tanggal": "2025-01-09 09:00:00",
  "deskripsi": "Mobil pribadi dominan"
}
```

**Response (200 OK):**
```json
{
  "status": true,
  "message": "Data objek melintas updated successfully",
  "data": {
    "id": 1,
    "tipe_objek": "mobil",
    "jumlah": 30,
    "lokasi": "Jl. Gatot Subroto Km 5",
    ...
  }
}
```

#### 3.5 Delete Objek Melintas
Menghapus data objek melintas.

**Endpoint:** `DELETE /api/objek-melintas/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "status": true,
  "message": "Data objek melintas deleted successfully"
}
```

---

## HTTP Status Codes

- `200 OK` - Request berhasil
- `201 Created` - Resource berhasil dibuat
- `400 Bad Request` - Request tidak valid atau validation error
- `401 Unauthorized` - Token tidak valid atau expired
- `404 Not Found` - Resource tidak ditemukan
- `500 Internal Server Error` - Error di server

---

## Error Codes

| Error | Description |
|-------|-------------|
| `Token not provided` | Header Authorization tidak ada |
| `Invalid or expired token` | Token tidak valid atau sudah expired |
| `Missing required fields` | Field wajib tidak diisi |
| `Validation failed` | Validasi data gagal |
| `Data not found` | Resource tidak ditemukan |

---

## Testing dengan Postman/cURL

### Login dan dapatkan token
```bash
curl -X POST http://localhost:8080/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}'
```

### Get list pelanggaran
```bash
curl -X GET "http://localhost:8080/api/pelanggaran?page=1&per_page=20" \
  -H "Authorization: Bearer {your-token}"
```

### Create pelanggaran baru
```bash
curl -X POST http://localhost:8080/api/pelanggaran \
  -H "Authorization: Bearer {your-token}" \
  -H "Content-Type: application/json" \
  -d '{
    "tipe_pelanggaran": "contraflow",
    "lokasi": "Jl. Sudirman Km 10",
    "tanggal": "2025-01-09 08:30:00",
    "deskripsi": "Kendaraan melawan arus"
  }'
```
