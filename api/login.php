<?php
session_start();
include "koneksi.php";

// Debug: Cek apakah koneksi berhasil
if (!$conn) {
    die("Koneksi ke TiDB Gagal: " . mysqli_connect_error());
}

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Debug: Cek input
    // echo "Mencoba login dengan: " . $username; 

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password'");
    
    if (!$query) {
        die("Query Error: " . mysqli_error($conn));
    }

    $data = mysqli_fetch_assoc($query);
    $cek = mysqli_num_rows($query);

    if ($cek > 0) {
        $_SESSION['username'] = $username;
        $_SESSION['role']     = $data['role'];
        $_SESSION['status']   = "login";

        if ($data['role'] == "admin") {
            header("Location: /admin/index.php");
        } else if ($data['role'] == "guru") {
            header("Location: /guru/index.php");
        } else {
            header("Location: /murid/index.php");
        }
        exit();
    } else {
        // Jika gagal, tampilkan pesan di URL
        header("Location: /login.php?pesan=gagal_data_tidak_ada");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Smart Arca</title>
    <link rel="stylesheet" href="/css/landing.css">
</head>
<body style="display: flex; justify-content: center; align-items: center; height: 100vh; background: #f0f2f5; font-family: sans-serif;">
    <div style="background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 350px;">
        <h2 style="text-align: center; color: #2c3e50;">Login Sistem</h2>
        
        <?php 
        if(isset($_GET['pesan'])) {
            if($_GET['pesan'] == "gagal_data_tidak_ada") {
                echo "<p style='color: red; text-align: center; font-size: 0.8rem;'>Username atau Password salah!</p>";
            } else if($_GET['pesan'] == "belum_login") {
                echo "<p style='color: orange; text-align: center; font-size: 0.8rem;'>Silakan login terlebih dahulu.</p>";
            }
        }
        ?>

        <form action="/login.php" method="POST">
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px;">Username</label>
                <input type="text" name="username" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box;" required>
            </div>
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 5px;">Password</label>
                <input type="password" name="password" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box;" required>
            </div>
            <button type="submit" name="login" style="width: 100%; padding: 12px; background: #e67e22; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">
                MASUK SEKARANG
            </button>
        </form>
    </div>
</body>
</html>