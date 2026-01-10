<?php
// Mengambil data dari Environment Variables Vercel
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$db   = getenv('DB_NAME');
$port = getenv('DB_PORT') ?: '4000';

$conn = mysqli_init();

// WAJIB untuk TiDB Cloud: Mengaktifkan SSL
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

// Melakukan koneksi
$status = mysqli_real_connect($conn, $host, $user, $pass, $db, $port);

if (!$status) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}
?>