<?php
session_start();
include '../includes/koneksi.php';

if ($_SESSION['status'] != "login" || $_SESSION['role'] != "murid") { header("Location: ../login.php"); exit(); }

$id_murid = $_SESSION['id_user'];

function buatRupiah($angka){ return "Rp " . number_format($angka,0,',','.'); }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Riwayat Pembayaran</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="sidebar">
        <h2>Area Siswa</h2>
        <a href="index.php">Dashboard</a>
        <a href="jadwal_saya.php">Jadwal Les</a>
        <a href="rekap_belajar.php">Rekap Hasil Belajar</a>
        <a href="pembayaran_saya.php" style="background-color: #495057; color: white;">Riwayat Pembayaran</a> <a href="../logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Riwayat Pembayaran Les</h1>
        <p>Berikut adalah catatan pembayaran yang telah masuk ke sistem kami.</p>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nominal</th>
                        <th>Keterangan</th>
                        <th>Penerima</th> </tr>
                </thead>
                <tbody>
                    <?php
                    // Query menampilkan pembayaran milik murid ini saja
                    $query = "SELECT pembayaran.*, penerima.nama_lengkap AS nama_penerima
                              FROM pembayaran
                              JOIN users AS penerima ON pembayaran.id_penerima = penerima.id
                              WHERE pembayaran.id_murid = '$id_murid'
                              ORDER BY pembayaran.tanggal DESC";
                    
                    $result = mysqli_query($koneksi, $query);

                    if(mysqli_num_rows($result) == 0){
                        echo "<tr><td colspan='4' style='text-align:center;'>Belum ada data pembayaran.</td></tr>";
                    }

                    while ($data = mysqli_fetch_assoc($result)) {
                    ?>
                    <tr>
                        <td><?php echo date('d-m-Y', strtotime($data['tanggal'])); ?></td>
                        <td style="color: green; font-weight: bold;"><?php echo buatRupiah($data['jumlah']); ?></td>
                        <td><?php echo $data['keterangan']; ?></td>
                        <td><?php echo $data['nama_penerima']; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>