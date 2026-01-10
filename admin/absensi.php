<?php
session_start();
include '../includes/koneksi.php';

if ($_SESSION['status'] != "login" || $_SESSION['role'] != "admin") { header("Location: ../login.php"); exit(); }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Data Absensi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="sidebar">
        <h2>Smart Arca Admin</h2>
        <a href="index.php">Dashboard</a>
        <a href="data_guru.php">Data Guru</a>
        <a href="data_murid.php">Data Murid</a>
        <a href="jadwal.php">Jadwal Les</a>
        <a href="absensi.php" style="background-color: #495057; color: white;">Data Absensi</a> <a href="pembayaran.php">Keuangan</a>
        <a href="kelola_gaji.php">Kelola Gaji Guru</a>
        <a href="modul.php">Modul Belajar</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Data Absensi & Jurnal Mengajar</h1>
        <p>Rekap kehadiran dan catatan belajar siswa.</p>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Siswa</th>
                        <th>Guru Pengajar</th>
                        <th>Status</th>
                        <th>Catatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query JOIN 4 Tabel: Absensi -> Jadwal -> Murid -> Guru
                    $query = "SELECT absensi.*, 
                                     murid.nama_lengkap AS nama_murid, 
                                     guru.nama_lengkap AS nama_guru
                              FROM absensi
                              JOIN jadwal ON absensi.id_jadwal = jadwal.id
                              JOIN users AS murid ON jadwal.id_murid = murid.id
                              JOIN users AS guru ON jadwal.id_guru = guru.id
                              ORDER BY absensi.tanggal DESC";
                    
                    $result = mysqli_query($koneksi, $query);

                    while ($data = mysqli_fetch_assoc($result)) {
                        // Warna status
                        $bg_status = "#eee";
                        if($data['status']=='hadir') $bg_status = "#d4edda"; // Hijau muda
                        if($data['status']=='izin') $bg_status = "#fff3cd"; // Kuning muda
                        if($data['status']=='sakit') $bg_status = "#ffeeba"; // Kuning
                        if($data['status']=='alpa') $bg_status = "#f8d7da"; // Merah muda
                    ?>
                    <tr>
                        <td><?php echo date('d-m-Y', strtotime($data['tanggal'])); ?></td>
                        <td><b><?php echo $data['nama_murid']; ?></b></td>
                        <td><?php echo $data['nama_guru']; ?></td>
                        <td>
                            <span style="background: <?php echo $bg_status; ?>; padding: 5px 10px; border-radius: 4px; font-weight: bold;">
                                <?php echo strtoupper($data['status']); ?>
                            </span>
                        </td>
                        <td><small><?php echo nl2br($data['catatan_guru']); ?></small></td>
                        <td>
                            <a href="edit_absensi.php?id=<?php echo $data['id']; ?>" class="btn btn-blue" style="padding: 5px 10px;">Edit</a>
                            <a href="hapus_absensi.php?id=<?php echo $data['id']; ?>" class="btn btn-red" style="padding: 5px 10px;" onclick="return confirm('Hapus data absensi ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>