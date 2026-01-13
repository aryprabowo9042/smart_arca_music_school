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
        if (password_verify($password, $user['password'])) {
            // Set Session
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // REDIRECT BERDASARKAN ROLE (Kunci Utama)
            if ($user['role'] == 'admin') {
                header("Location: index.php");
            } elseif ($user['role'] == 'guru') {
                header("Location: ../guru/index.php");
            } elseif ($user['role'] == 'murid') {
                header("Location: ../murid/index.php");
            }
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
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden">
        <div class="bg-blue-600 p-8 text-white text-center">
            <div class="w-16 h-16 bg-white text-blue-600 rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-4 shadow-lg">SA</div>
            <h2 class="text-2xl font-bold">Portal Smart Arca</h2>
            <p class="text-blue-100 text-sm opacity-80 mt-1">Silakan masuk untuk melanjutkan</p>
        </div>

        <form method="POST" class="p-8 space-y-5">
            <?php if($error): ?>
                <div class="bg-red-50 text-red-500 p-3 rounded-xl text-sm border border-red-100 flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1 ml-1">Username</label>
                <div class="relative">
                    <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-300"></i>
                    <input type="text" name="username" class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none" placeholder="Masukkan username" required>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1 ml-1">Password</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-300"></i>
                    <input type="password" name="password" class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" name="login" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-blue-200 transition-all active:scale-95">
                MASUK SEKARANG
            </button>
            
            <p class="text-center text-gray-400 text-xs mt-4">
                Lupa password? Hubungi Admin Sekolah.
            </p>
        </form>
    </div>

</body>
</html>
