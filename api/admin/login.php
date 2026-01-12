<?php
session_start();
require_once(__DIR__ . '/../koneksi.php');

// Jika sudah login, langsung lempar ke index sesuai role
if (isset($_SESSION['status']) && $_SESSION['status'] == 'login') {
    if ($_SESSION['role'] == 'admin') { header("Location: index.php"); exit(); }
}

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; 

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password' LIMIT 1");
    
    if ($query && mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        
        // Set Session dengan lengkap
        $_SESSION['status']   = "login";
        $_SESSION['username'] = $data['username'];
        $_SESSION['role']     = $data['role'];

        // Menggunakan JavaScript Redirect agar lebih stabil di serverless (Vercel)
        if ($data['role'] == "admin") {
            echo "<script>window.location.href='index.php';</script>";
        } elseif ($data['role'] == "guru") {
            echo "<script>window.location.href='../guru/index.php';</script>";
        } else {
            echo "<script>window.location.href='../murid/index.php';</script>";
        }
        exit();
    } else {
        echo "<script>alert('Gagal! Username atau Password salah.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Smart Arca</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%); display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-card { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.2); width: 320px; text-align: center; }
        .login-card h2 { color: #1a73e8; margin-bottom: 10px; font-size: 28px; }
        .login-card p { color: #666; margin-bottom: 30px; font-size: 14px; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 2px solid #eee; border-radius: 10px; box-sizing: border-box; transition: 0.3s; font-size: 16px; }
        input:focus { border-color: #1a73e8; outline: none; }
        button { width: 100%; padding: 14px; background: #1a73e8; color: white; border: none; border-radius: 10px; cursor: pointer; font-weight: bold; font-size: 16px; margin-top: 15px; transition: 0.3s; }
        button:hover { background: #1557b0; transform: translateY(-2px); }
        .footer { margin-top: 25px; font-size: 12px; color: #aaa; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>SMART ARCA</h2>
        <p>Sistem Informasi Sekolah Musik</p>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required autofocus>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">MASUK KE DASHBOARD</button>
        </form>
        <div class="footer">© 2026 Smart Arca Music School</div>
    </div>
</body>
</html>
