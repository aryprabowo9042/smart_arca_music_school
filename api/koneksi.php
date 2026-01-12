<?php
$host = "gateway01.ap-southeast-1.prod.aws.tidbcloud.com"; 
$user = "USERNAME_ASLI_BAPAK.root"; 
$pass = "PASSWORD_ASLI_BAPAK"; 
$db   = "smart_arca"; 
$port = 4000;

$conn = mysqli_init();
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
$real_connect = mysqli_real_connect($conn, $host, $user, $pass, $db, $port, NULL, MYSQLI_CLIENT_SSL);

if (!$real_connect) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
