<?php
session_start();
ob_start();

// 1. Logika Logout
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    echo "<script>window.location.replace('login.php');</script>";
    exit();
}

// 2. Proteksi Halaman (Jangan diusir jika session ada)
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "<script>window.location.replace('login.php');</script>";
    exit();
}

require_once(__DIR__ . '/../koneksi.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-4xl mx-auto p-10">
        <div class="bg-white p-8 rounded-3xl shadow-xl text-center">
            <h1 class="text-3xl font-bold text-blue-600 mb-2">DASHBOARD ADMIN</h1>
            <p class="text-gray-500 mb-8">Berhasil Login sebagai <?php echo $_SESSION['username']; ?></p>
            
            <div class="grid grid-cols-2 gap-4">
                <a href="users.php" class="p-4 bg-blue-50 text-blue-600 font-bold rounded-2xl hover:bg-blue-600 hover:text-white transition">KELOLA USER</a>
                <a href="jadwal.php" class="p-4 bg-green-50 text-green-600 font-bold rounded-2xl hover:bg-green-600 hover:text-white transition">KELOLA JADWAL</a>
                <a href="honor.php" class="p-4 bg-orange-50 text-orange-600 font-bold rounded-2xl hover:bg-orange-600 hover:text-white transition">KEUANGAN</a>
                <a href="index.php?action=logout" class="p-4 bg-red-50 text-red-600 font-bold rounded-2xl hover:bg-red-600 hover:text-white transition">KELUAR</a>
            </div>
        </div>
    </div>
</body>
</html>
