<?php
// 1. PROTEKSI HALAMAN
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'admin') {
    header("Location: ../index.php"); 
    exit();
}

// 2. KONEKSI (Naik satu tingkat ke folder api)
require_once(__DIR__ . '/../koneksi.php');

$admin_name = $_COOKIE['user_username'] ?? 'Administrator';

// 3. AMBIL DATA RINGKASAN (Contoh)
$q_murid = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'murid'");
$res_murid = mysqli_fetch_assoc($q_murid);

$q_guru = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'guru'");
$res_guru = mysqli_fetch_assoc($q_guru);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">

    <nav class="bg-red-800 text-white py-4 px-6 shadow-xl border-b-4 border-yellow-400 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <i class="fas fa-user-shield text-yellow-400 text-xl"></i>
                <h1 class="font-black italic uppercase tracking-tighter">Admin Panel</h1>
            </div>
            <a href="../logout.php" class="bg-white text-red-700 px-4 py-2 rounded-xl font-black text-xs uppercase shadow-lg">Keluar</a>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto p-8">
        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl mb-10 border-l-[12px] border-red-600 flex justify-between items-center">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 leading-none">Manajemen Sekolah,</p>
                <h2 class="text-4xl font-black text-slate-800 italic uppercase tracking-tighter leading-none"><?php echo $admin_name; ?></h2>
            </div>
            <div class="bg-red-50 p-4 rounded-2xl text-red-600">
                <i class="fas fa-calendar-alt mr-2"></i> <span class="font-bold uppercase text-xs"><?php echo date('d M Y'); ?></span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border-b-8 border-yellow-400">
                <p class="text-[10px] font-black text-slate-400 uppercase mb-2">Total Murid Aktif</p>
                <h3 class="text-5xl font-black text-red-700 italic"><?php echo $res_murid['total']; ?></h3>
                <p class="text-[10px] font-bold text-slate-400 mt-2 italic tracking-widest">Siswa Terdaftar</p>
            </div>
            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border-b-8 border-red-700">
                <p class="text-[10px] font-black text-slate-400 uppercase mb-2">Total Guru Pengajar</p>
                <h3 class="text-5xl font-black text-slate-800 italic"><?php echo $res_guru['total']; ?></h3>
                <p class="text-[10px] font-bold text-slate-400 mt-2 italic tracking-widest">Staf Pengajar</p>
            </div>
            <div class="bg-red-700 p-8 rounded-[2.5rem] shadow-xl text-white">
                <p class="text-[10px] font-black text-red-200 uppercase mb-2 tracking-widest">Quick Action</p>
                <button class="w-full bg-yellow-400 text-red-800 font-black py-3 rounded-xl uppercase text-xs mb-3 shadow-lg">Tambah Jadwal</button>
                <button class="w-full bg-white/10 text-white font-black py-3 rounded-xl uppercase text-xs border border-white/20">Laporan Bulanan</button>
            </div>
        </div>

        <div class="mt-12 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <a href="#" class="bg-white p-6 rounded-3xl shadow-md border hover:border-red-500 transition">
                <i class="fas fa-users text-red-600 text-2xl mb-3"></i>
                <p class="text-[10px] font-black uppercase tracking-widest">Data Murid</p>
            </a>
            <a href="#" class="bg-white p-6 rounded-3xl shadow-md border hover:border-red-500 transition">
                <i class="fas fa-chalkboard-teacher text-red-600 text-2xl mb-3"></i>
                <p class="text-[10px] font-black uppercase tracking-widest">Data Guru</p>
            </a>
            <a href="#" class="bg-white p-6 rounded-3xl shadow-md border hover:border-red-500 transition">
                <i class="fas fa-calendar-check text-red-600 text-2xl mb-3"></i>
                <p class="text-[10px] font-black uppercase tracking-widest">Kelola Jadwal</p>
            </a>
            <a href="#" class="bg-white p-6 rounded-3xl shadow-md border hover:border-red-500 transition">
                <i class="fas fa-money-bill-wave text-red-600 text-2xl mb-3"></i>
                <p class="text-[10px] font-black uppercase tracking-widest">Keuangan</p>
            </a>
        </div>
    </div>
</body>
</html>
