<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan file koneksi terpanggil
if (!file_exists(__DIR__ . '/koneksi.php')) {
    die("Error: File koneksi.php tidak ditemukan di folder api!");
}
require_once(__DIR__ . '/koneksi.php');

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password' LIMIT 1");
    
    if ($query && mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        $_SESSION['username'] = $username;
        $_SESSION['role']     = $data['role'];
        $_SESSION['status']   = "login";

        // LANGSUNG PINDAH KE DASHBOARD
        header("Location: /api/admin/index.php");
        exit();
    } else {
        echo "<script>alert('Gagal! Username atau Password salah atau Database kosong.'); window.location.href='/api/login.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Login Sistem</title></head>
<body style="font-family:sans-serif; background:#f0f2f5;">
    <div style="width:300px; margin:100px auto; padding:20px; background:white; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.1); text-align:center;">
        <h3>SMART ARCA LOGIN</h3>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required style="width:90%; padding:10px; margin:10px 0; border:1px solid #ccc; border-radius:5px;">
            <input type="password" name="password" placeholder="Password" required style="width:90%; padding:10px; margin:10px 0; border:1px solid #ccc; border-radius:5px;">
            <button type="submit" name="login" style="width:95%; padding:10px; background:#0070f3; color:white; border:none; border-radius:5px; cursor:pointer; font-weight:bold;">MASUK SEKARANG</button>
        </form>
    </div>
</body>
</html>
