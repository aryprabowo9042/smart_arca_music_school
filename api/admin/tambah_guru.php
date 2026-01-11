<?php
session_start();
// PERBAIKAN PATH: ../ berarti naik satu tingkat untuk menemukan koneksi.php
require_once(__DIR__ . '/../koneksi.php');

if (isset($_POST['simpan'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; 
    $role     = 'guru'; // Set otomatis sebagai guru

    $query = mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')");

    if ($query) {
        echo "<script>alert('Data Guru Berhasil Ditambah!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Gagal Menambah Data!');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Guru - Smart Arca</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f4f7f6; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 400px; margin: auto; }
        h2 { color: #1a73e8; margin-top: 0; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #0070f3; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 16px; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; }
    </style>
</head>
<body>

<div class="card">
    <h2>Tambah Guru Baru</h2>
    <form method="POST">
        <label>Nama / Username Guru:</label>
        <input type="text" name="username" required placeholder="Contoh: Pak Ahmad">
        
        <label>Password:</label>
        <input type="password" name="password" required placeholder="Masukkan password">
        
        <button type="submit" name="simpan">SIMPAN DATA GURU</button>
    </form>
    <a href="index.php" class="back-link">← Kembali ke Dashboard</a>
</div>

</body>
</html>
