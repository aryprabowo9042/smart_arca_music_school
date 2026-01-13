<?php
session_start();
ob_start();
require_once(__DIR__ . '/../koneksi.php');

// Hapus session lama jika ada error muter-muter
if (isset($_GET['reset'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$error = '';

if (isset($_POST['login'])) {
    $u = mysqli_real_escape_string($conn, $_POST['username']);
    $p = $_POST['password'];

    // Gunakan LOWER untuk menghindari masalah huruf besar/kecil
    $query = mysqli_query($conn, "SELECT * FROM users WHERE LOWER(username) = LOWER('$u')");
    
    if (mysqli_num_rows($query) === 1) {
        $user = mysqli_fetch_assoc($query);
        
        $is_valid = false;
        if (password_verify($p, $user['password'])) {
            $is_valid = true;
        } elseif ($p === $user['password']) {
            $is_valid = true;
        }

        if ($is_valid) {
            // SET SESSION
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            session_write_close(); // Paksa simpan sebelum pindah

            // Redirect pakai JS agar tidak dicekal Vercel
            if ($user['role'] == 'admin') {
                echo "<script>window.location.replace('index.php');</script>";
            } elseif ($user['role'] == 'guru') {
                echo "<script>window.location.replace('../guru/index.php');</script>";
            } elseif ($user['role'] == 'murid') {
                echo "<script>window.location.replace('../murid/index.php');</script>";
            }
            exit();
        } else {
            $error = 'Password salah!';
        }
    } else {
        $error = 'Username tidak ditemukan! Coba cek huruf besar/kecilnya.';
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
        <h2 class="text-2xl font-bold text-center mb-6">LOGIN SYSTEM</h2>
        <?php if($error): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-center text-sm"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" class="space-y-4">
            <input type="text" name="username" placeholder="Username" class="w-full p-4 bg-gray-50 border rounded-xl" required>
            <input type="password" name="password" placeholder="Password" class="w-full p-4 bg-gray-50 border rounded-xl" required>
            <button type="submit" name="login" class="w-full bg-blue-600 text-white font-bold py-4 rounded-xl">MASUK</button>
        </form>
        <div class="mt-4 text-center">
            <a href="login.php?reset=1" class="text-xs text-gray-400 underline">Bersihkan Session (Jika Muter-muter)</a>
        </div>
    </div>
</body>
</html>
