<?php
session_start();

// Mencari file koneksi.php di folder api/ (naik satu tingkat)
$path_koneksi = dirname(__DIR__) . '/koneksi.php';

if (file_exists($path_koneksi)) {
    require_once($path_koneksi);
} else {
    die("Sistem Error: File koneksi tidak ditemukan di: " . $path_koneksi);
}

// Proteksi Halaman
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login" || $_SESSION['role'] != "admin") {
    header("Location: /login.php?pesan=belum_login");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="/css/landing.css">
</head>
<body style="padding: 50px; font-family: sans-serif;">
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
        <h1>Selamat Datang di Smart Arca, <?php echo $_SESSION['username']; ?>!</h1>
        <p>Status: <span style="color: green;">● Online (TiDB Cloud Terhubung)</span></p>
        <hr>
        <a href="/logout.php" style="color: red; font-weight: bold; text-decoration: none;">🚪 Keluar Sistem</a>
    </div>
</body>
</html>