<?php
session_start();
include '../includes/koneksi.php';

if ($_SESSION['status'] != "login" || $_SESSION['role'] != "guru") { header("Location: ../login.php"); exit(); }

$id_guru = $_SESSION['id_user'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Jadwal Mengajar</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="sidebar">
        <h2>Panel Guru</h2>
        <a href="index.php">Dashboard</a>
        <a href="jadwal_saya.php" style="background-color: #495057; color: white;">Jadwal Mengajar</a>
        <a href="keuangan.php">Input Pembayaran</a>
        <a href="dompet.php">Dompet Saya</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Jadwal Mengajar Anda</h1>
        <p>Berikut adalah jadwal kelas di mana Anda terdaftar sebagai pengajar.</p>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Hari</th>
                        <th>Jam</th>
                        <th>Siswa</th>
                        <th>Ruangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT jadwal.*, users.nama_lengkap AS nama_murid 
                              FROM jadwal 
                              JOIN users ON jadwal.id_murid = users.id 
                              WHERE jadwal.id_guru = '$id_guru'
                              ORDER BY FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'), jam_mulai ASC";
                    
                    $result = mysqli_query($koneksi, $query);

                    while ($data = mysqli_fetch_assoc($result)) {
                    ?>
                    <tr>
                        <td><b><?php echo $data['hari']; ?></b></td>
                        <td><?php echo date('H:i', strtotime($data['jam_mulai'])) . " - " . date('H:i', strtotime($data['jam_selesai'])); ?></td>
                        <td><?php echo $data['nama_murid']; ?></td>
                        <td><?php echo $data['ruangan']; ?></td>
                        <td>
                            <a href="isi_jurnal.php?id=<?php echo $data['id']; ?>" class="btn btn-green" style="font-size:12px; padding:5px 10px;">Isi Jurnal</a>
                            
                            <a href="edit_jadwal.php?id=<?php echo $data['id']; ?>" class="btn btn-blue" style="font-size:12px; padding:5px 10px;">Ubah Jadwal</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>