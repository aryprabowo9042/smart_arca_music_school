<?php
// Mengambil variabel dari Vercel Environment Variables
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$db   = getenv('DB_NAME');
$port = getenv('DB_PORT');

// Koneksi dengan SSL (Wajib untuk TiDB Cloud)
$conn = mysqli_init();
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
mysqli_real_connect($conn, $host, $user, $pass, $db, $port, NULL, MYSQLI_CLIENT_SSL);

if (mysqli_connect_errno()) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>