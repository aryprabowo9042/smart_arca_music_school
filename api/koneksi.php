<?php
$host = "gateway01.ap-southeast-1.prod.aws.tidbcloud.com"; // Sesuaikan dengan host TiDB Bapak
$user = "data-user-bapak.root"; // Sesuaikan username asli
$pass = "password-asli-bapak"; // Sesuaikan password asli
$db   = "smart_arca"; // Sesuaikan nama database Bapak
$port = 4000;

// Gunakan mysqli_connect dengan tambahan port jika perlu
$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
