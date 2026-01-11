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

        // ARAHKAN KE FILE BARU (dashboard.php)
        echo "<script>window.location.href='/api/dashboard.php';</script>";
        
    } else {
        echo "<script>alert('Gagal! Cek Username/Password.'); window.location.href='/api/login.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Login</title></head>
<body>
    <div style="width:300px; margin:100px auto; padding:20px; border:1px solid #ccc; text-align:center;">
        <h3>LOGIN TES</h3>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required style="width:90%; padding:10px; margin:5px;"><br>
            <input type="password" name="password" placeholder="Password" required style="width:90%; padding:10px; margin:5px;"><br>
            <button type="submit" name="login" style="width:100%; padding:10px; cursor:pointer;">MASUK</button>
        </form>
    </div>
</body>
</html>
