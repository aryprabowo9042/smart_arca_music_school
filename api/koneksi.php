<?php
// Mengambil data dari Environment Variables Vercel
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$db   = getenv('DB_NAME');
$port = getenv('DB_PORT') ?: '4000';

// 1. Inisialisasi koneksi
$conn = mysqli_init();

// 2. WAJIB: Atur SSL sebelum melakukan koneksi
// Untuk TiDB Cloud di Vercel, kita cukup mengaktifkan mode SSL
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

// 3. Lakukan koneksi dengan flag MYSQLI_CLIENT_SSL
$status = mysqli_real_connect(
    $conn, 
    $host, 
    $user, 
    $pass, 
    $db, 
    $port, 
    NULL, 
    MYSQLI_CLIENT_SSL
);

if (!$status) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}
?>