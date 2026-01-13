<?php
// 1. Inisialisasi Session & Buffer
session_start();
ob_start();
require_once(__DIR__ . '/../koneksi.php');

// 2. Jika sudah ada Cookie atau Session, langsung arahkan ke Dashboard
$current_role = $_SESSION['role'] ?? $_COOKIE['user_role'] ?? '';
if ($current_role) {
    if ($current_role == 'admin') { echo "<script>window.location.replace('index.php');</script>"; exit(); }
    if ($current_role == 'guru') { echo "<script>window.location.replace('../guru/index.php');</script>"; exit(); }
    if ($current_role == 'murid') { echo "<script>window.location.replace('../murid/index.php');</script>"; exit(); }
}

$error = '';

if (isset($_POST['login'])) {
    $u = mysqli_real_escape_string($conn, $_POST['username']);
    $p = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$u'");
    
    if (mysqli_num_rows($query) === 1) {
        $user = mysqli_fetch_assoc($query);
        
        // Cek Password (Mendukung Hash & Teks Biasa untuk darurat)
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

            // SET COOKIE (Cadangan jika Session Vercel Error - Berlaku 1 Hari)
            setcookie('user_id', $user['id'], time() + 86400, "/");
            setcookie('user_role', $user['role'], time() + 86400, "/");

            // REDIRECT MENGGUNAKAN JAVASCRIPT (Anti-403)
            $target = 'index.php';
            if ($user['role'] == 'guru') $target = '../guru/index.php';
            if ($user['role'] == 'murid') $target = '../murid/index.php';

            echo "<script>
                alert('Login Berhasil sebagai " . ucfirst($user['role']) . "');
                window.location.replace('" . $target . "');
            </script>";
            exit();
        } else {
            $error = 'Password salah!';
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
    <title>Login - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4 font-[sans-serif]">

    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl overflow-hidden transform transition-all hover:scale-[1.01]">
        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-8 text-white text-center">
            <div class="w-16 h-16 bg-white text-blue-600 rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-4 shadow-lg">SA</div>
            <h2 class="text-2xl font-bold tracking-tight">Portal Akademik</h2>
            <p class="text-blue-100 text-xs opacity-80 mt-1 uppercase tracking-widest">Smart Arca Music School</p>
        </div>

        <form method="POST" class="p-8 space-y-6">
            <?php if($error): ?>
                <div class="bg-red-50 text-red-600 p-4 rounded-xl text-sm border border-red-100 flex items-center gap-3 animate-pulse">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="space-y-1">
                <label class="text-xs font-bold text-gray-400 uppercase ml-1">Username</label>
                <div class="relative">
                    <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="username" class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all outline-none" placeholder="Masukkan ID Anda" required>
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-bold text-gray-400 uppercase ml-1">Password</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="password" name="password" class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all outline-none" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" name="login" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-blue-200 transition-all active:scale-95 flex items-center justify-center gap-2">
                <span>MASUK KE SISTEM</span>
                <i class="fas fa-arrow-right text-xs"></i>
            </button>
            
            <div class="text-center">
                <p class="text-gray-400 text-[10px] uppercase tracking-tighter">Versi Aplikasi 2.0.1 - © 2026 Smart Arca</p>
            </div>
        </form>
    </div>

</body>
</html>
