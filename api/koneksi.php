<?php
// Pastikan data ini sesuai dengan yang ada di TiDB Cloud Console Bapak
$host = "gateway01.ap-southeast-1.prod.aws.tidbcloud.com"; 
$user = "data-user-bapak.root"; 
$pass = "password-asli-bapak"; 
$db   = "smart_arca"; 
$port = 4000;

// Membuat objek koneksi
$conn = mysqli_init();

// Mengatur SSL agar koneksi aman (Wajib untuk TiDB Cloud Serverless)
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

// Melakukan koneksi dengan bendera MYSQLI_CLIENT_SSL
$real_connect = mysqli_real_connect($conn, $host, $user, $pass, $db, $port, NULL, MYSQLI_CLIENT_SSL);

if (!$real_connect) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
