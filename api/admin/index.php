<?php
session_start();
include '../includes/koneksi.php';

// 1. Cek Keamanan: Apakah user login sebagai admin?
if ($_SESSION['status'] != "login" || $_SESSION['role'] != "admin") {
    header("Location: ../login.php");
    exit();
}

// 2. Hitung Statistik Data untuk Dashboard
// Menghitung jumlah Guru
$query_guru = mysqli_query($koneksi, "SELECT * FROM users WHERE role='guru'");
$total_guru = mysqli_num_rows($query_guru);

// Menghitung jumlah Murid
$query_murid = mysqli_query($koneksi, "SELECT * FROM users WHERE role='murid'");
$total_murid = mysqli_num_rows($query_murid);

// Menghitung jumlah Modul Belajar
$query_modul = mysqli_query($koneksi, "SELECT * FROM modul");
$total_modul = mysqli_num_rows($query_modul);

// (Tambahan) Menghitung Permintaan Tarik Dana yang PENDING
// Ini agar Admin langsung tahu jika ada guru yang minta gaji
$query_wd = mysqli_query($koneksi, "SELECT * FROM penarikan WHERE status='pending'");
$total_wd_pending = mysqli_num_rows($query_wd);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Admin - Smart Arca</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="sidebar">
        <h2>Smart Arca Admin</h2>
        
        <a href="index.php" style="background-color: #495057; color: white;">Dashboard</a>
        
        <a href="data_guru.php">Data Guru</a>
        <a href="data_murid.php">Data Murid</a>
        
        <a href="jadwal.php">Jadwal Les</a>
        <a href="absensi.php">Data Absensi</a>
        
        <a href="pembayaran.php">Keuangan (SPP)</a>
        <a href="kelola_gaji.php">Kelola Gaji Guru</a>
        
        <a href="modul.php">Modul Belajar</a>
        
        <a href="../logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Dashboard</h1>
        <p>Selamat datang kembali, <b><?php echo $_SESSION['nama_lengkap']; ?></b>.</p>
        
        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
            
            <div class="stat-box" style="background-color: #007bff;">
                <h3><?php echo $total_guru; ?></h3>
                <p>Total Guru</p>
            </div>

            <div class="stat-box" style="background-color: #17a2b8;">
                <h3><?php echo $total_murid; ?></h3>
                <p>Total Siswa</p>
            </div>

            <div class="stat-box" style="background-color: #ffc107; color: #333;">
                <h3><?php echo $total_modul; ?></h3>
                <p>Modul Belajar</p>
            </div>

            <?php if($total_wd_pending > 0): ?>
            <div class="stat-box" style="background-color: #dc3545;">
                <h3><?php echo $total_wd_pending; ?></h3>
                <p>Request Tarik Dana</p>
                <small style="color: #ffd;">Segera Cek Menu Kelola Gaji!</small>
            </div>
            <?php endif; ?>

        </div>
    </div>

</body>
</html>