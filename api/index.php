<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Arca Music School</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Overlay Hero dengan gradasi Merah ke Kuning (transparan) */
        .hero-bg {
            background-image: linear-gradient(rgba(185, 28, 28, 0.8), rgba(245, 158, 11, 0.6)), url('https://images.unsplash.com/photo-1511379938547-c1f69419868d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="font-sans text-gray-800 bg-white">

    <nav class="bg-white shadow-lg fixed w-full z-50 border-b-4 border-yellow-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                
                <div class="flex-shrink-0 flex items-center gap-3">
                    <?php 
                    // Pastikan file bernama logo.png ada di folder yang sama dengan index.php
                    if(file_exists('logo.png')): 
                    ?>
                        <img class="h-14 w-auto object-contain" src="logo.png" alt="Logo Smart Arca">
                    <?php else: ?>
                        <div class="bg-red-600 text-yellow-400 w-12 h-12 rounded-xl flex items-center justify-center text-2xl shadow-md border-2 border-yellow-400">
                            <i class="fas fa-music"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div>
                        <h1 class="font-black text-2xl text-red-700 leading-none tracking-tighter">SMART ARCA</h1>
                        <p class="text-[10px] text-yellow-500 font-bold tracking-widest uppercase">Music School</p>
                    </div>
                </div>

                <div>
                    <a href="api/admin/login.php" class="bg-red-600 hover:bg-red-700 text-yellow-400 px-6 py-2.5 rounded-full font-bold transition shadow-lg hover:shadow-red-200 flex items-center gap-2 border-2 border-yellow-400">
                        <i class="fas fa-user-circle"></i> Login Portal
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="hero-bg h-screen flex items-center justify-center text-center px-4">
        <div class="max-w-3xl text-white space-y-6 pt-16">
            <span class="bg-yellow-400 text-red-700 px-4 py-1 rounded-full text-xs font-bold uppercase tracking-widest mb-4 inline-block shadow-lg">
                #1 Sekolah Musik Terpercaya
            </span>
            <h1 class="text-5xl md:text-7xl font-black leading-tight drop-shadow-2xl">
                Harmoni Nada dalam <span class="text-yellow-400">Jiwa</span>
            </h1>
            <p class="text-lg text-white font-medium drop-shadow-md">
                Kembangkan bakat musikmu di Smart Arca. Belajar lebih asyik dengan mentor profesional dan fasilitas lengkap.
            </p>
            <div class="flex flex-col md:flex-row gap-4 justify-center mt-10">
                <a href="https://wa.me/628123456789" class="bg-yellow-400 hover:bg-yellow-500 text-red-700 px-8 py-4 rounded-2xl font-black text-lg transition shadow-xl flex items-center justify-center gap-2 transform hover:-translate-y-1">
                    <i class="fab fa-whatsapp text-2xl"></i> HUBUNGI KAMI
                </a>
                <a href="#program" class="bg-white/10 hover:bg-white/20 backdrop-blur-md text-white border-2 border-white/50 px-8 py-4 rounded-2xl font-bold text-lg transition flex items-center justify-center">
                    LIHAT PROGRAM
                </a>
            </div>
        </div>
    </div>

    <div id="program" class="py-24 bg-white relative overflow-hidden">
        <div class="absolute top-0 right-0 -mr-20 bg-yellow-100 w-64 h-64 rounded-full opacity-50"></div>
        
        <div class="max-w-7xl mx-auto px-4 relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-black text-gray-900 italic uppercase">Pilihan <span class="text-red-600">Instrumen</span></h2>
                <div class="w-24 h-2 bg-yellow-400 mx-auto mt-2 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <div class="bg-white p-1 border-2 border-gray-100 rounded-3xl hover:border-red-500 transition-all duration-300 group shadow-sm hover:shadow-2xl">
                    <div class="p-8">
                        <div class="bg-red-50 w-20 h-20 rounded-2xl flex items-center justify-center text-red-600 text-3xl mb-6 group-hover:bg-red-600 group-hover:text-yellow-400 transition-all duration-500 shadow-inner">
                            <i class="fas fa-guitar"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-3 text-gray-800">Gitar & Bass</h3>
                        <p class="text-gray-500 leading-relaxed text-sm">Teknik dasar hingga tingkat lanjut untuk gitar elektrik, akustik, dan bass.</p>
                    </div>
                    <div class="bg-yellow-400 h-3 rounded-b-2xl"></div>
                </div>

                <div class="bg-white p-1 border-2 border-gray-100 rounded-3xl hover:border-red-500 transition-all duration-300 group shadow-sm hover:shadow-2xl">
                    <div class="p-8">
                        <div class="bg-red-50 w-20 h-20 rounded-2xl flex items-center justify-center text-red-600 text-3xl mb-6 group-hover:bg-red-600 group-hover:text-yellow-400 transition-all duration-500 shadow-inner">
                            <i class="fas fa-keyboard"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-3 text-gray-800">Piano & Keyboard</h3>
                        <p class="text-gray-500 leading-relaxed text-sm">Eksplorasi klasik dan modern melalui sentuhan jemari di atas tuts piano.</p>
                    </div>
                    <div class="bg-yellow-400 h-3 rounded-b-2xl"></div>
                </div>

                <div class="bg-white p-1 border-2 border-gray-100 rounded-3xl hover:border-red-500 transition-all duration-300 group shadow-sm hover:shadow-2xl">
                    <div class="p-8">
                        <div class="bg-red-50 w-20 h-20 rounded-2xl flex items-center justify-center text-red- Merah 600 text-3xl mb-6 group-hover:bg-red-600 group-hover:text-yellow-400 transition-all duration-500 shadow-inner">
                            <i class="fas fa-microphone-alt"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-3 text-gray-800">Vokal</h3>
                        <p class="text-gray-500 leading-relaxed text-sm">Olah vokal dan karakter suara untuk menjadi penyanyi yang berkarakter.</p>
                    </div>
                    <div class="bg-yellow-400 h-3 rounded-b-2xl"></div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-red-700 text-white py-16 border-t-8 border-yellow-400">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-black mb-2 tracking-tighter">SMART ARCA <span class="text-yellow-400">MUSIC SCHOOL</span></h2>
            <p class="text-red-200 text-sm mb-10 max-w-md mx-auto italic">"Mencetak musisi berbakat dengan pondasi teknik yang kuat dan hati yang tulus."</p>
            
            <div class="flex justify-center gap-6 mb-10">
                <a href="#" class="text-2xl hover:text-yellow-400 transition"><i class="fab fa-instagram"></i></a>
                <a href="#" class="text-2xl hover:text-yellow-400 transition"><i class="fab fa-facebook"></i></a>
                <a href="#" class="text-2xl hover:text-yellow-400 transition"><i class="fab fa-youtube"></i></a>
            </div>

            <div class="border-t border-red-600 pt-8 text-xs text-red-300 font-bold tracking-widest uppercase">
                &copy; <?php echo date('Y'); ?> SMART ARCA. All Rights Reserved.
            </div>
        </div>
    </footer>

</body>
</html>
