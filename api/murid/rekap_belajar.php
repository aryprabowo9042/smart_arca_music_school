<?php
session_start();
include '../includes/koneksi.php';

if ($_SESSION['status'] != "login" || $_SESSION['role'] != "murid") { header("Location: ../login.php"); exit(); }

$id_murid = $_SESSION['id_user'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Rekap Hasil Belajar</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="sidebar">
        <h2>Area Siswa</h2>
        <a href="index.php">Dashboard</a>
        <a href="jadwal_saya.php">Jadwal Les</a>
        <a href="rekap_belajar.php" style="background-color: #495057; color: white;">Rekap Hasil Belajar</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Rekap Hasil Belajar</h1>
        <p>Berikut adalah catatan perkembangan belajar Anda dari Guru.</p>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Guru</th>
                        <th>Status</th>
                        <th>Catatan / Materi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query kompleks (3 Table Join): Absensi -> Jadwal -> Guru
                    // Tujuannya: Mengambil data absensi, tapi HANYA yang jadwalnya milik murid ini
                    $query = "SELECT absensi.*, users.nama_lengkap AS nama_guru
                              FROM absensi
                              JOIN jadwal ON absensi.id_jadwal = jadwal.id
                              JOIN users ON jadwal.id_guru = users.id
                              WHERE jadwal.id_murid = '$id_murid'
                              ORDER BY absensi.tanggal DESC"; // Urutkan dari yang terbaru
                    
                    $result = mysqli_query($koneksi, $query);

                    // Cek jika belum ada data
                    if(mysqli_num_rows($result) == 0){
                        echo "<tr><td colspan='4' style='text-align:center;'>Belum ada data hasil belajar.</td></tr>";
                    }

                    while ($data = mysqli_fetch_assoc($result)) {
                        // Logika warna status
                        $warna_status = "black";
                        if($data['status'] == 'hadir') $warna_status = "green";
                        if($data['status'] == 'izin' || $data['status'] == 'sakit') $warna_status = "orange";
                        if($data['status'] == 'alpa') $warna_status = "red";
                    ?>
                    <tr>
                        <td><?php echo date('d-m-Y', strtotime($data['tanggal'])); ?></td>
                        <td><?php echo $data['nama_guru']; ?></td>
                        <td style="color: <?php echo $warna_status; ?>; font-weight: bold;">
                            <?php echo strtoupper($data['status']); ?>
                        </td>
                        <td><?php echo nl2br($data['catatan_guru']); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>