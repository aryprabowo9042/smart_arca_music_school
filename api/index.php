<?php
require_once('koneksi.php');

// 1. CEK STATUS LOGIN
$role = $_COOKIE['user_role'] ?? null;
$is_login = ($role !== null);

// 2. LOGIKA PROSES LOGIN (DIPERBAIKI)
$error = '';
if (isset($_POST['login'])) {
    $u = mysqli_real_escape_string($conn, $_POST['username']);
    $p = $_POST['password'];

    $q = mysqli_query($conn, "SELECT * FROM users WHERE username = '$u' AND password = '$p' LIMIT 1");

    if (mysqli_num_rows($q) > 0) {
        $user = mysqli_fetch_assoc($q);
        
        // MENYIMPAN SEMUA BEKAL KE KUKI
        setcookie('user_id', $user['id'], time() + (86400 * 30), "/"); 
        setcookie('user_role', $user['role'], time() + (86400 * 30), "/");
        setcookie('user_username', $user['username'], time() + (86400 * 30), "/");
        
        header("Location: " . $user['role'] . "/index.php");
        exit();
    } else {
        $error = "Username atau Password salah!";
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
                    colors: { arcaRed: '#d31f26', arcaYellow: '#fdb813' },
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] }
                }
            }
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800;900&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M54.627 0l.83.828-1.415 1.415-.83-.828 1.415-1.415zm4.95 1.414l.828.83-1.414 1.415-.828-.83 1.414-1.415zm-4.243 4.243l.828.83-1.415 1.415-.828-.83 1.415-1.415zm19.8 0l.828.83-1.414 1.415-.828-.83 1.414-1.415zm-8.485 8.485l.828.83-1.415 1.415-.828-.83 1.415-1.415zm19.8 0l.83.83-1.415 1.414-.83-.83 1.415-1.414zm-16.97 16.97l.828.83-1.414 1.415-.828-.83 1.414-1.415zm33.94 0l.83.83-1.415 1.415-.83-.83 1.415-1.415zM28 28c0-11.046 8.954-20 20-20s20 8.954 20 20-8.954 20-20 20-20-8.954-20-20zm-8 0c0-6.627 5.373-12 12-12s12 5.373 12 12-5.373 12-12 12-12-5.373-12-12zm-8 0c0-2.21 1.79-4 4-4s4 1.79 4 4-1.79 4-4 4-4-1.79-4-4zm-8 0c0 1.105-.895 2-2 2s-2-.895-2-2 .895-2 2-2 2 .895 2 2zm-8 0c0-13.255 10.745-24 24-24s24 10.745 24 24-10.745 24-24 24-24-10.745-24-24zm-8 0c0-8.837 7.163-16 16-16s16 7.163 16 16-7.163 16-16 16-16-7.163-16-16zm-8 0c0-4.418 3.582-8 8-8s8 3.582 8 8-3.582 8-8 8-8-3.582-8-8zm-8 0c0-2.21 1.79-4 4-4s4 1.79 4 4-1.79 4-4 4-4-1.79-4-4zm-8 0c0 1.105-.895 2-2 2s-2-.895-2-2 .895-2 2-2 2 .895 2 2z' fill='%23d31f26' fill-opacity='0.03'/%3E%3C/svg%3E");
        }
        .hero-bg {
            background-image: url('https://images.unsplash.com/photo-1511379938547-c1f69419868d?q=80&w=2070&auto=format&fit=crop');
            background-size: cover; background-position: center; mix-blend-mode: overlay; opacity: 0.2;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="overflow-x-hidden" x-data="{ 
    openModal: null, 
    silabus: {
        'Drum': 'Fokus: Stick control, koordinasi kaki-tangan, hingga basic rock beat.',
        'Keyboard': 'Fokus: Fingering, notasi balok, akord mayor (C, G, F).',
        'Gitar Akustik': 'Fokus: Akord dasar, pola strumming 4/4, lagu pop.',
        'Gitar Elektrik': 'Fokus: Power chords, palm muting, cara baca TAB.',
        'Bass Elektrik': 'Fokus: Plucking jari, skala mayor, menjaga groove.',
        'Vokal': 'Fokus: Pernapasan diafragma, intonasi, ekspresi lagu.'
    }
}">

    <nav class="bg-white/90 backdrop-blur-md py-4 px-6 shadow-sm border-b-4 border-arcaYellow sticky top-0 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="bg-arcaYellow text-arcaRed w-10 h-10 rounded-full flex items-center justify-center text-xl shadow-lg rotate-12"><i class="fas fa-music"></i></div>
                <h1 class="font-black italic uppercase text-xl text-arcaRed">Smart Arca</h1>
            </div>
            <div class="flex gap-4">
                <?php if($is_login): ?><a href="<?php echo $role; ?>/index.php" class="bg-arcaRed text-white px-6 py-2 rounded-full font-black text-xs uppercase shadow-lg">Dashboard</a>
                <?php else: ?><a href="#login-section" class="bg-arcaRed text-white px-8 py-2 rounded-full font-black text-xs uppercase shadow-lg">Login</a><?php endif; ?>
            </div>
        </div>
    </nav>

    <header class="relative py-24 bg-gradient-to-br from-arcaRed to-red-800 text-center overflow-hidden text-white">
        <div class="absolute inset-0 hero-bg"></div>
        <div class="relative z-10 max-w-4xl mx-auto px-6">
            <h1 class="text-5xl md:text-7xl font-black italic uppercase mb-8 leading-tight">Wujudkan <span class="text-arcaYellow">Mimpimu</span></h1>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-2xl mx-auto mt-10">
                <div class="bg-white p-6 rounded-3xl shadow-xl text-slate-800 text-left border-l-8 border-arcaRed">
                    <p class="text-[9px] font-black text-slate-400 uppercase">Lokasi:</p>
                    <p class="text-xs font-bold italic uppercase">Jl. Tamtama, Weleri, Kendal</p>
                </div>
                <div class="bg-white p-6 rounded-3xl shadow-xl text-slate-800 text-left border-l-8 border-arcaYellow">
                    <p class="text-[9px] font-black text-slate-400 uppercase">Admin:</p>
                    <a href="https://wa.me/62895360796038" target="_blank" class="text-arcaRed font-black italic text-sm"><i class="fab fa-whatsapp text-green-500"></i> 0895-3607-96038</a>
                </div>
            </div>
        </div>
    </header>

    <section class="py-20 px-6">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl font-black text-center text-slate-800 uppercase italic mb-12">Program Unggulan</h2>
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-6">
                <template x-for="(desc, title) in silabus">
                    <button @click="openModal = title" class="bg-white p-10 rounded-[2.5rem] shadow-xl hover:shadow-2xl hover:-translate-y-2 transition-all border-2 border-slate-50 hover:border-arcaRed text-center group">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center text-arcaRed text-2xl mb-4 mx-auto group-hover:bg-arcaRed group-hover:text-white transition shadow-inner"><i class="fas fa-play"></i></div>
                        <h4 class="text-sm font-black uppercase italic" x-text="'Kelas ' + title"></h4>
                    </button>
                </template>
            </div>
        </div>
    </section>

    <template x-if="openModal">
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-black/60 backdrop-blur-sm" @click.self="openModal = null">
            <div class="bg-white w-full max-w-md rounded-[3rem] shadow-2xl overflow-hidden animate-bounce-short">
                <div class="bg-arcaRed p-6 text-white flex justify-between border-b-4 border-arcaYellow">
                    <h3 class="font-black italic uppercase text-xs" x-text="'Silabus ' + openModal"></h3>
                    <button @click="openModal = null"><i class="fas fa-times"></i></button>
                </div>
                <div class="p-8 text-slate-600 font-bold italic text-sm leading-relaxed" x-text="silabus[openModal]"></div>
                <div class="p-6 bg-white border-t text-center">
                    <a href="https://wa.me/62895360796038" target="_blank" class="bg-arcaRed text-white px-8 py-3 rounded-full font-black uppercase text-[10px] shadow-lg italic inline-block">Daftar Sekarang</a>
                </div>
            </div>
        </div>
    </template>

    <?php if(!$is_login): ?>
    <section id="login-section" class="py-20 bg-slate-50">
        <div class="max-w-md mx-auto px-6">
            <div class="bg-white p-10 rounded-[3rem] shadow-2xl border-b-8 border-arcaYellow text-center">
                <h2 class="text-2xl font-black italic mb-8 uppercase">Login Portal</h2>
                <form method="POST" class="space-y-4">
                    <input type="text" name="username" placeholder="Username" class="w-full p-4 rounded-2xl bg-slate-50 border-2 font-bold italic outline-none focus:border-arcaRed transition shadow-inner" required>
                    <input type="password" name="password" placeholder="Password" class="w-full p-4 rounded-2xl bg-slate-50 border-2 font-bold outline-none focus:border-arcaRed transition shadow-inner" required>
                    <button type="submit" name="login" class="w-full bg-arcaRed text-white font-black py-4 rounded-2xl uppercase italic shadow-xl hover:bg-red-700 transition">Masuk Dashboard</button>
                    <?php if($error): ?><p class="text-red-600 font-black italic text-[10px] uppercase mt-2"><?php echo $error; ?></p><?php endif; ?>
                </form>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <footer class="bg-slate-900 text-white py-10 text-center border-t-8 border-arcaYellow">
        <p class="text-[9px] font-black uppercase tracking-widest">&copy; 2026 Smart Arca Music School.</p>
    </footer>

    <script>
    (function(){if(!window.chatbase||window.chatbase("getState")!=="initialized"){window.chatbase=(...arguments)=>{if(!window.chatbase.q){window.chatbase.q=[]}window.chatbase.q.push(arguments)};window.chatbase=new Proxy(window.chatbase,{get(target,prop){if(prop==="q"){return target.q}return(...args)=>target(prop,...args)}})}const onLoad=function(){const script=document.createElement("script");script.src="https://www.chatbase.co/embed.min.js";script.id="N1ganJY1PR_sq1a-xetvM";script.domain="www.chatbase.co";document.body.appendChild(script)};if(document.readyState==="complete"){onLoad()}else{window.addEventListener("load",onLoad)}})();
    </script>
</body>
</html>
