-- Data Default untuk Database
-- Jalankan file ini setelah import db.sql

USE project_notaris;

-- Insert Status Pengajuan Default
INSERT INTO Status_Pengajuan (nama_status, deskripsi) VALUES
('Menunggu', 'Pengajuan baru, menunggu peninjauan'),
('Diproses', 'Pengajuan sedang dalam proses'),
('Menunggu Dokumen', 'Menunggu dokumen tambahan dari client'),
('Selesai', 'Pengajuan telah selesai diproses'),
('Ditolak', 'Pengajuan ditolak'),
('Dibatalkan', 'Pengajuan dibatalkan oleh client');

-- Insert User AdminNotaris (password: admin123)
-- Password hash untuk 'admin123' (generate dengan generate_password.php jika perlu)
-- Default hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi (password: password)
-- Untuk password yang benar, jalankan generate_password.php atau update manual
INSERT INTO User (username, password, nama_lengkap, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator Notaris', 'AdminNotaris');

-- Insert User biasa (password: user123)
INSERT INTO User (username, password, nama_lengkap, role) VALUES
('user', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'User Test', 'user');

-- CATATAN: Hash di atas adalah hash untuk password 'password'
-- Untuk menggunakan password 'admin123' dan 'user123', jalankan generate_password.php
-- atau update manual dengan query berikut setelah import:
-- UPDATE User SET password = '$2y$10$[hash_dari_generate_password.php]' WHERE username = 'admin';
-- UPDATE User SET password = '$2y$10$[hash_dari_generate_password.php]' WHERE username = 'user';

-- Insert Notaris Contoh
INSERT INTO Notaris (nama_notaris, nomor_notaris, alamat_kantor, telepon) VALUES
('Dr. Ahmad Hidayat, S.H., M.H.', 'N-001', 'Jl. Sudirman No. 123, Jakarta Pusat', '021-12345678'),
('Siti Nurhaliza, S.H.', 'N-002', 'Jl. Thamrin No. 456, Jakarta Pusat', '021-87654321');

-- Insert PPAT Contoh
INSERT INTO Ppat (nama_ppat, nomor_ppat, alamat_kantor, telepon) VALUES
('Budi Santoso, S.H., M.Kn.', 'PPAT-001', 'Jl. Gatot Subroto No. 789, Jakarta Selatan', '021-11223344');

-- Insert Client Contoh (Pribadi)
INSERT INTO Client (jenis_client, nomor_telepon, email, alamat) VALUES
('pribadi', '081234567890', 'john.doe@email.com', 'Jl. Merdeka No. 1, Jakarta'),
('pribadi', '081987654321', 'jane.smith@email.com', 'Jl. Kemerdekaan No. 2, Jakarta');

-- Insert Detail Pribadi
INSERT INTO Pribadi (id_client, nama_lengkap, nik, tempat_lahir, tanggal_lahir) VALUES
(1, 'John Doe', '3201010101010001', 'Jakarta', '1990-01-01'),
(2, 'Jane Smith', '3201010101010002', 'Bandung', '1992-05-15');

-- Insert Client Contoh (Perusahaan)
INSERT INTO Client (jenis_client, nomor_telepon, email, alamat) VALUES
('perusahaan', '021-55555555', 'info@company.com', 'Jl. Industri No. 100, Jakarta');

-- Insert Detail Perusahaan
INSERT INTO Perusahaan (id_client, nama_perusahaan, npwp, nama_direktur) VALUES
(3, 'PT Contoh Perusahaan', '01.234.567.8-901.000', 'Ahmad Wijaya');

-- Insert Pengajuan Contoh
INSERT INTO Pengajuan (id_client, id_notaris, id_status, jenis_pengajuan, deskripsi, tanggal_pengajuan) VALUES
(1, 1, 1, 'Akta Jual Beli Tanah', 'Pengajuan akta jual beli tanah di Jakarta Selatan', '2024-01-15'),
(2, 1, 2, 'Legalisasi Dokumen', 'Legalisasi ijazah dan transkrip nilai', '2024-01-20'),
(3, 2, 3, 'Surat Kuasa', 'Surat kuasa untuk pengurusan dokumen', '2024-01-25'),
(1, 1, 4, 'Akta Hibah', 'Akta hibah tanah dari orang tua', '2024-02-01'),
(2, 2, 2, 'Akta Perjanjian Sewa', 'Perjanjian sewa menyewa ruko', '2024-02-10');

-- Insert Jadwal Contoh
INSERT INTO Jadwal (id_pengajuan, tanggal_jadwal, kegiatan, keterangan) VALUES
(1, '2024-02-01 10:00:00', 'Penandatanganan Akta', 'Bertemu di kantor notaris untuk penandatanganan'),
(2, '2024-02-05 14:00:00', 'Verifikasi Dokumen', 'Verifikasi dokumen asli'),
(3, '2024-02-10 09:00:00', 'Konsultasi', 'Konsultasi mengenai surat kuasa');

