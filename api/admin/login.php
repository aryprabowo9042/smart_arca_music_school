<?php
// api/admin/login.php
require_once(__DIR__ . '/../koneksi.php');
error_reporting(0);

// Fitur Reset DB (Tetap ada)
if (isset($_GET['mode']) && $_GET['mode'] == 'bersihkan') {
    mysqli_query($conn, "DELETE FROM users");
    mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('admin', 'admin123', 'admin')");
    mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('guru', 'guru123', 'guru')");
    mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('murid', 'murid123', 'murid')");
    echo "<h1>DATABASE BERSIH.</h1><a href='login.php'>Kembali</a>";
    exit();
}

// LOGIKA LOGIN
if (isset($_POST['login'])) {
    $u = trim($_POST['username']);
    $p = $_POST['password'];

    $q = mysqli_query($conn, "SELECT * FROM users WHERE username = '$u' LIMIT 1");
    
    if (mysqli_num_rows($q) > 0) {
        $user = mysqli_fetch_assoc($q);
        
        if ($p === $user['password'] || password_verify($p, $user['password'])) {
            
            // --- BAGIAN YANG SAYA PERBAIKI ---
            // Menyimpan ID, Role, dan Username sekaligus
            setcookie('user_id', $user['id'], time() + (86400 * 30), "/"); // <--- INI YANG TADI KURANG
            setcookie('user_role', $user['role'], time() + (86400 * 30), "/");
            setcookie('user_username', $user['username'], time() + (86400 * 30), "/");

            $tujuan = 'index.php';
            if ($user['role'] == 'guru') $tujuan = '../guru/index.php';
            if ($user['role'] == 'murid') $tujuan = '../murid/index.php';

            echo "
            <!DOCTYPE html>
            <html>
            <head><script src='https://cdn.tailwindcss.com'></script></head>
            <body class='bg-green-500 flex items-center justify-center h-screen text-white text-center'>
                <div>
                    <h1 class='text-4xl font-bold mb-4'>LOGIN BERHASIL!</h1>
                    <p>Mengalihkan...</p>
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
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-800 flex items-center justify-center h-screen p-4">
    <div class="bg-white p-8 rounded-xl w-full max-w-sm">
        <h2 class="text-xl font-bold mb-6">LOGIN</h2>
        <?php if(isset($error)) echo "<p class='text-red-500 text-center text-sm font-bold mb-4'>$error</p>"; ?>
        <form method="POST" class="space-y-4">
            <input type="text" name="username" class="w-full p-3 border rounded" placeholder="Username" required>
            <input type="password" name="password" class="w-full p-3 border rounded" placeholder="Password" required>
            <button type="submit" name="login" class="w-full bg-blue-600 text-white p-3 rounded font-bold">MASUK</button>
        </form>
    </div>
</body>
</html>
