<?php
session_start();
include 'includes/koneksi.php';

// Variabel untuk menyimpan pesan error
$error = "";

// Cek apakah tombol login ditekan
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Kita enkripsi password input dengan MD5 agar cocok dengan database

    // Mencegah SQL Injection sederhana
    $username = mysqli_real_escape_string($koneksi, $username);
    $password = mysqli_real_escape_string($koneksi, $password);

    // Cek ke database
    $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($koneksi, $query);
    $cek = mysqli_num_rows($result);

    if ($cek > 0) {
        $data = mysqli_fetch_assoc($result);

        // Simpan data user ke session
        $_SESSION['id_user'] = $data['id'];
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $data['role'];
        $_SESSION['nama_lengkap'] = $data['nama_lengkap'];
        $_SESSION['status'] = "login";

        // Cek Role dan Alihkan ke Halaman yang Tepat
        if ($data['role'] == "admin") {
            header("Location: admin/index.php");
        } else if ($data['role'] == "guru") {
            header("Location: guru/index.php");
        } else if ($data['role'] == "murid") {
            header("Location: murid/index.php");
        }
    } else {
        $error = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Smart Arca Music School</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 300px; }
        .login-container h2 { text-align: center; margin-bottom: 20px; color: #333; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .btn-login { width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .btn-login:hover { background-color: #218838; }
        .error-msg { color: red; text-align: center; margin-bottom: 10px; font-size: 14px; }
        .back-link { display: block; text-align: center; margin-top: 10px; text-decoration: none; color: #666; font-size: 14px; }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Login Sistem</h2>
        
        <?php if($error != ""): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required autofocus placeholder="Masukkan username">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Masukkan password">
            </div>
            <button type="submit" name="login" class="btn-login">Masuk</button>
        </form>
        
        <a href="index.php" class="back-link">Kembali ke Beranda</a>
    </div>

</body>
</html>