<?php
session_start();
// Mengaktifkan laporan error
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Panggil koneksi (Naik satu tingkat ke folder api)
require_once(__DIR__ . '/../koneksi.php');

// Ambil data user
$query_users = mysqli_query($conn, "SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin - Smart Arca</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background-color: #f0f2f5; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 900px; margin: auto; }
        h1 { color: #1a73e8; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .btn-tambah { display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin-bottom: 20px; font-weight: bold; }
        .btn-logout { display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #d93025; color: white; text-decoration: none; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f8f9fa; }
    </style>
</head>
<body>

<div class="card">
    <h1>Panel Administrasi Smart Arca</h1>
    
    <a href="tambah_murid.php" class="btn-tambah">+ TAMBAH MURID BARU</a>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Username</th>
                <th>Role</th>
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
