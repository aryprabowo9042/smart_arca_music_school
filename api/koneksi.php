<?php
// AMBIL DATA INI DARI DASHBOARD TiDB CLOUD BAPAK
$host = "gateway01.ap-southeast-1.prod.aws.tidbcloud.com"; 
$user = "2VSxBZjDY3MNSj4.root"; 
$pass = "ytvrGFzlPIBWg4eC"; 
$db   = "test"; 
$port = 4000;

$conn = mysqli_init();

// Pengaturan SSL (Wajib untuk TiDB Serverless)
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

// Melakukan koneksi
$real_connect = mysqli_real_connect(
    $conn, 
    $host, 
    $user, 
    $pass, 
    $db, 
    $port, 
    NULL, 
    MYSQLI_CLIENT_SSL
);

if (!$real_connect) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
