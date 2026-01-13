<?php
session_start();
ob_start();
require_once(__DIR__ . '/../koneksi.php');

// Reset Session jika diperlukan
if (isset($_GET['reset'])) {
    session_destroy();
    header("Location: login.php"); exit();
}

$error = '';

if (isset($_POST['login'])) {
    $u = trim($_POST['username']); // Gunakan trim untuk hapus spasi tak sengaja
    $p = $_POST['password'];

    // Query pencarian
    $query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$u'");
    
    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);
        
        // Cek Password
        if ($p === $user['password'] || password_verify($p, $user['password'])) {
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            session_write_close();

            $target = ($user['role'] == 'admin') ? 'index.php' : (($user['role'] == 'guru') ? '../guru/index.php' : '../murid/index.php');
            echo "<script>window.location.replace('$target');</script>";
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "User '$u' tidak ada di database!";
    }
}

// AMBIL DAFTAR USER UNTUK PENGECEKAN (DEBUG)
$list_user = mysqli_query($conn, "SELECT username, role FROM users LIMIT 5");
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Debug - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 flex flex-col items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-sm">
        <h2 class="text-2xl font-bold text-center mb-6">LOGIN PORTAL</h2>
        
        <?php if($error): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-center text-xs font-bold"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <input type="text" name="username" placeholder="Username" class="w-full p-4 bg-gray-50 border rounded-xl outline-none" required>
            <input type="password" name="password" placeholder="Password" class="w-full p-4 bg-gray-50 border rounded-xl outline-none" required>
            <button type="submit" name="login" class="w-full bg-blue-600 text-white font-bold py-4 rounded-xl shadow-lg active:scale-95 transition-all">MASUK</button>
        </form>

        <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
            <p class="text-[10px] font-bold text-yellow-700 uppercase mb-2">Data di Database Bapak saat ini:</p>
            <ul class="text-[11px] text-gray-600">
                <?php while($row = mysqli_fetch_assoc($list_user)): ?>
                    <li class="border-b border-yellow-100 py-1">
                        User: <span class="text-blue-600 font-bold">"<?php echo $row['username']; ?>"</span> | Role: <?php echo $row['role']; ?>
                    </li>
                <?php endwhile; ?>
            </ul>
            <p class="text-[9px] mt-2 text-gray-400 italic">*Pastikan username yang Bapak ketik persis sama dengan yang di atas (termasuk huruf besar/kecil).*</p>
        </div>
        
        <div class="mt-4 text-center">
            <a href="login.php?reset=1" class="text-xs text-gray-400 underline">Reset Sesi</a>
        </div>
    </div>
</body>
</html>
