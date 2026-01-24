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
    } else { $error = "Username/Password salah!"; }
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
        .chat-scroll::-webkit-scrollbar { width: 4px; }
        .chat-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="bg-white" x-data="{ 
    openModal: null, 
    chatOpen: false, 
    messages: [{role: 'bot', text: 'Halo! Saya Arca AI. Ada yang bisa saya bantu hari ini?'}], 
    userInput: '', 
    loading: false,
    silabus: {
        'Drum': 'Fokus: Pengenalan drum set, matched grip, koordinasi tangan-kaki, membaca notasi ritme (quarter & eighth notes), hingga basic rock beat.',
        'Keyboard': 'Fokus: Penjarian (fingering), notasi balok (Kunci G & F), akord mayor dasar (C, G, F), dan memainkan melodi lagu anak-anak.',
        'Gitar Akustik': 'Fokus: Akor dasar (G, C, D, Em, Am, E), pola strumming 4/4 (Down-Up), perpindahan akor bersih, dan lagu pop sederhana.',
        'Gitar Elektrik': 'Fokus: Pengenalan Amp & Jack, cara membaca TAB, chord ceria (C, G, D), dan teknik memetik (downstroke & upstroke).',
        'Bas': 'Fokus: Postur & plucking, notasi ritme, pengenalan fretboard (senar E & A), skala mayor, dan menjaga tempo (groove).',
        'Vokal': 'Fokus: Pernapasan diafragma, postur bernyanyi, pitch control (intonasi), resonansi dasar, dan ekspresi lagu.'
    },
    // FUNGSI KIRIM PESAN (Pindah ke dalam x-data agar lancar)
    async sendMessage() {
        if (this.userInput.trim() === '' || this.loading) return;
        
        const txt = this.userInput;
        this.messages.push({ role: 'user', text: txt });
        this.userInput = '';
        this.loading = true;

        // Auto scroll ke bawah
        this.$nextTick(() => { 
            const box = document.getElementById('chat-box');
            box.scrollTop = box.scrollHeight;
        });

        try {
            const response = await fetch('api/chat.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: txt })
            });
            const data = await response.json();
            this.messages.push({ role: 'bot', text: data.reply });
        } catch (e) {
            this.messages.push({ role: 'bot', text: 'Koneksi terputus. Coba lagi ya.' });
        } finally {
            this.loading = false;
            this.$nextTick(() => { 
                const box = document.getElementById('chat-box');
                box.scrollTop = box.scrollHeight;
            });
        }
    }
}">

    <nav class="bg-red-700 text-white py-4 px-6 shadow-xl border-b-4 border-yellow-400 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="bg-yellow-400 text-red-700 w-10 h-10 rounded-lg flex items-center justify-center text-xl shadow-md"><i class="fas fa-music"></i></div>
                <h1 class="font-black italic uppercase text-lg tracking-tighter leading-none">Smart Arca</h1>
            </div>
            <div class="flex gap-4">
                <?php if($is_login): ?>
                    <a href="<?php echo $role; ?>/index.php" class="bg-yellow-400 text-red-800 px-6 py-2 rounded-full font-black text-xs uppercase shadow-lg">Dashboard</a>
                <?php else: ?>
                    <a href="#login-section" class="bg-yellow-400 text-red-800 px-8 py-3 rounded-full font-black text-xs uppercase shadow-lg">Login Portal</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <header class="py-20 px-6 bg-gradient-to-b from-red-50 to-white text-center">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-5xl md:text-7xl font-black text-slate-900 italic uppercase tracking-tighter mb-6 leading-tight">Wujudkan <span class="text-red-600">Mimpimu</span></h1>
            
            <div class="mt-12 grid grid-cols-1 md:grid-cols-2 gap-6 text-left max-w-2xl mx-auto">
                <div class="bg-white p-6 rounded-[2.5rem] shadow-xl border-l-8 border-red-600">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Lokasi:</p>
                    <p class="text-xs font-bold text-slate-700 uppercase italic leading-tight">Jl. Tamtama, Sekepel, Penyangkringan, Weleri, Kendal</p>
                </div>
                <div class="bg-white p-6 rounded-[2.5rem] shadow-xl border-l-8 border-yellow-400 flex flex-col justify-center">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 leading-none">Admin (Mbak Fia):</p>
                    <a href="https://wa.me/62895360796038" target="_blank" class="text-red-700 font-black italic flex items-center gap-2 text-sm uppercase hover:text-red-800 transition">
                        <i class="fab fa-whatsapp text-green-500 text-xl"></i> +62 895-3607-96038
                    </a>
                </div>
            </div>
        </div>
    </header>

    <section class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-6 text-center">
            <h2 class="text-3xl font-black text-slate-800 uppercase italic mb-12">Program Unggulan</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-8">
                <template x-for="(desc, title) in silabus">
                    <button @click="openModal = title" class="bg-white border-2 border-slate-100 p-8 rounded-[2.5rem] shadow-xl hover:border-red-600 transition group">
                        <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center text-red-600 text-xl mb-4 mx-auto group-hover:bg-red-600 group-hover:text-white transition shadow-inner"><i class="fas fa-play"></i></div>
                        <h4 class="text-xs font-black uppercase italic" x-text="'Kelas ' + title"></h4>
                        <p class="text-[8px] font-bold text-slate-400 uppercase mt-2">Cek Silabus <i class="fas fa-chevron-right ml-1"></i></p>
                    </button>
                </template>
            </div>
        </div>
    </section>

    <template x-if="openModal">
        <div class="fixed inset-0 z-[200] flex items-center justify-center p-6 bg-black/80 backdrop-blur-sm" @click.self="openModal = null">
            <div class="bg-white w-full max-w-md rounded-[3rem] shadow-2xl overflow-hidden animate-in zoom-in duration-300">
                <div class="bg-red-700 p-6 text-white flex justify-between border-b-4 border-yellow-400 items-center">
                    <h3 class="font-black italic uppercase tracking-tighter" x-text="'Silabus ' + openModal"></h3>
                    <button @click="openModal = null" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center"><i class="fas fa-times"></i></button>
                </div>
                <div class="p-8 bg-slate-50 text-slate-600 font-bold italic text-sm leading-relaxed" x-text="silabus[openModal]"></div>
                <div class="p-6 bg-white text-center border-t">
                    <a href="https://wa.me/62895360796038" target="_blank" class="bg-red-700 text-white px-8 py-3 rounded-2xl font-black uppercase text-[10px] shadow-lg italic tracking-widest block">Daftar Sekarang</a>
                </div>
            </div>
        </div>
    </template>

    <div class="fixed bottom-6 right-6 z-[100]">
        <button @click="chatOpen = !chatOpen" class="bg-red-700 text-yellow-400 w-16 h-16 rounded-full shadow-2xl flex items-center justify-center text-2xl border-4 border-yellow-400 hover:scale-110 transition active:scale-95">
            <i class="fas" :class="chatOpen ? 'fa-times' : 'fa-comment-dots'"></i>
        </button>

        <div x-show="chatOpen" x-cloak x-transition class="absolute bottom-24 right-0 w-[350px] bg-white rounded-[2rem] shadow-2xl border-b-8 border-yellow-400 flex flex-col overflow-hidden">
            <div class="bg-red-700 p-4 text-white flex items-center gap-3 border-b-2 border-yellow-400">
                <i class="fas fa-robot text-yellow-400 text-xl"></i>
                <p class="font-black italic text-xs uppercase tracking-tighter">Arca AI Assistant</p>
            </div>
            <div class="h-[300px] overflow-y-auto p-4 space-y-4 bg-slate-50 chat-scroll" id="chat-box">
                <template x-for="msg in messages">
                    <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                        <div :class="msg.role === 'user' ? 'bg-red-600 text-white rounded-t-2xl rounded-bl-2xl shadow-md' : 'bg-white text-slate-700 rounded-t-2xl rounded-br-2xl shadow-sm border border-slate-100'" class="p-3 max-w-[85%] font-bold italic text-[11px] leading-relaxed" x-text="msg.text"></div>
                    </div>
                </template>
                <div x-show="loading" class="text-[9px] font-black uppercase text-slate-400 animate-pulse italic">Arca sedang mengetik...</div>
            </div>
            <div class="p-4 border-t bg-white flex gap-2">
                <input type="text" x-model="userInput" @keyup.enter="sendMessage()" placeholder="Tanya apa saja..." class="flex-1 bg-slate-100 p-3 rounded-xl outline-none text-xs font-bold italic focus:ring-2 focus:ring-red-600 transition">
                <button @click="sendMessage()" class="bg-red-700 text-yellow-400 w-12 h-10 rounded-xl shadow-lg flex items-center justify-center transition active:scale-90"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </div>

    <?php if(!$is_login): ?>
    <section id="login-section" class="py-20 bg-slate-100">
        <div class="max-w-md mx-auto px-6 bg-white p-10 rounded-[3rem] shadow-2xl border-b-8 border-yellow-400 text-center">
            <h2 class="text-3xl font-black italic mb-6 tracking-tighter">LOGIN PORTAL</h2>
            <form method="POST" class="space-y-4">
                <input type="text" name="username" placeholder="Username" class="w-full p-4 rounded-2xl bg-slate-50 border-2 font-bold italic focus:border-red-600 outline-none transition">
                <input type="password" name="password" placeholder="Password" class="w-full p-4 rounded-2xl bg-slate-50 border-2 font-bold focus:border-red-600 outline-none transition">
                <button type="submit" name="login" class="w-full bg-red-600 text-white font-black py-4 rounded-2xl uppercase italic shadow-lg active:scale-95 transition">Masuk Dashboard</button>
            </form>
        </div>
    </section>
    <?php endif; ?>

    <footer class="bg-slate-900 text-white py-12 text-center border-t-8 border-yellow-400">
        <p class="text-[9px] font-black uppercase tracking-widest text-slate-500 italic">&copy; 2026 Smart Arca Music School. Professional Education.</p>
    </footer>
</body>
</html>
