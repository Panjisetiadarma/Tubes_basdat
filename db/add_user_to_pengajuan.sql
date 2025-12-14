-- ============================================
-- SQL untuk Menambahkan Kolom id_user ke Tabel Pengajuan
-- Untuk tracking user yang membuat pengajuan
-- ============================================

USE project_notaris;

-- Tambah kolom id_user ke tabel Pengajuan (jika belum ada)
SET @exist := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE TABLE_SCHEMA = 'project_notaris' 
               AND TABLE_NAME = 'Pengajuan' 
               AND COLUMN_NAME = 'id_user');
SET @sqlstmt := IF(@exist = 0, 
    'ALTER TABLE Pengajuan ADD COLUMN id_user INT, ADD FOREIGN KEY (id_user) REFERENCES User(id_user) ON DELETE SET NULL', 
    'SELECT "Kolom id_user sudah ada" AS message');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Alternatif sederhana (jika stored procedure tidak didukung):
-- ALTER TABLE Pengajuan ADD COLUMN IF NOT EXISTS id_user INT;
-- ALTER TABLE Pengajuan ADD FOREIGN KEY (id_user) REFERENCES User(id_user) ON DELETE SET NULL;


