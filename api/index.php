<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Arca Music School</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
        .hero-gradient {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
        }
        .blob {
            position: absolute;
            width: 500px;
            height: 500px;
            background: #facc15;
            filter: blur(80px);
            border-radius: 50%;
            z-index: -1;
            opacity: 0.15;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 overflow-x-hidden">

    <nav class="fixed w-full z-50 top-0 px-4 py-4">
        <div class="max-w-7xl mx-auto glass rounded-2xl shadow-xl border border-white/20 px-6 py-3 flex justify-between items-center border-b-4 border-yellow-400">
            <div class="flex items-center gap-4">
                <div class="relative">
                    <img src="api/logo.png" alt="Logo" class="h-12 w-auto object-contain drop-shadow-md" 
                         onerror="this.src='https://ui-avatars.com/api/?name=SA&background=ef4444&color=facc15&bold=true'">
                </div>
                <div class="hidden md:block">
                    <h1 class="font-extrabold text-xl tracking-tighter text-red-600 leading-none">SMART ARCA</h1>
                    <p class="text-[9px] font-bold text-yellow-500 tracking-[0.2em] uppercase">Music School</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="api/admin/login.php" class="bg-red-600 hover:bg-red-700 text-yellow-400 px-5 py-2.5 rounded-xl font-bold text-sm transition-all shadow-lg flex items-center gap-2 transform hover:scale-105 border-b-4 border-red-800">
                    <i class="fas fa-user-circle text-lg"></i>
                    <span>LOGIN</span>
                </a>
            </div>
        </div>
    </nav>

    <section class="relative min-h-screen flex items-center justify-center pt-20 overflow-hidden">
        <div class="blob -top-20 -left-20"></div>
        <div class="blob bottom-0 right-0" style="background: #dc2626;"></div>

        <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-8 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 bg-red-100 text-red-600 px-4 py-2 rounded-full text-xs font-bold tracking-wide animate-bounce">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-red-600"></span>
                    </span>
                    PENDAFTARAN DIBUKA
                </div>
                
                <h1 class="text-5xl md:text-7xl font-black text-slate-900 leading-[1.1]">
                    Smart Arca <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-yellow-500">
                        Music School
                    </span>
                </h1>
                
                <p class="text-lg text-slate-600 max-w-lg mx-auto lg:mx-0 leading-relaxed font-medium">
                    Temukan potensi musikmu dengan bimbingan profesional. Kami hadir untuk mencetak generasi musisi yang berkarakter dan kompeten.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="https://wa.me/628123456789" class="bg-yellow-400 hover:bg-yellow-500 text-red-800 px-8 py-4 rounded-2xl font-extrabold text-lg shadow-2xl transition-all transform hover:-translate-y-1 flex items-center justify-center gap-3">
                        <i class="fab fa-whatsapp text-2xl"></i>
                        DAFTAR SEKARANG
                    </a>
                    <a href="#program" class="bg-white hover:bg-slate-50 text-slate-700 border-2 border-slate-200 px-8 py-4 rounded-2xl font-bold text-lg transition-all flex items-center justify-center">
                        LIHAT PROGRAM
                    </a>
                </div>
            </div>

            <div class="relative hidden lg:block">
                <div class="absolute -inset-4 bg-yellow-400 rounded-[3rem] rotate-3 opacity-20"></div>
                <img src="https://images.unsplash.com/photo-1514317011159-45ce51582b00?q=80&w=2000&auto=format&fit=crop" 
                     class="relative rounded-[3rem] shadow-2xl border-4 border-white object-cover h-[500px] w-full" alt="Music Education">
                <div class="absolute bottom-10 -left-10 bg-white p-6 rounded-3xl shadow-2xl border-b-4 border-red-600 animate-pulse">
                    <i class="fas fa-music text-3xl text-red-600"></i>
                </div>
            </div>
        </div>
    </section>

    <section id="program" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <h2 class="text-3xl md:text-5xl font-black mb-4">Program <span class="text-red-600">Unggulan</span></h2>
            <div class="w-20 h-2 bg-yellow-400 mx-auto rounded-full mb-16"></div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php 
                $programs = [
                    ['icon' => 'fa-guitar', 'title' => 'Gitar & Bass', 'desc' => 'Pelajari melodi dan ritme bersama mentor gitar handal.'],
                    ['icon' => 'fa-keyboard', 'title' => 'Piano & Organ', 'desc' => 'Kuasai teknik piano klasik hingga modern dengan kurikulum terbaik.'],
                    ['icon' => 'fa-microphone-alt', 'title' => 'Vokal & Paduan Suara', 'desc' => 'Olah karakter vokalmu untuk tampil percaya diri di panggung.']
                ];
                foreach($programs as $p):
                ?>
                <div class="group bg-slate-50 p-8 rounded-[2.5rem] border-2 border-transparent hover:border-yellow-400 hover:bg-white transition-all duration-500 shadow-sm hover:shadow-2xl text-left">
                    <div class="w-16 h-16 bg-red-600 text-yellow-400 rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-lg transform group-hover:rotate-12 transition-all">
                        <i class="fas <?php echo $p['icon']; ?>"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-slate-800 group-hover:text-red-600"><?php echo $p['title']; ?></h3>
                    <p class="text-slate-500 text-sm leading-relaxed"><?php echo $p['desc']; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <footer class="bg-slate-900 text-white pt-20 pb-10 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-red-600 via-yellow-400 to-red-600"></div>
        <div class="max-w-7xl mx-auto px-6 text-center">
            <h2 class="text-3xl font-black mb-2 italic uppercase">Smart Arca <span class="text-yellow-400">Music School</span></h2>
            <p class="text-slate-400 text-sm mb-10">Harmoni masa depan dalam setiap nada.</p>
            
            <div class="flex justify-center gap-4 mb-10">
                <a href="#" class="w-12 h-12 rounded-xl border border-white/10 flex items-center justify-center hover:bg-red-600 transition-all"><i class="fab fa-instagram"></i></a>
                <a href="#" class="w-12 h-12 rounded-xl border border-white/10 flex items-center justify-center hover:bg-red-600 transition-all"><i class="fab fa-youtube"></i></a>
                <a href="#" class="w-12 h-12 rounded-xl border border-white/10 flex items-center justify-center hover:bg-red-600 transition-all"><i class="fab fa-facebook"></i></a>
            </div>

            <div class="pt-10 border-t border-white/5 text-[10px] text-slate-500 font-bold tracking-widest uppercase">
                &copy; <?php echo date('Y'); ?> SMART ARCA. ALL RIGHTS RESERVED.
            </div>
        </div>
    </footer>

</body>
</html>
