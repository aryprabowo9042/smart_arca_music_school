<?php
session_start();
// Mengaktifkan laporan error untuk memudahkan pelacakan
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Panggil koneksi (Naik satu tingkat karena file ini ada di folder admin)
require_once(__DIR__ . '/../koneksi.php');

// Mengambil data pengguna dari database
$query_users = mysqli_query($conn, "SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin - Smart Arca</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 20px; background-color: #f0f2f5; }
        .dashboard-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 900px; margin: auto; }
        h1 { color: #1a73e8; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff; }
        th, td { border: 1px solid #e0e0e0; padding: 12px; text-align: left; }
        th { background-color: #f8f9fa; color: #5f6368; }
        tr:hover { background-color: #f1f3f4; }
        .btn-logout { display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #d93025; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
    </style>
</head>
<body>

<div class="dashboard-card">
    <h1>Panel Administrasi Smart Arca</h1>
    <p>Status Sistem: <span style="color: green; font-weight: bold;">● Terhubung ke TiDB Cloud</span></p>

    <h3>Daftar Pengguna Terdaftar</h3>
    <a href="../api/tambah_murid.php" style="display: inline-block; padding: 10px 15px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin-bottom: 15px;">+ Tambah Murid Baru</a>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Username</th>
                <th>Peran (Role)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while($user = mysqli_fetch_assoc($query_users)) { ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <a href="../logout.php" class="btn-logout">Keluar Sistem</a>
</div>

</body>
</html>
