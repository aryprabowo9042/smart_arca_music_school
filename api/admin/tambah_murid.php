<?php
session_start();
include '../includes/koneksi.php';

if ($_SESSION['status'] != "login" || $_SESSION['role'] != "admin") { header("Location: ../login.php"); exit(); }

if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $hp = $_POST['hp'];
    $level = $_POST['level']; 
    $kelas = $_POST['kelas']; // Menangkap data kelas musik

    // Query Insert data (Perhatikan penambahan kolom kelas_musik)
    $query = "INSERT INTO users (nama_lengkap, username, password, role, no_hp, level_musik, kelas_musik) 
              VALUES ('$nama', '$username', '$password', 'murid', '$hp', '$level', '$kelas')";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Murid berhasil ditambahkan!'); window.location='data_murid.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tambah Murid</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="sidebar">
        <h2>Smart Arca Admin</h2>
        <a href="index.php">Dashboard</a>
        <a href="data_guru.php">Data Guru</a>
        <a href="data_murid.php" style="background-color: #495057; color: white;">Data Murid</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Tambah Murid Baru</h1>
        <div class="card" style="max-width: 600px;">
            <form action="" method="POST">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" required>
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label>Kelas Musik (Instrumen)</label>
                    <select name="kelas" style="width: 100%; padding: 8px;">
                        <option value="Piano/Keyboard">Piano/Keyboard</option>
                        <option value="Gitar Klasik">Gitar Klasik</option>
                        <option value="Gitar Elektrik">Gitar Elektrik</option>
                        <option value="Drum">Drum</option>
                        <option value="Vokal">Vokal</option>
                        <option value="Theory">Music Theory</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Level Musik</label>
                    <select name="level" style="width: 100%; padding: 8px;">
                        <option value="Basic">Basic (Pemula)</option>
                        <option value="Intermediate">Intermediate (Menengah)</option>
                        <option value="Advance">Advance (Mahir)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>No. HP</label>
                    <input type="text" name="hp">
                </div>
                <button type="submit" name="simpan" class="btn btn-green">Simpan Data</button>
            </form>
        </div>
    </div>
</body>
</html>