<?php
session_start();
include "koneksi.php";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; // Sebaiknya gunakan password_verify jika dipassword hash

    // Sesuaikan nama tabel Anda (contoh: users atau admin)
    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password'");
    $data = mysqli_fetch_assoc($query);
    $cek = mysqli_num_rows($query);

    if ($cek > 0) {
        $_SESSION['username'] = $username;
        $_SESSION['role']     = $data['role']; // admin, guru, atau murid
        $_SESSION['status']   = "login";

        // Redirect menggunakan path absolut agar Vercel tidak bingung
        if ($data['role'] == "admin") {
            header("Location: /admin/index.php");
        } else if ($data['role'] == "guru") {
            header("Location: /guru/index.php");
        } else if ($data['role'] == "murid") {
            header("Location: /murid/index.php");
        }
    } else {
        header("Location: /login.php?pesan=gagal");
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Smart Arca Music School</title>
    <link rel="stylesheet" href="/css/landing.css"> </head>
<body style="display: flex; justify-content: center; align-items: center; height: 100vh; background: #f0f2f5;">

    <div style="background: white; padding: 30px; border-radius: 10px; shadow: 0 4px 10px rgba(0,0,0,0.1); width: 300px;">
        <h2 style="text-align: center;">Login Sistem</h2>
        
        <?php if(isset($_GET['pesan']) && $_GET['pesan'] == "gagal") { ?>
            <p style="color: red; font-size: 0.8rem; text-align: center;">Username atau Password Salah!</p>
        <?php } ?>

        <form action="" method="POST">
            <div style="margin-bottom: 15px;">
                <label>Username</label>
                <input type="text" name="username" style="width: 100%; padding: 8px; margin-top: 5px;" required>
            </div>
            <div style="margin-bottom: 15px;">
                <label>Password</label>
                <input type="password" name="password" style="width: 100%; padding: 8px; margin-top: 5px;" required>
            </div>
            <button type="submit" name="login" style="width: 100%; padding: 10px; background: #e67e22; color: white; border: none; border-radius: 5px; cursor: pointer;">
                Login Sekarang
            </button>
        </form>
        <p style="text-align: center; margin-top: 15px;">
            <a href="/" style="font-size: 0.8rem; color: #666;"> Kembali ke Beranda</a>
        </p>
    </div>

</body>
</html>