-- ============================================
-- SQL untuk Mengupdate Password yang Terenkripsi
-- Menghapus enkripsi password yang sudah ada
-- ============================================

USE project_notaris;

-- Update password untuk user yang sudah ada
-- Ganti password terenkripsi dengan password plain text

-- Untuk admin (password: admin123)
UPDATE User 
SET password = 'admin123' 
WHERE username = 'admin' AND (password LIKE '$2y$%' OR LENGTH(password) > 20);

-- Untuk user (password: user123)
UPDATE User 
SET password = 'user123' 
WHERE username = 'user' AND (password LIKE '$2y$%' OR LENGTH(password) > 20);

-- Atau jika ingin mengupdate semua password yang terenkripsi menjadi password default
-- UPDATE User 
-- SET password = 'password123' 
-- WHERE password LIKE '$2y$%' OR LENGTH(password) > 20;

-- Verifikasi hasil
SELECT id_user, username, nama_lengkap, role, 
       CASE 
           WHEN password LIKE '$2y$%' OR LENGTH(password) > 20 THEN 'TERENKRIPSI'
           ELSE 'PLAIN TEXT'
       END AS status_password,
       password
FROM User;


