<?php
// 1. Inisialisasi Session
session_start();

// 2. Menggunakan Path Absolut untuk memanggil koneksi.php
// __DIR__ akan mengambil lokasi folder 'admin', lalu '../' naik ke folder 'api'
$file_koneksi = __DIR__ . '/../koneksi.php';

if (file_exists($file_koneksi)) {
    include $file_koneksi;
} else {
    // Jika file tidak ditemukan, tampilkan pesan error yang rapi
    die("Sistem Error: File koneksi.php tidak ditemukan di jalur: " . $file_koneksi);
}

// 3. Proteksi Halaman: Cek apakah user sudah login & apakah role-nya 'admin'
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login" || $_SESSION['role'] != "admin") {
    header("Location: /login.php?pesan=belum_login");
    exit();
}

// 4. Mengambil data statistik sederhana dari TiDB Cloud
$query_siswa = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='murid'");
$data_siswa = mysqli_fetch_assoc($query_siswa);

$query_guru = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='guru'");
$data_guru = mysqli_fetch_assoc($query_guru);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | Smart Arca Music School</title>
    
    <link rel="stylesheet" href="/css/landing.css">
    
    <style>
        :root { --sidebar-bg: #2c3e50; --accent: #e67e22; --bg-light: #f8f9fa; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; margin: 0; background: var(--bg-light); display: flex; }
        
        /* Sidebar */
        .sidebar { width: 260px; background: var(--sidebar-bg); color: white; height: 100vh; position: fixed; padding: 25px; box-sizing: border-box; }
        .sidebar h2 { color: var(--accent); font-size: 1.5rem; margin-bottom: 40px; text-align: center; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { margin-bottom: 20px; }
        .sidebar ul li a { color: #ecf0f1; text-decoration: none; font-size: 1rem; display: block; padding: 10px; border-radius: 8px; transition: 0.3s; }
        .sidebar ul li a:hover { background: #34495e; color: var(--accent); }
        .sidebar .active { background: var(--accent); color: white !important; }

        /* Main Content */
        .main-content { margin-left: 260px; padding: 40px; width: 100%; box-sizing: border-box; }
        .header-top { background: white; padding: 20px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 30px; }
        
        /* Cards */
        .card-box { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-bottom: 4px solid var(--accent); }
        .card h3 { margin: 0; color: #7f8c8d; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; }
        .card p { font-size: 2.5rem; font-weight: bold; margin: 15px 0; color: var(--sidebar-bg); }
        
        .logout-link { background: #e74c3c; color: white !important; text-align: center; font-weight: bold; margin-top: 50px; }
        .logout-link:hover { background: #c0392b !important; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Smart Arca</h2>
        <ul>
            <li><a href="/admin/index.php" class="active">🏠 Dashboard</a></li>
            <li><a href="#">👥 Data Siswa</a></li>
            <li><a href="#">🎸 Data Guru</a></li>
            <li><a href="#">🎹 Jadwal & Kelas</a></li>
            <li><a href="#">💳 Pembayaran</a></li>
            <li><a href="/logout.php" class="logout-link">🚪 Keluar Sistem</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header-top">
            <div>
                <h1 style="margin: 0; font-size: 1.6rem;">Panel Administrator</h1>
                <p style="margin: 5px 0 0; color: #95a5a6;">Halo, <strong><?php echo $_SESSION['username']; ?></strong>. Selamat mengelola sekolah hari ini.</p>
            </div>
            <div style="text-align: right;">
                <span style="background: #e8f5e9; color: #2e7d32; padding: 8px 15px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; border: 1px solid #c8e6c9;">
                    ● Database TiDB Terhubung
                </span>
            </div>
        </div>

        <div class="card-box">
            <div class="card">
                <h3>Total Siswa</h3>
                <p><?php echo $data_siswa['total'] ?? 0; ?></p>
                <small style="color: #27ae60;">Siswa terdaftar aktif</small>
            </div>

            <div class="card">
                <h3>Total Guru</h3>
                <p><?php echo $data_guru['total'] ?? 0; ?></p>
                <small style="color: #27ae60;">Instruktur musik aktif</small>
            </div>

            <div class="card">
                <h3>Waktu Server</h3>
                <p style="font-size: 1.4rem; margin-top: 25px;"><?php echo date('d M Y'); ?></p>
                <small style="color: #7f8c8d;">Update: <?php echo date('H:i'); ?> WIB</small>
            </div>
        </div>

        <div style="margin-top: 30px; background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
            <h3 style="margin-top: 0; color: var(--sidebar-bg);">Aktivitas Terakhir</h3>
            <hr style="border: 0.5px solid #f1f1f1; margin: 15px 0;">
            <p style="color: #95a5a6; font-style: italic;">Sistem berjalan normal. Belum ada aktivitas pendaftaran siswa baru hari ini.</p>
        </div>
    </div>

</body>
</html>