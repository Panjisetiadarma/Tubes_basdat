-- ============================================
-- SQL untuk Tabel Konsultasi dan Chat Message
-- ============================================

USE project_notaris;

-- Tabel Konsultasi (jika belum ada)
CREATE TABLE IF NOT EXISTS Konsultasi (
    id_konsultasi INT AUTO_INCREMENT PRIMARY KEY,
    id_client INT NOT NULL,
    id_notaris INT, -- jika dari notaris
    id_ppat INT,    -- jika dari ppat
    id_user INT,    -- jika dari staff/admin yang menangani
    jenis_konsultasi ENUM('chat', 'video_call', 'janji_temu') NOT NULL,
    topik VARCHAR(200),
    pesan TEXT,
    tanggal_konsultasi DATETIME,
    durasi INT, -- dalam menit
    status ENUM('terjadwal', 'berlangsung', 'selesai', 'dibatalkan') DEFAULT 'terjadwal',
    link_meeting VARCHAR(500), -- untuk video call
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_client) REFERENCES Client(id_client) ON DELETE CASCADE,
    FOREIGN KEY (id_notaris) REFERENCES Notaris(id_notaris) ON DELETE SET NULL,
    FOREIGN KEY (id_ppat) REFERENCES Ppat(id_ppat) ON DELETE SET NULL,
    FOREIGN KEY (id_user) REFERENCES User(id_user) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabel Chat_Message (jika belum ada)
CREATE TABLE IF NOT EXISTS Chat_Message (
    id_message INT AUTO_INCREMENT PRIMARY KEY,
    id_konsultasi INT NOT NULL,
    id_pengirim INT NOT NULL, -- id_user yang mengirim
    pesan TEXT NOT NULL,
    tipe ENUM('text', 'file', 'image') DEFAULT 'text',
    file_url VARCHAR(500),
    waktu_kirim TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    dibaca BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_konsultasi) REFERENCES Konsultasi(id_konsultasi) ON DELETE CASCADE,
    FOREIGN KEY (id_pengirim) REFERENCES User(id_user) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- View untuk konsultasi lengkap dengan detail client dan penangan
CREATE OR REPLACE VIEW View_Konsultasi_Lengkap AS
SELECT 
    k.*,
    c.nama AS nama_client,
    c.email AS email_client,
    c.nomor_telepon AS telepon_client,
    n.nama_notaris,
    pp.nama_ppat,
    u.nama_lengkap AS staff_nama,
    u.username AS staff_username
FROM Konsultasi k
LEFT JOIN (
    SELECT 
        c.id_client,
        c.jenis_client,
        c.nomor_telepon,
        c.email,
        c.alamat,
        COALESCE(p.nama_lengkap, pr.nama_perusahaan) AS nama
    FROM Client c
    LEFT JOIN Pribadi p ON c.id_client = p.id_client AND c.jenis_client = 'pribadi'
    LEFT JOIN Perusahaan pr ON c.id_client = pr.id_client AND c.jenis_client = 'perusahaan'
) c ON k.id_client = c.id_client
LEFT JOIN Notaris n ON k.id_notaris = n.id_notaris
LEFT JOIN Ppat pp ON k.id_ppat = pp.id_ppat
LEFT JOIN User u ON k.id_user = u.id_user;

-- View untuk chat messages dengan detail pengirim
CREATE OR REPLACE VIEW View_Chat_Message AS
SELECT 
    cm.*,
    u.nama_lengkap AS nama_pengirim,
    u.username AS username_pengirim,
    u.role AS role_pengirim
FROM Chat_Message cm
LEFT JOIN User u ON cm.id_pengirim = u.id_user;

-- Data contoh (opsional - untuk testing)
-- INSERT INTO Konsultasi (id_client, id_user, jenis_konsultasi, topik, pesan, tanggal_konsultasi, status) 
-- VALUES (1, 1, 'chat', 'Konsultasi Akta Jual Beli', 'Saya ingin berkonsultasi mengenai pembuatan akta jual beli tanah', NOW(), 'berlangsung');

