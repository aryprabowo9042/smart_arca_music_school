<?php
// api/admin/login.php
require_once(__DIR__ . '/../koneksi.php');

// Matikan error reporting agar tampilan bersih
error_reporting(0);

// --- LOGIKA LOGIN UTAMA ---
if (isset($_POST['login'])) {
    $u = trim($_POST['username']);
    $p = $_POST['password'];

    // Cari user
    $q = mysqli_query($conn, "SELECT * FROM users WHERE username = '$u' LIMIT 1");
    
    if (mysqli_num_rows($q) > 0) {
        $user = mysqli_fetch_assoc($q);
        
        // Cek Password (Support Teks Biasa & Hash)
        if ($p === $user['password'] || password_verify($p, $user['password'])) {
            
            // SIMPAN COOKIE (Berlaku 30 Hari)
            setcookie('user_id', $user['id'], time() + (86400 * 30), "/");
            setcookie('user_role', $user['role'], time() + (86400 * 30), "/");
            setcookie('user_username', $user['username'], time() + (86400 * 30), "/");

            // Redirect sesuai Role
            $tujuan = 'index.php';
            if ($user['role'] == 'guru') $tujuan = '../guru/index.php';
            if ($user['role'] == 'murid') $tujuan = '../murid/index.php';

            // Tampilan Sukses Login
            echo "
            <!DOCTYPE html>
            <html>
            <head><script src='https://cdn.tailwindcss.com'></script></head>
            <body class='bg-blue-600 flex items-center justify-center h-screen'>
                <div class='bg-white p-8 rounded-2xl shadow-2xl text-center max-w-sm'>
                    <div class='text-5xl mb-4'>👋</div>
                    <h1 class='text-2xl font-bold text-gray-800 mb-2'>Login Berhasil!</h1>
                    <p class='text-gray-500 mb-6'>Mengalihkan ke dashboard...</p>
                    <script>
                        setTimeout(function() { window.location.href = '$tujuan'; }, 1000);
                    </script>
                </div>
            </body>
            </html>
            ";
            exit();
        } else {
            $error = "Password Salah!";
        }
    } else {
        $error = "Username Tidak Ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Portal - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">
    
    <div class="bg-white p-8 rounded-3xl shadow-xl w-full max-w-sm border border-gray-100">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-black text-blue-600 tracking-tighter">SMART ARCA</h1>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Portal Akademik</p>
        </div>
        
        <?php if(isset($error)): ?>
            <div class="bg-red-50 text-red-500 p-3 rounded-xl mb-6 text-center text-xs font-bold border border-red-100">
                <i class="fas fa-exclamation-circle mr-1"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase ml-1">Username</label>
                <div class="relative">
                    <span class="absolute left-4 top-3 text-gray-400"><i class="fas fa-user"></i></span>
                    <input type="text" name="username" class="w-full pl-10 p-3 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 focus:bg-white transition" placeholder="Username" required>
                </div>
            </div>

            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase ml-1">Password</label>
                <div class="relative">
                    <span class="absolute left-4 top-3 text-gray-400"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="w-full pl-10 p-3 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 focus:bg-white transition" placeholder="Password" required>
                </div>
            </div>

            <button type="submit" name="login" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-blue-200 transition transform active:scale-95">
                MASUK
            </button>
        </form>

        <div class="mt-8 text-center">
            <a href="../../index.php" class="text-xs text-gray-400 hover:text-blue-600 transition">
                &larr; Kembali ke Website
            </a>
        </div>
    </div>

</body>
</html>
