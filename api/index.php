<?php
// 1. CEK STATUS LOGIN UNTUK TOMBOL DINAMIS
$is_logged_in = isset($_COOKIE['user_role']);
$user_role = $_COOKIE['user_role'] ?? '';
$dashboard_link = "#";

if ($is_logged_in) {
    // Jalur absolut diawali '/' agar tidak terjadi duplikasi folder saat diklik
    if ($user_role == 'admin') $dashboard_link = "/api/admin/honor.php";
    elseif ($user_role == 'guru') $dashboard_link = "/api/guru/index.php";
    elseif ($user_role == 'murid') $dashboard_link = "/api/murid/index.php";
}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Arca Music School</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
        .swiper { width: 100%; height: 500px; border-radius: 2.5rem; }
        .swiper-slide img { width: 100%; height: 100%; object-fit: cover; }
        .text-gradient { background: linear-gradient(to r, #dc2626, #facc15); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">

    <nav class="fixed w-full z-50 top-0 px-4 py-4">
        <div class="max-w-7xl mx-auto glass rounded-[2rem] shadow-2xl border-b-4 border-yellow-400 px-6 py-3 flex justify-between items-center transition-all">
            <div class="flex items-center gap-3">
                <img src="/api/logo.png" alt="Logo" class="h-12 w-auto" 
                     onerror="this.src='https://ui-avatars.com/api/?name=SA&background=ef4444&color=facc15&bold=true'">
                <div class="hidden sm:block">
                    <h1 class="font-extrabold text-xl tracking-tighter text-red-600 leading-none uppercase italic">Smart Arca</h1>
                    <p class="text-[9px] font-bold text-yellow-500 tracking-[0.2em] uppercase">Music School</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <?php if ($is_logged_in): ?>
                    <a href="<?php echo $dashboard_link; ?>" class="bg-red-600 hover:bg-red-700 text-yellow-400 px-6 py-2.5 rounded-2xl font-black text-xs shadow-lg transition flex items-center gap-2 uppercase">
                        <i class="fas fa-th-large"></i> Dashboard
                    </a>
                    <a href="/api/logout.php" class="bg-slate-100 hover:bg-slate-200 text-slate-600 p-2.5 rounded-2xl transition">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                <?php else: ?>
                    <a href="/api/admin/login.php" class="bg-red-600 hover:bg-red-700 text-yellow-400 px-8 py-2.5 rounded-2xl font-black text-xs shadow-lg transition flex items-center gap-2 border-b-4 border-red-800 uppercase">
                        <i class="fas fa-user-circle text-lg"></i> Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <section class="relative min-h-screen flex items-center pt-24 pb-12 overflow-hidden">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-red-600/10 rounded-full blur-[100px]"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-yellow-400/10 rounded-full blur-[100px]"></div>

        <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-8 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 bg-red-50 text-red-600 px-5 py-2 rounded-full text-xs font-black tracking-widest shadow-sm border border-red-100">
                    <i class="fas fa-music text-yellow-500 animate-bounce"></i> PENDAFTARAN DIBUKA
                </div>
                
                <h1 class="text-5xl md:text-8xl font-black text-slate-900 leading-[1.1] tracking-tighter uppercase italic">
                    Smart Arca <br>
                    <span class="text-gradient">Music School</span>
                </h1>
                
                <p class="text-lg text-slate-500 max-w-lg mx-auto lg:mx-0 font-medium leading-relaxed">
                    Wujudkan impian musisimu bersama kami. Belajar piano, gitar, drum, dan vokal dengan metode asyik dan pengajar profesional.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="https://wa.me/62895360796038" target="_blank" class="bg-yellow-400 hover:bg-yellow-500 text-red-700 px-10 py-5 rounded-[2rem] font-black text-xl shadow-2xl transition transform hover:-translate-y-2 flex items-center justify-center gap-3 border-b-8 border-yellow-600 uppercase">
                        <i class="fab fa-whatsapp text-3xl"></i>
                        Hubungi Fia
                    </a>
                    <a href="#program" class="bg-white hover:bg-slate-50 text-slate-700 px-10 py-5 rounded-[2rem] font-bold text-xl shadow-lg border border-slate-200 flex items-center justify-center uppercase">
                        Lihat Program
                    </a>
                </div>
            </div>

            <div class="relative w-full group">
                <div class="absolute -inset-6 bg-red-600 rounded-[4rem] rotate-3 opacity-10 group-hover:rotate-0 transition-all duration-700"></div>
                <div class="swiper mySwiper shadow-2xl border-[10px] border-white relative z-10">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <img src="https://images.unsplash.com/photo-1520523839897-bd0b52f945a0?q=80&w=1200" alt="Piano Class">
                        </div>
                        <div class="swiper-slide">
                            <img src="https://images.unsplash.com/photo-1543443258-92b04ad5ec6b?q=80&w=1200" alt="Drum Class">
                        </div>
                        <div class="swiper-slide">
                            <img src="https://images.unsplash.com/photo-1525201548942-d8b8967d0f52?q=80&w=1200" alt="Guitar Class">
                        </div>
                    </div>
                    <div class="swiper-button-next !text-red-600"></div>
                    <div class="swiper-button-prev !text-red-600"></div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </div>
    </section>

    <section id="program" class="py-24 bg-white border-t-8 border-yellow-400 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <h2 class="text-4xl md:text-6xl font-black mb-16 italic tracking-tighter uppercase">Program <span class="text-red-600 underline decoration-yellow-400 decoration-8">Unggulan</span></h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-slate-50 p-10 rounded-[3rem] border-b-[12px] border-red-600 shadow-xl hover:bg-white hover:-translate-y-4 transition-all duration-500 group text-center">
                    <div class="w-20 h-20 bg-red-600 text-white rounded-[1.5rem] flex items-center justify-center text-3xl mb-8 shadow-xl group-hover:rotate-12 transition-all mx-auto">
                        <i class="fas fa-drum"></i>
                    </div>
                    <h3 class="text-2xl font-black mb-3 uppercase italic">Drum</h3>
                    <p class="text-slate-500 text-sm font-medium leading-relaxed">Asah ketangkasan ritme dan beat dalam kelas drum modern yang energik.</p>
                </div>

                <div class="bg-slate-50 p-10 rounded-[3rem] border-b-[12px] border-yellow-400 shadow-xl hover:bg-white hover:-translate-y-4 transition-all duration-500 group text-center">
                    <div class="w-20 h-20 bg-yellow-400 text-red-700 rounded-[1.5rem] flex items-center justify-center text-3xl mb-8 shadow-xl group-hover:rotate-12 transition-all mx-auto">
                        <i class="fas fa-guitar"></i>
                    </div>
                    <h3 class="text-2xl font-black mb-3 uppercase italic">Gitar</h3>
                    <p class="text-slate-500 text-sm font-medium leading-relaxed">Pelajari melodi dan teknik petikan dari klasik hingga rock and roll.</p>
                </div>

                <div class="bg-slate-50 p-10 rounded-[3rem] border-b-[12px] border-red-600 shadow-xl hover:bg-white hover:-translate-y-4 transition-all duration-500 group text-center">
                    <div class="w-20 h-20 bg-red-600 text-white rounded-[1.5rem] flex items-center justify-center text-3xl mb-8 shadow-xl group-hover:rotate-12 transition-all mx-auto">
                        <i class="fas fa-keyboard"></i>
                    </div>
                    <h3 class="text-2xl font-black mb-3 uppercase italic">Piano</h3>
                    <p class="text-slate-500 text-sm font-medium leading-relaxed">Sentuhan jari yang elegan untuk menguasai tuts piano klasik dan pop.</p>
                </div>

                <div class="bg-slate-50 p-10 rounded-[3rem] border-b-[12px] border-yellow-400 shadow-xl hover:bg-white hover:-translate-y-4 transition-all duration-500 group text-center">
                    <div class="w-20 h-20 bg-slate-800 text-white rounded-[1.5rem] flex items-center justify-center text-3xl mb-8 shadow-xl group-hover:rotate-12 transition-all mx-auto">
                        <i class="fas fa-microphone-alt"></i>
                    </div>
                    <h3 class="text-2xl font-black mb-3 uppercase italic">Vokal</h3>
                    <p class="text-slate-500 text-sm font-medium leading-relaxed">Temukan karakter suaramu dan tampil percaya diri di atas panggung.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-slate-900 text-white pt-24 pb-12 border-t-[16px] border-red-600">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <h2 class="text-4xl font-black italic tracking-tighter mb-4 uppercase">Smart Arca <span class="text-yellow-400 font-normal underline decoration-red-600 underline-offset-8 decoration-4">Music School</span></h2>
            <p class="text-slate-500 text-sm font-bold mb-12 uppercase tracking-[0.5em]">Kendal, Jawa Tengah</p>
            
            <div class="flex justify-center gap-6 mb-16">
                <a href="#" class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center text-2xl hover:bg-red-600 transition-all"><i class="fab fa-instagram text-white"></i></a>
                <a href="#" class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center text-2xl hover:bg-red-600 transition-all"><i class="fab fa-youtube text-white"></i></a>
                <a href="https://wa.me/62895360796038" class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center text-2xl hover:bg-green-600 transition-all"><i class="fab fa-whatsapp text-white"></i></a>
            </div>

            <div class="border-t border-white/5 pt-10 text-[10px] text-slate-600 font-bold tracking-[0.6em] uppercase">
                &copy; <?php echo date('Y'); ?> SMART ARCA. ALL RIGHTS RESERVED.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        var swiper = new Swiper(".mySwiper", {
            loop: true,
            autoplay: { delay: 4000, disableOnInteraction: false },
            pagination: { el: ".swiper-pagination", clickable: true },
            navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
        });
    </script>
</body>
</html>
