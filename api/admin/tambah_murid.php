<?php
session_start();
require_once(__DIR__ . '/koneksi.php');

// Proses Simpan Data
if (isset($_POST['simpan'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; // Sebaiknya di-hash, tapi kita samakan dulu dengan sistem bapak
    $role     = 'murid';

    $query = mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')");

    if ($query) {
        echo "<script>alert('Data Murid Berhasil Ditambah!'); window.location.href='admin/index.php';</script>";
    } else {
        echo "<script>alert('Gagal Menambah Data!');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Murid - Smart Arca</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f6; padding: 20px; }
        .form-card { background: white; padding: 20px; border-radius: 8px; max-width: 400px; margin: auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #0070f3; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; }
    </style>
</head>
<body>

<div class="form-card">
    <h2>Tambah Murid Baru</h2>
    <form method="POST">
        <label>Username / Nama Murid:</label>
        <input type="text" name="username" required placeholder="Masukkan nama murid">
        
        <label>Password:</label>
        <input type="password" name="password" required placeholder="Masukkan password">
        
        <button type="submit" name="simpan">SIMPAN DATA MURID</button>
    </form>
    <a href="admin/index.php" class="back-link">← Kembali ke Dashboard</a>
</div>

</body>
</html>
