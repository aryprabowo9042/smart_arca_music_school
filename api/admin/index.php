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

// Statistik
$jml_murid = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM users WHERE role='murid'"));
$jml_guru  = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM users WHERE role='guru'"));
$total_omzet = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal_bayar) as total FROM absensi"))['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">

    <nav class="bg-white shadow-sm border-b px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="bg-blue-600 text-white p-2 rounded-lg font-bold">SA</div>
            <h1 class="text-xl font-bold text-gray-800">Smart Arca Admin</h1>
        </div>
        <a href="index.php?action=logout" class="text-red-500 hover:bg-red-50 p-2 rounded-lg transition font-semibold">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </a>
    </nav>

    <div class="max-w-6xl mx-auto p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="bg-blue-100 text-blue-600 w-12 h-12 rounded-xl flex items-center justify-center text-xl"><i class="fas fa-users"></i></div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Murid</p>
                    <h3 class="text-2xl font-bold text-gray-800"><?php echo $jml_murid; ?></h3>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="bg-purple-100 text-purple-600 w-12 h-12 rounded-xl flex items-center justify-center text-xl"><i class="fas fa-chalkboard-teacher"></i></div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Guru</p>
                    <h3 class="text-2xl font-bold text-gray-800"><?php echo $jml_guru; ?></h3>
                </div>
            </div>
            <div class="bg-blue-600 p-6 rounded-2xl shadow-lg flex items-center gap-4 text-white">
                <div class="bg-white/20 w-12 h-12 rounded-xl flex items-center justify-center text-xl"><i class="fas fa-wallet"></i></div>
                <div>
                    <p class="text-sm text-blue-100 font-medium">Total Omzet</p>
                    <h3 class="text-2xl font-bold">Rp <?php echo number_format($total_omzet); ?></h3>
                </div>
            </div>
        </div>

        <h2 class="text-lg font-bold text-gray-700 mb-4">Menu Utama</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <a href="honor.php" class="bg-white p-6 rounded-2xl border-2 border-transparent hover:border-blue-500 transition shadow-sm group">
                <i class="fas fa-money-bill-wave text-3xl text-green-500 mb-4 block"></i>
                <h4 class="font-bold text-gray-800 group-hover:text-blue-600">Keuangan</h4>
                <p class="text-xs text-gray-400 mt-1">Laporan bagi hasil & biaya operasional</p>
            </a>
            <a href="users.php" class="bg-white p-6 rounded-2xl border-2 border-transparent hover:border-blue-500 transition shadow-sm group">
                <i class="fas fa-user-cog text-3xl text-blue-500 mb-4 block"></i>
                <h4 class="font-bold text-gray-800 group-hover:text-blue-600">Manajemen User</h4>
                <p class="text-xs text-gray-400 mt-1">Kelola data Guru, Murid, dan Admin</p>
            </a>
            <a href="jadwal.php" class="bg-white p-6 rounded-2xl border-2 border-transparent hover:border-blue-500 transition shadow-sm group">
                <i class="fas fa-calendar-alt text-3xl text-orange-500 mb-4 block"></i>
                <h4 class="font-bold text-gray-800 group-hover:text-blue-600">Jadwal Les</h4>
                <p class="text-xs text-gray-400 mt-1">Atur plotting guru dan murid</p>
            </a>
        </div>
    </div>

</body>
</html>
