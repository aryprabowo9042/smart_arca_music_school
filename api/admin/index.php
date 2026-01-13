<?php
session_start();
ob_start();

// 1. LOGIKA LOGOUT (Satu halaman agar tidak 403)
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    setcookie('user_login', '', time() - 3600, '/');
    echo "<script>window.location.replace('login.php');</script>";
    exit();
}

// 2. PROTEKSI SESSION (Gunakan JS Redirect jika gagal)
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "<script>window.location.replace('login.php');</script>";
    exit();
}

require_once(__DIR__ . '/../koneksi.php');

// Ambil Statistik
$jml_murid = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM users WHERE role='murid'"));
$jml_guru  = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM users WHERE role='guru'"));
$total_omzet = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal_bayar) as total FROM absensi"))['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">

    <nav class="bg-blue-600 text-white p-4 shadow-md flex justify-between items-center">
        <h1 class="font-bold">SMART ARCA ADMIN</h1>
        <a href="index.php?action=logout" class="bg-red-500 px-3 py-1 rounded text-sm font-bold">KELUAR</a>
    </nav>

    <div class="p-6 max-w-4xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-blue-500">
                <p class="text-xs text-gray-500 uppercase">Total Murid</p>
                <h3 class="text-2xl font-bold"><?php echo $jml_murid; ?></h3>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-green-500">
                <p class="text-xs text-gray-500 uppercase">Total Guru</p>
                <h3 class="text-2xl font-bold"><?php echo $jml_guru; ?></h3>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-yellow-500">
                <p class="text-xs text-gray-500 uppercase">Omzet</p>
                <h3 class="text-2xl font-bold">Rp <?php echo number_format($total_omzet); ?></h3>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <a href="users.php" class="bg-white p-6 rounded-xl shadow hover:bg-blue-50 transition text-center">
                <i class="fas fa-users text-3xl text-blue-600 mb-2"></i>
                <p class="font-bold text-gray-700">Manajemen User</p>
            </a>
            <a href="jadwal.php" class="bg-white p-6 rounded-xl shadow hover:bg-blue-50 transition text-center">
                <i class="fas fa-calendar-alt text-3xl text-green-600 mb-2"></i>
                <p class="font-bold text-gray-700">Jadwal Les</p>
            </a>
            <a href="honor.php" class="bg-white p-6 rounded-xl shadow hover:bg-blue-50 transition text-center">
                <i class="fas fa-wallet text-3xl text-yellow-600 mb-2"></i>
                <p class="font-bold text-gray-700">Keuangan</p>
            </a>
        </div>
    </div>

</body>
</html>
