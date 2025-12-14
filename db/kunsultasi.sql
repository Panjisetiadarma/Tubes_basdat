-- Tabel untuk chat/konsultasi
CREATE TABLE Konsultasi (
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
    FOREIGN KEY (id_client) REFERENCES Client(id_client),
    FOREIGN KEY (id_notaris) REFERENCES Notaris(id_notaris),
    FOREIGN KEY (id_ppat) REFERENCES Ppat(id_ppat),
    FOREIGN KEY (id_user) REFERENCES User(id_user)
);

-- Tabel untuk pesan chat
CREATE TABLE Chat_Message (
    id_message INT AUTO_INCREMENT PRIMARY KEY,
    id_konsultasi INT NOT NULL,
    id_pengirim INT NOT NULL, -- id_user yang mengirim
    pesan TEXT NOT NULL,
    tipe ENUM('text', 'file', 'image') DEFAULT 'text',
    file_url VARCHAR(500),
    waktu_kirim TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    dibaca BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_konsultasi) REFERENCES Konsultasi(id_konsultasi),
    FOREIGN KEY (id_pengirim) REFERENCES User(id_user)
);

--profile lengkap user 
-- Tambah kolom untuk profil di tabel User
ALTER TABLE User ADD COLUMN (
    foto_profil VARCHAR(255),
    nomor_telepon VARCHAR(20),
    alamat TEXT,
    bio TEXT,
    last_login TIMESTAMP NULL
);

-- Tabel untuk keahlian/sertifikasi Notaris
CREATE TABLE Sertifikasi_Notaris (
    id_sertifikasi INT AUTO_INCREMENT PRIMARY KEY,
    id_notaris INT NOT NULL,
    nama_sertifikasi VARCHAR(100),
    nomor_sertifikasi VARCHAR(50),
    lembaga_penerbit VARCHAR(100),
    tanggal_terbit DATE,
    tanggal_kadaluarsa DATE,
    file_sertifikasi VARCHAR(255),
    FOREIGN KEY (id_notaris) REFERENCES Notaris(id_notaris)
);

-- Tabel untuk jadwal kerja Notaris/PPAT
CREATE TABLE Jadwal_Kerja (
    id_jadwal INT AUTO_INCREMENT PRIMARY KEY,
    id_notaris INT,
    id_ppat INT,
    hari ENUM('Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'),
    jam_mulai TIME,
    jam_selesai TIME,
    aktif BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_notaris) REFERENCES Notaris(id_notaris),
    FOREIGN KEY (id_ppat) REFERENCES Ppat(id_ppat)
);

--riwayat transaksi pembayaran

-- Tambah kolom untuk invoice di Transaksi
ALTER TABLE Transaksi ADD COLUMN (
    nomor_invoice VARCHAR(50) UNIQUE,
    tanggal_jatuh_tempo DATE,
    bank_tujuan VARCHAR(100),
    nomor_rekening VARCHAR(50),
    bukti_pembayaran VARCHAR(255)
);

-- Tabel untuk jenis layanan/tarif
CREATE TABLE Layanan (
    id_layanan INT AUTO_INCREMENT PRIMARY KEY,
    nama_layanan VARCHAR(100) NOT NULL,
    jenis_layanan ENUM('notaris', 'ppat', 'konsultasi') NOT NULL,
    deskripsi TEXT,
    tarif_dasar DECIMAL(15,2),
    satuan_tarif ENUM('dokumen', 'jam', 'sesi') DEFAULT 'dokumen',
    estimasi_waktu INT -- dalam hari
);

-- Hubungkan pengajuan dengan layanan
ALTER TABLE Pengajuan ADD COLUMN id_layanan INT;
ALTER TABLE Pengajuan ADD FOREIGN KEY (id_layanan) REFERENCES Layanan(id_layanan);

--notifikasi
CREATE TABLE Notifikasi (
    id_notifikasi INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    judul VARCHAR(200) NOT NULL,
    pesan TEXT,
    tipe ENUM('pengajuan', 'jadwal', 'pembayaran', 'sistem') NOT NULL,
    id_terkait INT, -- misal: id_pengajuan, id_transaksi, dll
    dibaca BOOLEAN DEFAULT FALSE,
    waktu_notifikasi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES User(id_user)
);

--query

-- View untuk daftar klien lengkap
CREATE VIEW View_Client_Detail AS
SELECT 
    c.id_client,
    c.jenis_client,
    c.nomor_telepon,
    c.email,
    c.alamat,
    c.created_at,
    COALESCE(p.nama_lengkap, pr.nama_perusahaan) AS nama,
    p.nik,
    pr.npwp,
    pr.nama_direktur
FROM Client c
LEFT JOIN Pribadi p ON c.id_client = p.id_client AND c.jenis_client = 'pribadi'
LEFT JOIN Perusahaan pr ON c.id_client = pr.id_client AND c.jenis_client = 'perusahaan';

-- View untuk riwayat transaksi lengkap
CREATE VIEW View_Transaksi_Lengkap AS
SELECT 
    t.*,
    p.jenis_pengajuan,
    p.deskripsi AS deskripsi_pengajuan,
    c.nama,
    c.email AS email_client,
    u.nama_lengkap AS staff_penangan
FROM Transaksi t
JOIN Pengajuan p ON t.id_pengajuan = p.id_pengajuan
JOIN View_Client_Detail c ON p.id_client = c.id_client
LEFT JOIN User u ON p.id_user = u.id_user;

-- View untuk jadwal konsultasi
CREATE VIEW View_Konsultasi AS
SELECT 
    k.*,
    c.nama AS nama_client,
    n.nama_notaris,
    pp.nama_ppat,
    u.nama_lengkap AS staff_nama
FROM Konsultasi k
JOIN View_Client_Detail c ON k.id_client = c.id_client
LEFT JOIN Notaris n ON k.id_notaris = n.id_notaris
LEFT JOIN Ppat pp ON k.id_ppat = pp.id_ppat
LEFT JOIN User u ON k.id_user = u.id_user;