<?php
session_start();
ob_start();
require_once(__DIR__ . '/../koneksi.php');

// Proteksi Double Check
$is_logged_in = (isset($_SESSION['status']) && $_SESSION['status'] == 'login') || isset($_COOKIE['user_login']);
$is_guru = (isset($_SESSION['role']) && $_SESSION['role'] == 'guru') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'guru');

if (!$is_logged_in || !$is_guru) {
    echo "<script>window.location.replace('../admin/login.php');</script>";
    exit();
}

$display_name = $_SESSION['username'] ?? $_COOKIE['user_login'];

// Ambil ID Guru berdasarkan nama
$user_check = mysqli_query($conn, "SELECT id FROM users WHERE username='$display_name' LIMIT 1");
$u = mysqli_fetch_assoc($user_check);
$id_guru_login = $u['id'];

// Ambil Jadwal Mengajar Guru ini saja
$query_jadwal = mysqli_query($conn, "SELECT jadwal.*, m.username as nama_murid 
                                     FROM jadwal 
                                     JOIN users m ON jadwal.id_murid = m.id 
                                     WHERE jadwal.id_guru = '$id_guru_login'
                                     ORDER BY hari, jam ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru - Smart Arca</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f6; padding: 15px; }
        .card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); max-width: 800px; margin: auto; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        .btn { padding: 8px 12px; background: #1a73e8; color: white; text-decoration: none; border-radius: 5px; font-size: 12px; font-weight: bold; }
    </style>
</head>
<body>
<div class="card">
    <div class="header">
        <h3>Halo, Guru <?php echo htmlspecialchars($display_name); ?></h3>
        <a href="/api/logout.php" style="color:red; text-decoration:none; font-weight:bold;">Keluar</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Hari / Jam</th>
                <th>Murid</th>
                <th>Materi</th>
            </tr>
        </thead>
        <tbody>
            <?php while($j = mysqli_fetch_assoc($query_jadwal)) { ?>
            <tr>
                <td><?php echo $j['hari']; ?><br><small><?php echo $j['jam']; ?></small></td>
                <td><?php echo htmlspecialchars($j['nama_murid']); ?></td>
                <td><a href="absen.php?id_jadwal=<?php echo $j['id']; ?>" class="btn">ISI LAPORAN</a></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
