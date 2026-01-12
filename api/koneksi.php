<?php
$host = "alamat-host-tidb";
$user = "alamat-user";
$pass = "password-bapak";
$db   = "nama-db";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
