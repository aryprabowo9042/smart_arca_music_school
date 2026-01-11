<?php
session_start();
// Mengaktifkan laporan error untuk memudahkan pelacakan
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Panggil koneksi (Naik satu tingkat ke folder api)
require_once(__DIR__ . '/../koneksi.php');

// Ambil data semua user dari database
$query_users = mysqli_query($conn, "SELECT * FROM users ORDER BY role ASC, username ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Smart Arca</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; padding: 20px; background-color: #f0f2f5; color: #333; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        
        h1 { color: #1a73e8; margin-bottom: 5px; }
        p.status { margin-bottom: 25px; color: #666; font-size: 14px; }
        
        .action-buttons { margin-bottom: 25px; display: flex; gap: 10px; flex-wrap: wrap; }
        
        .btn { display: inline-block; padding: 12px 20px; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 14px; transition: 0.3s; }
        .btn-murid { background-color: #28a745; }
        .btn-murid:hover { background-color: #218838; }
        .btn-guru { background-color: #1a73e8; }
        .btn-guru:hover { background-color: #1557b0; }
        .btn-logout { background-color: #dc3545; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; overflow: hidden; border-radius: 10px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #f8f9fa; color: #5f6368; font-weight: 600; text-transform: uppercase; font-size: 12px; }
        
        .badge { padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; text-transform: capitalize; }
        .badge-admin { background: #fff3cd; color: #856404; }
        .badge-guru { background: #cfe2ff; color: #084298; }
        .badge-murid { background: #d1e7dd; color: #0f5132; }
    </style>
</head>
<body>

<div class="container">
    <h1>Panel Administrasi</h1>
    <p class="status">Dashboard Manajemen Smart Arca Music School</p>

    <div class="action-buttons">
        <a href="tambah_murid.php" class="btn btn-murid">+ TAMBAH MURID</a>
        <a href="tambah_guru.php" class="btn btn-guru">+ TAMBAH GURU</a>
        <a href="../logout.php" class="btn btn-logout" style="margin-left: auto;">KELUAR</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Username / Nama</th>
                <th>Hak Akses (Role)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while($user = mysqli_fetch_assoc($query_users)) { 
                // Menentukan warna badge berdasarkan role
                $roleClass = 'badge-murid';
                if($user['role'] == 'admin') $roleClass = 'badge-admin';
                if($user['role'] == 'guru') $roleClass = 'badge-badge-guru';
                if($user['role'] == 'guru') $roleClass = 'badge-guru'; // Perbaikan nama class
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td style="font-weight: 500;"><?php echo htmlspecialchars($user['username']); ?></td>
                <td><span class="badge <?php echo $roleClass; ?>"><?php echo htmlspecialchars($user['role']); ?></span></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
