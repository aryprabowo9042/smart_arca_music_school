<?php
session_start();
ob_start();

// Proteksi Guru: Jika tidak ada session, lempar ke login
// PENTING: Gunakan path yang benar ke login.php
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'guru') {
    // Jika Vercel error membaca session, coba cek cookie sebagai cadangan
    if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'guru') {
        echo "<script>window.location.replace('../admin/login.php');</script>";
        exit();
    }
}

require_once(__DIR__ . '/../koneksi.php');

$nama_guru = $_SESSION['username'] ?? 'Guru';
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-indigo-600 p-4 text-white flex justify-between">
        <h1 class="font-bold">GURU DASHBOARD</h1>
        <a href="../admin/index.php?action=logout" class="text-sm bg-indigo-800 px-3 py-1 rounded">Keluar</a>
    </nav>
    <div class="p-6 text-center">
        <h2 class="text-xl">Selamat Datang, <strong><?php echo $nama_guru; ?></strong></h2>
        <p class="text-gray-500">Anda berhasil masuk ke sistem guru.</p>
    </div>
</body>
</html>
