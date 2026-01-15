<?php
// 1. PROTEKSI HALAMAN
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

require_once(__DIR__ . '/../koneksi.php');
$username = $_COOKIE['user_username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">

    <nav class="bg-red-600 shadow-xl px-6 py-4 flex justify-between items-center mb-10 border-b-4 border-yellow-400">
        <div class="flex items-center gap-3">
            <a href="../../index.php" class="text-white bg-red-700 w-10 h-10 rounded-xl flex items-center justify-center hover:bg-red-800 transition">
                <i class="fas fa-home"></i>
            </a>
            <img src="../logo.png" class="h-10 w-auto bg-white rounded-lg p-1" onerror="this.src='https://ui-avatars.com/api/?name=SA&background=ffffff&color=dc2626'">
            <h1 class="text-white font-black text-xl italic uppercase tracking-tighter">Admin Central</h1>
        </div>

        <div class="flex items-center gap-4">
            <span class="text-white font-bold text-xs uppercase hidden sm:block">Halo, <?php echo $username; ?></span>
            <a href="../logout.php" class="text-white hover:text-yellow-300 text-2xl transition">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-black text-slate-800 uppercase italic">Panel Kendali Utama</h2>
            <p class="text-slate-500 font-medium uppercase text-[10px] tracking-[0.3em] mt-2">Pilih menu untuk mengelola sekolah musik</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <a href="honor.php" class="group relative bg-white p-10 rounded-[3rem] shadow-xl border-b-[12px] border-red-600 hover:-translate-y-2 transition-all duration-300 overflow-hidden">
                <div class="absolute -right-4 -top-4 text-red-50 text-8xl group-hover:rotate-12 transition-transform">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-red-600 text-white rounded-2xl flex items-center justify-center text-3xl mb-6 shadow-lg">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <h3 class="text-2xl font-black text-slate-800 uppercase italic">Manajemen Keuangan</h3>
                    <p class="text-slate-500 text-sm mt-2 font-medium">Kelola saldo, honor guru, dan arus kas masuk/keluar.</p>
                </div>
            </a>

            <a href="#" class="group relative bg-white p-10 rounded-[3rem] shadow-xl border-b-[12px] border-yellow-400 hover:-translate-y-2 transition-all duration-300 overflow-hidden opacity-80">
                <div class="absolute -right-4 -top-4 text-yellow-50 text-8xl group-hover:rotate-12 transition-transform">
                    <i class="fas fa-users"></i>
                </div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-yellow-400 text-red-700 rounded-2xl flex items-center justify-center text-3xl mb-6 shadow-lg">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h3 class="text-2xl font-black text-slate-800 uppercase italic">Data Pengguna</h3>
                    <p class="text-slate-500 text-sm mt-2 font-medium">Kelola data guru, murid, dan jadwal kursus.</p>
                </div>
                <div class="absolute top-4 right-4 bg-red-600 text-white text-[8px] font-black px-2 py-1 rounded">MINTA UPDATE</div>
            </a>

        </div>

        <div class="mt-20 text-center">
            <p class="text-slate-300 text-[9px] font-black uppercase tracking-[0.5em]">&copy; Smart Arca Music School</p>
        </div>
    </div>

</body>
</html>
