<?php
// Mengambil data kredensial dari Environment Variables Vercel
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$db   = getenv('DB_NAME');
$port = getenv('DB_PORT') ?: '4000'; // Default TiDB adalah 4000

// Koneksi menggunakan mysqli dengan dukungan SSL (diperlukan oleh beberapa database cloud)
$conn = mysqli_init();

// TiDB Cloud biasanya memerlukan koneksi SSL aman
if (!$conn) {
    die("mysqli_init failed");
}

$link = mysqli_real_connect(
    $conn, 
    $host, 
    $user, 
    $pass, 
    $db, 
    $port,
    NULL,
    MYSQLI_CLIENT_SSL
);

if (!$link) {
    die("Koneksi TiDB Gagal: " . mysqli_connect_error());
}

// Set charset ke utf8mb4 agar mendukung semua karakter
mysqli_set_charset($conn, "utf8mb4");
?>