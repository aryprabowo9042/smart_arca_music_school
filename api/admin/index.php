<?php
// 1. LOGIKA LOGOUT (PRIORITAS UTAMA)
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    // Hapus semua cookie
    setcookie('user_role', '', time() - 3600, '/');
    setcookie('user_id', '', time() - 3600, '/');
    setcookie('user_username', '', time() - 3600, '/');
    setcookie('user_name', '', time() - 3600, '/');

    // Arahkan ke Landing Page (Mundur 2 folder: api/admin/ -> root)
    header("Location: ../../index.php");
    exit();
}

// 2. CEK LOGIN (COOKIE MODE)
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

require_once(__DIR__ . '/../koneksi.php');
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
<body class="bg-gray-100 min-h-screen">

    <nav class="bg-blue-700 p-4 text-white flex justify-between items-center shadow-lg sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <i class="fas fa-user-shield text-2xl"></i>
            <div>
                <h1 class="font-bold text-lg leading-tight">ADMINISTRATOR</h1>
                <p class="text-[10px] text-blue-200">Panel Kontrol Utama</p>
            </div>
        </div>
        <a href="index.php?action=logout" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded text-sm font-bold transition flex items-center gap-2">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </a>
    </nav>

    <div class="p-6 max-w-5xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <a href="users.php" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition group">
                <div class="flex justify-between items-start mb-4">
                    <div class="bg-blue-100 p-3 rounded-xl group-hover:bg-blue-600 transition">
                        <i class="fas fa-users text-blue-600 text-xl group-hover:text-white transition"></i>
                    </div>
                </div>
                <h3 class="font-bold text-gray-700">Kelola User</h3>
                <p class="text-xs text-gray-400 mt-1">Tambah/Hapus Guru & Murid</p>
            </a>

            <a href="jadwal.php" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition group">
                <div class="flex justify-between items-start mb-4">
                    <div class="bg-green-100 p-3 rounded-xl group-hover:bg-green-600 transition">
                        <i class="fas fa-calendar-alt text-green-600 text-xl group-hover:text-white transition"></i>
                    </div>
                </div>
                <h3 class="font-bold text-gray-700">Atur Jadwal</h3>
                <p class="text-xs text-gray-400 mt-1">Plotting Guru & Murid</p>
            </a>

            <a href="honor.php" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition group">
                <div class="flex justify-between items-start mb-4">
                    <div class="bg-purple-100 p-3 rounded-xl group-hover:bg-purple-600 transition">
                        <i class="fas fa-wallet text-purple-600 text-xl group-hover:text-white transition"></i>
                    </div>
                </div>
                <h3 class="font-bold text-gray-700">Keuangan</h3>
                <p class="text-xs text-gray-400 mt-1">Gaji Guru & SPP Masuk</p>
            </a>

             <a href="../../index.php" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition group">
                <div class="flex justify-between items-start mb-4">
                    <div class="bg-orange-100 p-3 rounded-xl group-hover:bg-orange-600 transition">
                        <i class="fas fa-globe text-orange-600 text-xl group-hover:text-white transition"></i>
                    </div>
                </div>
                <h3 class="font-bold text-gray-700">Lihat Website</h3>
                <p class="text-xs text-gray-400 mt-1">Halaman Depan</p>
            </a>

        </div>

        <div class="mt-10 text-center text-gray-400 text-xs">
            &copy; <?php echo date('Y'); ?> Smart Arca Music School System
        </div>
    </div>
</body>
</html>
