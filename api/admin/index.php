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
<body class="bg-slate-50 min-h-screen pb-10">

    <nav class="bg-red-600 shadow-xl px-6 py-4 flex justify-between items-center mb-10 border-b-4 border-yellow-400 sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <a href="../../index.php" class="text-white bg-red-700 w-10 h-10 rounded-xl flex items-center justify-center hover:bg-red-800 transition">
                <i class="fas fa-home"></i>
            </a>
            <img src="../logo.png" class="h-10 w-auto bg-white rounded-lg p-1" onerror="this.src='https://ui-avatars.com/api/?name=SA&background=ffffff&color=dc2626'">
            <h1 class="text-white font-black text-xl italic uppercase tracking-tighter">Admin Panel</h1>
        </div>

        <div class="flex items-center gap-4">
            <span class="text-white font-bold text-xs uppercase hidden sm:block">Halo, <?php echo $username; ?></span>
            <a href="../logout.php" class="text-white hover:text-yellow-300 text-2xl transition">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </nav>

    <div class="max-w-5xl mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-black text-slate-800 uppercase italic">Panel Kendali Utama</h2>
            <p class="text-slate-500 font-medium uppercase text-[10px] tracking-[0.3em] mt-2">Pilih menu untuk mengelola sekolah musik</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <a href="honor.php" class="group bg-white p-8 rounded-[2.5rem] shadow-xl border-b-[10px] border-red-600 hover:-translate-y-2 transition-all duration-300">
                <div class="w-14 h-14 bg-red-600 text-white rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-lg group-hover:rotate-12 transition-transform">
                    <i class="fas fa-wallet"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 uppercase italic leading-none">Keuangan</h3>
                <p class="text-slate-400 text-[10px] mt-2 font-bold uppercase tracking-widest">Honor & Kas</p>
            </a>

            <a href="jadwal.php" class="group bg-white p-8 rounded-[2.5rem] shadow-xl border-b-[10px] border-yellow-400 hover:-translate-y-2 transition-all duration-300">
                <div class="w-14 h-14 bg-yellow-400 text-red-700 rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-lg group-hover:rotate-12 transition-transform">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 uppercase italic leading-none">Jadwal</h3>
                <p class="text-slate-400 text-[10px] mt-2 font-bold uppercase tracking-widest">Atur Pertemuan</p>
            </a>

            <a href="users.php" class="group bg-white p-8 rounded-[2.5rem] shadow-xl border-b-[10px] border-slate-800 hover:-translate-y-2 transition-all duration-300">
                <div class="w-14 h-14 bg-slate-800 text-white rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-lg group-hover:rotate-12 transition-transform">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 uppercase italic leading-none">Pengguna</h3>
                <p class="text-slate-400 text-[10px] mt-2 font-bold uppercase tracking-widest">Guru & Murid</p>
            </a>

        </div>

        <div class="mt-12 bg-white rounded-[2rem] p-8 shadow-inner border border-slate-100 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-red-50 text-red-600 rounded-full flex items-center justify-center text-xl">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div>
                    <h4 class="font-black text-slate-800 uppercase text-xs">Pusat Bantuan Sistem</h4>
                    <p class="text-slate-400 text-[10px] font-bold">Pastikan koneksi internet stabil saat input data.</p>
                </div>
            </div>
            <a href="https://wa.me/62895360796038" target="_blank" class="bg-slate-900 text-white px-6 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-red-600 transition">
                Kontak Support
            </a>
        </div>

        <div class="mt-10 text-center">
            <p class="text-slate-300 text-[9px] font-black uppercase tracking-[0.5em] italic">&copy; Smart Arca Music School System</p>
        </div>
    </div>

</body>
</html>
