<?php
session_start();
include '../includes/koneksi.php';

if ($_SESSION['status'] != "login" || $_SESSION['role'] != "admin") { header("Location: ../login.php"); exit(); }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Jadwal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="sidebar">
        <h2>Smart Arca Admin</h2>
        <a href="index.php">Dashboard</a>
        <a href="data_guru.php">Data Guru</a>
        <a href="data_murid.php">Data Murid</a>
        <a href="jadwal.php" style="background-color: #495057; color: white;">Jadwal Les</a>
        <a href="absensi.php">Data Absensi</a>
        <a href="pembayaran.php">Keuangan</a>
        <a href="kelola_gaji.php">Kelola Gaji Guru</a>
        <a href="modul.php">Modul Belajar</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Kelola Jadwal Les</h1>
        <a href="tambah_jadwal.php" class="btn btn-green">+ Tambah Jadwal Baru</a>
        <br><br>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Hari</th>
                        <th>Jam</th>
                        <th>Siswa</th>
                        <th>Guru</th>
                        <th>Ruangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT jadwal.*, murid.nama_lengkap AS nama_murid, guru.nama_lengkap AS nama_guru 
                              FROM jadwal 
                              JOIN users AS murid ON jadwal.id_murid = murid.id 
                              JOIN users AS guru ON jadwal.id_guru = guru.id 
                              ORDER BY FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'), jam_mulai ASC";
                    
                    $result = mysqli_query($koneksi, $query);

                    while ($data = mysqli_fetch_assoc($result)) {
                    ?>
                    <tr>
                        <td><b><?php echo $data['hari']; ?></b></td>
                        <td><?php echo date('H:i', strtotime($data['jam_mulai'])) . " - " . date('H:i', strtotime($data['jam_selesai'])); ?></td>
                        <td><?php echo $data['nama_murid']; ?></td>
                        <td><?php echo $data['nama_guru']; ?></td>
                        <td><?php echo $data['ruangan']; ?></td>
                        <td>
                            <a href="edit_jadwal.php?id=<?php echo $data['id']; ?>" class="btn btn-blue" style="padding: 5px 10px; font-size: 12px;">Edit</a>
                            
                            <a href="hapus_jadwal.php?id=<?php echo $data['id']; ?>" class="btn btn-red" style="padding: 5px 10px; font-size: 12px;" onclick="return confirm('Hapus jadwal ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>