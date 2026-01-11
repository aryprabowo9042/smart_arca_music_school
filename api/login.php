<?php
session_start();
require_once(__DIR__ . '/koneksi.php');

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password' LIMIT 1");
    
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        $_SESSION['username'] = $username;
        $_SESSION['role']     = $data['role'];
        $_SESSION['status']   = "login";

        // TUJUAN BERSIH (Karena vercel.json sudah mengurus rutenya)
        if ($data['role'] == "admin") {
            echo "<script>window.location.href='/admin/index.php';</script>";
        } else if ($data['role'] == "guru") {
            echo "<script>window.location.href='/guru/index.php';</script>";
        } else if ($data['role'] == "murid") {
            echo "<script>window.location.href='/murid/index.php';</script>";
        }
    } else {
        echo "<script>alert('Gagal! Cek Username/Password.'); window.location.href='/login.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Login Smart Arca</title></head>
<body>
    <div style="width:300px; margin:100px auto; padding:20px; border:1px solid #ccc; text-align:center; font-family:sans-serif;">
        <h3>LOGIN SISTEM</h3>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required style="width:90%; padding:10px; margin:5px;"><br>
            <input type="password" name="password" placeholder="Password" required style="width:90%; padding:10px; margin:5px;"><br>
            <button type="submit" name="login" style="width:100%; padding:10px; background:#0070f3; color:white; border:none; cursor:pointer;">MASUK</button>
        </form>
    </div>
</body>
</html>
