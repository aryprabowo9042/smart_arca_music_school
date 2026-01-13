<?php
session_start();
ob_start();

// LOGIKA LOGOUT ANTI-403
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    setcookie('user_login', '', time() - 3600, '/');
    setcookie('user_role', '', time() - 3600, '/');
    echo "<script>window.location.replace('login.php');</script>";
    exit();
}

require_once(__DIR__ . '/../koneksi.php');

$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'admin');
if (!$is_admin) { echo "<script>window.location.replace('login.php');</script>"; exit(); }

$jml_murid = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM users WHERE role='murid'"));
$jml_guru  = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM users WHERE role='guru'"));
$total_omzet = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal_bayar) as total FROM absensi"))['total'] ?? 0;
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Smart Arca</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 20px; margin:0; }
        .card-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 20px; }
        .card { background: white; padding: 20px; border-radius: 12px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .menu-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; }
        .menu-btn { background: #1a73e8; color: white; padding: 15px; border-radius: 10px; text-decoration: none; text-align: center; font-weight: bold; font-size: 14px; }
        .btn-logout { display: block; margin-top: 40px; text-align: center; color: #dc3545; font-weight: bold; text-decoration: none; border: 1px solid #dc3545; padding: 10px; border-radius: 8px; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Smart Arca Admin</h2>
    <div class="card-grid">
        <div class="card"><small>Murid</small><h3><?php echo $jml_murid; ?></h3></div>
        <div class="card"><small>Guru</small><h3><?php echo $jml_guru; ?></h3></div>
        <div class="card" style="background:#1a73e8; color:white;"><small>Total Omzet</small><h3>Rp <?php echo number_format($total_omzet); ?></h3></div>
    </div>
    <div class="menu-grid">
        <a href="honor.php" class="menu-btn">KEUANGAN</a>
        <a href="users.php" class="menu-btn">USERS</a>
        <a href="jadwal.php" class="menu-btn">JADWAL</a>
    </div>
    <a href="index.php?action=logout" class="btn-logout">KELUAR (LOGOUT)</a>
</body>
</html>
