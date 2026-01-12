<?php
session_start();
require_once(__DIR__ . '/../koneksi.php');

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; 

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password' LIMIT 1");
    
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        $_SESSION['status'] = "login";
        $_SESSION['role'] = $data['role'];
        $_SESSION['username'] = $data['username'];

        // Menggunakan JavaScript agar lebih pasti pindah halaman di Vercel
        if ($data['role'] == 'admin') {
            echo "<script>window.location.href='index.php';</script>";
        } elseif ($data['role'] == 'guru') {
            echo "<script>window.location.href='/api/guru/index.php';</script>";
        } else {
            echo "<script>window.location.href='/api/murid/index.php';</script>";
        }
        exit();
    } else {
        echo "<script>alert('User tidak ditemukan!');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Login</title></head>
<body>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit" name="login">LOGIN</button>
    </form>
</body>
</html>
