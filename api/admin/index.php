<?php
session_start();

// Mencegah output sebelum header (untuk menghindari error 'headers already sent')
ob_start();

// MENCARI FILE KONEKSI SECARA MUTLAK
// __DIR__ adalah folder 'api/admin', lalu dirname() akan membawa kita ke folder 'api'
$folder_api = dirname(__DIR__);
$file_koneksi = $folder_api . '/koneksi.php';

if (file_exists($file_koneksi)) {
    require_once($file_koneksi);
} else {
    die("Sistem Error: File koneksi tidak ditemukan di: " . $file_koneksi);
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
    <title>Dashboard Admin | Smart Arca</title>
    <link rel="stylesheet" href="/css/landing.css">
    <style>
        body { font-family: sans-serif; background: #f0f2f5; margin: 0; display: flex; }
        .sidebar { width: 250px; background: #2c3e50; color: white; height: 100vh; position: fixed; padding: 20px; }
        .main { margin-left: 250px; padding: 40px; width: 100%; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Smart Arca</h2>
        <hr>
        <p>User: <strong><?php echo $_SESSION['username']; ?></strong></p>
        <a href="/logout.php" style="color: #ff7675; text-decoration: none; font-weight: bold;">[🚪 Keluar]</a>
    </div>
    <div class="main">
        <div class="card">
            <h1>Panel Admin Berhasil Terbuka!</h1>
            <p>Selamat, Anda telah berhasil menghubungkan Vercel dengan TiDB Cloud secara sempurna.</p>
        </div>
    </div>
</body>
</html>
<?php ob_end_flush(); ?>