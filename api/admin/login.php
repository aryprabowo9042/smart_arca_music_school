<?php
session_start();
ob_start();
require_once(__DIR__ . '/../koneksi.php');

// Hapus Sesi jika diminta
if (isset($_GET['reset'])) {
    session_destroy();
    header("Location: login.php"); exit();
}

$error = '';
if (isset($_POST['login'])) {
    $u = trim($_POST['username']); 
    $p = $_POST['password'];

    // Ambil satu user saja (limit 1) untuk menghindari error data ganda
    $query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$u' LIMIT 1");
    
    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);
        
        // Cek apakah password cocok (Teks biasa atau Enkripsi)
        if ($p === $user['password'] || password_verify($p, $user['password'])) {
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            session_write_close();

            // Redirect ke dashboard admin
            echo "<script>window.location.replace('index.php');</script>";
            exit();
        } else {
            $error = "Password untuk '$u' salah!";
        }
    } else {
        $error = "Username '$u' tidak ditemukan!";
    }
}

$list_user = mysqli_query($conn, "SELECT username, role FROM users LIMIT 10");
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 flex flex-col items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-md">
        <h2 class="text-3xl font-bold text-center mb-8 text-gray-800">LOGIN PORTAL</h2>
        
        <?php if($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm font-bold border border-red-100 text-center italic">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">
            <input type="text" name="username" class="w-full p-4 bg-gray-50 border rounded-2xl outline-none" placeholder="Username" required>
            <input type="password" name="password" class="w-full p-4 bg-gray-50 border rounded-2xl outline-none" placeholder="Password" required>
            <button type="submit" name="login" class="w-full bg-blue-600 text-white font-bold py-4 rounded-2xl shadow-lg active:scale-95 transition-all">MASUK</button>
        </form>

        <div class="mt-8 p-5 bg-amber-50 border border-amber-100 rounded-2xl">
            <p class="text-[10px] font-bold text-amber-700 uppercase mb-3 underline">Status Database:</p>
            <ul class="text-[12px] text-gray-700 space-y-2">
                <?php while($row = mysqli_fetch_assoc($list_user)): ?>
                    <li class="flex justify-between border-b border-amber-100 pb-1">
                        <span>User: <strong class="text-blue-600">"<?php echo $row['username']; ?>"</strong></span>
                        <span class="bg-amber-200 px-2 rounded text-[9px] font-bold"><?php echo $row['role']; ?></span>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
        
        <div class="mt-6 text-center">
            <a href="login.php?reset=1" class="text-xs text-gray-400 hover:text-blue-500 underline">Reset Sesi & Cache</a>
        </div>
    </div>
</body>
</html>
