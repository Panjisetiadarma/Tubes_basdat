-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 13 Des 2025 pada 15.37
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project_notaris`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `arsip_file`
--

CREATE TABLE `arsip_file` (
  `id_file` int(11) NOT NULL,
  `id_pengajuan` int(11) NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `path_file` varchar(255) NOT NULL,
  `tipe_file` varchar(50) DEFAULT NULL,
  `ukuran_file` int(11) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `client`
--

CREATE TABLE `client` (
  `id_client` int(11) NOT NULL,
  `jenis_client` enum('pribadi','perusahaan') NOT NULL,
  `nomor_telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `client`
--

INSERT INTO `client` (`id_client`, `jenis_client`, `nomor_telepon`, `email`, `alamat`, `created_at`) VALUES
(1, 'pribadi', '081234567890', 'john.doe@email.com', 'Jl. Merdeka No. 1, Jakarta', '2025-12-12 18:57:50'),
(2, 'pribadi', '081987654321', 'jane.smith@email.com', 'Jl. Kemerdekaan No. 2, Jakarta', '2025-12-12 18:57:50'),
(3, 'perusahaan', '021-55555555', 'info@company.com', 'Jl. Industri No. 100, Jakarta', '2025-12-12 18:57:50');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal`
--

CREATE TABLE `jadwal` (
  `id_jadwal` int(11) NOT NULL,
  `id_pengajuan` int(11) NOT NULL,
  `tanggal_jadwal` datetime NOT NULL,
  `kegiatan` varchar(100) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jadwal`
--

INSERT INTO `jadwal` (`id_jadwal`, `id_pengajuan`, `tanggal_jadwal`, `kegiatan`, `keterangan`, `created_at`) VALUES
(1, 1, '2024-02-01 10:00:00', 'Penandatanganan Akta', 'Bertemu di kantor notaris untuk penandatanganan', '2025-12-12 18:57:50'),
(2, 2, '2024-02-05 14:00:00', 'Verifikasi Dokumen', 'Verifikasi dokumen asli', '2025-12-12 18:57:50'),
(3, 3, '2024-02-10 09:00:00', 'Konsultasi', 'Konsultasi mengenai surat kuasa', '2025-12-12 18:57:50');

-- --------------------------------------------------------

--
-- Struktur dari tabel `notaris`
--

CREATE TABLE `notaris` (
  `id_notaris` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `nama_notaris` varchar(100) NOT NULL,
  `nomor_notaris` varchar(50) DEFAULT NULL,
  `alamat_kantor` text DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `notaris`
--

INSERT INTO `notaris` (`id_notaris`, `id_user`, `nama_notaris`, `nomor_notaris`, `alamat_kantor`, `telepon`) VALUES
(1, NULL, 'Dr. Ahmad Hidayat, S.H., M.H.', 'N-001', 'Jl. Sudirman No. 123, Jakarta Pusat', '021-12345678'),
(2, NULL, 'Siti Nurhaliza, S.H.', 'N-002', 'Jl. Thamrin No. 456, Jakarta Pusat', '021-87654321');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengajuan`
--

CREATE TABLE `pengajuan` (
  `id_pengajuan` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `id_notaris` int(11) DEFAULT NULL,
  `id_ppat` int(11) DEFAULT NULL,
  `id_status` int(11) NOT NULL,
  `jenis_pengajuan` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `tanggal_pengajuan` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengajuan`
--

INSERT INTO `pengajuan` (`id_pengajuan`, `id_client`, `id_notaris`, `id_ppat`, `id_status`, `jenis_pengajuan`, `deskripsi`, `tanggal_pengajuan`, `tanggal_selesai`, `created_at`) VALUES
(1, 1, 1, NULL, 1, 'Akta Jual Beli Tanah', 'Pengajuan akta jual beli tanah di Jakarta Selatan', '2024-01-15', NULL, '2025-12-12 18:57:50'),
(2, 2, 1, NULL, 2, 'Legalisasi Dokumen', 'Legalisasi ijazah dan transkrip nilai', '2024-01-20', NULL, '2025-12-12 18:57:50'),
(3, 3, 2, NULL, 3, 'Surat Kuasa', 'Surat kuasa untuk pengurusan dokumen', '2024-01-25', NULL, '2025-12-12 18:57:50'),
(4, 1, 1, NULL, 4, 'Akta Hibah', 'Akta hibah tanah dari orang tua', '2024-02-01', NULL, '2025-12-12 18:57:50'),
(5, 2, 2, NULL, 2, 'Akta Perjanjian Sewa', 'Perjanjian sewa menyewa ruko', '2024-02-10', NULL, '2025-12-12 18:57:50');

-- --------------------------------------------------------

--
-- Struktur dari tabel `perusahaan`
--

CREATE TABLE `perusahaan` (
  `id_perusahaan` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `nama_perusahaan` varchar(100) NOT NULL,
  `npwp` varchar(30) DEFAULT NULL,
  `nama_direktur` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `perusahaan`
--

INSERT INTO `perusahaan` (`id_perusahaan`, `id_client`, `nama_perusahaan`, `npwp`, `nama_direktur`) VALUES
(1, 3, 'PT Contoh Perusahaan', '01.234.567.8-901.000', 'Ahmad Wijaya');

-- --------------------------------------------------------

--
-- Struktur dari tabel `ppat`
--

CREATE TABLE `ppat` (
  `id_ppat` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `nama_ppat` varchar(100) NOT NULL,
  `nomor_ppat` varchar(50) DEFAULT NULL,
  `alamat_kantor` text DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `ppat`
--

INSERT INTO `ppat` (`id_ppat`, `id_user`, `nama_ppat`, `nomor_ppat`, `alamat_kantor`, `telepon`) VALUES
(1, NULL, 'Budi Santoso, S.H., M.Kn.', 'PPAT-001', 'Jl. Gatot Subroto No. 789, Jakarta Selatan', '021-11223344');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pribadi`
--

CREATE TABLE `pribadi` (
  `id_pribadi` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `nik` varchar(20) DEFAULT NULL,
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pribadi`
--

INSERT INTO `pribadi` (`id_pribadi`, `id_client`, `nama_lengkap`, `nik`, `tempat_lahir`, `tanggal_lahir`) VALUES
(1, 1, 'John Doe', '3201010101010001', 'Jakarta', '1990-01-01'),
(2, 2, 'Jane Smith', '3201010101010002', 'Bandung', '1992-05-15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `status_pengajuan`
--

CREATE TABLE `status_pengajuan` (
  `id_status` int(11) NOT NULL,
  `nama_status` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `status_pengajuan`
--

INSERT INTO `status_pengajuan` (`id_status`, `nama_status`, `deskripsi`) VALUES
(1, 'Menunggu', 'Pengajuan baru, menunggu peninjauan'),
(2, 'Diproses', 'Pengajuan sedang dalam proses'),
(3, 'Menunggu Dokumen', 'Menunggu dokumen tambahan dari client'),
(4, 'Selesai', 'Pengajuan telah selesai diproses'),
(5, 'Ditolak', 'Pengajuan ditolak'),
(6, 'Dibatalkan', 'Pengajuan dibatalkan oleh client');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `id_pengajuan` int(11) NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `tanggal_transaksi` date DEFAULT NULL,
  `status_pembayaran` enum('pending','lunas','gagal') DEFAULT 'pending',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `role` enum('AdminNotaris','user','notaris','ppat','staff') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id_user`, `username`, `password`, `nama_lengkap`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$3CKqEDcej8R2OTtsYpS.PeZEVtkfLdDp9dpNy2TieA/Y1r5eH7TXC', 'Administrator Notaris', 'AdminNotaris', '2025-12-12 18:57:50'),
(2, 'user', '$2y$10$4KOKjyxwR3fIU1dAN3FH7.P77/0/mNcOcjQKcbL/wyZSxPSUK9JLa', 'User Test', 'user', '2025-12-12 18:57:50');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `arsip_file`
--
ALTER TABLE `arsip_file`
  ADD PRIMARY KEY (`id_file`),
  ADD KEY `id_pengajuan` (`id_pengajuan`);

--
-- Indeks untuk tabel `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`id_client`);

--
-- Indeks untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id_jadwal`),
  ADD KEY `id_pengajuan` (`id_pengajuan`);

--
-- Indeks untuk tabel `notaris`
--
ALTER TABLE `notaris`
  ADD PRIMARY KEY (`id_notaris`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `pengajuan`
--
ALTER TABLE `pengajuan`
  ADD PRIMARY KEY (`id_pengajuan`),
  ADD KEY `id_client` (`id_client`),
  ADD KEY `id_notaris` (`id_notaris`),
  ADD KEY `id_ppat` (`id_ppat`),
  ADD KEY `id_status` (`id_status`);

--
-- Indeks untuk tabel `perusahaan`
--
ALTER TABLE `perusahaan`
  ADD PRIMARY KEY (`id_perusahaan`),
  ADD UNIQUE KEY `npwp` (`npwp`),
  ADD KEY `id_client` (`id_client`);

--
-- Indeks untuk tabel `ppat`
--
ALTER TABLE `ppat`
  ADD PRIMARY KEY (`id_ppat`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `pribadi`
--
ALTER TABLE `pribadi`
  ADD PRIMARY KEY (`id_pribadi`),
  ADD UNIQUE KEY `nik` (`nik`),
  ADD KEY `id_client` (`id_client`);

--
-- Indeks untuk tabel `status_pengajuan`
--
ALTER TABLE `status_pengajuan`
  ADD PRIMARY KEY (`id_status`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_pengajuan` (`id_pengajuan`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `arsip_file`
--
ALTER TABLE `arsip_file`
  MODIFY `id_file` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `client`
--
ALTER TABLE `client`
  MODIFY `id_client` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id_jadwal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `notaris`
--
ALTER TABLE `notaris`
  MODIFY `id_notaris` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `pengajuan`
--
ALTER TABLE `pengajuan`
  MODIFY `id_pengajuan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `perusahaan`
--
ALTER TABLE `perusahaan`
  MODIFY `id_perusahaan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `ppat`
--
ALTER TABLE `ppat`
  MODIFY `id_ppat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `pribadi`
--
ALTER TABLE `pribadi`
  MODIFY `id_pribadi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `status_pengajuan`
--
ALTER TABLE `status_pengajuan`
  MODIFY `id_status` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `arsip_file`
--
ALTER TABLE `arsip_file`
  ADD CONSTRAINT `arsip_file_ibfk_1` FOREIGN KEY (`id_pengajuan`) REFERENCES `pengajuan` (`id_pengajuan`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD CONSTRAINT `jadwal_ibfk_1` FOREIGN KEY (`id_pengajuan`) REFERENCES `pengajuan` (`id_pengajuan`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `notaris`
--
ALTER TABLE `notaris`
  ADD CONSTRAINT `notaris_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Ketidakleluasaan untuk tabel `pengajuan`
--
ALTER TABLE `pengajuan`
  ADD CONSTRAINT `pengajuan_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `client` (`id_client`),
  ADD CONSTRAINT `pengajuan_ibfk_2` FOREIGN KEY (`id_notaris`) REFERENCES `notaris` (`id_notaris`),
  ADD CONSTRAINT `pengajuan_ibfk_3` FOREIGN KEY (`id_ppat`) REFERENCES `ppat` (`id_ppat`),
  ADD CONSTRAINT `pengajuan_ibfk_4` FOREIGN KEY (`id_status`) REFERENCES `status_pengajuan` (`id_status`);

--
-- Ketidakleluasaan untuk tabel `perusahaan`
--
ALTER TABLE `perusahaan`
  ADD CONSTRAINT `perusahaan_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `client` (`id_client`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `ppat`
--
ALTER TABLE `ppat`
  ADD CONSTRAINT `ppat_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Ketidakleluasaan untuk tabel `pribadi`
--
ALTER TABLE `pribadi`
  ADD CONSTRAINT `pribadi_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `client` (`id_client`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_pengajuan`) REFERENCES `pengajuan` (`id_pengajuan`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
