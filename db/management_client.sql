-- ============================================
-- SQL untuk Management Client
-- Tabel sudah ada di db.sql, ini hanya dokumentasi
-- ============================================

USE project_notaris;

-- Tabel Client (sudah ada)
-- CREATE TABLE Client (
--     id_client INT AUTO_INCREMENT PRIMARY KEY,
--     jenis_client ENUM('pribadi', 'perusahaan') NOT NULL,
--     nomor_telepon VARCHAR(20),
--     email VARCHAR(100),
--     alamat TEXT,
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
-- );

-- Tabel Pribadi (sudah ada)
-- CREATE TABLE Pribadi (
--     id_pribadi INT AUTO_INCREMENT PRIMARY KEY,
--     id_client INT NOT NULL,
--     nama_lengkap VARCHAR(100) NOT NULL,
--     nik VARCHAR(20) UNIQUE,
--     tempat_lahir VARCHAR(50),
--     tanggal_lahir DATE,
--     FOREIGN KEY (id_client) REFERENCES Client(id_client) ON DELETE CASCADE
-- );

-- Tabel Perusahaan (sudah ada)
-- CREATE TABLE Perusahaan (
--     id_perusahaan INT AUTO_INCREMENT PRIMARY KEY,
--     id_client INT NOT NULL,
--     nama_perusahaan VARCHAR(100) NOT NULL,
--     npwp VARCHAR(30) UNIQUE,
--     nama_direktur VARCHAR(100),
--     FOREIGN KEY (id_client) REFERENCES Client(id_client) ON DELETE CASCADE
-- );

-- View untuk melihat client lengkap
CREATE OR REPLACE VIEW View_Client_Management AS
SELECT 
    c.id_client,
    c.jenis_client,
    c.nomor_telepon,
    c.email,
    c.alamat,
    c.created_at,
    p.nama_lengkap,
    p.nik,
    p.tempat_lahir,
    p.tanggal_lahir,
    pr.nama_perusahaan,
    pr.npwp,
    pr.nama_direktur,
    COALESCE(p.nama_lengkap, pr.nama_perusahaan) AS nama
FROM Client c
LEFT JOIN Pribadi p ON c.id_client = p.id_client AND c.jenis_client = 'pribadi'
LEFT JOIN Perusahaan pr ON c.id_client = pr.id_client AND c.jenis_client = 'perusahaan';


