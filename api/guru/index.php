<?php
session_start();
ob_start();
require_once(__DIR__ . '/../koneksi.php');

$is_guru = (isset($_SESSION['role']) && $_SESSION['role'] == 'guru') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'guru');
if (!$is_guru) { echo "<script>window.location.replace('../admin/login.php');</script>"; exit(); }

$display_name = $_SESSION['username'] ?? $_COOKIE['user_login'];
$u = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM users WHERE username='$display_name' LIMIT 1"));
$id_guru = $u['id'];

$honor_q = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal_bayar) as total FROM absensi a JOIN jadwal j ON a.id_jadwal = j.id WHERE j.id_guru = '$id_guru'"));
$saldo_guru = ($honor_q['total'] ?? 0) * 0.5;
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guru Smart Arca</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f6; padding: 15px; }
        .card { background: white; padding: 20px; border-radius: 12px; margin-bottom: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .saldo { background: #1a73e8; color: white; padding: 15px; border-radius: 10px; margin: 15px 0; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
        .btn { padding: 6px 12px; background: #1a73e8; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="card">
        <h3>Halo, Guru <?php echo $display_name; ?></h3>
        <div class="saldo">
            <small>Estimasi Saldo Honor (50%):</small>
            <h2 style="margin:5px 0;">Rp <?php echo number_format($saldo_guru, 0, ',', '.'); ?></h2>
        </div>
        <a href="/api/logout.php" style="color:red; text-decoration:none; font-weight:bold;">Logout</a>
    </div>
    <div class="card">
        <h4>Jadwal Mengajar</h4>
        <table>
            <?php
            $q = mysqli_query($conn, "SELECT j.*, m.username FROM jadwal j JOIN users m ON j.id_murid = m.id WHERE id_guru = '$id_guru'");
            while($row = mysqli_fetch_assoc($q)) { ?>
            <tr>
                <td><strong><?php echo $row['hari']; ?></strong><br><?php echo $row['jam']; ?></td>
                <td><?php echo $row['username']; ?></td>
                <td><a href="absen.php?id_jadwal=<?php echo $row['id']; ?>" class="btn">ISI ABSEN</a></td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
