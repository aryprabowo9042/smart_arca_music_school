<?php
// 1. Memulai session
session_start();

// 2. Memanggil koneksi menggunakan jalur absolut __DIR__
// __DIR__ adalah folder 'admin', lalu kita naik ke folder 'api' untuk ambil koneksi.php
$path_ke_koneksi = __DIR__ . '/../koneksi.php';

if (file_exists($path_ke_koneksi)) {
    include_once($path_ke_koneksi);
} else {
    die("Error Sistem: File koneksi.php tidak ditemukan di lokasi: " . $path_ke_koneksi);
}

// 3. Proteksi Halaman: Cek login dan role admin
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login" || $_SESSION['role'] != "admin") {
    header("Location: /login.php?pesan=belum_login");
    exit();
}

// 4. Ambil data statistik dari TiDB Cloud
$query_siswa = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='murid'");
$res_siswa = mysqli_fetch_assoc($query_siswa);

$query_guru = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='guru'");
$res_guru = mysqli_fetch_assoc($query_guru);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | Smart Arca Music School</title>
    <link rel="stylesheet" href="/css/landing.css">
    <style>
        :root { --pda: #e67e22; --dark: #2c3e50; }
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #f8f9fa; display: flex; }
        .sidebar { width: 250px; background: var(--dark); color: white; height: 100vh; position: fixed; padding: 25px; box-sizing: border-box; }
        .main-content { margin-left: 250px; width: 100%; padding: 40px; box-sizing: border-box; }
        .sidebar h2 { color: var(--pda); margin-bottom: 30px; border-bottom: 1px solid #34495e; padding-bottom: 10px; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { margin-bottom: 15px; }
        .sidebar ul li a { color: #ecf0f1; text-decoration: none; display: block; padding: 10px; border-radius: 5px; }
        .sidebar ul li a:hover { background: #34495e; }
        .card-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px; }
        .card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 5px solid var(--pda); }
        .card h3 { margin: 0; color: #7f8c8d; font-size: 0.8rem; text-transform: uppercase; }
        .card p { font-size: 2.2rem; font-weight: bold; margin: 10px 0; color: var(--dark); }
        .btn-logout { background: #e74c3c; color: white !important; text-align: center; font-weight: bold; margin-top: 30px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Smart Arca</h2>
        <ul>
            <li><a href="/admin/index.php" style="background: var(--pda); color: white;">🏠 Dashboard</a></li>
            <li><a href="#">👥 Data Siswa</a></li>
            <li><a href="#">🎸 Data Guru</a></li>
            <li><a href="/logout.php" class="btn-logout">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
            <h1 style="margin: 0;">Halo, <?php echo $_SESSION['username']; ?>!</h1>
            <p style="color: #7f8c8d;">Selamat mengelola Smart Arca Music School.</p>
        </div>

        <div class="card-grid">
            <div class="card">
                <h3>Total Siswa</h3>
                <p><?php echo $res_siswa['total'] ?? 0; ?></p>
            </div>
            <div class="card">
                <h3>Total Guru</h3>
                <p><?php echo $res_guru['total'] ?? 0; ?></p>
            </div>
            <div class="card" style="border-left-color: #27ae60;">
                <h3>Status Database</h3>
                <p style="font-size: 1.2rem; margin-top: 25px; color: #27ae60;">TERHUBUNG</p>
            </div>
        </div>
    </div>

</body>
</html>