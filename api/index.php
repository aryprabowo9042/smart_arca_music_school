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
        
        // Pengalihan otomatis ke folder role masing-masing
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
    <title>Smart Arca Music School - Weleri Kendal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
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
                    <p class="text-[10px] font-bold text-red-200 uppercase tracking-widest leading-none">Weleri - Kendal</p>
                </div>
            </div>

            <div class="flex gap-4">
                <?php if($is_login): ?>
                    <a href="<?php echo $role; ?>/index.php" class="bg-yellow-400 hover:bg-yellow-300 text-red-800 px-6 py-2 rounded-full font-black text-xs uppercase shadow-lg transition flex items-center gap-2">
                        <i class="fas fa-tachometer-alt"></i> Dashboard <?php echo ucfirst($role); ?>
                    </a>
                    <a href="logout.php" class="bg-white hover:bg-slate-100 text-red-600 px-4 py-2 rounded-full font-black text-xs uppercase shadow-lg transition">Keluar</a>
                <?php else: ?>
                    <a href="#login-section" class="bg-yellow-400 hover:bg-yellow-300 text-red-800 px-8 py-3 rounded-full font-black text-xs uppercase shadow-lg tracking-widest">Login Portal <i class="fas fa-sign-in-alt ml-2"></i></a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <header class="py-20 px-6 bg-gradient-to-b from-red-50 to-white">
        <div class="max-w-5xl mx-auto text-center">
            <span class="inline-block bg-red-100 text-red-600 px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-[0.2em] mb-6 italic">Weleri Kendal Professional Music School</span>
            <h1 class="text-5xl md:text-7xl font-black text-slate-900 italic uppercase tracking-tighter mb-6 leading-tight">Wujudkan <span class="text-red-600">Mimpimu</span><br>Lewat <span class="text-yellow-500">Nada & Irama</span></h1>
            
            <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6 text-left max-w-5xl mx-auto">
                <div class="bg-white p-6 rounded-[2.5rem] shadow-xl border-l-8 border-red-600">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 leading-none">Alamat Kami:</p>
                    <p class="text-[11px] font-bold text-slate-700 leading-relaxed uppercase italic">
                        Jl. Tamtama, Sekepel, Penyangkringan, Kec. Weleri, Kab. Kendal, Jawa Tengah 51355
                    </p>
                </div>

                <div class="bg-white p-6 rounded-[2.5rem] shadow-xl border-l-8 border-yellow-400 flex flex-col justify-center">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 leading-none">Kontak Admin (Fia):</p>
                    <a href="https://wa.me/62895360796038" target="_blank" class="bg-green-50 p-3 rounded-2xl text-green-700 font-black italic hover:bg-green-100 transition flex items-center justify-between">
                        <span class="text-xs">+62 895-3607-96038</span>
                        <i class="fab fa-whatsapp text-2xl"></i>
                    </a>
                </div>

                <div class="bg-red-700 p-6 rounded-[2.5rem] shadow-xl text-white flex flex-col justify-center">
                    <p class="text-[9px] font-black text-red-200 uppercase tracking-[0.2em] mb-2 leading-none">Petunjuk Lokasi:</p>
                    <a href="https://www.google.com/maps/search/?api=1&query=Jl.+Tamtama,+Sekepel,+Penyangkringan,+Weleri,+Kendal" target="_blank" class="bg-yellow-400 text-red-800 p-3 rounded-2xl font-black italic hover:bg-yellow-300 transition flex items-center justify-between shadow-lg">
                        <span class="text-xs uppercase">Buka Google Maps</span>
                        <i class="fas fa-map-marked-alt text-xl"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <section class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-6">
            <h2 class="text-center text-3xl font-black text-slate-800 uppercase italic tracking-tighter mb-12">Program Kursus Unggulan</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white border-2 border-slate-50 p-8 rounded-[2.5rem] shadow-xl hover:border-red-500 transition group">
                    <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center text-red-600 text-2xl mb-6 group-hover:bg-red-600 group-hover:text-white transition shadow-inner"><i class="fas fa-guitar"></i></div>
                    <h3 class="text-xl font-black text-slate-800 uppercase italic mb-2 tracking-tighter leading-none">Gitar & Bass</h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest italic">Professional Coaching</p>
                </div>
                <div class="bg-white border-2 border-slate-50 p-8 rounded-[2.5rem] shadow-xl hover:border-yellow-400 transition group">
                    <div class="w-14 h-14 bg-yellow-100 rounded-2xl flex items-center justify-center text-yellow-600 text-2xl mb-6 group-hover:bg-yellow-400 group-hover:text-white transition shadow-inner"><i class="fas fa-keyboard"></i></div>
                    <h3 class="text-xl font-black text-slate-800 uppercase italic mb-2 tracking-tighter leading-none">Piano & Vokal</h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest italic">Classical & Pop Method</p>
                </div>
                <div class="bg-white border-2 border-slate-50 p-8 rounded-[2.5rem] shadow-xl hover:border-red-500 transition group">
                    <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center text-red-600 text-2xl mb-6 group-hover:bg-red-600 group-hover:text-white transition shadow-inner"><i class="fas fa-drum"></i></div>
                    <h3 class="text-xl font-black text-slate-800 uppercase italic mb-2 tracking-tighter leading-none">Drum & Beat</h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest italic">Rhythm & Rudiments</p>
                </div>
            </div>
        </div>
    </section>

    <?php if(!$is_login): ?>
    <section id="login-section" class="py-20 bg-slate-100">
        <div class="max-w-md mx-auto px-6">
            <div class="bg-white p-10 rounded-[3rem] shadow-2xl border-b-8 border-yellow-400">
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-black text-slate-800 uppercase italic tracking-tighter mb-1">Login Portal</h2>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic leading-none">Khusus Siswa & Guru Smart Arca</p>
                </div>
                
                <?php if($error): ?>
                    <div class="bg-red-50 text-red-600 p-4 rounded-2xl mb-6 text-xs font-bold border border-red-100 italic flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-3 mb-2 block">Username</label>
                        <input type="text" name="username" class="w-full p-4 rounded-2xl bg-slate-50 border-2 border-slate-50 focus:border-red-600 outline-none font-bold italic" placeholder="Username" required>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-3 mb-2 block">Password</label>
                        <input type="password" name="password" class="w-full p-4 rounded-2xl bg-slate-50 border-2 border-slate-50 focus:border-red-600 outline-none font-bold" placeholder="••••••••" required>
                    </div>
                    <button type="submit" name="login" class="w-full bg-red-600 hover:bg-red-700 text-white font-black py-5 rounded-2xl uppercase italic shadow-red-200 shadow-xl transition active:scale-95 text-xs tracking-widest">Akses Dashboard <i class="fas fa-sign-in-alt ml-2"></i></button>
                </form>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <footer class="bg-slate-900 text-white py-16 px-6 border-t-8 border-yellow-400">
        <div class="max-w-6xl mx-auto flex flex-col md:flex-row justify-between items-center gap-12">
            <div class="text-center md:text-left">
                <h3 class="text-2xl font-black italic uppercase tracking-tighter mb-2 leading-none text-yellow-400">Smart Arca Music</h3>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest italic mb-6">Weleri Kendal Professional Music Education</p>
                <div class="space-y-2">
                    <p class="text-[10px] text-slate-400 uppercase font-bold flex items-center gap-2 justify-center md:justify-start">
                        <i class="fas fa-map-marker-alt text-red-600"></i> Jl. Tamtama, Sekepel, Penyangkringan, Weleri, Kendal
                    </p>
                    <p class="text-[10px] text-slate-400 uppercase font-bold flex items-center gap-2 justify-center md:justify-start">
                        <i class="fab fa-whatsapp text-green-500"></i> Admin Fia: +62 895-3607-96038
                    </p>
                </div>
            </div>
            <div class="text-center md:text-right">
                <p class="text-[10px] font-black uppercase text-slate-500 mb-6 tracking-widest">Informasi Pendaftaran</p>
                <a href="https://wa.me/62895360796038" target="_blank" class="bg-red-700 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase shadow-xl hover:bg-red-800 transition inline-flex items-center gap-3">
                    <i class="fas fa-paper-plane"></i> Daftar via WhatsApp
                </a>
                <p class="mt-10 text-[9px] text-slate-600 font-bold uppercase tracking-[0.3em]">&copy; 2026 Smart Arca Music School. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>
