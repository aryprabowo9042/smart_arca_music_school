<?php
// Pastikan tidak ada spasi sebelum tag PHP
session_start();
ob_start();
require_once(__DIR__ . '/../koneksi.php');

if (isset($_POST['login'])) {
    $u = mysqli_real_escape_string($conn, $_POST['username']);
    $p = mysqli_real_escape_string($conn, $_POST['password']);

    $result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$u'");
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Cek password (mendukung teks biasa & hash)
        if ($p === $user['password'] || password_verify($p, $user['password'])) {
            // SET SESSION DENGAN BENAR
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Paksa simpan session sebelum pindah halaman
            session_write_close();

            // Redirect menggunakan JavaScript (Lebih stabil di Vercel dibanding header)
            if ($user['role'] == 'admin') {
                echo "<script>window.location.replace('index.php');</script>";
            } elseif ($user['role'] == 'guru') {
                echo "<script>window.location.replace('../guru/index.php');</script>";
            } elseif ($user['role'] == 'murid') {
                echo "<script>window.location.replace('../murid/index.php');</script>";
            }
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "User tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-sm">
        <h2 class="text-2xl font-bold text-center mb-6">LOGIN</h2>
        <?php if(isset($error)): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-center font-bold text-sm"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" class="space-y-4">
            <input type="text" name="username" placeholder="Username" class="w-full p-3 border rounded-lg" required>
            <input type="password" name="password" placeholder="Password" class="w-full p-3 border rounded-lg" required>
            <button type="submit" name="login" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold">MASUK</button>
        </form>
    </div>
</body>
</html>
