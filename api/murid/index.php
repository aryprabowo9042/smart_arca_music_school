<?php
session_start();
include '../includes/koneksi.php';

// Cek apakah user adalah Murid
if ($_SESSION['status'] != "login" || $_SESSION['role'] != "murid") {
    header("Location: ../login.php");
    exit();
}

$id_murid = $_SESSION['id_user'];

// 1. Ambil data lengkap murid
$query_murid = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$id_murid'");
$data_murid = mysqli_fetch_assoc($query_murid);

// Pengecekan data murid (takutnya data kosong)
$kelas_murid = isset($data_murid['kelas_musik']) ? $data_murid['kelas_musik'] : '';
$level_murid = isset($data_murid['level_musik']) ? $data_murid['level_musik'] : '';

// 2. Hitung jumlah jadwal
$q_jadwal = mysqli_query($koneksi, "SELECT * FROM jadwal WHERE id_murid='$id_murid'");
$jml_jadwal = mysqli_num_rows($q_jadwal);

// 3. Hitung jumlah modul (DENGAN PENGECEKAN ERROR)
$sql_modul = "SELECT * FROM modul WHERE kelas_musik='$kelas_murid' AND level_target='$level_murid'";
$q_modul = mysqli_query($koneksi, $sql_modul);

if (!$q_modul) {
    // Jika query gagal, tampilkan pesan error SQL-nya agar ketahuan masalahnya
    die("Error Database pada Modul: " . mysqli_error($koneksi)); 
}

$jml_modul = mysqli_num_rows($q_modul);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Siswa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="sidebar">
        <h2>Area Siswa</h2>
        <a href="index.php" style="background-color: #495057; color: white;">Dashboard</a>
        <a href="jadwal_saya.php">Jadwal Les</a>
        <a href="rekap_belajar.php">Rekap Hasil Belajar</a>
        <a href="pembayaran_saya.php">Riwayat Pembayaran</a>
        <a href="modul_saya.php">Materi Belajar</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Halo, <?php echo $_SESSION['nama_lengkap']; ?>!</h1>
        <p>Selamat datang di area siswa Smart Arca Music School.</p>
        
        <div style="display: flex; gap: 20px;">
            <div class="stat-box" style="background-color: #fd7e14;">
                <h3 style="font-size: 20px;"><?php echo $kelas_murid ? $kelas_murid : '-'; ?></h3>
                <p>Kelas Instrumen</p>
            </div>
            
            <div class="stat-box" style="background-color: #20c997;">
                <h3 style="font-size: 20px;"><?php echo $level_murid ? $level_murid : '-'; ?></h3>
                <p>Level Saat Ini</p>
            </div>

            <div class="stat-box" style="background-color: #0dcaf0; color: #333;">
                <h3 style="font-size: 20px;"><?php echo $jml_modul; ?> Modul</h3>
                <p>Materi Tersedia</p>
            </div>
        </div>
    </div>

</body>
</html>