<?php
session_start();

// Jika sudah login, arahkan otomatis ke dashboard masing-masing
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') { header("Location: admin/index.php"); exit(); }
    if ($_SESSION['role'] == 'guru') { header("Location: guru/index.php"); exit(); }
    if ($_SESSION['role'] == 'murid') { header("Location: murid/index.php"); exit(); }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Arca Music School</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; }
        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-900 min-h-screen flex items-center justify-center p-6">

    <div class="max-w-4xl w-full grid md:grid-cols-2 gap-8 items-center">
        
        <div class="text-white space-y-6">
            <div class="w-20 h-20 bg-white text-blue-600 rounded-2xl flex items-center justify-center text-3xl font-bold shadow-2xl transform -rotate-12">
                SA
            </div>
            <div>
                <h1 class="text-4xl md:text-5xl font-bold leading-tight">Smart Arca <br><span class="text-blue-300">Music School</span></h1>
                <p class="mt-4 text-blue-100 text-lg">Sistem Informasi Akademik & Manajemen Keuangan Kursus Musik Profesional.</p>
            </div>
            <div class="flex gap-4 text-sm text-blue-200">
                <span class="flex items-center gap-2"><i class="fas fa-check-circle"></i> Monitoring Absensi</span>
                <span class="flex items-center gap-2"><i class="fas fa-check-circle"></i> Kuitansi Digital</span>
            </div>
        </div>

        <div class="glass-effect p-8 rounded-3xl shadow-2xl space-y-6">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-800">Selamat Datang</h2>
                <p class="text-gray-500 text-sm">Pilih akses masuk sesuai peran Anda</p>
            </div>

            <div class="grid gap-4">
                <a href="admin/login.php" class="group flex items-center gap-4 p-4 rounded-2xl border-2 border-transparent bg-blue-50 hover:bg-blue-600 hover:text-white transition-all duration-300 shadow-sm hover:shadow-lg">
                    <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center group-hover:bg-white group-hover:text-blue-600 transition-colors">
                        <i class="fas fa-user-graduate text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg">Portal Siswa</h3>
                        <p class="text-xs opacity-70 group-hover:text-blue-100 transition-colors">Cek jadwal & cetak kuitansi les</p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 group-hover:text-white"></i>
                </a>

                <a href="admin/login.php" class="group flex items-center gap-4 p-4 rounded-2xl border-2 border-transparent bg-indigo-50 hover:bg-indigo-600 hover:text-white transition-all duration-300 shadow-sm hover:shadow-lg">
                    <div class="w-12 h-12 rounded-xl bg-indigo-600 text-white flex items-center justify-center group-hover:bg-white group-hover:text-indigo-600 transition-colors">
                        <i class="fas fa-chalkboard-teacher text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg">Portal Guru</h3>
                        <p class="text-xs opacity-70 group-hover:text-indigo-100 transition-colors">Lapor absensi & cek honor</p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 group-hover:text-white"></i>
                </a>

                <a href="admin/login.php" class="group flex items-center gap-4 p-4 rounded-2xl border-2 border-transparent bg-gray-50 hover:bg-gray-800 hover:text-white transition-all duration-300 shadow-sm hover:shadow-lg">
                    <div class="w-12 h-12 rounded-xl bg-gray-800 text-white flex items-center justify-center group-hover:bg-white group-hover:text-gray-800 transition-colors">
                        <i class="fas fa-user-shield text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg">Administrator</h3>
                        <p class="text-xs opacity-70 group-hover:text-gray-300 transition-colors">Kelola jadwal, user, & keuangan</p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 group-hover:text-white"></i>
                </a>
            </div>

            <div class="pt-4 border-t border-gray-100 text-center">
                <p class="text-xs text-gray-400 uppercase tracking-widest">&copy; 2026 Smart Arca Music School</p>
            </div>
        </div>

    </div>

</body>
</html>
