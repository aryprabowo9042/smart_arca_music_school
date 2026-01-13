<?php
session_start();
ob_start();

// LOGIKA LOGOUT LANGSUNG (MENGHINDARI 403)
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    setcookie('user_login', '', time() - 3600, '/');
    setcookie('user_role', '', time() - 3600, '/');
    header("Location: login.php");
    exit();
}

require_once(__DIR__ . '/../koneksi.php');

$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'admin');
if (!$is_admin) { header("Location: login.php"); exit(); }

$jml_murid = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM users WHERE role='murid'"));
$jml_guru  = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM users WHERE role='guru'"));
$q_omzet = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal_bayar) as total FROM absensi"));
$total_omzet = $q_omzet['total'] ?? 0;
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Smart Arca</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 20px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
        .card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center; }
        .menu { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-top: 20px; }
        .btn-menu { background: white; padding: 20px; border-radius: 10px; text-decoration: none; color: #333; font-weight: bold; border: 1px solid #ddd; text-align: center; }
        .btn-logout { display: block; margin-top: 30px; color: red; text-decoration: none; font-weight: bold; text-align: center; }
    </style>
</head>
<body>
    <h2>Dashboard Admin</h2>
    <div class="grid">
        <div class="card"><small>Murid</small><h3><?php echo $jml_murid; ?></h3></div>
        <div class="card"><small>Guru</small><h3><?php echo $jml_guru; ?></h3></div>
        <div class="card" style="background:#1a73e8; color:white;"><small>Total Omzet</small><h3>Rp <?php echo number_format($total_omzet); ?></h3></div>
    </div>
    <div class="menu">
        <a href="honor.php" class="btn-menu">💰 Keuangan</a>
        <a href="users.php" class="btn-menu">👥 Users</a>
        <a href="jadwal.php" class="btn-menu">📅 Jadwal</a>
    </div>
    <a href="index.php?action=logout" class="btn-logout">KELUAR / LOGOUT</a>
</body>
</html>
