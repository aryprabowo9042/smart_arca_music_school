<?php
session_start();
include '../includes/koneksi.php';

if ($_SESSION['status'] != "login" || $_SESSION['role'] != "murid") { header("Location: ../login.php"); exit(); }

$id_murid = $_SESSION['id_user'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Jadwal Les Saya</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="sidebar">
        <h2>Area Siswa</h2>
        <a href="index.php">Dashboard</a>
        <a href="jadwal_saya.php" style="background-color: #495057; color: white;">Jadwal Les</a>
        <a href="rekap_belajar.php">Rekap Hasil Belajar</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Jadwal Les Saya</h1>
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Hari</th>
                        <th>Jam</th>
                        <th>Guru Pengajar</th>
                        <th>Ruangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query mengambil jadwal milik murid ini, dan mengambil nama guru dari tabel users
                    $query = "SELECT jadwal.*, guru.nama_lengkap AS nama_guru 
                              FROM jadwal 
                              JOIN users AS guru ON jadwal.id_guru = guru.id 
                              WHERE jadwal.id_murid = '$id_murid'
                              ORDER BY FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')";
                    
                    $result = mysqli_query($koneksi, $query);

                    while ($data = mysqli_fetch_assoc($result)) {
                    ?>
                    <tr>
                        <td><b><?php echo $data['hari']; ?></b></td>
                        <td><?php echo date('H:i', strtotime($data['jam_mulai'])) . " - " . date('H:i', strtotime($data['jam_selesai'])); ?></td>
                        <td><?php echo $data['nama_guru']; ?></td>
                        <td><?php echo $data['ruangan']; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>