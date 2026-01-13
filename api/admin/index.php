<?php
// api/admin/index.php

// 1. Cek Logout
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    setcookie('user_role', '', time() - 3600, '/'); // Hapus cookie
    header("Location: login.php");
    exit();
}

// 2. DIAGNOSA COOKIE (JANGAN DI-REDIRECT DULU)
$role = isset($_COOKIE['user_role']) ? $_COOKIE['user_role'] : '';

// Jika Cookie Kosong atau Salah, TAMPILKAN ERROR (Jangan dilempar balik biar gak looping)
if ($role != 'admin') {
    ?>
    <!DOCTYPE html>
    <html>
    <head><script src="https://cdn.tailwindcss.com"></script></head>
    <body class="bg-red-50 p-10 text-center">
        <h1 class="text-3xl font-bold text-red-600 mb-4">AKSES DITOLAK (DEBUG MODE)</h1>
        <p class="mb-4">Sistem mendeteksi Anda belum login atau Cookie tidak terbaca.</p>
        
        <div class="bg-white p-4 rounded shadow inline-block text-left mb-6">
            <p><strong>Status Cookie:</strong> <?php echo $role ? $role : "KOSONG (Tidak terbaca)"; ?></p>
            <p><strong>Yang Diharapkan:</strong> admin</p>
        </div>
        <br>
        <a href="login.php" class="bg-red-600 text-white px-6 py-2 rounded font-bold">KEMBALI KE LOGIN</a>
    </body>
    </html>
    <?php
    exit(); // Stop script disini
}

// --- JIKA LOLOS DARI ATAS, BERARTI LOGIN SUKSES ---
require_once(__DIR__ . '/../koneksi.php');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-blue-700 p-4 text-white flex justify-between items-center shadow-lg">
        <h1 class="font-bold text-xl">DASHBOARD ADMIN</h1>
        <a href="index.php?action=logout" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded text-sm font-bold transition">LOGOUT</a>
    </nav>

    <div class="p-8 max-w-4xl mx-auto">
        <div class="bg-white p-8 rounded-2xl shadow-md text-center">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Selamat Datang!</h2>
            <p class="text-gray-500 mb-8">Anda berhasil login sebagai Administrator.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="users.php" class="block p-6 bg-blue-50 text-blue-700 font-bold rounded-xl hover:bg-blue-100 transition">
                    👤 KELOLA USER
                </a>
                <a href="jadwal.php" class="block p-6 bg-green-50 text-green-700 font-bold rounded-xl hover:bg-green-100 transition">
                    📅 KELOLA JADWAL
                </a>
                <a href="honor.php" class="block p-6 bg-orange-50 text-orange-700 font-bold rounded-xl hover:bg-orange-100 transition">
                    💰 KEUANGAN
                </a>
            </div>
        </div>
    </div>
</body>
</html>
