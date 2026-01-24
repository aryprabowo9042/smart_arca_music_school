<?php
require_once('koneksi.php');
$role = $_COOKIE['user_role'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Arca Music School</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-white font-sans" x-data="{ openModal: null, chatOpen: false, messages: [{role: 'bot', text: 'Halo! Saya Arca AI. Ada yang bisa saya bantu?'}], userInput: '', loading: false }">

    <nav class="bg-red-700 text-white py-4 px-6 border-b-4 border-yellow-400 sticky top-0 z-50 flex justify-between items-center">
        <h1 class="font-black italic uppercase text-xl">Smart Arca</h1>
        <?php if($role): ?>
            <a href="<?php echo $role; ?>/index.php" class="bg-yellow-400 text-red-800 px-6 py-2 rounded-full font-black text-xs uppercase">Dashboard</a>
        <?php else: ?>
            <a href="#login" class="bg-yellow-400 text-red-800 px-6 py-2 rounded-full font-black text-xs uppercase">Login</a>
        <?php endif; ?>
    </nav>

    <header class="py-20 text-center bg-slate-50">
        <h2 class="text-6xl font-black italic uppercase tracking-tighter">Wujudkan <span class="text-red-600">Mimpimu</span></h2>
        <p class="text-slate-400 mt-4 font-bold uppercase tracking-widest">Sekolah Musik Profesional di Weleri</p>
    </header>

    <section class="py-20 max-w-6xl mx-auto px-6">
        <h3 class="text-center font-black uppercase text-3xl italic mb-12">Program Kursus</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-8">
            <template x-for="item in ['Drum', 'Keyboard', 'Gitar Akustik', 'Gitar Elektrik', 'Bas', 'Vokal']">
                <button @click="openModal = item" class="bg-white border-2 p-10 rounded-[2.5rem] shadow-xl hover:border-red-600 transition text-center font-black uppercase italic text-sm" x-text="'Kelas ' + item"></button>
            </template>
        </div>
    </section>

    <div class="fixed bottom-6 right-6 z-[100]">
        <button @click="chatOpen = !chatOpen" class="bg-red-700 text-yellow-400 w-16 h-16 rounded-full shadow-2xl flex items-center justify-center text-2xl border-4 border-yellow-400">
            <i class="fas" :class="chatOpen ? 'fa-times' : 'fa-comment-dots'"></i>
        </button>
        <div x-show="chatOpen" class="absolute bottom-20 right-0 w-[320px] bg-white rounded-[2rem] shadow-2xl border-b-8 border-yellow-400 overflow-hidden flex flex-col">
            <div class="bg-red-700 p-4 text-white font-black italic uppercase text-xs">Arca AI Assistant</div>
            <div class="h-[300px] overflow-y-auto p-4 space-y-4 bg-slate-50 text-[10px] font-bold" id="chat-box">
                <template x-for="msg in messages">
                    <div :class="msg.role === 'user' ? 'text-right' : 'text-left'">
                        <div :class="msg.role === 'user' ? 'bg-red-600 text-white' : 'bg-white border text-slate-700'" class="inline-block p-3 rounded-2xl shadow-sm" x-text="msg.text"></div>
                    </div>
                </template>
            </div>
            <div class="p-4 border-t flex gap-2">
                <input type="text" x-model="userInput" @keyup.enter="sendMessage()" placeholder="Tanya Arca..." class="flex-1 bg-slate-100 p-3 rounded-xl text-xs outline-none">
                <button @click="sendMessage()" class="bg-red-700 text-yellow-400 px-3 rounded-xl"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </div>

    <template x-if="openModal">
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 p-6" @click.self="openModal = null">
            <div class="bg-white w-full max-w-lg rounded-[3rem] overflow-hidden shadow-2xl">
                <div class="bg-red-700 p-6 text-white font-black uppercase italic flex justify-between">
                    <span x-text="'Silabus ' + openModal"></span>
                    <button @click="openModal = null">X</button>
                </div>
                <div class="p-8 text-sm font-bold uppercase italic text-slate-600">
                    <p>Materi Dasar: Postur, Teknik Memegang Instrumen, dan Skala Dasar.</p>
                    <p class="mt-4">Materi Lanjutan: Repertoar Lagu, Teknik Improvisasi, dan Persiapan Pertunjukan.</p>
                </div>
            </div>
        </div>
    </template>

    <script>
        function sendMessage() {
            const el = document.querySelector('[x-data]');
            const d = el.__x.$data;
            if(!d.userInput) return;
            d.messages.push({role:'user', text: d.userInput});
            let txt = d.userInput; d.userInput = ''; d.loading = true;
            fetch('chat.php', {
                method: 'POST',
                body: JSON.stringify({message: txt})
            }).then(r => r.json()).then(res => {
                d.messages.push({role:'bot', text: res.reply});
                setTimeout(() => { document.getElementById('chat-box').scrollTop = 9999; }, 100);
            }).finally(() => d.loading = false);
        }
    </script>
</body>
</html>
