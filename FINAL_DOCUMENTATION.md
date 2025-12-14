# 📋 Dokumentasi Final - Sistem Notaris Pro

## 🎯 Ringkasan Aplikasi

Aplikasi **NotarisPro** adalah sistem manajemen layanan notaris berbasis web yang dikembangkan menggunakan PHP dan MySQL. Sistem ini dirancang untuk mengelola pengajuan dokumen, transaksi, jadwal, konsultasi, dan arsip file dengan dukungan role-based access control (Admin dan User).

---

## 📁 Struktur File

### **File Utama**
```
Tubes_basdat/
├── auth.php                    # Login & Register
├── dashboard.php               # Dashboard utama dengan search & statistik
├── management_client.php       # Management client (Admin only)
├── client.php                  # Data client
├── pengajuan.php               # Data pengajuan
├── transaksi.php               # Data transaksi
├── riwayat.php                 # Riwayat transaksi
├── jadwal.php                  # Jadwal janji temu
├── arsip.php                   # Arsip file dengan upload
├── konsultasi.php              # Konsultasi online dengan chat
├── profil.php                  # Profil akun user
├── koneksi.php                 # Koneksi database & helper functions
├── common_crud.php             # CRUD helper functions
└── update_passwords_plain.php  # Utility update password
```

### **Komponen**
```
components/
├── navbar.php                  # Navbar dashboard
└── sidebar.php                 # Sidebar dengan menu role-based
```

### **Database**
```
db/
├── db.sql                      # Schema database utama
├── db_default_data.sql         # Data default
├── konsultasi_complete.sql     # Schema konsultasi & chat
├── management_client.sql       # View management client
├── add_user_to_pengajuan.sql   # SQL tambah kolom id_user
├── user_profile.sql            # SQL tambah kolom profil
└── update_passwords_no_encrypt.sql  # Update password ke plain text
```

### **Styles**
```
styles/
├── main.css
├── dashboard.css
├── navbar.css
├── sidebar.css
├── auth.css
└── [file spesifik lainnya]
```

---

## 🔐 Sistem Autentikasi

### **Login & Register**
- **File**: `auth.php`
- **Fitur**:
  - Login dengan username & password (plain text - development only)
  - Register user baru
  - Validasi form
  - Auto login setelah register
  - Password minimum 3 karakter

### **Password**
- **Tidak dienkripsi** (untuk development)
- Fungsi `hash_password()` dan `verify_password()` di `koneksi.php` menggunakan plain text
- Script `update_passwords_plain.php` untuk update password yang terenkripsi

---

## 👥 Role-Based Access Control

### **Admin (AdminNotaris)**
**Akses Penuh:**
- ✅ Dashboard dengan semua statistik
- ✅ Management Client (CRUD lengkap)
- ✅ Data Client
- ✅ Pengajuan (lihat semua, tambah, hapus)
- ✅ Transaksi (lihat semua, tambah, hapus)
- ✅ Jadwal (lihat semua janji, tambah, hapus)
- ✅ Arsip File (lihat semua, upload, hapus)
- ✅ Riwayat Transaksi (lihat semua)
- ✅ Konsultasi Online
- ✅ Profil Akun

### **User**
**Akses Terbatas:**
- ✅ Dashboard dengan statistik terbatas
- ✅ Pengajuan Saya (hanya pengajuan mereka)
- ✅ Riwayat Transaksi (hanya transaksi mereka)
- ✅ Konsultasi Online
- ✅ Profil Akun

---

## 📊 Fitur Utama

### **1. Dashboard** (`dashboard.php`)
**Fitur:**
- Welcome section dengan nama user
- Statistik cards:
  - Dokumen Diproses
  - Janji Mendatang
  - Verifikasi Selesai
  - Konsultasi Aktif
- Aktivitas terbaru (5 terakhir)
- **Form Search** - pencarian pengajuan (nama, jenis, status, client, dll)
- **Aksi Cepat:**
  - Tambah Pengajuan (Admin)
  - Management Client (Admin)
  - Upload Dokumen
  - Buat Janji
  - Konsultasi
  - Riwayat Transaksi
- **Daftar Pengajuan** dengan tabel lengkap
- Modal tambah pengajuan (Admin)

### **2. Management Client** (`management_client.php`)
**Fitur:**
- ✅ Tambah client (Pribadi/Perusahaan)
- ✅ Edit client
- ✅ Hapus client
- ✅ Tabel dengan detail lengkap
- ✅ Form dinamis berdasarkan jenis client
- **Akses**: Admin only

### **3. Pengajuan** (`pengajuan.php`)
**Fitur:**
- ✅ Daftar pengajuan
- ✅ Tambah pengajuan (Admin)
- ✅ Hapus pengajuan (Admin)
- ✅ Filter: Admin lihat semua, User lihat pengajuan mereka
- ✅ Tabel dengan info client, notaris, status

### **4. Transaksi** (`transaksi.php`)
**Fitur:**
- ✅ Daftar transaksi
- ✅ Tambah transaksi (Admin)
- ✅ Hapus transaksi (Admin)
- ✅ Filter: Admin lihat semua, User lihat transaksi mereka
- ✅ Info pengajuan terkait

### **5. Jadwal** (`jadwal.php`)
**Fitur:**
- ✅ Daftar jadwal janji
- ✅ Tambah jadwal (Admin)
- ✅ Hapus jadwal (Admin)
- ✅ **Admin**: Lihat semua janji dengan info client & kontak
- ✅ **User**: Hanya lihat janji dari pengajuan mereka
- ✅ Format tanggal & waktu

### **6. Arsip File** (`arsip.php`)
**Fitur:**
- ✅ Upload file dokumen (PDF, DOC, DOCX, JPG, PNG)
- ✅ Daftar file arsip
- ✅ Download file
- ✅ Hapus file (Admin)
- ✅ Info ukuran file (format otomatis)
- ✅ Filter: Admin lihat semua, User lihat arsip mereka
- ✅ Folder upload: `uploads/arsip/`

### **7. Konsultasi Online** (`konsultasi.php`)
**Fitur:**
- ✅ Daftar konsultasi
- ✅ Tambah konsultasi (Chat, Video Call, Janji Temu)
- ✅ Detail konsultasi dengan chat messages
- ✅ Kirim pesan chat
- ✅ Update status konsultasi
- ✅ Info client, penangan, link meeting

### **8. Riwayat Transaksi** (`riwayat.php`)
**Fitur:**
- ✅ Daftar riwayat transaksi
- ✅ Info lengkap: ID, pengajuan, jenis, jumlah, metode, tanggal, status
- ✅ Badge status berwarna (pending, lunas, gagal)
- ✅ Filter: Admin lihat semua, User lihat transaksi mereka
- ✅ Format rupiah

### **9. Profil Akun** (`profil.php`)
**Fitur:**
- ✅ Edit profil (nama, telepon, alamat, bio)
- ✅ Upload foto profil
- ✅ Ubah password
- ✅ Informasi akun (ID, tanggal bergabung, login terakhir)
- ✅ Header profil dengan gradient

---

## 🗄️ Database Schema

### **Tabel Utama**

#### **User**
- `id_user`, `username`, `password` (plain text), `nama_lengkap`, `role`
- `foto_profil`, `nomor_telepon`, `alamat`, `bio`, `last_login`

#### **Client**
- `id_client`, `jenis_client` (pribadi/perusahaan), `nomor_telepon`, `email`, `alamat`

#### **Pribadi**
- `id_pribadi`, `id_client`, `nama_lengkap`, `nik`, `tempat_lahir`, `tanggal_lahir`

#### **Perusahaan**
- `id_perusahaan`, `id_client`, `nama_perusahaan`, `npwp`, `nama_direktur`

#### **Pengajuan**
- `id_pengajuan`, `id_client`, `id_notaris`, `id_ppat`, `id_status`, `id_user`
- `jenis_pengajuan`, `deskripsi`, `tanggal_pengajuan`, `tanggal_selesai`

#### **Jadwal**
- `id_jadwal`, `id_pengajuan`, `tanggal_jadwal`, `kegiatan`, `keterangan`

#### **Transaksi**
- `id_transaksi`, `id_pengajuan`, `jumlah`, `metode_pembayaran`
- `tanggal_transaksi`, `status_pembayaran`, `keterangan`

#### **Arsip_File**
- `id_file`, `id_pengajuan`, `nama_file`, `path_file`, `tipe_file`, `ukuran_file`

#### **Konsultasi**
- `id_konsultasi`, `id_client`, `id_notaris`, `id_ppat`, `id_user`
- `jenis_konsultasi`, `topik`, `pesan`, `tanggal_konsultasi`, `status`, `link_meeting`

#### **Chat_Message**
- `id_message`, `id_konsultasi`, `id_pengirim`, `pesan`, `tipe`, `file_url`, `waktu_kirim`

---

## 🔧 Setup & Instalasi

### **1. Database Setup**
```sql
-- Import file SQL berikut (urutan):
1. db/db.sql                    # Schema utama
2. db/db_default_data.sql       # Data default
3. db/konsultasi_complete.sql   # Schema konsultasi
4. db/add_user_to_pengajuan.sql # Tambah kolom id_user
5. db/user_profile.sql          # Tambah kolom profil
```

### **2. Konfigurasi Database**
Edit `koneksi.php`:
```php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'project_notaris';
```

### **3. Update Password (Jika Perlu)**
Akses: `http://localhost/Tubes_basdat/update_passwords_plain.php`
- Update semua password menjadi plain text
- Atau update individual

### **4. Folder Upload**
Pastikan folder berikut ada dan writable:
- `uploads/arsip/` - untuk file arsip
- `uploads/profil/` - untuk foto profil

---

## 🎨 Tema & Styling

### **Konsistensi Tema**
Semua halaman menggunakan:
- ✅ Font: Inter & Poppins (Google Fonts)
- ✅ Bootstrap 5.3.0
- ✅ Font Awesome 6.4.0
- ✅ Class: `dashboard-body`, `dashboard-container`, `main-content`
- ✅ Glass effect cards
- ✅ Gradient colors (primary: #6A85FF, secondary: #7DE2F2)

### **Komponen Reusable**
- `components/navbar.php` - Navbar dengan user menu
- `components/sidebar.php` - Sidebar dengan menu role-based

---

## 🐛 Perbaikan yang Dilakukan

### **1. Struktur File**
- ✅ Menghapus duplikat `Konsultasi.php` (huruf besar)
- ✅ Semua file menggunakan lowercase
- ✅ Konsistensi nama file

### **2. Database**
- ✅ Menambahkan kolom `id_user` ke tabel `Pengajuan` (untuk tracking)
- ✅ Menambahkan kolom profil ke tabel `User`
- ✅ View untuk management client

### **3. Filter Data**
- ✅ **Jadwal**: Admin lihat semua, User lihat janji mereka
- ✅ **Pengajuan**: Admin lihat semua, User lihat pengajuan mereka
- ✅ **Transaksi**: Admin lihat semua, User lihat transaksi mereka
- ✅ **Riwayat**: Admin lihat semua, User lihat riwayat mereka
- ✅ **Arsip**: Admin lihat semua, User lihat arsip mereka

### **4. Fitur Lengkap**
- ✅ Upload file di arsip.php (dengan validasi)
- ✅ Form search di dashboard
- ✅ Tabel pengajuan di dashboard
- ✅ Modal upload dokumen di dashboard
- ✅ Modal buat janji di dashboard
- ✅ Management client lengkap dengan CRUD

### **5. Bug Fixes**
- ✅ Perbaikan query filter user
- ✅ Perbaikan insert data dengan NULL handling
- ✅ Perbaikan struktur HTML konsisten
- ✅ Perbaikan sidebar toggle
- ✅ Perbaikan error handling

---

## 📝 Catatan Penting

### **Security**
⚠️ **PENTING**: Aplikasi ini menggunakan password **plain text** karena untuk development. 
**JANGAN gunakan di production!**

### **File Upload**
- Maksimal ukuran file: 10MB (dapat diubah di `php.ini`)
- Format yang didukung: PDF, DOC, DOCX, JPG, PNG
- File disimpan di: `uploads/arsip/` dan `uploads/profil/`

### **Browser Support**
- Chrome, Firefox, Edge (versi terbaru)
- Responsive design untuk mobile

---

## 🚀 Fitur Tambahan yang Bisa Dikembangkan

1. **Notifikasi Real-time** - Notifikasi untuk update status
2. **Export Data** - Export ke Excel/PDF
3. **Laporan** - Generate laporan bulanan/tahunan
4. **Email Notification** - Notifikasi via email
5. **Multi-language** - Dukungan bahasa
6. **API** - RESTful API untuk mobile app
7. **Advanced Search** - Filter advanced dengan multiple criteria
8. **Calendar View** - Tampilan kalender untuk jadwal
9. **Dashboard Charts** - Grafik statistik interaktif
10. **File Preview** - Preview file tanpa download

---

## 📞 Support

Jika ada pertanyaan atau menemukan bug, silakan:
1. Cek file log di folder `logs/`
2. Cek error di browser console
3. Cek error PHP di `php.ini` (display_errors = On)

---

## ✅ Checklist Final

- [x] Semua fitur lengkap dan berfungsi
- [x] Role-based access control bekerja
- [x] Filter data berdasarkan user role
- [x] Upload file berfungsi
- [x] Search berfungsi
- [x] Tema konsisten
- [x] Tidak ada error/bug
- [x] Struktur file rapi
- [x] Dokumentasi lengkap

---

**Versi**: 1.0 Final  
**Tanggal**: 2024  
**Developer**: NotarisPro Team


