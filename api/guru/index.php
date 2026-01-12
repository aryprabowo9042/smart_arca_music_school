<?php
session_start();
require_once(__DIR__ . '/../koneksi.php');

if (!isset($_SESSION['status']) || $_SESSION['role'] !== 'guru') {
    header("Location: ../admin/login.php");
    exit();
}

$nama_guru = $_SESSION['username'];
$user_check = mysqli_query($conn, "SELECT id FROM users WHERE username='$nama_guru' LIMIT 1");
$u = mysqli_fetch_assoc($user_check);
$id_guru_login = $u['id'];

$query_jadwal = mysqli_query($conn, "SELECT jadwal.*, m.username as nama_murid 
                                     FROM jadwal 
                                     JOIN users m ON jadwal.id_murid = m.id 
                                     WHERE jadwal.id_guru = '$id_guru_login'
                                     ORDER BY hari, jam ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f6; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #f8f9fa; }
        .btn { padding: 8px 12px; background: #1a73e8; color: white; text-decoration: none; border-radius: 5px; font-size: 12px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <a href="../logout.php" style="float:right; color:#dc3545; text-decoration:none; font-weight:bold;">Logout</a>
        <h2>Dashboard Guru: <?php echo htmlspecialchars($nama_guru); ?></h2>
        <hr>
        <h3>Jadwal Mengajar Anda</h3>
        <table>
            <thead>
                <tr>
                    <th>Hari, Jam</th>
                    <th>Nama Murid</th>
                    <th>Alat Musik</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($j = mysqli_fetch_assoc($query_jadwal)) { ?>
                <tr>
                    <td><?php echo $j['hari']; ?>, <?php echo $j['jam']; ?></td>
                    <td><?php echo htmlspecialchars($j['nama_murid']); ?></td>
                    <td><strong><?php echo $j['alat_musik']; ?></strong></td>
                    <td><a href="absen.php?id_jadwal=<?php echo $j['id']; ?>" class="btn">ISI MATERI</a></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
