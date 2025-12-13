<?php
require_once 'koneksi.php';

// Ambil data client dari database
$query = "SELECT id_client, nama_lengkap, nomor_telepon, alamat, jenis_client FROM Client LEFT JOIN Pribadi ON Client.id_client=Pribadi.id_client";
$result = query($query);
$clients = [];
if ($result) {
    while ($row = fetch_array($result)) {
        $clients[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Clien</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/navbar.css">
    <style>
        body { font-family: 'Poppins', Arial, sans-serif; background: #fff; }
        .table-container { max-width: 900px; margin: 40px auto; }
        h2 { text-align: center; color: #1a237e; margin-bottom: 24px; }
        table { width: 100%; border-collapse: collapse; background: #f8faff; }
        th, td { border: 2px solid #90a4ae; padding: 14px 10px; text-align: center; }
        th { background: #e3eaff; color: #0d1a4a; font-weight: bold; }
        tr:nth-child(even) { background: #f4f7fb; }
        tr:hover { background: #e3eaff; }
    </style>
</head>
<body>
    <?php include 'components/navbar.html'; ?>
    <div class="table-container">
        <h2><span style="color: #1a37ff;">Data</span> <span style="color: #333;">Clien</span></h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>No tlp</th>
                    <th>Almt</th>
                    <th>Kategori</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($clients)): ?>
                    <tr><td colspan="5">Tidak ada data</td></tr>
                <?php else: ?>
                    <?php foreach ($clients as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['id_client']) ?></td>
                            <td><?= htmlspecialchars($c['nama_lengkap']) ?></td>
                            <td><?= htmlspecialchars($c['nomor_telepon']) ?></td>
                            <td><?= htmlspecialchars($c['alamat']) ?></td>
                            <td><?= htmlspecialchars($c['jenis_client']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
