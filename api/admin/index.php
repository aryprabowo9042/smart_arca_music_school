<?php
session_start();

// 1. Proteksi Halaman: Cek apakah user sudah login dan benar seorang admin
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login" || $_SESSION['role'] != "admin") {
    header("Location: /login.php?pesan=belum_login");
    exit();
}

// 2. Hubungkan ke database (naik satu folder ke api/koneksi.php)
include "../koneksi.php";

// Contoh mengambil data jumlah user (opsional)
$query_user = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
$row_user = mysqli_fetch_assoc($query_user);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Smart Arca</title>
    <link rel="stylesheet" href="/css/landing.css">
</head>
<body style="background: #f4f7f6; font-family: sans-serif;">

    <div style="display: flex;">
        <div style="width: 250px; height: 100vh; background: #2c3e50; color: white; padding: 20px;">
            <h3>Smart Arca Admin</h3>
            <hr>
            <p>Selamat Datang, <br><strong><?php echo $_SESSION['username']; ?></strong></p>
            <ul style="list-style: none; padding: 0; margin-top: 30px;">
                <li style="margin-bottom: 15px;"><a href="/admin/index.php" style="color: white; text-decoration: none;">🏠 Dashboard</a></li>
                <li style="margin-bottom: 15px;"><a href="#" style="color: white; text-decoration: none;">👥 Data Siswa</a></li>
                <li style="margin-bottom: 15px;"><a href="#" style="color: white; text-decoration: none;">🎸 Data Guru</a></li>
                <li style="margin-bottom: 15px;"><a href="/logout.php" style="color: #e74c3c; text-decoration: none; font-weight: bold;">🚪 Logout</a></li>
            </ul>
        </div>

        <div style="flex: 1; padding: 30px;">
            <h1>Dashboard Utama</h1>
            <div style="display: flex; gap: 20px; margin-top: 20px;">
                <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); flex: 1;">
                    <h3>Total Pengguna</h3>
                    <p style="font-size: 2rem; color: #e67e22;"><?php echo $row_user['total']; ?></p>
                </div>
                <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); flex: 1;">
                    <h3>Status Sistem</h3>
                    <p style="color: green; font-weight: bold;">Online (Vercel + TiDB)</p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>