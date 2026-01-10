<?php
// includes/koneksi.php

$host = "localhost";
$user = "root";      // Default user XAMPP
$pass = "";          // Default password XAMPP (kosong)
$db   = "smart_arca";

// Melakukan koneksi ke database
$koneksi = mysqli_connect($host, $user, $pass, $db);

// Cek apakah koneksi berhasil
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Opsional: Pesan sukses untuk debugging (bisa dihapus nanti)
// echo "Koneksi berhasil!";
?>