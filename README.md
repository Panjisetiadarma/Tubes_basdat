# Sistem Notaris Profesional

Aplikasi web untuk mengelola layanan notaris, pengajuan dokumen, dan manajemen client.

## Fitur

- ✅ **Authentication System** - Login dan Register dengan database MySQL
- ✅ **Dashboard** - Statistik dan ringkasan aktivitas
- ✅ **Manajemen Client** - Kelola data client pribadi dan perusahaan
- ✅ **Manajemen Pengajuan** - Tracking status pengajuan dokumen
- ✅ **Jadwal** - Manajemen jadwal pertemuan
- ✅ **Arsip File** - Upload dan kelola dokumen
- ✅ **Transaksi** - Tracking pembayaran

## Teknologi

- **Frontend**: HTML5, CSS3, JavaScript (Vanilla), Bootstrap 5
- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Server**: Apache (XAMPP)

## Struktur Project

```
Tubes_basdat/
├── api/                    # API Endpoints PHP
│   ├── login.php          # Login endpoint
│   ├── register.php       # Register endpoint
│   ├── logout.php         # Logout endpoint
│   ├── check_auth.php     # Check authentication
│   ├── client.php         # CRUD Client
│   ├── pengajuan.php      # CRUD Pengajuan
│   └── dashboard_stats.php # Dashboard statistics
├── assets/                # Assets (images, dll)
├── components/            # Komponen HTML
├── js/                    # JavaScript files
│   ├── auth.js           # Authentication manager
│   ├── dashboard.js      # Dashboard logic
│   ├── main.js           # Main page logic
│   └── navbar.js         # Navbar logic
├── styles/                # CSS files
├── uploads/              # Uploaded files (auto-created)
├── logs/                 # Error logs (auto-created)
├── config.php            # Database configuration
├── db.sql                # Database schema
├── db_default_data.sql   # Default data
├── index.html            # Home page
├── auth.html             # Login/Register page
├── dashboard.html         # Dashboard page
└── SETUP.md              # Setup instructions
```

## Quick Start

### 1. Setup Database

1. Buka phpMyAdmin: `http://localhost/phpmyadmin`
2. Import `db.sql` untuk membuat database dan tabel
3. (Opsional) Import `db_default_data.sql` untuk data contoh

### 2. Konfigurasi

Edit `config.php` dan sesuaikan kredensial database:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'project_notaris');
```

### 3. Jalankan Aplikasi

1. Pastikan XAMPP (Apache & MySQL) sudah running
2. Buka browser: `http://localhost/Tubes_basdat/`
3. Register user baru atau login dengan:
   - Username: `admin`
   - Password: `admin123` (jika sudah import db_default_data.sql)

## API Documentation

### Authentication

#### Login
```http
POST /api/login.php
Content-Type: application/json

{
  "email": "username@example.com",
  "password": "password123"
}
```

#### Register
```http
POST /api/register.php
Content-Type: application/json

{
  "nama_lengkap": "John Doe",
  "username": "johndoe",
  "email": "john@example.com",
  "password": "password123",
  "confirm_password": "password123",
  "phone": "081234567890"
}
```

#### Logout
```http
POST /api/logout.php
```

#### Check Auth
```http
GET /api/check_auth.php
```

### Data Management

#### Get Pengajuan
```http
GET /api/pengajuan.php
GET /api/pengajuan.php?id=1
```

#### Create Pengajuan
```http
POST /api/pengajuan.php
Content-Type: application/json

{
  "id_client": 1,
  "id_notaris": 1,
  "id_status": 1,
  "jenis_pengajuan": "Akta Jual Beli",
  "deskripsi": "Deskripsi pengajuan",
  "tanggal_pengajuan": "2024-01-15"
}
```

#### Get Client
```http
GET /api/client.php
GET /api/client.php?id=1
```

#### Create Client
```http
POST /api/client.php
Content-Type: application/json

{
  "jenis_client": "pribadi",
  "nama_lengkap": "John Doe",
  "nik": "3201010101010001",
  "nomor_telepon": "081234567890",
  "email": "john@example.com",
  "alamat": "Jl. Contoh No. 1"
}
```

#### Dashboard Stats
```http
GET /api/dashboard_stats.php
```

## Database Schema

### Tabel Utama

- **User** - Data user sistem (admin, notaris, ppat, staff)
- **Client** - Data client (pribadi/perusahaan)
- **Pribadi** - Detail client pribadi
- **Perusahaan** - Detail client perusahaan
- **Notaris** - Data notaris
- **Ppat** - Data PPAT
- **Pengajuan** - Data pengajuan dokumen
- **Status_Pengajuan** - Master status
- **Jadwal** - Jadwal pertemuan
- **Arsip_File** - File dokumen
- **Transaksi** - Data transaksi pembayaran

## Security Features

- ✅ Password hashing dengan `password_hash()`
- ✅ Session management yang aman
- ✅ Input sanitization
- ✅ SQL injection prevention (prepared statements)
- ✅ XSS protection
- ✅ CORS configuration

## Development

### Menambah Fitur Baru

1. Buat endpoint API di folder `api/`
2. Update JavaScript di folder `js/` untuk consume API
3. Update HTML jika perlu UI baru

### Testing

1. Test di browser dengan Developer Tools
2. Cek Network tab untuk request/response
3. Cek Console untuk error JavaScript
4. Cek `logs/error.log` untuk error PHP

## Troubleshooting

Lihat file `SETUP.md` untuk troubleshooting lengkap.

### Common Issues

**Database connection error**
- Pastikan MySQL running
- Cek konfigurasi di `config.php`

**Session tidak bekerja**
- Pastikan cookies enabled di browser
- Cek PHP session configuration

**API tidak response**
- Cek error log di `logs/error.log`
- Pastikan folder `api/` bisa diakses
- Cek CORS configuration

## License

Project ini dibuat untuk keperluan akademik.

## Author

Tubes Basis Data - Sistem Notaris
