<?php
session_start();
// PASTI KAN BARIS INI SEPERTI INI:
require_once(__DIR__ . '/../koneksi.php');

if (!isset($_SESSION['status']) || $_SESSION['role'] !== 'murid') {
    echo "<script>window.location.href='/api/admin/login.php';</script>";
    exit();
}

$nama_murid = $_SESSION['username'];
$query_belajar = mysqli_query($conn, "SELECT absensi.*, j.alat_musik, u.username as nama_guru 
                                      FROM absensi 
                                      JOIN jadwal j ON absensi.id_jadwal = j.id 
                                      JOIN users m ON j.id_murid = m.id 
                                      JOIN users u ON j.id_guru = u.id
                                      WHERE m.username = '$nama_murid'
                                      ORDER BY absensi.tanggal DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ruang Murid</title>
    <style>body { font-family: sans-serif; padding: 20px; background: #f0f2f5; }</style>
</head>
<body>
    <h2>Halo, <?php echo htmlspecialchars($nama_murid); ?></h2>
    <a href="/api/logout.php">Logout</a><hr>
    <?php while($row = mysqli_fetch_assoc($query_belajar)) { ?>
        <div style="background:white; padding:15px; margin-bottom:10px; border-radius:10px;">
            <strong><?php echo $row['tanggal']; ?></strong> - <?php echo $row['alat_musik']; ?><br>
            Materi: <?php echo $row['materi_ajar']; ?>
        </div>
    <?php } ?>
</body>
</html>
