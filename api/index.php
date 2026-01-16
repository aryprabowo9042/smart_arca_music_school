<?php
// LOGIKA: Cek apakah user sudah login atau belum
$role = $_COOKIE['user_role'] ?? null;
$is_login = ($role !== null);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Arca Music School</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-white">

    <nav class="bg-red-700 text-white py-4 px-6 shadow-xl border-b-4 border-yellow-400 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            
            <div class="flex items-center gap-3">
                <div class="bg-yellow-400 text-red-700 w-10 h-10 rounded-lg flex items-center justify-center text-xl shadow-md">
                    <i class="fas fa-music"></i>
                </div>
                <div>
                    <h1 class="font-black italic uppercase text-lg leading-none tracking-tighter">Smart Arca</h1>
                    <p class="text-[10px] font-bold text-red-200 uppercase tracking-widest">Music School</p>
                </div>
            </div>

            <div class="flex gap-4">
                <?php if($is_login): ?>
                    <a href="api/<?php echo $role; ?>/index.php" class="bg-yellow-400 hover:bg-yellow-300 text-red-800 px-6 py-2 rounded-full font-black text-xs uppercase shadow-lg transition transform active:scale-95 flex items-center gap-2">
                        <i class="fas fa-tachometer-alt"></i> Dashboard <?php echo ucfirst($role); ?>
                    </a>
                    <a href="api/logout.php" class="bg-white hover:bg-slate-100 text-red-600 px-4 py-2 rounded-full font-black text-xs uppercase shadow-lg transition transform active:scale-95">
                        Keluar
                    </a>
                <?php else: ?>
                    <a href="api/index.php" class="bg-yellow-400 hover:bg-yellow-300 text-red-800 px-8 py-3 rounded-full font-black text-xs uppercase shadow-lg transition transform active:scale-95 tracking-widest">
                        Login Siswa / Guru <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <header class="py-20 px-6 bg-gradient-to-b from-red-50 to-white">
        <div class="max-w-4xl mx-auto text-center">
            <span class="inline-block bg-red-100 text-red-600 px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-[0.2em] mb-6">
                Professional Music Education
            </span>
            
            <h1 class="text-5xl md:text-7xl font-black text-slate-900 italic uppercase tracking-tighter mb-6 leading-tight">
                Wujudkan <span class="text-red-600">Mimpimu</span><br>
                Lewat <span class="text-yellow-500 text-stroke-red">Nada & Irama</span>
            </h1>
            
            <p class="text-slate-500 font-bold text-sm md:text-base max-w-xl mx-auto mb-10 leading-relaxed">
                Bergabunglah bersama Smart Arca Music School. Kami menyediakan kurikulum terbaik dengan pengajar profesional untuk mengasah bakat musikmu.
            </p>

            <div class="flex justify-center gap-4">
                <a href="api/index.php" class="bg-red-600 text-white px-8 py-4 rounded-2xl font-black uppercase shadow-red-500/30 shadow-2xl hover:bg-red-700 transition transform hover:-translate-y-1">
                    Daftar Sekarang
                </a>
                <a href="#lokasi" class="bg-white text-slate-700 border-2 border-slate-200 px-8 py-4 rounded-2xl font-black uppercase hover:bg-slate-50 transition">
                    Info Lokasi
                </a>
            </div>
        </div>
    </header>

    <section class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-6">
            <h2 class="text-center text-3xl font-black text-slate-800 uppercase italic tracking-tighter mb-12">Program Unggulan</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white border-2 border-slate-100 p-8 rounded-[2rem] shadow-xl hover:border-red-500 transition duration-300 group">
                    <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center text-red-600 text-2xl mb-6 group-hover:bg-red-600 group-hover:text-white transition">
                        <i class="fas fa-guitar"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 uppercase italic mb-2">Gitar & Bass</h3>
                    <p class="text-xs text-slate-400 font-bold leading-relaxed">Kelas intensif untuk pemula hingga mahir dengan teknik modern.</p>
                </div>

                <div class="bg-white border-2 border-slate-100 p-8 rounded-[2rem] shadow-xl hover:border-yellow-400 transition duration-300 group">
                    <div class="w-14 h-14 bg-yellow-100 rounded-2xl flex items-center justify-center text-yellow-600 text-2xl mb-6 group-hover:bg-yellow-400 group-hover:text-white transition">
                        <i class="fas fa-drum"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 uppercase italic mb-2">Drum & Perkusi</h3>
                    <p class="text-xs text-slate-400 font-bold leading-relaxed">Pelajari ritme dan ketukan yang solid bersama instruktur ahli.</p>
                </div>

                <div class="bg-white border-2 border-slate-100 p-8 rounded-[2rem] shadow-xl hover:border-red-500 transition duration-300 group">
                    <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center text-red-600 text-2xl mb-6 group-hover:bg-red-600 group-hover:text-white transition">
                        <i class="fas fa-microphone"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 uppercase italic mb-2">Vokal & Piano</h3>
                    <p class="text-xs text-slate-400 font-bold leading-relaxed">Kembangkan karakter suaramu dan kemampuan bermusik.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-slate-900 text-white py-10 text-center border-t-8 border-yellow-400">
        <p class="font-black italic uppercase tracking-widest text-lg">Smart Arca Music School</p>
        <p class="text-slate-500 text-[10px] font-bold mt-2 uppercase">Copyright &copy; 2026 - All Rights Reserved</p>
    </footer>

</body>
</html>
