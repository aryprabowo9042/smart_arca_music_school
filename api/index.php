<?php
session_start();

// Cek apakah user sudah login?
if (isset($_SESSION['status']) && $_SESSION['status'] == "login") {
    // Gunakan path absolut dengan / agar tidak masuk ke folder api/admin
    if ($_SESSION['role'] == "admin") {
        header("Location: /admin/index.php");
    } else if ($_SESSION['role'] == "guru") {
        header("Location: /guru/index.php");
    } else if ($_SESSION['role'] == "murid") {
        header("Location: /murid/index.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Arca Music School - Sekolah Musik Weleri</title>
    <link rel="stylesheet" href="/css/landing.css">
    <link rel="icon" href="/images/logo.png" type="image/png">
</head>
<body>

    <header>
        <div class="container">
            <a href="#" class="logo">
                <img src="/images/logo.png" alt="Smart Arca Logo">
                <span>Smart Arca Music</span>
            </a>
            
            <nav>
                <ul>
                    <li><a href="#home">Beranda</a></li>
                    <li><a href="#program">Program Kelas</a></li>
                    <li><a href="#tentang">Tentang Kami</a></li>
                    <li><a href="/login.php" class="btn-login">Login Sistem</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section id="home" class="hero">
        <div class="container">
            <h1>Wujudkan Mimpi Musikmu</h1>
            <p>Bergabunglah dengan sekolah musik terbaik di Kendal. Belajar dari guru profesional dengan kurikulum standar internasional.</p>
            <a href="https://wa.me/6285878741474" class="btn-cta" target="_blank">Daftar Sekarang via WA</a>
        </div>
    </section>

    <footer>
        <div class="container">
            <h3 style="color: #fff; margin-bottom: 10px;">Smart Arca Music School</h3>
            <p style="font-size: 1rem; line-height: 1.8; color: #bbb;">
                Jl. Tamtama, Sekepel, Penyangkringan, Kec. Weleri, <br>
                Kabupaten Kendal, Jawa Tengah 51355
            </p>
            <p style="margin-top: 15px; font-size: 1.2rem; color: #e67e22; font-weight: bold;">
                WhatsApp: 0858-7874-1474
            </p>
            <p style="margin-top: 30px; font-size: 0.8rem; color: #666;">
                &copy; 2024 Smart Arca Music School. All Rights Reserved.
            </p>
        </div>
    </footer>

</body>
</html>