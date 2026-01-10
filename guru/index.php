<?php
session_start();
include '../includes/koneksi.php';

// Cek apakah yang login adalah Guru
if ($_SESSION['status'] != "login" || $_SESSION['role'] != "guru") {
    header("Location: ../login.php");
    exit();
}

$id_guru = $_SESSION['id_user'];

// Hitung jumlah jadwal yang dimiliki guru ini
$query_jadwal = mysqli_query($koneksi, "SELECT * FROM jadwal WHERE id_guru='$id_guru'");
$jumlah_kelas = mysqli_num_rows($query_jadwal);

// Hitung total uang yang diterima guru ini (Opsional, biar dashboard lebih keren)
$query_uang = mysqli_query($koneksi, "SELECT SUM(jumlah) AS total FROM pembayaran WHERE id_penerima='$id_guru'");
$data_uang = mysqli_fetch_assoc($query_uang);
$total_uang = $data_uang['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Guru</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="sidebar">
        <h2>Panel Guru</h2>
        <a href="index.php" style="background-color: #495057; color: white;">Dashboard</a>
        <a href="jadwal_saya.php">Jadwal Mengajar</a>
        <a href="keuangan.php">Input Pembayaran</a> <a href="../logout.php">Logout</a>
        <a href="dompet.php">Dompet Saya</a>
    </div>

    <div class="content">
        <h1>Selamat Datang, <?php echo $_SESSION['nama_lengkap']; ?>!</h1>
        <p>Selamat mengajar di Smart Arca Music School.</p>

        <div style="display: flex; gap: 20px;">
            <div class="stat-box" style="background-color: #6f42c1;">
                <h3><?php echo $jumlah_kelas; ?></h3>
                <p>Total Kelas Anda</p>
            </div>

            <div class="stat-box" style="background-color: #198754;">
                <h3>Rp <?php echo number_format($total_uang, 0, ',', '.'); ?></h3>
                <p>Uang Diterima</p>
            </div>
        </div>
    </div>

</body>
</html>