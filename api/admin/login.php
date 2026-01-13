<?php
session_start();
require_once(__DIR__ . '/../koneksi.php');

// Matikan error reporting yang mengganggu agar tampilan bersih
error_reporting(E_ALL ^ E_NOTICE);

if (isset($_POST['login'])) {
    $u = mysqli_real_escape_string($conn, $_POST['username']);
    $p = mysqli_real_escape_string($conn, $_POST['password']);

    // Query sangat sederhana
    $result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$u'");
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Cek password teks biasa (direct comparison)
        if ($p === $user['password']) {
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Pengalihan Jalur
            if ($user['role'] == 'admin') {
                header("Location: index.php");
            } elseif ($user['role'] == 'guru') {
                header("Location: ../guru/index.php");
            } elseif ($user['role'] == 'murid') {
                header("Location: ../murid/index.php");
            }
            exit();
        } else {
            $error = "Password untuk user '$u' salah!";
        }
    } else {
        $error = "Username '$u' tidak ditemukan di database!";
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
<body class="bg-blue-900 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-sm">
        <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">SMART ARCA LOGIN</h2>
        
        <?php if(isset($error)): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4 text-xs font-bold text-center">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <input type="text" name="username" placeholder="Username" class="w-full p-3 border rounded-lg outline-none focus:ring-2 focus:ring-blue-500" required>
            <input type="password" name="password" placeholder="Password" class="w-full p-3 border rounded-lg outline-none focus:ring-2 focus:ring-blue-500" required>
            <button type="submit" name="login" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition">MASUK</button>
        </form>
        <p class="text-center text-gray-400 text-xs mt-6">Input: admin / admin123</p>
    </div>
</body>
</html>
