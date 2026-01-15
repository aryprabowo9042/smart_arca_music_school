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
        .glass { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
        .swiper { width: 100%; height: 500px; border-radius: 2.5rem; }
        .swiper-slide { background: #eee; }
        .swiper-slide img { width: 100%; height: 100%; object-fit: cover; }
        .swiper-button-next, .swiper-button-prev { color: #dc2626 !important; background: rgba(255,255,255,0.8); width: 40px; height: 40px; border-radius: 50%; }
        .swiper-button-next:after, .swiper-button-prev:after { font-size: 18px; font-weight: bold; }
        .swiper-pagination-bullet-active { background: #facc15 !important; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">

    <nav class="fixed w-full z-50 top-0 px-4 py-4">
        <div class="max-w-7xl mx-auto glass rounded-2xl shadow-xl border-b-4 border-yellow-400 px-6 py-3 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <img src="api/logo.png" alt="Logo" class="h-12 w-auto object-contain" 
                     onerror="this.src='https://ui-avatars.com/api/?name=SA&background=ef4444&color=facc15&bold=true'">
                <div class="hidden md:block">
                    <h1 class="font-extrabold text-xl tracking-tighter text-red-600">SMART ARCA</h1>
                    <p class="text-[9px] font-bold text-yellow-500 tracking-[0.2em] uppercase">Music School</p>
                </div>
            </div>
            <a href="api/admin/login.php" class="bg-red-600 hover:bg-red-700 text-yellow-400 px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg flex items-center gap-2 border-b-4 border-red-800 transition-all">
                <i class="fas fa-user-circle"></i> LOGIN
            </a>
        </div>
    </nav>

    <section class="relative min-h-screen flex items-center justify-center pt-32 pb-20 overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center">
            
            <div class="space-y-8 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 bg-red-100 text-red-600 px-4 py-2 rounded-full text-xs font-bold tracking-wide">
                    <i class="fas fa-music text-yellow-500"></i> SMART ARCA MUSIC SCHOOL
                </div>
                
                <h1 class="text-5xl md:text-7xl font-black text-slate-900 leading-[1.1]">
                    Wujudkan Bakat <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-yellow-500">
                        Musisimu!
                    </span>
                </h1>
                
                <p class="text-lg text-slate-600 max-w-lg mx-auto lg:mx-0 font-medium">
                    Belajar musik lebih menyenangkan dengan fasilitas lengkap dan pengajar berpengalaman.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="https://wa.me/62895360796038" target="_blank" class="bg-yellow-400 hover:bg-yellow-500 text-red-800 px-8 py-4 rounded-2xl font-extrabold text-lg shadow-2xl transition-all flex items-center justify-center gap-3 border-b-4 border-yellow-600">
                        <i class="fab fa-whatsapp text-2xl"></i>
                        HUBUNGI FIA
                    </a>
                </div>
            </div>

            <div class="relative w-full h-[500px]">
                <div class="absolute -inset-4 bg-red-600 rounded-[3rem] rotate-3 opacity-10"></div>
                <div class="swiper mySwiper shadow-2xl border-4 border-white">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <img src="https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?q=80&w=2000&auto=format&fit=crop" alt="Studio Musik">
                        </div>
                        <div class="swiper-slide">
                            <img src="https://images.unsplash.com/photo-1520523839897-bd0b52f945a0?q=80&w=2000&auto=format&fit=crop" alt="Kelas Piano">
                        </div>
                        <div class="swiper-slide">
                            <img src="https://images.unsplash.com/photo-1543443258-92b04ad5ec6b?q=80&w=2000&auto=format&fit=crop" alt="Kelas Drum">
                        </div>
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
            <h2 class="text-3xl md:text-5xl font-black mb-16">Program <span class="text-red-600">Unggulan Kami</span></h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="group bg-slate-50 p-8 rounded-[2rem] border-b-8 border-red-600 hover:border-yellow-400 hover:bg-white transition-all shadow-sm hover:shadow-xl text-left">
                    <div class="w-14 h-14 bg-red-600 text-white rounded-xl flex items-center justify-center text-xl mb-6 shadow-lg">
                        <i class="fas fa-drum"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Drum</h3>
                    <p class="text-slate-500 text-xs">Asah ketukan dan ritme dalam kelas drum modern.</p>
                </div>

                <div class="group bg-slate-50 p-8 rounded-[2rem] border-b-8 border-red-600 hover:border-yellow-400 hover:bg-white transition-all shadow-sm hover:shadow-xl text-left">
                    <div class="w-14 h-14 bg-yellow-500 text-white rounded-xl flex items-center justify-center text-xl mb-6 shadow-lg">
                        <i class="fas fa-guitar"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Gitar & Bass</h3>
                    <p class="text-slate-500 text-xs">Teknik petikan dan melodi untuk semua genre musik.</p>
                </div>

                <div class="group bg-slate-50 p-8 rounded-[2rem] border-b-8 border-red-600 hover:border-yellow-400 hover:bg-white transition-all shadow-sm hover:shadow-xl text-left">
                    <div class="w-14 h-14 bg-red-500 text-white rounded-xl flex items-center justify-center text-xl mb-6 shadow-lg">
                        <i class="fas fa-keyboard"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Piano & Organ</h3>
                    <p class="text-slate-500 text-xs">Kuasai teknik piano klasik hingga kontemporer.</p>
                </div>

                <div class="group bg-slate-50 p-8 rounded-[2rem] border-b-8 border-red-600 hover:border-yellow-400 hover:bg-white transition-all shadow-sm hover:shadow-xl text-left">
                    <div class="w-14 h-14 bg-slate-800 text-white rounded-xl flex items-center justify-center text-xl mb-6 shadow-lg">
                        <i class="fas fa-microphone-alt"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Vokal</h3>
                    <p class="text-slate-500 text-xs">Olah vokal dan karakter suara untuk performa maksimal.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-slate-900 text-white pt-20 pb-10">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <h2 class="text-3xl font-black mb-2 italic tracking-tighter">SMART ARCA <span class="text-yellow-400">MUSIC SCHOOL</span></h2>
            <p class="text-slate-500 text-sm mb-10 italic underline">Hubungi Fia: +62 895-3607-96038</p>
            <div class="pt-10 border-t border-white/5 text-[10px] text-slate-600 font-bold uppercase tracking-[0.4em]">
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
