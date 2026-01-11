<?php
// MENGAKTIFKAN ERROR REPORTING (Sangat Penting untuk Debug)
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Mencari file koneksi
$path_koneksi = __DIR__ . '/koneksi.php';

if (!file_exists($path_koneksi)) {
    die("ERROR: File koneksi.php tidak ditemukan di lokasi: " . $path_koneksi);
}

require_once($path_koneksi);

// Cek apakah koneksi database benar-benar ada
if (!isset($conn)) {
    die("ERROR: Variabel koneksi \$conn tidak ditemukan. Periksa isi koneksi.php");
}

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password'");
    
    if (!$query) {
        die("ERROR QUERY: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        $_SESSION['username'] = $username;
        $_SESSION['role']     = $data['role'];
        $_SESSION['status']   = "login";

        // Gunakan JavaScript agar lebih stabil saat redirect di Vercel
        echo "<script>window.location.href='/admin/index.php';</script>";
        exit();
    } else {
        echo "<script>alert('Login Gagal! Username atau Password salah.'); window.location.href='/login.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Sistem</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f0f2f5; margin: 0; }
        .login-card { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 320px; text-align: center; }
        input { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #e67e22; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Login Sistem</h2>
        <form action="" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">MASUK SEKARANG</button>
        </form>
    </div>
</body>
</html>