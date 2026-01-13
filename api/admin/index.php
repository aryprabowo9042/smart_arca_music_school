<?php
session_start();
ob_start();

// 1. Logika Logout
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    setcookie('user_id', '', time() - 3600, '/');
    echo "<script>window.location.replace('login.php');</script>";
    exit();
}

// 2. Proteksi Session
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "<script>window.location.replace('login.php');</script>";
    exit();
}

require_once(__DIR__ . '/../koneksi.php');
// Sisa kode tampilan dashboard Bapak di sini...
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-3xl shadow-xl text-center">
        <h1 class="text-2xl font-bold text-gray-800">SELAMAT DATANG ADMIN</h1>
        <p class="text-gray-500 mb-6">Sistem Smart Arca Berjalan Normal</p>
        <div class="grid grid-cols-2 gap-4">
            <a href="users.php" class="p-4 bg-blue-50 text-blue-600 font-bold rounded-2xl hover:bg-blue-600 hover:text-white transition">KELOLA USER</a>
            <a href="index.php?action=logout" class="p-4 bg-red-50 text-red-600 font-bold rounded-2xl hover:bg-red-600 hover:text-white transition">LOGOUT</a>
        </div>
    </div>
</body>
</html>
