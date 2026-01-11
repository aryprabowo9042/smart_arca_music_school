<?php
// Pastikan tidak ada spasi sebelum tag PHP
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__ . '/koneksi.php');

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; 

    // Cek apakah database connect
    if (!$conn) {
        die("Koneksi database gagal: " . mysqli_connect_error());
    }

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password' LIMIT 1");
    
    if ($query && mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        
        // Set Session
        $_SESSION['username'] = $data['username'];
        $_SESSION['role']     = $data['role'];
        $_SESSION['status']   = "login";

        // Gunakan JavaScript untuk pindah halaman agar lebih stabil di Vercel
        if ($data['role'] == "admin") {
            echo "<script>window.location.href='admin/index.php';</script>";
        } else {
            echo "<script>window.location.href='admin/index.php';</script>";
        }
        exit();
    } else {
        echo "<script>alert('Gagal! Username atau Password salah.'); window.location.href='login.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Smart Arca</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 300px; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #1a73e8; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; width: 100%; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2 style="text-align:center; color:#1a73e8;">LOGIN</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">MASUK</button>
        </form>
    </div>
</body>
</html>
