<?php
// api/admin/login.php
require_once(__DIR__ . '/../koneksi.php');

// Matikan error reporting biar rapi
error_reporting(0);

// Fitur Bersihkan Database (Tetap ada buat jaga-jaga)
if (isset($_GET['mode']) && $_GET['mode'] == 'bersihkan') {
    mysqli_query($conn, "DELETE FROM users");
    mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('admin', 'admin123', 'admin')");
    mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('guru', 'guru123', 'guru')");
    mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('murid', 'murid123', 'murid')");
    echo "<h1>DATABASE BERSIH. Login: admin / admin123</h1><a href='login.php'>Kembali</a>";
    exit();
}

// LOGIKA LOGIN
if (isset($_POST['login'])) {
    $u = trim($_POST['username']);
    $p = $_POST['password'];

    // Ambil data user
    $q = mysqli_query($conn, "SELECT * FROM users WHERE username = '$u' LIMIT 1");
    
    if (mysqli_num_rows($q) > 0) {
        $user = mysqli_fetch_assoc($q);
        
        // Cek Password
        if ($p === $user['password'] || password_verify($p, $user['password'])) {
            
            // --- BAGIAN PENTING: SET COOKIE DENGAN PATH '/' ---
            // Kita set cookie agar berlaku di SELURUH domain ("/")
            setcookie('user_role', $user['role'], time() + (86400 * 30), "/"); 
            setcookie('user_username', $user['username'], time() + (86400 * 30), "/");

            // Tentukan tujuan
            $tujuan = 'index.php';
            if ($user['role'] == 'guru') $tujuan = '../guru/index.php';
            if ($user['role'] == 'murid') $tujuan = '../murid/index.php';

            echo "
            <!DOCTYPE html>
            <html>
            <head><script src='https://cdn.tailwindcss.com'></script></head>
            <body class='bg-green-500 flex items-center justify-center h-screen text-white text-center'>
                <div>
                    <h1 class='text-4xl font-bold mb-4'>LOGIN SUKSES!</h1>
                    <p class='mb-6'>Menyimpan data login...</p>
                    <a href='$tujuan' class='bg-white text-green-600 px-6 py-3 rounded-full font-bold shadow-lg hover:bg-gray-100'>
                        KLIK DISINI JIKA TIDAK OTOMATIS
                    </a>
                    <script>
                        // Beri jeda 1 detik agar Cookie sempat tersimpan
                        setTimeout(function() {
                            window.location.href = '$tujuan';
                        }, 1000);
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
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold">LOGIN</h2>
            <a href="login.php?mode=bersihkan" class="text-xs text-red-500 border border-red-500 px-2 py-1 rounded">Reset DB</a>
        </div>
        
        <?php if(isset($error)) echo "<p class='text-red-500 text-center text-sm font-bold mb-4'>$error</p>"; ?>

        <form method="POST" class="space-y-4">
            <input type="text" name="username" class="w-full p-3 border rounded" placeholder="Username" required>
            <input type="password" name="password" class="w-full p-3 border rounded" placeholder="Password" required>
            <button type="submit" name="login" class="w-full bg-blue-600 text-white p-3 rounded font-bold">MASUK</button>
        </form>
    </div>
</body>
</html>
