<?php
session_start();
ob_start(); // Tambahkan ini untuk mencegah error "headers already sent"
require_once(__DIR__ . '/../koneksi.php');

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; 

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password' LIMIT 1");
    
    if ($query && mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        $_SESSION['status']   = "login";
        $_SESSION['username'] = $data['username'];
        $_SESSION['role']     = $data['role'];

        // Gunakan Meta Redirect (Cara paling ampuh di Vercel)
        if ($data['role'] == "admin") {
            echo '<meta http-equiv="refresh" content="0;url=index.php">';
        } elseif ($data['role'] == "guru") {
            echo '<meta http-equiv="refresh" content="0;url=/api/guru/index.php">';
        } else {
            echo '<meta http-equiv="refresh" content="0;url=/api/murid/index.php">';
        }
        exit();
    } else {
        echo "<script>alert('Login Gagal! Cek Username/Password.');</script>";
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
        .box { background: white; padding: 30px; border-radius: 15px; width: 280px; text-align: center; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #0d47a1; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <div class="box">
        <h3>SMART ARCA</h3>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">MASUK</button>
        </form>
    </div>
</body>
</html>
