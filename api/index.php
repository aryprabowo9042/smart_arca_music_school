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
    </style>
</head>
<body class="bg-white" x-data="{ openModal: null }">

    <nav class="bg-red-700 text-white py-4 px-6 shadow-xl border-b-4 border-yellow-400 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="bg-yellow-400 text-red-700 w-10 h-10 rounded-lg flex items-center justify-center text-xl shadow-md">
                    <i class="fas fa-music"></i>
                </div>
                <div>
                    <h1 class="font-black italic uppercase text-lg leading-none tracking-tighter">Smart Arca</h1>
                    <p class="text-[10px] font-bold text-red-200 uppercase tracking-widest">Weleri - Kendal</p>
                </div>
            </div>
            <div class="flex gap-4">
                <?php if($is_login): ?>
                    <a href="<?php echo $role; ?>/index.php" class="bg-yellow-400 text-red-800 px-6 py-2 rounded-full font-black text-xs uppercase shadow-lg transition flex items-center gap-2">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                <?php else: ?>
                    <a href="#login-section" class="bg-yellow-400 text-red-800 px-8 py-3 rounded-full font-black text-xs uppercase shadow-lg tracking-widest">Login Portal</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <header class="py-20 px-6 bg-gradient-to-b from-red-50 to-white text-center">
        <div class="max-w-4xl mx-auto">
            <span class="inline-block bg-red-100 text-red-600 px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-[0.2em] mb-6">Weleri Kendal Music School</span>
            <h1 class="text-5xl md:text-7xl font-black text-slate-900 italic uppercase tracking-tighter mb-6 leading-tight">Wujudkan <span class="text-red-600">Mimpimu</span><br>Lewat <span class="text-yellow-500">Nada & Irama</span></h1>
            
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6 text-left max-w-2xl mx-auto">
                <div class="bg-white p-5 rounded-3xl shadow-lg border-l-8 border-red-600">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 leading-none">Lokasi Sekolah:</p>
                    <p class="text-xs font-bold text-slate-700 leading-relaxed uppercase italic">Jl. Tamtama, Sekepel, Penyangkringan, Weleri, Kendal</p>
                    <a href="https://www.google.com/maps/search/?api=1&query=Jl.+Tamtama,+Sekepel,+Penyangkringan,+Weleri,+Kendal" target="_blank" class="text-[9px] font-black text-red-600 uppercase mt-2 block">Buka Google Maps <i class="fas fa-external-link-alt ml-1"></i></a>
                </div>
                <div class="bg-white p-5 rounded-3xl shadow-lg border-l-8 border-yellow-400 flex flex-col justify-center">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 leading-none">Admin (Fia):</p>
                    <a href="https://wa.me/62895360796038" target="_blank" class="text-indigo-600 font-black italic flex items-center gap-2 text-sm uppercase">
                        <i class="fab fa-whatsapp text-green-500 text-xl"></i> +62 895-3607-96038
                    </a>
                </div>
            </div>
        </div>
    </header>

    <section class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-black text-slate-800 uppercase italic tracking-tighter mb-2">Program Kursus Unggulan</h2>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Pilih Instrumen dan Lihat Silabus Pembelajaran Kami</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-8">
                <button @click="openModal = 'drum'" class="bg-white border-2 border-slate-100 p-8 rounded-[2.5rem] shadow-xl hover:border-red-600 hover:-translate-y-2 transition duration-300 text-center group">
                    <div class="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center text-red-600 text-2xl mb-4 mx-auto group-hover:bg-red-600 group-hover:text-white transition shadow-inner"><i class="fas fa-drum"></i></div>
                    <h4 class="text-sm font-black uppercase italic text-slate-800">Kelas Drum</h4>
                    <p class="text-[9px] font-bold text-slate-400 uppercase mt-1">Cek Silabus <i class="fas fa-arrow-right ml-1"></i></p>
                </button>

                <button @click="openModal = 'keyboard'" class="bg-white border-2 border-slate-100 p-8 rounded-[2.5rem] shadow-xl hover:border-yellow-400 hover:-translate-y-2 transition duration-300 text-center group">
                    <div class="w-16 h-16 bg-yellow-100 rounded-2xl flex items-center justify-center text-yellow-600 text-2xl mb-4 mx-auto group-hover:bg-yellow-400 group-hover:text-white transition shadow-inner"><i class="fas fa-keyboard"></i></div>
                    <h4 class="text-sm font-black uppercase italic text-slate-800">Kelas Keyboard</h4>
                    <p class="text-[9px] font-bold text-slate-400 uppercase mt-1">Cek Silabus <i class="fas fa-arrow-right ml-1"></i></p>
                </button>

                <button @click="openModal = 'akustik'" class="bg-white border-2 border-slate-100 p-8 rounded-[2.5rem] shadow-xl hover:border-red-600 hover:-translate-y-2 transition duration-300 text-center group">
                    <div class="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center text-red-600 text-2xl mb-4 mx-auto group-hover:bg-red-600 group-hover:text-white transition shadow-inner"><i class="fas fa-guitar"></i></div>
                    <h4 class="text-sm font-black uppercase italic text-slate-800">Gitar Akustik</h4>
                    <p class="text-[9px] font-bold text-slate-400 uppercase mt-1">Cek Silabus <i class="fas fa-arrow-right ml-1"></i></p>
                </button>

                <button @click="openModal = 'elektrik'" class="bg-white border-2 border-slate-100 p-8 rounded-[2.5rem] shadow-xl hover:border-yellow-400 hover:-translate-y-2 transition duration-300 text-center group">
                    <div class="w-16 h-16 bg-yellow-100 rounded-2xl flex items-center justify-center text-yellow-600 text-2xl mb-4 mx-auto group-hover:bg-yellow-400 group-hover:text-white transition shadow-inner"><i class="fas fa-bolt text-red-600"></i></div>
                    <h4 class="text-sm font-black uppercase italic text-slate-800">Gitar Elektrik</h4>
                    <p class="text-[9px] font-bold text-slate-400 uppercase mt-1">Cek Silabus <i class="fas fa-arrow-right ml-1"></i></p>
                </button>

                <button @click="openModal = 'bas'" class="bg-white border-2 border-slate-100 p-8 rounded-[2.5rem] shadow-xl hover:border-red-600 hover:-translate-y-2 transition duration-300 text-center group">
                    <div class="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center text-red-600 text-2xl mb-4 mx-auto group-hover:bg-red-600 group-hover:text-white transition shadow-inner"><i class="fas fa-wave-square"></i></div>
                    <h4 class="text-sm font-black uppercase italic text-slate-800">Kelas Bas</h4>
                    <p class="text-[9px] font-bold text-slate-400 uppercase mt-1">Cek Silabus <i class="fas fa-arrow-right ml-1"></i></p>
                </button>

                <button @click="openModal = 'vokal'" class="bg-white border-2 border-slate-100 p-8 rounded-[2.5rem] shadow-xl hover:border-yellow-400 hover:-translate-y-2 transition duration-300 text-center group">
                    <div class="w-16 h-16 bg-yellow-100 rounded-2xl flex items-center justify-center text-yellow-600 text-2xl mb-4 mx-auto group-hover:bg-yellow-400 group-hover:text-white transition shadow-inner"><i class="fas fa-microphone-alt"></i></div>
                    <h4 class="text-sm font-black uppercase italic text-slate-800">Kelas Vokal</h4>
                    <p class="text-[9px] font-bold text-slate-400 uppercase mt-1">Cek Silabus <i class="fas fa-arrow-right ml-1"></i></p>
                </button>
            </div>
        </div>
    </section>

    <template x-if="openModal">
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-6 modal-bg" @click.self="openModal = null">
            <div class="bg-white w-full max-w-2xl rounded-[3rem] shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-300">
                <div class="bg-red-700 p-8 text-white flex justify-between items-center border-b-4 border-yellow-400">
                    <div>
                        <h3 class="text-2xl font-black italic uppercase tracking-tighter" x-text="'Silabus ' + openModal"></h3>
                        <p class="text-[10px] font-bold text-red-200 uppercase tracking-widest leading-none mt-1">Kurikulum Smart Arca Music School</p>
                    </div>
                    <button @click="openModal = null" class="bg-white/10 hover:bg-white/20 w-10 h-10 rounded-full flex items-center justify-center"><i class="fas fa-times"></i></button>
                </div>
                <div class="p-8 max-h-[60vh] overflow-y-auto bg-slate-50">
                    <div class="space-y-6 text-slate-700 text-sm font-semibold italic uppercase">
                        <template x-if="openModal === 'drum'">
                            <div class="space-y-4">
                                <div class="bg-white p-5 rounded-2xl shadow-sm border-l-4 border-red-600">
                                    <p class="text-red-600 text-[10px] font-black tracking-widest mb-1 leading-none">TINGKAT DASAR (1-12)</p>
                                    <p class="leading-relaxed">Pengenalan drum set, postur, stik control, ritme dasar (Quarter & Eighth Notes), Basic Rock Beat.</p>
                                </div>
                                <div class="bg-white p-5 rounded-2xl shadow-sm border-l-4 border-yellow-400">
                                    <p class="text-yellow-600 text-[10px] font-black tracking-widest mb-1 leading-none">TINGKAT MENENGAH (13-24)</p>
                                    <p class="leading-relaxed">Rudiments (Paradiddle, Flam), Teknik pedal, Hi-hat control, Fill-in variasi, Sinkopasi ritme.</p>
                                </div>
                            </div>
                        </template>

                        <template x-if="openModal === 'keyboard'">
                            <div class="space-y-4">
                                <div class="bg-white p-5 rounded-2xl shadow-sm border-l-4 border-red-600">
                                    <p class="text-red-600 text-[10px] font-black tracking-widest mb-1 leading-none">TINGKAT DASAR (12 SESI)</p>
                                    <p class="leading-relaxed">Pengenalan tuts, posisi tubuh, notasi balok dasar, kunci G & F, Akor mayor (C, G, F).</p>
                                </div>
                                <div class="bg-white p-5 rounded-2xl shadow-sm border-l-4 border-yellow-400">
                                    <p class="text-yellow-600 text-[10px] font-black tracking-widest mb-1 leading-none">TINGKAT MENENGAH</p>
                                    <p class="leading-relaxed">Variasi akor, kelincahan jari, teori musik mendalam, repertoar lagu modern & klasik.</p>
                                </div>
                            </div>
                        </template>

                        <template x-if="openModal === 'akustik'">
                            <div class="space-y-4">
                                <div class="bg-white p-5 rounded-2xl shadow-sm border-l-4 border-red-600">
                                    <p class="text-red-600 text-[10px] font-black tracking-widest mb-1 leading-none">DASAR POP GITAR</p>
                                    <p class="leading-relaxed">Akor dasar (G,C,D,Em,Am,E), Pola Strumming 4/4, Perpindahan akor bersih, Lagu pop sederhana.</p>
                                </div>
                                <div class="bg-white p-5 rounded-2xl shadow-sm border-l-4 border-yellow-400">
                                    <p class="text-yellow-600 text-[10px] font-black tracking-widest mb-1 leading-none">LANJUTAN</p>
                                    <p class="leading-relaxed">Fingerstyle dasar, Harmonics, Bending, Aransemen intro/outro lagu pop, Transpose kunci.</p>
                                </div>
                            </div>
                        </template>

                        <template x-if="openModal === 'elektrik'">
                            <div class="space-y-4">
                                <div class="bg-white p-5 rounded-2xl shadow-sm border-l-4 border-red-600">
                                    <p class="text-red-600 text-[10px] font-black tracking-widest mb-1 leading-none">DASAR ELEKTRIK</p>
                                    <p class="leading-relaxed">Pengenalan Amp & Efek, Power Chords, Palm Muting, Membaca TAB, Lead guitar dasar.</p>
                                </div>
                                <div class="bg-white p-5 rounded-2xl shadow-sm border-l-4 border-yellow-400">
                                    <p class="text-yellow-600 text-[10px] font-black tracking-widest mb-1 leading-none">TEKNIK LANJUTAN</p>
                                    <p class="leading-relaxed">Sweep Picking, Tapping, Harmonics, Skala Pentatonik, Improvisasi Blues/Rock/Metal.</p>
                                </div>
                            </div>
                        </template>

                        <template x-if="openModal === 'bas'">
                            <div class="space-y-4">
                                <div class="bg-white p-5 rounded-2xl shadow-sm border-l-4 border-red-600">
                                    <p class="text-red-600 text-[10px] font-black tracking-widest mb-1 leading-none">FONDASI BAS (1-16)</p>
                                    <p class="leading-relaxed">Postur & Plucking, Notasi ritme, Skala Mayor/Minor, Walking Bass dasar, Arpeggio.</p>
                                </div>
                                <div class="bg-white p-5 rounded-2xl shadow-sm border-l-4 border-yellow-400">
                                    <p class="text-yellow-600 text-[10px] font-black tracking-widest mb-1 leading-none">ADVANCED GROOVE</p>
                                    <p class="leading-relaxed">Teknik Slapping & Popping, Harmonics bas, Sound production (efek), improvisasi bass line.</p>
                                </div>
                            </div>
                        </template>

                        <template x-if="openModal === 'vokal'">
                            <div class="space-y-4">
                                <div class="bg-white p-5 rounded-2xl shadow-sm border-l-4 border-red-600">
                                    <p class="text-red-600 text-[10px] font-black tracking-widest mb-1 leading-none">TEKNIK DASAR (12 SESI)</p>
                                    <p class="leading-relaxed">Pernapasan Diafragma, Postur tubuh, Pitch & Intonasi, Resonansi dasar, Kesehatan vokal.</p>
                                </div>
                                <div class="bg-white p-5 rounded-2xl shadow-sm border-l-4 border-yellow-400">
                                    <p class="text-yellow-600 text-[10px] font-black tracking-widest mb-1 leading-none">EKSPRESI & GAYA</p>
                                    <p class="leading-relaxed">Artikulasi & Diksi, Dinamika vokal, Vibrato, Interpretasi lagu, Percaya diri panggung.</p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="p-6 bg-white border-t text-center">
                    <a href="https://wa.me/62895360796038" target="_blank" class="bg-red-600 text-white px-8 py-3 rounded-2xl font-black uppercase text-[10px] shadow-lg tracking-widest block">Konsultasi Gratis Sekarang</a>
                </div>
            </div>
        </div>
    </template>

    <?php if(!$is_login): ?>
    <section id="login-section" class="py-20 bg-slate-100">
        <div class="max-w-md mx-auto px-6">
            <div class="bg-white p-10 rounded-[3rem] shadow-2xl border-b-8 border-yellow-400">
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-black text-slate-800 uppercase italic tracking-tighter mb-1 leading-none">Login Portal</h2>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic">Akses Khusus Siswa & Guru</p>
                </div>
                
                <?php if($error): ?>
                    <div class="bg-red-50 text-red-600 p-4 rounded-2xl mb-6 text-xs font-bold border border-red-100 italic flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-3 mb-2 block tracking-widest leading-none">Username</label>
                        <input type="text" name="username" class="w-full p-4 rounded-2xl bg-slate-50 border-2 border-slate-50 focus:border-red-600 outline-none font-bold italic" required>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-3 mb-2 block tracking-widest leading-none">Password</label>
                        <input type="password" name="password" class="w-full p-4 rounded-2xl bg-slate-50 border-2 border-slate-50 focus:border-red-600 outline-none font-bold italic" required>
                    </div>
                    <button type="submit" name="login" class="w-full bg-red-600 text-white font-black py-5 rounded-2xl uppercase italic shadow-xl transition active:scale-95 text-[11px] tracking-widest">Akses Dashboard <i class="fas fa-sign-in-alt ml-2"></i></button>
                </form>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <footer class="bg-slate-900 text-white py-16 px-6 border-t-8 border-yellow-400">
        <div class="max-w-6xl mx-auto flex flex-col md:flex-row justify-between items-center gap-12">
            <div class="text-center md:text-left">
                <h3 class="text-2xl font-black italic uppercase tracking-tighter mb-2 text-yellow-400 leading-none">Smart Arca Music</h3>
                <p class="text-[10px] text-slate-400 uppercase font-bold tracking-[0.2em] leading-none mb-6 italic">Professional Music Education Weleri</p>
                <p class="text-[10px] text-slate-500 uppercase font-bold max-w-sm leading-relaxed">Jl. Tamtama, Sekepel, Penyangkringan, Weleri, Kendal, Jawa Tengah 51355</p>
            </div>
            <div class="text-center md:text-right">
                <p class="text-[10px] font-black uppercase text-slate-500 mb-6 tracking-widest">Daftar Sekarang Juga!</p>
                <a href="https://wa.me/62895360796038" target="_blank" class="bg-red-700 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase shadow-xl hover:bg-red-800 transition inline-flex items-center gap-3 italic">
                    <i class="fab fa-whatsapp"></i> Chat Admin (Mbak Fia)
                </a>
            </div>
        </div>
    </footer>

</body>
</html>
