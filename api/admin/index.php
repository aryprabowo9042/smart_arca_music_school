<?php
// 1. Memulai session
session_start();

// 2. Hubungkan ke database (naik satu tingkat ke folder api/)
// Kita tidak menggunakan folder 'includes' lagi agar sesuai struktur Vercel Anda
include "../koneksi.php";

// 3. Proteksi Halaman: Hanya admin yang boleh masuk
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login" || $_SESSION['role'] != "admin") {
    // Jika belum login atau bukan admin, tendang ke halaman login
    header("Location: /login.php?pesan=belum_login");
    exit();
}

// 4. Ambil data statistik sederhana dari database TiDB
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
    <title>Admin Dashboard - Smart Arca Music School</title>
    
    <link rel="stylesheet" href="/css/landing.css">
    
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; display: flex; background-color: #f4f7f6; }
        .sidebar { width: 260px; height: 100vh; background: #2c3e50; color: white; position: fixed; padding: 20px; }
        .main-content { margin-left: 300px; padding: 40px; width: 100%; }
        .sidebar h2 { color: #e67e22; font-size: 1.5rem; margin-bottom: 30px; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { padding: 12px 0; border-bottom: 1px solid #34495e; }
        .sidebar ul li a { color: #ecf0f1; text-decoration: none; display: block; transition: 0.3s; }
        .sidebar ul li a:hover { color: #e67e22; padding-left: 10px; }
        .card-container { display: flex; gap: 25px; margin-top: 30px; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); flex: 1; border-top: 5px solid #e67e22; }
        .card h3 { margin: 0; color: #7f8c8d; font-size: 0.9rem; text-transform: uppercase; }
        .card p { font-size: 2.5rem; font-weight: bold; margin: 10px 0; color: #2c3e50; }
        .welcome-msg { background: #fff; padding: 20px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .btn-logout { background: #e74c3c; color: white !important; padding: 10px; border-radius: 5px; text-align: center; margin-top: 50px; font-weight: bold; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Smart Arca</h2>
        <p style="font-size: 0.8rem; color: #bdc3c7;">ADMIN PANEL</p>
        <hr style="border: 0.5px solid #34495e;">
        <ul>
            <li><a href="/admin/index.php">🏠 Dashboard</a></li>
            <li><a href="#">👥 Data Siswa</a></li>
            <li><a href="#">🎸 Data Guru</a></li>
            <li><a href="#">🎹 Jadwal Kursus</a></li>
            <li><a href="#">💰 Pembayaran</a></li>
            <li><a href="/logout.php" class="btn-logout">🚪 Keluar Sistem</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="welcome-msg">
            <h1 style="margin: 0;">Selamat Datang, <?php echo $_SESSION['username']; ?>!</h1>
            <p style="color: #7f8c8d; margin-top: 5px;">Sistem Administrasi Smart Arca Music School - Weleri</p>
        </div>

        <div class="card-container">
            <div class="card">
                <h3>Total Siswa Aktif</h3>
                <p><?php echo $data_siswa['total']; ?></p>
                <span style="color: #27ae60; font-size: 0.8rem;">Siswa terdaftar</span>
            </div>

            <div class="card">
                <h3>Total Guru</h3>
                <p><?php echo $data_guru['total']; ?></p>
                <span style="color: #27ae60; font-size: 0.8rem;">Instruktur aktif</span>
            </div>

            <div class="card">
                <h3>Status Database</h3>
                <p style="font-size: 1.5rem; color: #27ae60; margin-top: 25px;">Terhubung</p>
                <span style="color: #7f8c8d; font-size: 0.8rem;">TiDB Cloud Serverless</span>
            </div>
        </div>

        <div style="margin-top: 40px; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <h3>Aktivitas Terbaru</h3>
            <p style="color: #95a5a6; font-style: italic;">Belum ada aktivitas pendaftaran baru hari ini.</p>
        </div>
    </div>

</body>
</html>