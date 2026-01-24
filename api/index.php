<?php
require_once('koneksi.php');

// 1. CEK STATUS LOGIN
$role = $_COOKIE['user_role'] ?? null;
$is_login = ($role !== null);

// 2. LOGIKA PROSES LOGIN
$error = '';
if (isset($_POST['login'])) {
    $user_input = mysqli_real_escape_string($conn, $_POST['username']);
    $pass_input = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = '$user_input' AND password = '$pass_input' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; scroll-behavior: smooth; }
        .modal-bg { background-color: rgba(0, 0, 0, 0.8); backdrop-filter: blur(5px); }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-white" x-data="{ openModal: null, chatOpen: false, messages: [{role: 'bot', text: 'Halo! Saya Arca AI. Ada yang bisa saya bantu?'}], userInput: '', loading: false }">

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
                    <a href="<?php echo $role; ?>/index.php" class="bg-yellow-400 text-red-800 px-6 py-2 rounded-full font-black text-xs uppercase shadow-lg transition">Dashboard</a>
                <?php else: ?>
                    <a href="#login-section" class="bg-yellow-400 text-red-800 px-8 py-3 rounded-full font-black text-xs uppercase shadow-lg">Login Portal</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <header class="py-20 px-6 bg-gradient-to-b from-red-50 to-white text-center">
        <div class="max-w-4xl mx-auto">
            <span class="inline-block bg-red-100 text-red-600 px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-[0.2em] mb-6">Weleri Kendal Music School</span>
            <h1 class="text-5xl md:text-7xl font-black text-slate-900 italic uppercase tracking-tighter mb-6 leading-tight">Wujudkan <span class="text-red-600">Mimpimu</span><br>Lewat <span class="text-yellow-500">Nada & Irama</span></h1>
            
            <div class="mt-12 grid grid-cols-1 md:grid-cols-2 gap-6 text-left max-w-2xl mx-auto">
                <div class="bg-white p-6 rounded-[2.5rem] shadow-xl border-l-8 border-red-600 flex flex-col justify-center">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 leading-none">Lokasi Sekolah:</p>
                    <p class="text-xs font-bold text-slate-700 leading-relaxed uppercase italic">Jl. Tamtama, Sekepel, Penyangkringan, Weleri, Kendal</p>
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
        <div class="max-w-6xl mx-auto px-6">
            <h2 class="text-3xl font-black text-center text-slate-800 uppercase italic tracking-tighter mb-12">Program Unggulan & Silabus</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                <button @click="openModal = 'drum'" class="bg-white border-2 border-slate-100 p-8 rounded-[2.5rem] shadow-xl hover:border-red-600 transition group text-center">
                    <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center text-red-600 text-xl mb-4 mx-auto group-hover:bg-red-600 group-hover:text-white transition"><i class="fas fa-drum"></i></div>
                    <h4 class="text-xs font-black uppercase italic">Kelas Drum</h4>
                </button>
                <button @click="openModal = 'keyboard'" class="bg-white border-2 border-slate-100 p-8 rounded-[2.5rem] shadow-xl hover:border-yellow-400 transition group text-center">
                    <div class="w-14 h-14 bg-yellow-100 rounded-2xl flex items-center justify-center text-yellow-600 text-xl mb-4 mx-auto group-hover:bg-yellow-400 group-hover:text-white transition"><i class="fas fa-keyboard"></i></div>
                    <h4 class="text-xs font-black uppercase italic">Kelas Keyboard</h4>
                </button>
                <button @click="openModal = 'vokal'" class="bg-white border-2 border-slate-100 p-8 rounded-[2.5rem] shadow-xl hover:border-red-600 transition group text-center">
                    <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center text-red-600 text-xl mb-4 mx-auto group-hover:bg-red-600 group-hover:text-white transition"><i class="fas fa-microphone-alt"></i></div>
                    <h4 class="text-xs font-black uppercase italic">Kelas Vokal</h4>
                </button>
            </div>
        </div>
    </section>

    <div class="fixed bottom-6 right-6 z-[100]">
        <button @click="chatOpen = !chatOpen" class="bg-red-700 text-yellow-400 w-16 h-16 rounded-full shadow-2xl flex items-center justify-center text-2xl border-4 border-yellow-400 hover:scale-110 transition active:scale-95">
            <i class="fas" :class="chatOpen ? 'fa-times' : 'fa-comment-dots'"></i>
        </button>

        <div x-show="chatOpen" x-cloak x-transition class="absolute bottom-20 right-0 w-[350px] bg-white rounded-[2rem] shadow-2xl border-b-8 border-yellow-400 overflow-hidden flex flex-col">
            <div class="bg-red-700 p-4 text-white flex items-center gap-3 border-b-4 border-yellow-400">
                <div class="w-8 h-8 bg-yellow-400 text-red-700 rounded-full flex items-center justify-center text-xs shadow-inner"><i class="fas fa-robot"></i></div>
                <div>
                    <p class="font-black italic text-xs leading-none uppercase tracking-tighter">Arca AI Assistant</p>
                    <p class="text-[8px] font-bold text-red-200 uppercase mt-1">Online - Siap Membantu</p>
                </div>
            </div>

            <div class="h-[300px] overflow-y-auto p-4 space-y-4 bg-slate-50 text-[11px]" id="chat-box">
                <template x-for="msg in messages">
                    <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                        <div :class="msg.role === 'user' ? 'bg-red-600 text-white rounded-t-2xl rounded-bl-2xl shadow-md' : 'bg-white text-slate-700 rounded-t-2xl rounded-br-2xl shadow-sm border border-slate-100'" class="p-3 max-w-[80%] font-semibold leading-relaxed">
                            <span x-text="msg.text"></span>
                        </div>
                    </div>
                </template>
                <div x-show="loading" class="flex justify-start italic text-[9px] text-slate-400 px-2 uppercase font-black">Arca sedang mengetik...</div>
            </div>

            <div class="p-4 border-t bg-white">
                <div class="flex gap-2">
                    <input type="text" x-model="userInput" @keyup.enter="sendMessage" placeholder="Tanya apa saja..." class="flex-1 bg-slate-100 p-3 rounded-xl outline-none focus:ring-2 focus:ring-red-600 font-bold italic text-xs">
                    <button @click="sendMessage" class="bg-red-700 text-yellow-400 w-10 h-10 rounded-xl flex items-center justify-center shadow-lg"><i class="fas fa-paper-plane"></i></button>
                </div>
            </div>
        </div>
    </div>

    <?php if(!$is_login): ?>
    <section id="login-section" class="py-20 bg-slate-100">
        <div class="max-w-md mx-auto px-6">
            <div class="bg-white p-10 rounded-[3rem] shadow-2xl border-b-8 border-yellow-400 text-center">
                <h2 class="text-3xl font-black text-slate-800 uppercase italic mb-6">Login Portal</h2>
                <form method="POST" class="space-y-4">
                    <input type="text" name="username" placeholder="Username" class="w-full p-4 rounded-2xl bg-slate-50 border-2 outline-none focus:border-red-600 font-bold italic" required>
                    <input type="password" name="password" placeholder="Password" class="w-full p-4 rounded-2xl bg-slate-50 border-2 outline-none focus:border-red-600 font-bold" required>
                    <button type="submit" name="login" class="w-full bg-red-600 text-white font-black py-4 rounded-2xl uppercase italic">Masuk Dashboard</button>
                </form>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <footer class="bg-slate-900 text-white py-16 px-6 border-t-8 border-yellow-400">
        <div class="max-w-6xl mx-auto flex flex-col md:flex-row justify-between items-center gap-10">
            <div class="text-center md:text-left">
                <h3 class="text-2xl font-black italic uppercase tracking-tighter text-yellow-400 mb-2">Smart Arca Music</h3>
                <p class="text-[10px] text-slate-500 uppercase font-bold tracking-widest italic">Weleri, Kendal, Jawa Tengah</p>
            </div>
            <div class="text-center md:text-right">
                <a href="https://wa.me/62895360796038" target="_blank" class="bg-red-700 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase shadow-xl hover:bg-red-800 transition inline-flex items-center gap-3 italic">
                    <i class="fab fa-whatsapp"></i> Chat Admin (Mbak Fia)
                </a>
            </div>
        </div>
        <p class="text-[9px] font-black uppercase tracking-widest text-slate-600 text-center mt-12">&copy; 2026 Smart Arca Music School. All Rights Reserved.</p>
    </footer>

    <script>
        function sendMessage() {
            const el = document.querySelector('[x-data]');
            const data = el.__x.$data;
            if (data.userInput.trim() === '' || data.loading) return;

            const userText = data.userInput;
            data.messages.push({ role: 'user', text: userText });
            data.userInput = '';
            data.loading = true;

            setTimeout(() => { document.getElementById('chat-box').scrollTop = 10000; }, 50);

            fetch('api/chat.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: userText })
            })
            .then(res => res.json())
            .then(res => {
                data.messages.push({ role: 'bot', text: res.reply });
            })
            .catch(() => {
                data.messages.push({ role: 'bot', text: 'Koneksi terputus. Coba lagi ya.' });
            })
            .finally(() => {
                data.loading = false;
                setTimeout(() => { document.getElementById('chat-box').scrollTop = 10000; }, 50);
            });
        }
    </script>
</body>
</html>
