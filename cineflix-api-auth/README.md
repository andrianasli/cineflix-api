# CineTix API — v2.0 (dengan Autentikasi)

REST API untuk sistem bioskop CineTix, dilengkapi fitur autentikasi berbasis **Bearer Token**.

---

## Perubahan dari v1.0

| Komponen | Keterangan |
|---|---|
| Tabel `api_users` | Menyimpan username dan password (bcrypt) untuk login API |
| Tabel `api_tokens` | Menyimpan token aktif beserta waktu expired (24 jam) |
| `POST /auth/register` | Endpoint registrasi akun API baru |
| `POST /auth/login` | Endpoint login → menghasilkan token 64-karakter hex |
| `POST /auth/logout` | Endpoint logout → menghapus token dari database |
| Semua endpoint lama | Wajib menyertakan `Authorization: Bearer <token>` |

---

## Struktur Proyek

```
cineflix-api/
├── config.php              ← Konfigurasi DB + fungsi requireAuth() & getBearerToken()
├── index.php               ← Router utama (sudah ditambah route auth)
├── seed_auth.php           ← Script untuk membuat akun admin default
├── database.sql            ← Schema DB lengkap (termasuk api_users & api_tokens)
├── openapi.yaml            ← Dokumentasi API (OpenAPI 3.0)
└── controllers/
    ├── AuthController.php  ← LOGIN / LOGOUT / REGISTER
    ├── UserController.php
    ├── FilmController.php
    ├── ScheduleController.php
    ├── BookingController.php
    ├── PaymentController.php
    └── StatisticsController.php
```

---

## Setup

### 1. Import database

```sql
-- Di phpMyAdmin atau MySQL CLI:
SOURCE /path/to/database.sql;
```

### 2. Buat akun admin default

```bash
php seed_auth.php
```

Ini akan membuat akun:
- **Username:** `admin`
- **Password:** `admin123`

### 3. Konfigurasi database

Edit `config.php` sesuai environment kamu:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'db_cinetix');
define('DB_USER', 'root');
define('DB_PASS', '');
```

---

## Cara Menggunakan API

### Langkah 1 — Login

```http
POST /cineflix-api/auth/login
Content-Type: application/json

{
  "username": "admin",
  "password": "admin123"
}
```

Response:
```json
{
  "message": "Login berhasil",
  "token": "a3f5c7d9e1b2f4a8c6d0e2f5a7b9c1d3e5f7a9b1c3d5e7f9a1b3c5d7e9f1a3b5",
  "expires_at": "2026-05-31 10:00:00"
}
```

### Langkah 2 — Gunakan token di setiap request

```http
GET /cineflix-api/films
Authorization: Bearer a3f5c7d9e1b2f4a8...
```

### Langkah 3 — Logout (opsional)

```http
POST /cineflix-api/auth/logout
Authorization: Bearer a3f5c7d9e1b2f4a8...
```

---

## Daftar Endpoint

### Authentication (tidak perlu token)

| Method | Endpoint | Deskripsi |
|---|---|---|
| POST | `/auth/register` | Daftar akun API baru |
| POST | `/auth/login` | Login → dapatkan token |
| POST | `/auth/logout` | Logout → hapus token |

### Protected Endpoints (wajib token)

| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | `/films` | Daftar film |
| GET | `/films/{id}` | Detail film |
| POST | `/films` | Tambah film |
| PUT | `/films/{id}` | Update film |
| DELETE | `/films/{id}` | Hapus film |
| GET | `/users` | Daftar user |
| GET | `/users/{id}` | Detail user |
| POST | `/users` | Tambah user |
| PUT | `/users/{id}` | Update user |
| DELETE | `/users/{id}` | Hapus user |
| GET | `/schedules` | Daftar jadwal |
| GET | `/schedules/{id}` | Detail jadwal |
| POST | `/schedules` | Tambah jadwal |
| PUT | `/schedules/{id}` | Update jadwal |
| DELETE | `/schedules/{id}` | Hapus jadwal |
| GET | `/bookings` | Daftar booking |
| GET | `/bookings/{id}` | Detail booking |
| POST | `/bookings` | Buat booking |
| PUT | `/bookings/{id}` | Update booking |
| DELETE | `/bookings/{id}` | Hapus booking |
| GET | `/payments` | Daftar payment |
| GET | `/payments/{id}` | Detail payment |
| POST | `/payments` | Buat payment |
| PUT | `/payments/{id}` | Update payment |
| DELETE | `/payments/{id}` | Hapus payment |
| GET | `/statistics` | Statistik penjualan |

---

## Validasi Token

| Kondisi | HTTP Status | Response |
|---|---|---|
| Tidak ada header Authorization | `401` | `"Token tidak ditemukan"` |
| Token salah / tidak ada di DB | `401` | `"Token tidak valid atau sudah expired"` |
| Token sudah expired (>24 jam) | `401` | `"Token tidak valid atau sudah expired"` |
| Token valid | `200` | Data yang diminta |

---

## Mekanisme Token

- Token dihasilkan dengan `bin2hex(random_bytes(32))` → **64 karakter hexadecimal**
- Disimpan di tabel `api_tokens` dengan kolom `expires_at`
- Berlaku selama **24 jam** sejak login
- Setiap login baru akan menghapus token lama (single-session)
- Token dikirim via header: `Authorization: Bearer <token>`

