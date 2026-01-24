<?php
// AMBIL DATA INI DARI DASHBOARD TiDB CLOUD (Tombol Connect)
$host = "gateway01.ap-southeast-1.prod.aws.tidbcloud.com"; 
$user = "2VSxBZjDY3MNSj4.root"; // <--- INI HARUS DIGANTI DENGAN USERNAME ASLI BAPAK
$pass = "XpaSuXbQGXB458nh"; // <--- INI HARUS DIGANTI DENGAN PASSWORD ASLI BAPAK
$db   = "test"; 
$port = 4000;

$conn = mysqli_init();
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
define('GROQ_API_KEY', getenv('GROQ_API_KEY'));
