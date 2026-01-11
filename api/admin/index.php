<?php
session_start();

// Mencegah error tampilan sebelum redirect
ob_start();

// CARA PALING AMPUH DI VERCEL:
// Mencari file koneksi.php di folder api/ (satu tingkat di atas folder admin)
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
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | Smart Arca</title>
    <link rel="stylesheet" href="/css/landing.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; background: #f4f7f6; }
        .sidebar { width: 250px; background: #2c3e50; color: white; height: 100vh; position: fixed; padding: 25px; }
        .main { margin-left: 250px; padding: 40px; width: 100%; }
        .sidebar h2 { color: #e67e22; border-bottom: 1px solid #34495e; padding-bottom: 10px; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 5px solid #e67e22; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Smart Arca</h2>
        <p>Halo, <strong><?php echo $_SESSION['username']; ?></strong></p>
        <hr>
        <ul style="list-style:none; padding:0;">
            <li style="margin: 15px 0;"><a href="/admin/index.php" style="color:white; text-decoration:none;">🏠 Dashboard</a></li>
            <li style="margin: 15px 0;"><a href="/logout.php" style="color:#e74c3c; text-decoration:none; font-weight:bold;">🚪 Logout</a></li>
        </ul>
    </div>
    <div class="main">
        <div class="card">
            <h1>Selamat Datang di Panel Admin</h1>
            <p>Koneksi ke TiDB Cloud Berhasil!</p>
            <p style="color: #27ae60; font-weight: bold;">● Sistem Online</p>
        </div>
    </div>
</body>
</html>
<?php ob_end_flush(); ?>