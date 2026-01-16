<?php
// 1. PROTEKSI HALAMAN (Hanya Admin)
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'admin') {
    header("Location: ../index.php"); 
    exit();
}

// 2. KONEKSI (Naik satu tingkat ke folder api)
require_once(__DIR__ . '/../koneksi.php');

$admin_name = $_COOKIE['user_username'] ?? 'Administrator';

// 3. AMBIL DATA RINGKASAN UNTUK DASHBOARD
$q_murid = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'murid'");
$res_murid = mysqli_fetch_assoc($q_murid);

$q_guru = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'guru'");
$res_guru = mysqli_fetch_assoc($q_guru);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen pb-20">

    <nav class="bg-red-800 text-white py-4 px-6 shadow-xl border-b-4 border-yellow-400 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <i class="fas fa-user-shield text-yellow-400 text-xl"></i>
                <h1 class="font-black italic uppercase tracking-tighter">Admin Control Panel</h1>
            </div>
            <a href="../logout.php" class="bg-white hover:bg-slate-100 text-red-700 px-4 py-2 rounded-xl font-black text-xs uppercase shadow-lg transition">
                <i class="fas fa-sign-out-alt mr-1"></i> Keluar
            </a>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto p-8">
        
        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl mb-10 border-l-[12px] border-red-600 flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Selamat Datang,</p>
                <h2 class="text-4xl font-black text-slate-800 italic uppercase tracking-tighter leading-none"><?php echo $admin_name; ?></h2>
                <p class="text-red-600 font-bold text-[10px] uppercase mt-2 tracking-widest"><i class="fas fa-circle text-[8px] animate-pulse"></i> Sistem Manajemen Smart Arca</p>
            </div>
            <div class="text-center md:text-right bg-slate-50 p-5 rounded-3xl border-2 border-dashed border-slate-200">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 leading-none">Status Server</p>
                <p class="text-sm font-black text-green-600 uppercase italic">Online & Terkoneksi</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border-b-8 border-yellow-400 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase mb-1">Murid Aktif</p>
                    <h3 class="text-5xl font-black text-red-700 italic"><?php echo $res_murid['total']; ?></h3>
                </div>
                <i class="fas fa-user-graduate text-slate-100 text-5xl"></i>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border-b-8 border-red-700 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase mb-1">Guru Pengajar</p>
                    <h3 class="text-5xl font-black text-slate-800 italic"><?php echo $res_guru['total']; ?></h3>
                </div>
                <i class="fas fa-chalkboard-teacher text-slate-100 text-5xl"></i>
            </div>

            <div class="bg-red-700 p-8 rounded-[2.5rem] shadow-xl text-white flex flex-col justify-center">
                <p class="text-[10px] font-black text-red-200 uppercase mb-4 tracking-widest text-center">Tindakan Cepat</p>
                <a href="tambah_jadwal.php" class="w-full bg-yellow-400 hover:bg-yellow-300 text-red-800 font-black py-4 rounded-2xl uppercase text-xs shadow-lg transition text-center flex items-center justify-center gap-2">
                    <i class="fas fa-plus-circle"></i> Tambah Jadwal Baru
                </a>
            </div>
        </div>

        <h2 class="text-xl font-black text-slate-800 uppercase italic mb-8 border-b pb-4">
            <i class="fas fa-th-large mr-2 text-red-600"></i> Menu Navigasi Utama
        </h2>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <a href="users.php" class="bg-white p-8 rounded-[2.5rem] shadow-md border-2 border-transparent hover:border-red-600 hover:-translate-y-2 transition duration-300 text-center group">
                <div class="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center text-red-600 text-2xl mb-4 mx-auto group-hover:bg-red-600 group-hover:text-white transition">
                    <i class="fas fa-users"></i>
                </div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 group-hover:text-red-600">Manajemen</p>
                <h4 class="text-sm font-black uppercase italic text-slate-800">Data Murid</h4>
            </a>

            <a href="users.php" class="bg-white p-8 rounded-[2.5rem] shadow-md border-2 border-transparent hover:border-red-600 hover:-translate-y-2 transition duration-300 text-center group">
                <div class="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center text-red-600 text-2xl mb-4 mx-auto group-hover:bg-red-600 group-hover:text-white transition">
                    <i class="fas fa-user-tie"></i>
                </div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 group-hover:text-red-600">Manajemen</p>
                <h4 class="text-sm font-black uppercase italic text-slate-800">Data Guru</h4>
            </a>

            <a href="jadwal.php" class="bg-white p-8 rounded-[2.5rem] shadow-md border-2 border-transparent hover:border-yellow-400 hover:-translate-y-2 transition duration-300 text-center group">
                <div class="w-16 h-16 bg-yellow-100 rounded-2xl flex items-center justify-center text-yellow-600 text-2xl mb-4 mx-auto group-hover:bg-yellow-400 group-hover:text-white transition">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 group-hover:text-yellow-600">Penjadwalan</p>
                <h4 class="text-sm font-black uppercase italic text-slate-800">Kelola Jadwal</h4>
            </a>

            <a href="honor.php" class="bg-white p-8 rounded-[2.5rem] shadow-md border-2 border-transparent hover:border-red-600 hover:-translate-y-2 transition duration-300 text-center group">
                <div class="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center text-red-600 text-2xl mb-4 mx-auto group-hover:bg-red-600 group-hover:text-white transition">
                    <i class="fas fa-wallet"></i>
                </div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 group-hover:text-red-600">Administrasi</p>
                <h4 class="text-sm font-black uppercase italic text-slate-800">Keuangan / Honor</h4>
            </a>
        </div>

    </div>

    <footer class="text-center mt-10">
        <p class="text-[9px] font-black text-slate-300 uppercase tracking-[0.3em]">&copy; 2026 Smart Arca Music School - System Administrator</p>
    </footer>

</body>
</html>
