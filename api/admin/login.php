<?php
session_start();
require_once(__DIR__ . '/../koneksi.php');

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; 

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password' LIMIT 1");
    
    if ($query && mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        
        $_SESSION['username'] = $data['username'];
        $_SESSION['role']     = $data['role'];
        $_SESSION['status']   = "login";

        // --- PERBAIKAN PENGATURAN ARAH LOGIN (REDIRECT) ---
        if ($data['role'] == "admin") {
            header("Location: index.php"); // Tetap di folder admin
        } elseif ($data['role'] == "guru") {
            header("Location: ../guru/index.php"); // Masuk ke folder guru
        } elseif ($data['role'] == "murid") {
            header("Location: ../murid/index.php"); // Masuk ke folder murid
        }
        exit();
    } else {
        echo "<script>alert('Gagal! Username atau Password salah.');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Smart Arca</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .box { background: white; padding: 35px; border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); width: 300px; text-align: center; }
        h2 { color: #1a73e8; margin-bottom: 25px; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #1a73e8; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 16px; margin-top: 10px; }
        button:hover { background: #1557b0; }
    </style>
</head>
<body>
    <div class="box">
        <h2>SMART ARCA</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required autofocus>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">MASUK</button>
        </form>
        <p style="font-size: 12px; color: #888; margin-top: 20px;">Sistem Informasi Sekolah Musik</p>
    </div>
</body>
</html>
