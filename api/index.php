<?php
// Cek status login dari cookie
$role = $_COOKIE['user_role'] ?? null;
$is_login = ($role !== null);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Arca Music School - Wujudkan Bakat Musikmu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; scroll-behavior: smooth; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-white text-slate-800">

    <nav class="fixed w-full z-50 glass border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="bg-indigo-600 text-white p-2 rounded-lg shadow-lg">
                    <i class="fas fa-music"></i>
                </div>
                <span class="text-2xl font-black italic uppercase tracking-tighter text-indigo-900">Smart Arca</span>
            </div>

            <div class="hidden md:flex gap-8 font-bold text-sm uppercase tracking-widest text-slate-500">
                <a href="#home" class="hover:text-indigo-600 transition">Home</a>
                <a href="#program" class="hover:text-indigo-600 transition">Programs</a>
                <a href="#about" class="hover:text-indigo-600 transition">About</a>
            </div>

            <div class="flex gap-4">
                <?php if($is_login): ?>
                    <div class="flex items-center gap-3 bg-slate-50 p-1 pr-4 rounded-full border border-slate-200">
                        <a href="api/<?php echo $role; ?>/index.php" class="bg-indigo-600 text-white px-5 py-2 rounded-full font-black text-[10px] uppercase shadow-md hover:bg-indigo-700 transition">
                            Dashboard
                        </a>
                        <a href="api/logout.php" class="text-red-500 hover:text-red-700 transition" title="Keluar">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <a href="api/index.php" class="bg-indigo-600 text-white px-8 py-3 rounded-full font-black text-[10px] uppercase shadow-xl hover:bg-indigo-700 transition tracking-widest">
                        Student Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <section id="home" class="pt-32 pb-20 px-6">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-8 text-center lg:text-left">
                <span class="bg-yellow-100 text-yellow-700 px-4 py-2 rounded-full text-xs font-black uppercase tracking-widest italic">Best Music School in Semarang</span>
                <h1 class="text-5xl md:text-7xl font-black text-slate-900 italic uppercase tracking-tighter leading-[0.9]">
                    Wujudkan <br> <span class="text-indigo-600">Bakat Musikmu</span> <br> Bersama Kami
                </h1>
                <p class="text-slate-400 font-bold max-w-lg mx-auto lg:mx-0 leading-relaxed uppercase text-xs tracking-widest">
                    Belajar musik lebih menyenangkan dengan kurikulum modern, fasilitas lengkap, dan pengajar profesional yang siap membimbingmu.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="#program" class="bg-indigo-600 text-white px-10 py-4 rounded-2xl font-black uppercase italic shadow-2xl hover:bg-indigo-700 transition">Lihat Program</a>
                    <a href="https://wa.me/your-number" class="border-2 border-slate-200 px-10 py-4 rounded-2xl font-black uppercase italic hover:bg-slate-50 transition">Konsultasi Gratis</a>
                </div>
            </div>
            <div class="relative group">
                <div class="absolute -inset-4 bg-indigo-100 rounded-[3rem] rotate-3 group-hover:rotate-1 transition duration-500"></div>
                <img src="https://images.unsplash.com/photo-1511379938547-c1f69419868d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Music Education" class="relative rounded-[3rem] shadow-2xl border-4 border-white object-cover aspect-square">
            </div>
        </div>
    </section>

    <section id="program" class="py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.3em] mb-4 leading-none italic">Our Programs</p>
            <h2 class="text-4xl font-black text-slate-800 uppercase italic tracking-tighter mb-16">Program Kursus Unggulan</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-10 rounded-[2.5rem] shadow-xl hover:-translate-y-4 transition duration-500 border-b-8 border-indigo-600">
                    <div class="bg-indigo-100 w-16 h-16 rounded-2xl flex items-center justify-center text-indigo-600 text-2xl mb-8 mx-auto shadow-inner">
                        <i class="fas fa-guitar"></i>
                    </div>
                    <h3 class="text-2xl font-black text-slate-800 uppercase italic mb-4">Gitar & Bass</h3>
                    <p class="text-slate-400 text-xs font-bold leading-relaxed mb-6 uppercase tracking-wider">Teknik dasar hingga improvisasi tingkat lanjut untuk semua genre musik.</p>
                </div>

                <div class="bg-white p-10 rounded-[2.5rem] shadow-xl hover:-translate-y-4 transition duration-500 border-b-8 border-yellow-400">
                    <div class="bg-yellow-100 w-16 h-16 rounded-2xl flex items-center justify-center text-yellow-600 text-2xl mb-8 mx-auto shadow-inner">
                        <i class="fas fa-keyboard"></i>
                    </div>
                    <h3 class="text-2xl font-black text-slate-800 uppercase italic mb-4">Piano & Keyboard</h3>
                    <p class="text-slate-400 text-xs font-bold leading-relaxed mb-6 uppercase tracking-wider">Metode pembelajaran klasik dan pop yang mudah dipahami oleh segala usia.</p>
                </div>

                <div class="bg-white p-10 rounded-[2.5rem] shadow-xl hover:-translate-y-4 transition duration-500 border-b-8 border-indigo-600">
                    <div class="bg-indigo-100 w-16 h-16 rounded-2xl flex items-center justify-center text-indigo-600 text-2xl mb-8 mx-auto shadow-inner">
                        <i class="fas fa-microphone-alt"></i>
                    </div>
                    <h3 class="text-2xl font-black text-slate-800 uppercase italic mb-4">Vokal / Singing</h3>
                    <p class="text-slate-400 text-xs font-bold leading-relaxed mb-6 uppercase tracking-wider">Olah vokal profesional untuk melatih teknik pernapasan dan karakter suaramu.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-indigo-900 py-16 text-white text-center">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-3xl font-black italic uppercase tracking-tighter mb-4">Smart Arca Music School</h2>
            <p class="text-indigo-300 font-bold text-xs uppercase tracking-[0.3em] mb-10 leading-none">Music Education for Everyone</p>
            <div class="flex justify-center gap-6 mb-12">
                <a href="#" class="w-12 h-12 rounded-full border border-indigo-700 flex items-center justify-center hover:bg-white hover:text-indigo-900 transition"><i class="fab fa-instagram"></i></a>
                <a href="#" class="w-12 h-12 rounded-full border border-indigo-700 flex items-center justify-center hover:bg-white hover:text-indigo-900 transition"><i class="fab fa-youtube"></i></a>
                <a href="#" class="w-12 h-12 rounded-full border border-indigo-700 flex items-center justify-center hover:bg-white hover:text-indigo-900 transition"><i class="fab fa-facebook-f"></i></a>
            </div>
            <p class="text-indigo-400 font-bold text-[10px] uppercase tracking-widest">&copy; 2026 Smart Arca. All Rights Reserved.</p>
        </div>
    </footer>

</body>
</html>
