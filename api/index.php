<?php
// 1. KONEKSI (Gunakan jalur absolut internal)
$path_koneksi = __DIR__ . '/koneksi.php';
if (file_exists($path_koneksi)) {
    require_once($path_koneksi);
} else {
    die("File koneksi.php tidak ditemukan di: " . $path_koneksi);
}

// 2. PROSES LOGIN
$error = '';
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; 

    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Simpan data login ke Cookie
        setcookie('user_id', $user['id'], time() + (86400 * 30), "/");
        setcookie('user_role', $user['role'], time() + (86400 * 30), "/");
        setcookie('user_username', $user['username'], time() + (86400 * 30), "/");

        // Arahkan ke dashboard sesuai role
        if ($user['role'] == 'guru') {
            header("Location: guru/index.php");
        } elseif ($user['role'] == 'admin') {
            header("Location: ../admin/index.php"); // Sesuaikan jika folder admin di luar api
        } else {
            header("Location: murid/index.php");
        }
        exit();
    } else {
        $error = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
</head>
<body class="bg-indigo-900 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl p-10 border-b-8 border-yellow-400">
        <div class="text-center mb-10">
            <div class="bg-yellow-400 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 text-indigo-900 text-2xl shadow-lg">
                <i class="fas fa-music"></i>
            </div>
            <h1 class="text-3xl font-black text-slate-800 uppercase italic tracking-tighter leading-none">Smart Arca</h1>
            <p class="text-slate-400 font-bold text-[9px] uppercase tracking-widest mt-2">Portal Masuk</p>
        </div>

        <?php if(!empty($error)): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-2xl mb-6 text-xs font-bold border border-red-100 italic">
                <i class="fas fa-exclamation-circle mr-2"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2 mb-2 block">Username</label>
                <input type="text" name="username" class="w-full p-4 rounded-2xl bg-slate-50 border-2 border-slate-50 focus:border-indigo-600 outline-none font-bold" placeholder="Username" required>
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2 mb-2 block">Password</label>
                <input type="password" name="password" class="w-full p-4 rounded-2xl bg-slate-50 border-2 border-slate-50 focus:border-indigo-600 outline-none font-bold" placeholder="••••••••" required>
            </div>
            <button type="submit" name="login" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 rounded-2xl uppercase italic shadow-xl transition active:scale-95">
                Masuk Sistem <i class="fas fa-arrow-right ml-2"></i>
            </button>
        </form>

        <div class="text-center mt-10">
            <a href="/" class="text-[10px] font-black text-indigo-400 uppercase tracking-widest hover:text-indigo-600 transition">
                <i class="fas fa-chevron-left mr-1"></i> Kembali ke Beranda
            </a>
        </div>
    </div>

</body>
</html>
