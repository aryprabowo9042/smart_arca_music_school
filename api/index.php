<?php
require_once('koneksi.php');
$role = $_COOKIE['user_role'] ?? null;
$is_login = ($role !== null);

// Logika Login Sederhana
if (isset($_POST['login'])) {
    $u = mysqli_real_escape_string($conn, $_POST['username']);
    $p = $_POST['password'];
    $q = mysqli_query($conn, "SELECT * FROM users WHERE username = '$u' AND password = '$p' LIMIT 1");
    if (mysqli_num_rows($q) > 0) {
        $user = mysqli_fetch_assoc($q);
        setcookie('user_role', $user['role'], time() + (86400 * 30), "/");
        header("Location: " . $user['role'] . "/index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Arca Music School - Weleri</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        arcaRed: '#d31f26',
                        arcaYellow: '#fdb813',
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800;900&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        /* Latar belakang pola musik halus untuk seluruh halaman */
        body {
            background-color: #ffffff;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M54.627 0l.83.828-1.415 1.415-.83-.828 1.415-1.415zm4.95 1.414l.828.83-1.414 1.415-.828-.83 1.414-1.415zm-4.243 4.243l.828.83-1.415 1.415-.828-.83 1.415-1.415zm19.8 0l.828.83-1.414 1.415-.828-.83 1.414-1.415zm-8.485 8.485l.828.83-1.415 1.415-.828-.83 1.415-1.415zm19.8 0l.83.83-1.415 1.414-.83-.83 1.415-1.414zm-16.97 16.97l.828.83-1.414 1.415-.828-.83 1.414-1.415zm33.94 0l.83.83-1.415 1.415-.83-.83 1.415-1.415zM28 28c0-11.046 8.954-20 20-20s20 8.954 20 20-8.954 20-20 20-20-8.954-20-20zm-8 0c0-6.627 5.373-12 12-12s12 5.373 12 12-5.373 12-12 12-12-5.373-12-12zm-8 0c0-2.21 1.79-4 4-4s4 1.79 4 4-1.79 4-4 4-4-1.79-4-4zm-8 0c0 1.105-.895 2-2 2s-2-.895-2-2 .895-2 2-2 2 .895 2 2zm-8 0c0-13.255 10.745-24 24-24s24 10.745 24 24-10.745 24-24 24-24-10.745-24-24zm-8 0c0-8.837 7.163-16 16-16s16 7.163 16 16-7.163 16-16 16-16-7.163-16-16zm-8 0c0-4.418 3.582-8 8-8s8 3.582 8 8-3.582 8-8 8-8-3.582-8-8zm-8 0c0-2.21 1.79-4 4-4s4 1.79 4 4-1.79 4-4 4-4-1.79-4-4zm-8 0c0 1.105-.895 2-2 2s-2-.895-2-2 .895-2 2-2 2 .895 2 2z' fill='%23d31f26' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E");
        }
        /* Kelas untuk latar belakang Hero yang transparan */
        .hero-bg-image {
            /* Ganti URL ini dengan gambar alat musik pilihan Bapak */
            background-image: url('https://images.unsplash.com/photo-1511379938547-c1f69419868d?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            mix-blend-mode: overlay; /* Kunci agar gambar menyatu dengan warna merah */
            opacity: 0.2; /* Tingkat transparansi gambar */
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans overflow-x-hidden" x-data="{ 
    openModal: null, 
    silabus: {
        'Drum': 'Fokus: Pengenalan drum set, matched grip, koordinasi tangan-kaki, membaca notasi ritme, hingga basic rock beat.',
        'Keyboard': 'Fokus: Penjarian (fingering), notasi balok (Kunci G & F), akord mayor dasar (C, G, F), dan lagu anak-anak.',
        'Gitar Akustik': 'Fokus: Akor dasar (G, C, D, Em, Am, E), pola strumming 4/4, perpindahan akor, dan lagu pop.',
        'Gitar Elektrik': 'Fokus: Pengenalan Amp & Jack, teknik power chords, palm muting, cara membaca TAB, dan down-up stroke.',
        'Bass Elektrik': 'Fokus: Postur & plucking jari, pengenalan fretboard senar E & A, skala mayor, dan menjaga tempo (groove).',
        'Vokal': 'Fokus: Pernapasan diafragma, postur bernyanyi, pitch control (intonasi), resonansi dasar, dan ekspresi lagu.'
    }
}">

    <nav class="bg-white/90 backdrop-blur-md py-4 px-6 shadow-sm border-b-4 border-arcaYellow sticky top-0 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3 group">
                <div class="bg-arcaYellow text-arcaRed w-12 h-12 rounded-full flex items-center justify-center text-2xl shadow-lg transform group-hover:rotate-12 transition">
                    <i class="fas fa-music"></i>
                </div>
                <h1 class="font-black italic uppercase text-2xl tracking-tighter text-arcaRed leading-none">Smart Arca</h1>
            </div>
            <div class="flex gap-4">
                <?php if($is_login): ?>
                    <a href="<?php echo $role; ?>/index.php" class="bg-arcaRed text-white px-6 py-3 rounded-full font-black text-sm uppercase shadow-lg hover:bg-red-700 hover:scale-105 transition">Dashboard</a>
                <?php else: ?>
                    <a href="#login-section" class="bg-arcaRed text-white px-8 py-3 rounded-full font-black text-sm uppercase shadow-lg hover:bg-red-700 hover:scale-105 transition">Login Portal</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <header class="relative py-32 px-6 bg-gradient-to-br from-arcaRed via-red-600 to-red-800 text-center overflow-hidden">
        <div class="absolute inset-0 hero-bg-image"></div>
        
        <div class="relative max-w-5xl mx-auto z-10">
            <span class="inline-block bg-white/20 text-yellow-300 px-6 py-2 rounded-full text-xs font-black uppercase tracking-[0.2em] mb-6 backdrop-blur-sm border-2 border-yellow-300/50 animate-pulse">Weleri Music Education Center</span>
            <h1 class="text-6xl md:text-8xl font-black text-white italic uppercase tracking-tighter mb-8 leading-tight drop-shadow-lg">
                Wujudkan <span class="text-arcaYellow underline decoration-4 decoration-white/30">Mimpimu</span><br>Lewat Musik
            </h1>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-left max-w-3xl mx-auto mt-16">
                <div class="bg-white/95 p-8 rounded-[2.5rem] shadow-2xl border-l-8 border-arcaRed transform hover:-translate-y-2 transition">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 flex items-center"><i class="fas fa-map-marker-alt mr-2 text-arcaRed"></i> Lokasi:</p>
                    <p class="text-sm font-extrabold text-slate-800 uppercase italic leading-tight">Jl. Tamtama, Sekepel, Penyangkringan, Weleri, Kendal</p>
                </div>
                <div class="bg-white/95 p-8 rounded-[2.5rem] shadow-2xl border-l-8 border-arcaYellow transform hover:-translate-y-2 transition">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 flex items-center"><i class="fas fa-headset mr-2 text-arcaYellow"></i> Admin (Mbak Fia):</p>
                    <a href="https://wa.me/62895360796038" target="_blank" class="text-arcaRed font-extrabold italic flex items-center gap-2 text-lg uppercase hover:underline">
                        <i class="fab fa-whatsapp text-green-500 text-2xl"></i> +62 895-3607-96038
                    </a>
                </div>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 60L60 55C120 50 240 40 360 45C480 50 600 70 720 75C840 80 960 70 1080 60C1200 50 1320 40 1380 35L1440 30V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0V60Z" fill="#ffffff"/>
            </svg>
        </div>
    </header>

    <section class="py-24 relative z-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                 <h2 class="text-4xl font-black text-slate-800 uppercase italic tracking-tighter inline-block relative z-10">
                    Program Unggulan
                    <span class="absolute -bottom-2 left-0 w-full h-4 bg-arcaYellow/50 -z-10 rotate-1"></span>
                </h2>
            </div>
           
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <template x-for="(desc, title) in silabus">
                    <button @click="openModal = title" class="bg-white/90 backdrop-blur border-4 border-slate-50 p-10 rounded-[3rem] shadow-xl hover:border-arcaRed hover:shadow-2xl hover:-translate-y-3 transition-all duration-300 group text-center relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-arcaYellow/10 rounded-bl-[5rem] -z-10 group-hover:bg-arcaRed/10 transition"></div>
                        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center text-arcaRed text-3xl mb-6 mx-auto group-hover:bg-arcaRed group-hover:text-white transition-colors shadow-md group-hover:rotate-12">
                            <i class="fas fa-play"></i>
                        </div>
                        <h4 class="text-xl font-black uppercase italic text-slate-800" x-text="'Kelas ' + title"></h4>
                        <div class="mt-6 inline-flex items-center text-xs font-extrabold text-white bg-arcaRed px-6 py-2 rounded-full uppercase tracking-widest group-hover:bg-arcaYellow group-hover:text-arcaRed transition shadow-md">
                            Cek Silabus <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition"></i>
                        </div>
                    </button>
                </template>
            </div>
        </div>
    </section>

    <template x-if="openModal">
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" @click.self="openModal = null" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100">
            <div class="bg-white w-full max-w-lg rounded-[3.5rem] shadow-3xl overflow-hidden border-4 border-white relative">
                <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-br from-arcaRed to-orange-600 rounded-b-[50%] -z-10"></div>
                <div class="p-8 text-white flex justify-between items-center relative z-10">
                    <h3 class="font-black italic uppercase text-2xl tracking-tighter drop-shadow-md" x-text="'Silabus ' + openModal"></h3>
                    <button @click="openModal = null" class="w-12 h-12 rounded-full bg-white/20 hover:bg-white/40 flex items-center justify-center transition text-white"><i class="fas fa-times text-xl"></i></button>
                </div>
                <div class="p-12 bg-white m-4 rounded-[2.5rem] shadow-xl text-center relative z-20">
                    <div class="w-16 h-16 bg-arcaYellow text-white rounded-full flex items-center justify-center text-2xl mx-auto mb-6 shadow-md">
                        <i class="fas fa-info"></i>
                    </div>
                    <p class="text-lg text-slate-700 font-bold italic leading-relaxed" x-text="silabus[openModal]"></p>
                </div>
                 <div class="p-6 pb-10 text-center relative z-20">
                    <a href="https://wa.me/62895360796038" target="_blank" class="inline-block bg-arcaRed text-white px-12 py-5 rounded-full font-black uppercase text-sm shadow-xl italic tracking-widest transition hover:scale-105 hover:bg-red-700 hover:shadow-red-500/30 ring-4 ring-red-200">
                        Daftar Kelas Ini Sekarang <i class="fas fa-rocket ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </template>

    <?php if(!$is_login): ?>
    <section id="login-section" class="py-24 relative overflow-hidden">
         <div class="absolute inset-0 bg-arcaRed/5 -z-10"></div>
        <div class="max-w-md mx-auto px-6 relative z-10">
            <div class="bg-white/80 backdrop-blur p-12 rounded-[3.5rem] shadow-2xl border-t-8 border-arcaRed text-center transform hover:rotate-1 transition duration-500">
                <div class="w-24 h-24 bg-gradient-to-tr from-arcaRed to-yellow-500 text-white rounded-[2rem] flex items-center justify-center text-4xl mx-auto mb-8 shadow-xl rotate-3 border-4 border-white">
                    <i class="fas fa-user-astronaut"></i>
                </div>
                <h2 class="text-4xl font-black text-slate-800 uppercase italic mb-10 tracking-tighter leading-none">Login Portal</h2>
                <form method="POST" class="space-y-6">
                    <div class="relative">
                         <span class="absolute left-6 top-5 text-arcaRed text-xl"><i class="fas fa-user"></i></span>
                        <input type="text" name="username" placeholder="USERNAME" class="w-full p-5 pl-16 rounded-full bg-slate-100 border-2 border-transparent outline-none focus:border-arcaYellow focus:bg-white font-bold italic text-lg transition uppercase tracking-widest shadow-inner" required>
                    </div>
                    <div class="relative">
                        <span class="absolute left-6 top-5 text-arcaRed text-xl"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" placeholder="PASSWORD" class="w-full p-5 pl-16 rounded-full bg-slate-100 border-2 border-transparent outline-none focus:border-arcaYellow focus:bg-white font-bold text-lg transition uppercase tracking-widest shadow-inner" required>
                    </div>
                    <button type="submit" name="login" class="w-full bg-gradient-to-r from-arcaRed to-red-600 text-white font-black py-5 rounded-full uppercase italic shadow-xl transition hover:scale-105 hover:shadow-red-500/50 text-lg tracking-widest">Masuk Dashboard <i class="fas fa-sign-in-alt ml-2"></i></button>
                </form>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <footer class="bg-slate-900 text-white py-16 px-6 border-t-8 border-arcaYellow relative overflow-hidden">
         <div class="absolute top-0 left-0 w-full h-full hero-bg-image opacity-5 mix-blend-overlay pointer-events-none"></div>
        <div class="max-w-6xl mx-auto text-center relative z-10">
            <div class="inline-block bg-arcaYellow text-arcaRed p-4 rounded-2xl text-3xl mb-6 shadow-lg rotate-6">
                <i class="fas fa-music"></i>
            </div>
            <h3 class="text-3xl font-black italic uppercase tracking-tighter text-white mb-4">Smart Arca Music School</h3>
            <p class="text-xs text-slate-400 uppercase font-extrabold tracking-[0.3em] mb-12 italic">Tempat Lahirnya Musisi Masa Depan</p>
            <div class="flex justify-center gap-4 mb-12">
                <a href="#" class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center hover:bg-arcaRed transition"><i class="fab fa-instagram"></i></a>
                <a href="#" class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center hover:bg-arcaRed transition"><i class="fab fa-facebook-f"></i></a>
                <a href="https://wa.me/62895360796038" class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center hover:bg-green-500 transition"><i class="fab fa-whatsapp"></i></a>
            </div>
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-500">&copy; 2026 Smart Arca Music School. Professional Education.</p>
        </div>
    </footer>

    <script>
    (function(){if(!window.chatbase||window.chatbase("getState")!=="initialized"){window.chatbase=(...arguments)=>{if(!window.chatbase.q){window.chatbase.q=[]}window.chatbase.q.push(arguments)};window.chatbase=new Proxy(window.chatbase,{get(target,prop){if(prop==="q"){return target.q}return(...args)=>target(prop,...args)}})}const onLoad=function(){const script=document.createElement("script");script.src="https://www.chatbase.co/embed.min.js";script.id="N1ganJY1PR_sq1a-xetvM";script.domain="www.chatbase.co";document.body.appendChild(script)};if(document.readyState==="complete"){onLoad()}else{window.addEventListener("load",onLoad)}})();
    </script>

</body>
</html>
