<?php
session_start();
require_once(__DIR__ . '/../koneksi.php');

// Jika sudah login, lempar ke dashboard yang sesuai
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') { header("Location: index.php"); exit(); }
    if ($_SESSION['role'] == 'guru') { header("Location: ../guru/index.php"); exit(); }
    if ($_SESSION['role'] == 'murid') { header("Location: ../murid/index.php"); exit(); }
}

$error = '';
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    if (mysqli_num_rows($query) === 1) {
        $user = mysqli_fetch_assoc($query);
        
        // CEK PASSWORD (Bisa Hash atau Teks Biasa)
        $login_sukses = false;
        if (password_verify($password, $user['password'])) {
            $login_sukses = true;
        } elseif ($password === $user['password']) {
            $login_sukses = true;
        }

        if ($login_sukses) {
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // REDIRECT BERDASARKAN ROLE
            if ($user['role'] == 'admin') {
                header("Location: index.php");
            } elseif ($user['role'] == 'guru') {
                header("Location: ../guru/index.php");
            } elseif ($user['role'] == 'murid') {
                header("Location: ../murid/index.php");
            }
            exit();
        } else {
            $error = 'Password salah! Coba periksa kembali.';
        }
    } else {
        $error = 'Username tidak ditemukan!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl p-8">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-blue-600">Smart Arca Login</h2>
            <p class="text-gray-400 text-sm">Masukkan akses Anda</p>
        </div>

        <?php if($error): ?>
            <div class="bg-red-100 text-red-600 p-3 rounded-xl mb-4 text-sm text-center font-bold">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <input type="text" name="username" placeholder="Username" class="w-full p-4 bg-gray-50 border rounded-xl outline-none focus:border-blue-500" required>
            <input type="password" name="password" placeholder="Password" class="w-full p-4 bg-gray-50 border rounded-xl outline-none focus:border-blue-500" required>
            <button type="submit" name="login" class="w-full bg-blue-600 text-white font-bold py-4 rounded-xl shadow-lg active:scale-95 transition-all">MASUK</button>
        </form>
    </div>
</body>
</html>
