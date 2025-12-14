INSERT INTO user (id_user, username, password, nama_lengkap, role)
VALUES
(4,'staff1','123','Staff Administrasi 1','staff'),
(5,'staff2','123','Staff Administrasi 2','staff'),
(6,'notaris1','123','Notaris Andi','notaris'),
(7,'notaris2','123','Notaris Bunga','notaris'),
(8,'ppat1','123','PPAT Rudi','ppat'),
(9,'ppat2','123','PPAT Sari','ppat'),
(10,'user1','123','Client User 1','user'),
(11,'user2','123','Client User 2','user'),
(12,'user3','123','Client User 3','user'),
(13,'admin2','123','Admin Cadangan','AdminNotaris');

INSERT INTO client (id_client, jenis_client, nomor_telepon, email, alamat)
VALUES
(4,'pribadi','0811111111','a@gmail.com','Jakarta'),
(5,'pribadi','0822222222','b@gmail.com','Bandung'),
(6,'pribadi','0833333333','c@gmail.com','Bogor'),
(7,'pribadi','0844444444','d@gmail.com','Depok'),
(8,'pribadi','0855555555','e@gmail.com','Bekasi'),
(9,'perusahaan','021111111','corp1@pt.com','Jakarta'),
(10,'perusahaan','021222222','corp2@pt.com','Surabaya'),
(11,'perusahaan','021333333','corp3@pt.com','Semarang'),
(12,'perusahaan','021444444','corp4@pt.com','Medan'),
(13,'perusahaan','021555555','corp5@pt.com','Makassar');

INSERT INTO pribadi (id_pribadi, id_client, nama_lengkap, nik, tempat_lahir, tanggal_lahir)
VALUES
(3,4,'Andi Saputra','3200000000000003','Jakarta','1995-01-01'),
(4,5,'Budi Hartono','3200000000000004','Bandung','1994-02-02'),
(5,6,'Citra Lestari','3200000000000005','Bogor','1993-03-03'),
(6,7,'Dewi Anggraini','3200000000000006','Depok','1992-04-04'),
(7,8,'Eko Prasetyo','3200000000000007','Bekasi','1991-05-05'),
(8,1,'John Tambahan','3200000000000008','Jakarta','1989-06-06'),
(9,2,'Jane Tambahan','3200000000000009','Bandung','1988-07-07'),
(10,4,'Andi Kedua','3200000000000010','Jakarta','1990-08-08'),
(11,5,'Budi Kedua','3200000000000011','Bandung','1991-09-09'),
(12,6,'Citra Kedua','3200000000000012','Bogor','1992-10-10');

INSERT INTO perusahaan (id_perusahaan, id_client, nama_perusahaan, npwp, nama_direktur)
VALUES
(2,9,'PT Maju Jaya','01.000.000.1-001.000','Direktur A'),
(3,10,'PT Sejahtera','01.000.000.1-002.000','Direktur B'),
(4,11,'PT Makmur','01.000.000.1-003.000','Direktur C'),
(5,12,'PT Sentosa','01.000.000.1-004.000','Direktur D'),
(6,13,'PT Nusantara','01.000.000.1-005.000','Direktur E'),
(7,9,'PT Global','01.000.000.1-006.000','Direktur F'),
(8,10,'PT Lokal','01.000.000.1-007.000','Direktur G'),
(9,11,'PT Digital','01.000.000.1-008.000','Direktur H'),
(10,12,'PT Industri','01.000.000.1-009.000','Direktur I'),
(11,13,'PT Teknologi','01.000.000.1-010.000','Direktur J');

INSERT INTO status_pengajuan (id_status, nama_status, deskripsi)
VALUES
(7,'Revisi','Perlu perbaikan'),
(8,'Diverifikasi','Sudah diverifikasi'),
(9,'Dijadwalkan','Sudah dijadwalkan'),
(10,'Arsip','Sudah diarsipkan');

INSERT INTO notaris (id_notaris, id_user, nama_notaris, nomor_notaris, alamat_kantor, telepon)
VALUES
(3,6,'Notaris Andi','N-003','Jakarta','0213333'),
(4,7,'Notaris Bunga','N-004','Bandung','0214444'),
(5,NULL,'Notaris C','N-005','Bogor','0215555'),
(6,NULL,'Notaris D','N-006','Depok','0216666'),
(7,NULL,'Notaris E','N-007','Bekasi','0217777'),
(8,NULL,'Notaris F','N-008','Surabaya','0218888'),
(9,NULL,'Notaris G','N-009','Semarang','0219999'),
(10,NULL,'Notaris H','N-010','Medan','0211010'),
(11,NULL,'Notaris I','N-011','Makassar','0211111'),
(12,NULL,'Notaris J','N-012','Bali','0211212');

INSERT INTO ppat (id_ppat, id_user, nama_ppat, nomor_ppat, alamat_kantor, telepon)
VALUES
(2,8,'PPAT Rudi','P-002','Jakarta','022222'),
(3,9,'PPAT Sari','P-003','Bandung','023333'),
(4,NULL,'PPAT A','P-004','Bogor','024444'),
(5,NULL,'PPAT B','P-005','Depok','025555'),
(6,NULL,'PPAT C','P-006','Bekasi','026666'),
(7,NULL,'PPAT D','P-007','Surabaya','027777'),
(8,NULL,'PPAT E','P-008','Semarang','028888'),
(9,NULL,'PPAT F','P-009','Medan','029999'),
(10,NULL,'PPAT G','P-010','Makassar','020101'),
(11,NULL,'PPAT H','P-011','Bali','020202');

INSERT INTO pengajuan (id_pengajuan, id_client, id_notaris, id_ppat, id_status, jenis_pengajuan, tanggal_pengajuan)
VALUES
(6,4,3,2,1,'Akta Jual Beli','2024-03-01'),
(7,5,4,3,2,'Akta Hibah','2024-03-02'),
(8,6,5,4,3,'Legalisasi','2024-03-03'),
(9,7,6,5,4,'Surat Kuasa','2024-03-04'),
(10,8,7,6,5,'Perjanjian','2024-03-05'),
(11,9,8,7,6,'Akta Tanah','2024-03-06'),
(12,10,9,8,7,'Akta Waris','2024-03-07'),
(13,11,10,9,8,'Akta Usaha','2024-03-08'),
(14,12,11,10,9,'Akta Pendirian','2024-03-09'),
(15,13,12,11,10,'Akta Perubahan','2024-03-10');

INSERT INTO jadwal (id_jadwal, id_pengajuan, tanggal_jadwal, kegiatan)
VALUES
(4,6,'2024-03-11 10:00','Penandatanganan'),
(5,7,'2024-03-12 10:00','Verifikasi'),
(6,8,'2024-03-13 10:00','Konsultasi'),
(7,9,'2024-03-14 10:00','Penandatanganan'),
(8,10,'2024-03-15 10:00','Verifikasi'),
(9,11,'2024-03-16 10:00','Konsultasi'),
(10,12,'2024-03-17 10:00','Penandatanganan'),
(11,13,'2024-03-18 10:00','Verifikasi'),
(12,14,'2024-03-19 10:00','Konsultasi'),
(13,15,'2024-03-20 10:00','Finalisasi');

INSERT INTO transaksi (id_transaksi, id_pengajuan, jumlah, metode_pembayaran, status_pembayaran)
VALUES
(1,6,1500000,'Transfer','lunas'),
(2,7,1200000,'Transfer','lunas'),
(3,8,800000,'Cash','lunas'),
(4,9,500000,'Cash','pending'),
(5,10,2000000,'Transfer','lunas'),
(6,11,3000000,'Transfer','pending'),
(7,12,1000000,'Cash','lunas'),
(8,13,1800000,'Transfer','lunas'),
(9,14,2500000,'Transfer','pending'),
(10,15,4000000,'Cash','lunas');
