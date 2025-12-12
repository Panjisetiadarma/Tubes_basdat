-- Membuat database
CREATE DATABASE IF NOT EXISTS project_notaris;
USE project_notaris;

-- Tabel User (untuk login sistem)
CREATE TABLE User (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('AdminNotaris', 'user', 'notaris', 'ppat', 'staff') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Client (sebagai induk, jika diperlukan)
-- Namun karena ada Pribadi dan Perusahaan, kita buat dua tabel terpisah atau satu tabel dengan tipe?
-- Alternatif 1: Buat tabel Client dengan tipe, lalu tabel Pribadi dan Perusahaan menyimpan detail spesifik.
-- Alternatif 2: Buat tabel Pribadi dan Perusahaan terpisah tanpa tabel Client.
-- Saya pilih Alternatif 1 untuk fleksibilitas.

CREATE TABLE Client (
    id_client INT AUTO_INCREMENT PRIMARY KEY,
    jenis_client ENUM('pribadi', 'perusahaan') NOT NULL,
    nomor_telepon VARCHAR(20),
    email VARCHAR(100),
    alamat TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Pribadi (detail untuk client pribadi)
CREATE TABLE Pribadi (
    id_pribadi INT AUTO_INCREMENT PRIMARY KEY,
    id_client INT NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    nik VARCHAR(20) UNIQUE,
    tempat_lahir VARCHAR(50),
    tanggal_lahir DATE,
    FOREIGN KEY (id_client) REFERENCES Client(id_client) ON DELETE CASCADE
);

-- Tabel Perusahaan (detail untuk client perusahaan)
CREATE TABLE Perusahaan (
    id_perusahaan INT AUTO_INCREMENT PRIMARY KEY,
    id_client INT NOT NULL,
    nama_perusahaan VARCHAR(100) NOT NULL,
    npwp VARCHAR(30) UNIQUE,
    nama_direktur VARCHAR(100),
    FOREIGN KEY (id_client) REFERENCES Client(id_client) ON DELETE CASCADE
);

-- Tabel Notaris
CREATE TABLE Notaris (
    id_notaris INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT, -- jika notaris juga adalah user sistem
    nama_notaris VARCHAR(100) NOT NULL,
    nomor_notaris VARCHAR(50),
    alamat_kantor TEXT,
    telepon VARCHAR(20),
    FOREIGN KEY (id_user) REFERENCES User(id_user)
);

-- Tabel PPAT
CREATE TABLE Ppat (
    id_ppat INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT, -- jika ppat juga adalah user sistem
    nama_ppat VARCHAR(100) NOT NULL,
    nomor_ppat VARCHAR(50),
    alamat_kantor TEXT,
    telepon VARCHAR(20),
    FOREIGN KEY (id_user) REFERENCES User(id_user)
);

-- Tabel Status_Pengajuan (untuk master data status)
CREATE TABLE Status_Pengajuan (
    id_status INT AUTO_INCREMENT PRIMARY KEY,
    nama_status VARCHAR(50) NOT NULL,
    deskripsi TEXT
);

-- Tabel Pengajuan (inti proses)
CREATE TABLE Pengajuan (
    id_pengajuan INT AUTO_INCREMENT PRIMARY KEY,
    id_client INT NOT NULL,
    id_notaris INT, -- bisa null jika belum ditentukan
    id_ppat INT,    -- bisa null jika belum ditentukan
    id_status INT NOT NULL,
    jenis_pengajuan VARCHAR(50) NOT NULL, -- misalnya: 'akta jual beli', 'akta hibah', dll.
    deskripsi TEXT,
    tanggal_pengajuan DATE,
    tanggal_selesai DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_client) REFERENCES Client(id_client),
    FOREIGN KEY (id_notaris) REFERENCES Notaris(id_notaris),
    FOREIGN KEY (id_ppat) REFERENCES Ppat(id_ppat),
    FOREIGN KEY (id_status) REFERENCES Status_Pengajuan(id_status)
);

-- Tabel Jadwal (bisa terkait dengan pengajuan)
CREATE TABLE Jadwal (
    id_jadwal INT AUTO_INCREMENT PRIMARY KEY,
    id_pengajuan INT NOT NULL,
    tanggal_jadwal DATETIME NOT NULL,
    kegiatan VARCHAR(100) NOT NULL,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pengajuan) REFERENCES Pengajuan(id_pengajuan) ON DELETE CASCADE
);

-- Tabel Arsip_File (file-file yang diunggah)
CREATE TABLE Arsip_File (
    id_file INT AUTO_INCREMENT PRIMARY KEY,
    id_pengajuan INT NOT NULL,
    nama_file VARCHAR(255) NOT NULL,
    path_file VARCHAR(255) NOT NULL,
    tipe_file VARCHAR(50),
    ukuran_file INT, -- dalam byte
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pengajuan) REFERENCES Pengajuan(id_pengajuan) ON DELETE CASCADE
);

-- Tabel Transaksi (pembayaran)
CREATE TABLE Transaksi (
    id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
    id_pengajuan INT NOT NULL,
    jumlah DECIMAL(15,2) NOT NULL,
    metode_pembayaran VARCHAR(50),
    tanggal_transaksi DATE,
    status_pembayaran ENUM('pending', 'lunas', 'gagal') DEFAULT 'pending',
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pengajuan) REFERENCES Pengajuan(id_pengajuan)
);