<?php
require_once('koneksi.php');

// 1. Hapus SEMUA data user
mysqli_query($conn, "DELETE FROM users");

// 2. Reset ID auto increment (opsional, biar rapi mulai dari 1 lagi)
// Note: Perintah ini mungkin beda di tiap DB, tapi DELETE di atas sudah cukup.

// 3. Masukkan 3 User Standar (Password Teks Biasa)
$pass_admin = 'admin123';
$pass_guru = 'guru123';
$pass_murid = 'murid123';

mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('admin', '$pass_admin', 'admin')");
mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('guru', '$pass_guru', 'guru')");
mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('murid', '$pass_murid', 'murid')");

echo "<h1 style='color:green; text-align:center;'>DATABASE BERHASIL DIBERSIHKAN!</h1>";
echo "<p style='text-align:center;'>Sekarang hanya ada 3 user: admin, guru, murid.</p>";
echo "<p style='text-align:center;'><a href='admin/login.php'>KLIK DISINI UNTUK LOGIN</a></p>";
?>
