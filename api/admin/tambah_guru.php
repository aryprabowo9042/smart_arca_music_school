<?php
session_start();
include '../includes/koneksi.php';

if ($_SESSION['status'] != "login" || $_SESSION['role'] != "admin") {
    header("Location: ../login.php");
    exit();
}

// Proses saat tombol simpan ditekan
if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Enkripsi password
    $hp = $_POST['hp'];
    $alamat = $_POST['alamat'];

    // Insert ke database dengan role 'guru'
    $query = "INSERT INTO users (nama_lengkap, username, password, role, no_hp, alamat) 
              VALUES ('$nama', '$username', '$password', 'guru', '$hp', '$alamat')";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Guru berhasil ditambahkan!'); window.location='data_guru.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tambah Guru</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="sidebar">
        <h2>Smart Arca Admin</h2>
        <a href="index.php">Dashboard</a>
        <a href="data_guru.php" style="background-color: #495057; color: white;">Data Guru</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Tambah Guru Baru</h1>
        <div class="card" style="max-width: 600px;">
            <form action="" method="POST">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" required>
                </div>
                <div class="form-group">
                    <label>Username (untuk login)</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>No. HP</label>
                    <input type="text" name="hp">
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat" rows="3"></textarea>
                </div>
                <button type="submit" name="simpan" class="btn btn-green">Simpan Data</button>
                <a href="data_guru.php" class="btn btn-red">Batal</a>
            </form>
        </div>
    </div>

</body>
</html>