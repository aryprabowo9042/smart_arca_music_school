<?php
session_start();
ob_start();
require_once(__DIR__ . '/../koneksi.php');

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; 

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password' LIMIT 1");
    
    if ($query && mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        
        // Simpan ke Session
        $_SESSION['status']   = "login";
        $_SESSION['username'] = $data['username'];
        $_SESSION['role']     = $data['role'];

        // Simpan ke Cookie selama 1 jam (Cadangan untuk Vercel)
        setcookie("user_login", $data['username'], time() + 3600, "/");
        setcookie("user_role", $data['role'], time() + 3600, "/");

        // Redirect menggunakan JavaScript agar lebih stabil
        if ($data['role'] == "admin") {
            echo "<script>window.location.replace('index.php');</script>";
        } elseif ($data['role'] == "guru") {
            echo "<script>window.location.replace('../guru/index.php');</script>";
        } else {
            echo "<script>window.location.replace('../murid/index.php');</script>";
        }
        exit();
    } else {
        echo "<script>alert('Login Gagal! Akun tidak ditemukan di TiDB.'); window.location.replace('login.php');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Smart Arca</title>
    <style>
        body { font-family: sans-serif; background: #1a73e8; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .box { background: white; padding: 30px; border-radius: 15px; width: 280px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #0d47a1; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <div class="box">
        <h2 style="color:#1a73e8; margin-bottom:0;">SMART ARCA</h2>
        <p style="font-size:12px; color:#666; margin-top:5px;">Silakan Masuk</p>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">MASUK</button>
        </form>
    </div>
</body>
</html>
