<?php
session_start();
ob_start();

// Cari koneksi di folder yang sama (api/)
$path_koneksi = __DIR__ . '/koneksi.php';
require_once($path_koneksi);

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; 

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password'");
    $cek = mysqli_num_rows($query);

    if ($cek > 0) {
        $data = mysqli_fetch_assoc($query);
        $_SESSION['username'] = $username;
        $_SESSION['role']     = $data['role'];
        $_SESSION['status']   = "login";

        // Gunakan path absolut untuk redirect
        if ($data['role'] == "admin") {
            header("Location: /admin/index.php");
        } else {
            header("Location: /index.php");
        }
        exit();
    } else {
        header("Location: /login.php?pesan=gagal");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Sistem | Smart Arca</title>
    <link rel="stylesheet" href="/css/landing.css">
</head>
<body style="display:flex; justify-content:center; align-items:center; height:100vh; background:#f0f2f5;">
    <div style="background:white; padding:40px; border-radius:15px; box-shadow:0 10px 25px rgba(0,0,0,0.1); width:320px;">
        <h2 style="text-align:center;">Login Sistem</h2>
        <?php if(isset($_GET['pesan']) && $_GET['pesan'] == "gagal") echo "<p style='color:red; text-align:center;'>User/Pass Salah!</p>"; ?>
        <form action="" method="POST">
            <input type="text" name="username" placeholder="Username" style="width:100%; padding:10px; margin-bottom:15px;" required>
            <input type="password" name="password" placeholder="Password" style="width:100%; padding:10px; margin-bottom:20px;" required>
            <button type="submit" name="login" style="width:100%; padding:10px; background:#e67e22; color:white; border:none; border-radius:5px; cursor:pointer;">MASUK SEKARANG</button>
        </form>
    </div>
</body>
</html>
<?php ob_end_flush(); ?>