<?php
require_once('koneksi.php');

// 1. STATUS LOGIN
$role = $_COOKIE['user_role'] ?? null;
$is_login = ($role !== null);

// 2. PROSES LOGIN
$error = '';
if (isset($_POST['login'])) {
    $u = mysqli_real_escape_string($conn, $_POST['username']);
    $p = $_POST['password'];
    $q = mysqli_query($conn, "SELECT * FROM users WHERE username = '$u' AND password = '$p' LIMIT 1");
    if (mysqli_num_rows($q) > 0) {
        $user = mysqli_fetch_assoc($q);
        setcookie('user_role', $user['role'], time() + (86400 * 30), "/");
        header("Location: " . $user['role'] . "/index.php");
        exit();
    } else { $error = "Username atau Password salah!"; }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Arca Music School - Weleri</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; scroll-behavior: smooth; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-white" x-data="{ 
    openModal: null, 
    silabus: {
        'Drum': 'Fokus: Pengenalan drum set, matched grip, koordinasi tangan-kaki, membaca notasi ritme (quarter & eighth notes), hingga basic rock beat.',
        'Keyboard': 'Fokus: Penjarian (fingering), notasi balok (Kunci G & F), akord mayor dasar (C, G, F), dan memainkan melodi lagu sederhana.',
        'Gitar Akustik': 'Fokus: Akord dasar (G, C, D, Em, Am, E), pola strumming 4/4 dasar, perpindahan akord bersih, dan pengenalan nada senar terbuka.',
        'Gitar Elektrik': 'Fokus: Pengenalan Amp & Jack, teknik power chords, palm muting, cara membaca TAB, dan teknik memetik down-up stroke.',
        'Bass Elektrik': 'Fokus: Postur & plucking jari, pengenalan fretboard senar E & A, skala mayor dasar, dan menjaga tempo (groove) dengan metronom.',
        'Vokal': 'Fokus: Pernapasan diafragma, postur bernyanyi yang benar, pitch control (intonasi), resonansi dasar, dan ekspresi lagu.'
    }
}">

    <nav class="bg-red-700 text-white py-4 px-6 shadow-xl border-b-4 border-yellow-400 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="bg-yellow-400 text-red-700 w-10 h-10 rounded-lg flex items-center justify-center text-xl shadow-md">
                    <i class="fas fa-music"></i>
                </div>
                <div>
                    <h1 class="font-black italic uppercase text-lg leading-none tracking-tighter">Smart Arca</h1>
                    <p class="text-[10px] font-bold text-red-200 uppercase tracking-widest leading-none">Weleri - Kendal</p>
                </div>
            </div>
            <div class="flex gap-4">
                <?php if($is_login): ?>
                    <a href="<?php echo $role; ?>/index.php" class="bg-yellow-400 text-red-800 px-6 py-2 rounded-full font-black text-xs uppercase shadow-lg transition hover:scale-105">Dashboard</a>
                <?php else: ?>
                    <a href="#login-section" class="bg-yellow-400 text-red-800 px-8 py-3 rounded-full font-black text-xs uppercase shadow-lg transition hover:scale-105">Login Portal</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <header class="py-24 px-6 bg-gradient-to-b from-red-50 to-white text-center">
        <div class="max-w-4xl mx-auto">
            <span class="inline-block bg-red-100 text-red-600 px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-[0.2em] mb-6">Weleri Kendal Music School</span>
            <h1 class="text-5xl md:text-7xl font-black text-slate-900 italic uppercase tracking-tighter mb-8 leading-tight">Wujudkan <span class="text-red-600">Mimpimu</span><br>Lewat <span class="text-yellow-500">Nada & Irama</span></h1>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-left max-w-2xl mx-auto mt-12">
                <div class="bg-white p-6 rounded-[2.5rem] shadow-xl border-l-8 border-red-600">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 leading-none">Lokasi Sekolah:</p>
                    <p class="text-xs font-bold text-slate-700 leading-relaxed uppercase italic">Jl. Tamtama, Sekepel, Penyangkringan, Weleri, Kendal</p>
                </div>
                <div class="bg-white p-6 rounded-[2.5rem] shadow-xl border-l-8 border-yellow-400">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 leading-none">Kontak Admin (Mbak Fia):</p>
                    <a href="https://wa.me/62895360796038" target="_blank" class="text-red-700 font-black italic flex items-center gap-2 text-sm uppercase hover:text-red-800 transition">
                        <i class="fab fa-whatsapp text-green-500 text-xl"></i> +62 895-3607-96038
                    </a>
                </div>
            </div>
        </div>
    </header>

    <section class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-6">
            <h2 class="text-3xl font-black text-center text-slate-800 uppercase italic tracking-tighter mb-12">Program Kursus & Silabus</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-8">
                <template x-for="(desc, title) in silabus">
                    <button @click="openModal = title" class="bg-white border-2 border-slate-100 p-10 rounded-[3rem] shadow-xl hover:border-red-600 transition group text-center relative overflow-hidden">
                        <div class="w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center text-red-600 text-2xl mb-4 mx-auto group-hover:bg-red-600 group-hover:text-white transition shadow-inner">
                            <i class="fas fa-play"></i>
                        </div>
                        <h4 class="text-sm font-black uppercase italic text-slate-800" x-text="'Kelas ' + title"></h4>
                        <p class="text-[9px] font-bold text-slate-400 uppercase mt-2">Klik Untuk Silabus <i class="fas fa-chevron-right ml-1"></i></p>
                    </button>
                </template>
            </div>
        </div>
    </section>

    <template x-if="openModal">
        <div class="fixed inset-0 z-[200] flex items-center justify-center p-6 bg-black/80 backdrop-blur-sm" @click.self="openModal = null" x-transition>
            <div class="bg-white w-full max-w-md rounded-[3rem] shadow-2xl overflow-hidden border-b-8 border-yellow-400">
                <div class="bg-red-700 p-6 text-white flex justify-between items-center border-b-4 border-yellow-400">
                    <h3 class="font-black italic uppercase text-sm tracking-tighter" x-text="'Silabus Kelas ' + openModal"></h3>
                    <button @click="openModal = null" class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition"><i class="fas fa-times"></i></button>
                </div>
                <div class="p-10 bg-slate-50 text-slate-600 font-bold italic text-sm leading-relaxed" x-text="silabus[openModal]"></div>
                <div class="p-6 bg-white text-center">
                    <a href="https://wa.me/62895360796038" target="_blank" class="bg-red-700 text-white px-10 py-4 rounded-2xl font-black uppercase text-[10px] shadow-lg italic tracking-widest block transition active:scale-95">Daftar Kelas Ini</a>
                </div>
            </div>
        </div>
    </template>

    <?php if(!$is_login): ?>
    <section id="login-section" class="py-24 bg-slate-50">
        <div class="max-w-md mx-auto px-6">
            <div class="bg-white p-12 rounded-[3.5rem] shadow-2xl border-b-8 border-yellow-400 text-center">
                <div class="w-20 h-20 bg-red-700 text-white rounded-[2rem] flex items-center justify-center text-3xl mx-auto mb-6 shadow-xl border-4 border-yellow-400">
                    <i class="fas fa-lock"></i>
                </div>
                <h2 class="text-3xl font-black text-slate-800 uppercase italic mb-8 tracking-tighter leading-none">Login Portal</h2>
                <form method="POST" class="space-y-5">
                    <input type="text" name="username" placeholder="USERNAME" class="w-full p-5 rounded-2xl bg-slate-50 border-2 border-slate-50 outline-none focus:border-red-600 font-bold italic text-center transition uppercase tracking-widest" required>
                    <input type="password" name="password" placeholder="PASSWORD" class="w-full p-5 rounded-2xl bg-slate-50 border-2 border-slate-50 outline-none focus:border-red-600 font-bold text-center transition uppercase tracking-widest" required>
                    <button type="submit" name="login" class="w-full bg-red-700 text-white font-black py-5 rounded-2xl uppercase italic shadow-xl transition active:scale-95 hover:bg-red-800">Masuk Dashboard</button>
                </form>
                <?php if($error): ?><p class="mt-4 text-[10px] font-bold text-red-600 uppercase italic tracking-widest"><?php echo $error; ?></p><?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <footer class="bg-slate-900 text-white py-12 px-6 border-t-8 border-yellow-400">
        <div class="max-w-6xl mx-auto text-center">
            <h3 class="text-2xl font-black italic uppercase tracking-tighter text-yellow-400 mb-2">Smart Arca Music School</h3>
            <p class="text-[10px] text-slate-500 uppercase font-bold tracking-[0.3em] mb-10 italic">Weleri • Kendal • Jawa Tengah</p>
            <div class="h-px bg-slate-800 max-w-xs mx-auto mb-8"></div>
            <p class="text-[9px] font-black uppercase tracking-widest text-slate-600">&copy; 2026 Smart Arca Music School. Professional Music Education.</p>
        </div>
    </footer>

</body>
</html>
