<?php
session_start();
require_once(__DIR__ . '/../koneksi.php');

if (!isset($_SESSION['status']) || $_SESSION['role'] !== 'guru') {
    echo "<script>window.location.href='/api/admin/login.php';</script>";
    exit();
}

$nama_guru = $_SESSION['username'];
$query_jadwal = mysqli_query($conn, "SELECT jadwal.*, m.username as nama_murid 
                                     FROM jadwal 
                                     JOIN users m ON jadwal.id_murid = m.id 
                                     JOIN users g ON jadwal.id_guru = g.id
                                     WHERE g.username = '$nama_guru'");
?>
<!DOCTYPE html>
<html>
<head><title>Panel Guru</title></head>
<body>
    <h2>Panel Guru: <?php echo $nama_guru; ?></h2>
    <a href="/api/logout.php">Logout</a><hr>
    <table border="1">
        <?php while($j = mysqli_fetch_assoc($query_jadwal)) { ?>
        <tr>
            <td><?php echo $j['nama_murid']; ?></td>
            <td><?php echo $j['alat_musik']; ?></td>
            <td><a href="absen.php?id_jadwal=<?php echo $j['id']; ?>">Isi Materi</a></td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
