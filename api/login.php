<?php
session_start();
// Gunakan __DIR__ agar mencari di folder yang sama
require_once(__DIR__ . '/koneksi.php');

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Query ke tabel users yang baru Anda buat di TiDB
    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password'");
    
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        $_SESSION['username'] = $username;
        $_SESSION['role']     = $data['role'];
        $_SESSION['status']   = "login";

        // Arahkan ke dashboard admin
        header("Location: /admin/index.php");
        exit();
    } else {
        header("Location: /login.php?pesan=gagal");
        exit();
    }
}
?>