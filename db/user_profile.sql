-- ============================================
-- SQL untuk Menambahkan Kolom Profil ke Tabel User
-- ============================================

USE project_notaris;

-- Tambah kolom untuk profil di tabel User (jika belum ada)
-- Cek dulu apakah kolom sudah ada, jika belum tambahkan

-- Kolom foto_profil
SET @exist := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE TABLE_SCHEMA = 'project_notaris' 
               AND TABLE_NAME = 'User' 
               AND COLUMN_NAME = 'foto_profil');
SET @sqlstmt := IF(@exist = 0, 
    'ALTER TABLE User ADD COLUMN foto_profil VARCHAR(255) DEFAULT NULL', 
    'SELECT "Kolom foto_profil sudah ada" AS message');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Kolom nomor_telepon
SET @exist := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE TABLE_SCHEMA = 'project_notaris' 
               AND TABLE_NAME = 'User' 
               AND COLUMN_NAME = 'nomor_telepon');
SET @sqlstmt := IF(@exist = 0, 
    'ALTER TABLE User ADD COLUMN nomor_telepon VARCHAR(20) DEFAULT NULL', 
    'SELECT "Kolom nomor_telepon sudah ada" AS message');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Kolom alamat
SET @exist := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE TABLE_SCHEMA = 'project_notaris' 
               AND TABLE_NAME = 'User' 
               AND COLUMN_NAME = 'alamat');
SET @sqlstmt := IF(@exist = 0, 
    'ALTER TABLE User ADD COLUMN alamat TEXT DEFAULT NULL', 
    'SELECT "Kolom alamat sudah ada" AS message');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Kolom bio
SET @exist := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE TABLE_SCHEMA = 'project_notaris' 
               AND TABLE_NAME = 'User' 
               AND COLUMN_NAME = 'bio');
SET @sqlstmt := IF(@exist = 0, 
    'ALTER TABLE User ADD COLUMN bio TEXT DEFAULT NULL', 
    'SELECT "Kolom bio sudah ada" AS message');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Kolom last_login
SET @exist := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE TABLE_SCHEMA = 'project_notaris' 
               AND TABLE_NAME = 'User' 
               AND COLUMN_NAME = 'last_login');
SET @sqlstmt := IF(@exist = 0, 
    'ALTER TABLE User ADD COLUMN last_login TIMESTAMP NULL DEFAULT NULL', 
    'SELECT "Kolom last_login sudah ada" AS message');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Alternatif sederhana (jika stored procedure tidak didukung):
-- ALTER TABLE User ADD COLUMN IF NOT EXISTS foto_profil VARCHAR(255) DEFAULT NULL;
-- ALTER TABLE User ADD COLUMN IF NOT EXISTS nomor_telepon VARCHAR(20) DEFAULT NULL;
-- ALTER TABLE User ADD COLUMN IF NOT EXISTS alamat TEXT DEFAULT NULL;
-- ALTER TABLE User ADD COLUMN IF NOT EXISTS bio TEXT DEFAULT NULL;
-- ALTER TABLE User ADD COLUMN IF NOT EXISTS last_login TIMESTAMP NULL DEFAULT NULL;


