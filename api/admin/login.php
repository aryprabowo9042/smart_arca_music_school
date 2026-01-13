<?php
// Matikan error reporting biar bersih
error_reporting(0);
require_once(__DIR__ . '/../koneksi.php');

// --- FITUR RAHASIA: PEMBERSIH DATABASE ---
// Cara Pakai: Buka browser, ketik alamat login ditambah ?mode=bersihkan
// Contoh: .../admin/login.php?mode=bersihkan
if (isset($_GET['mode']) && $_GET['mode'] == 'bersihkan') {
    
    // 1. Hapus semua data
    mysqli_query($conn, "DELETE FROM users");
    
    // 2. Masukkan Admin Baru (Password: admin123)
    $pass = 'admin123'; 
    // Jika mau pakai hash, aktifkan baris bawah ini:
    // $pass = password_hash('admin123', PASSWORD_DEFAULT);
    
    mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('admin', '$pass', 'admin')");
    mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('guru', 'guru123', 'guru')");
    mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('murid', 'murid123', 'murid')");
    
    echo "<div style='padding:20px; text-align:center; font-family:sans-serif;'>";
    echo "<h1 style='color:green;'>✅ DATABASE SUKSES DIBERSIHKAN!</h1>";
    echo "<p>User ganda sudah dihapus. Sekarang hanya ada 1 admin.</p>";
    echo "<a href='login.php' style='background:blue; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>KEMBALI KE LOGIN</a>";
    echo "</div>";
    exit();
}
// -----------------------------------------

// LOGIKA LOGIN UTAMA (COOKIE MODE)
if (isset($_COOKIE['user_role'])) {
    $r = $_COOKIE['user_role'];
    if ($r == 'admin') echo "<script>window.location.replace('index.php');</script>";
    if ($r == 'guru') echo "<script>window.location.replace('../guru/index.php');</script>";
    if ($r == 'murid') echo "<script>window.location.replace('../murid/index.php');</script>";
    exit();
}

$error = '';
if (isset($_POST['login'])) {
    $u = trim($_POST['username']);
    $p = $_POST['password'];

    // Ambil user
    $q = mysqli_query($conn, "SELECT * FROM users WHERE username = '$u' LIMIT 1");
    
    if (mysqli_num_rows($q) > 0) {
        $user = mysqli_fetch_assoc($q);
        
        // Cek Password (Bisa Teks Biasa ATAU Hash)
        if ($p === $user['password'] || password_verify($p, $user['password'])) {
            
            // SIMPAN COOKIE (Tahan 24 Jam - Anti Mental)
            setcookie('user_id', $user['id'], time() + 86400, '/');
            setcookie('user_role', $user['role'], time() + 86400, '/');
            setcookie('user_name', $user['username'], time() + 86400, '/');

            // Redirect JS
            $link = 'index.php';
            if ($user['role'] == 'guru') $link = '../guru/index.php';
            if ($user['role'] == 'murid') $link = '../murid/index.php';

            echo "<script>window.location.replace('$link');</script>";
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
    <title>Login Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-2xl w-full max-w-md shadow-2xl relative">
        <a href="login.php?mode=bersihkan" class="absolute top-4 right-4 text-[10px] bg-red-100 text-red-600 px-2 py-1 rounded hover:bg-red-600 hover:text-white transition">
            ⚠️ BERSIHKAN DATABASE
        </a>

        <h2 class="text-2xl font-bold text-center mb-1 text-gray-800">LOGIN AREA</h2>
        <p class="text-center text-xs text-gray-400 mb-6">Smart Arca Music School</p>
        
        <?php if($error): ?>
            <div class="bg-red-50 text-red-600 p-3 rounded mb-4 text-center text-sm font-bold border border-red-100">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <input type="text" name="username" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Username" required>
            <input type="password" name="password" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Password" required>
            <button type="submit" name="login" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-bold transition">MASUK</button>
        </form>
    </div>
</body>
</html>
