<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Arca Music School</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(15px); }
        .swiper { width: 100%; height: 500px; border-radius: 2.5rem; }
        .swiper-slide img { width: 100%; height: 100%; object-fit: cover; }
        .hero-title { line-height: 1.1; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">

    <nav class="fixed w-full z-50 top-0 px-4 py-4">
        <div class="max-w-7xl mx-auto glass rounded-3xl shadow-2xl border-b-4 border-yellow-400 px-6 py-3 flex justify-between items-center">
            
            <div class="flex items-center gap-4">
                <div class="h-14 w-14 bg-white rounded-xl shadow-inner flex items-center justify-center overflow-hidden border border-gray-100">
                    <img src="logo.png" 
                         onerror="this.onerror=null; this.src='api/logo.png'; this.setAttribute('onerror', 'this.src=\'https://ui-avatars.com/api/?name=SA&background=ef4444&color=facc15&bold=true\'')" 
                         alt="Logo" 
                         class="h-full w-full object-contain p-1">
                </div>
                <div>
                    <h1 class="font-extrabold text-xl tracking-tighter text-red-600 leading-none">SMART ARCA</h1>
                    <p class="text-[9px] font-bold text-yellow-500 tracking-[0.2em] uppercase leading-none mt-1">Music School</p>
                </div>
            </div>

            <a href="api/admin/login.php" class="bg-red-600 hover:bg-red-700 text-yellow-400 px-6 py-2.5 rounded-2xl font-bold text-xs shadow-lg border-b-4 border-red-800 transition-all flex items-center gap-2">
                <i class="fas fa-sign-in-alt"></i> LOGIN
            </a>
        </div>
    </nav>

    <section class="relative min-h-screen flex items-center pt-24 pb-12 overflow-hidden">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-red-600/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-yellow-400/10 rounded-full blur-3xl"></div>

        <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center">
            
            <div class="space-y-8 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 bg-red-50 text-red-600 px-5 py-2 rounded-full text-xs font-black tracking-widest shadow-sm border border-red-100 uppercase">
                    <i class="fas fa-award text-yellow-500"></i> Smart Arca Music School
                </div>
                
                <h1 class="text-5xl md:text-7xl font-extrabold text-slate-900 hero-title">
                    Wujudkan Bakat <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-yellow-500">
                        Musisimu!
                    </span>
                </h1>
                
                <p class="text-lg text-slate-500 max-w-lg mx-auto lg:mx-0 font-medium leading-relaxed">
                    Belajar musik lebih seru dengan pengajar ahli dan kurikulum internasional. Pilih instrumenmu sekarang!
                </p>

                <div class="flex justify-center lg:justify-start">
                    <a href="https://wa.me/62895360796038" target="_blank" class="bg-yellow-400 hover:bg-yellow-500 text-red-700 px-10 py-5 rounded-[2rem] font-black text-xl shadow-2xl transition-all transform hover:-translate-y-2 flex items-center gap-3 border-b-8 border-yellow-600">
                        <i class="fab fa-whatsapp text-3xl"></i>
                        HUBUNGI FIA
                    </a>
                </div>
            </div>

            <div class="relative w-full">
                <div class="absolute -inset-6 bg-yellow-400 rounded-[3.5rem] rotate-3 opacity-10"></div>
                <div class="swiper mySwiper shadow-2xl border-8 border-white">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide"><img src="https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?q=80&w=2000" alt="Studio"></div>
                        <div class="swiper-slide"><img src="https://images.unsplash.com/photo-1520523839897-bd0b52f945a0?q=80&w=2000" alt="Piano"></div>
                        <div class="swiper-slide"><img src="https://images.unsplash.com/photo-1543443258-92b04ad5ec6b?q=80&w=2000" alt="Drum"></div>
                    </div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </div>
    </section>

    <section id="program" class="py-24 bg-white border-t-8 border-yellow-400">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <h2 class="text-4xl md:text-6xl font-black mb-16 italic tracking-tighter">Program <span class="text-red-600 underline decoration-yellow-400">Unggulan</span></h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-slate-50 p-8 rounded-[2.5rem] border-b-8 border-red-600 shadow-lg hover:shadow-2xl transition-all">
                    <div class="w-16 h-16 bg-red-600 text-white rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-xl"><i class="fas fa-drum"></i></div>
                    <h3 class="text-2xl font-black mb-2 text-slate-800 uppercase">Drum</h3>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-widest">Power & Rhythm</p>
                </div>
                <div class="bg-slate-50 p-8 rounded-[2.5rem] border-b-8 border-yellow-400 shadow-lg hover:shadow-2xl transition-all">
                    <div class="w-16 h-16 bg-yellow-400 text-red-700 rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-xl"><i class="fas fa-guitar"></i></div>
                    <h3 class="text-2xl font-black mb-2 text-slate-800 uppercase">Gitar</h3>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-widest">Technique & Soul</p>
                </div>
                <div class="bg-slate-50 p-8 rounded-[2.5rem] border-b-8 border-red-600 shadow-lg hover:shadow-2xl transition-all">
                    <div class="w-16 h-16 bg-red-600 text-white rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-xl"><i class="fas fa-keyboard"></i></div>
                    <h3 class="text-2xl font-black mb-2 text-slate-800 uppercase">Piano</h3>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-widest">Classic & Jazz</p>
                </div>
                <div class="bg-slate-50 p-8 rounded-[2.5rem] border-b-8 border-yellow-400 shadow-lg hover:shadow-2xl transition-all">
                    <div class="w-16 h-16 bg-slate-800 text-white rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-xl"><i class="fas fa-microphone-alt"></i></div>
                    <h3 class="text-2xl font-black mb-2 text-slate-800 uppercase">Vokal</h3>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-widest">Voice & Character</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-slate-900 text-white py-20 border-t-8 border-red-600">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <h2 class="text-4xl font-black italic tracking-tighter mb-4">SMART ARCA <span class="text-yellow-400">MUSIC SCHOOL</span></h2>
            <p class="text-red-400 text-sm font-bold mb-12 uppercase tracking-[0.3em]">Mencetak Generasi Musisi Berkarakter</p>
            
            <div class="border-t border-white/5 pt-10 text-[10px] text-slate-500 font-bold tracking-[0.5em] uppercase">
                &copy; <?php echo date('Y'); ?> SMART ARCA. ALL RIGHTS RESERVED.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        var swiper = new Swiper(".mySwiper", {
            loop: true,
            autoplay: { delay: 4000 },
            pagination: { el: ".swiper-pagination", clickable: true },
            navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
        });
    </script>
</body>
</html>
