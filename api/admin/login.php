<?php
session_start();
ob_start();
require_once(__DIR__ . '/../koneksi.php');

// Reset jika ada parameter reset
if (isset($_GET['reset'])) {
    session_destroy();
    setcookie('user_role', '', time() - 3600, '/');
    header("Location: login.php"); exit();
}

$error = '';

if (isset($_POST['login'])) {
    $u = trim($_POST['username']); 
    $p = $_POST['password'];

    // TRIK "PENYAPU RANJAU": Ambil SEMUA user dengan nama tersebut, jangan cuma satu
    $query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$u'");
    
    $login_berhasil = false;
    $data_user_benar = null;

    // Cek satu per satu sampai ketemu yang passwordnya benar
    if (mysqli_num_rows($query) > 0) {
        while ($user = mysqli_fetch_assoc($query)) {
            // Coba cocokkan password (baik teks biasa maupun enkripsi)
            if ($p === $user['password'] || password_verify($p, $user['password'])) {
                $login_berhasil = true;
                $data_user_benar = $user;
                break; // HORE! Ketemu! Berhenti mencari.
            }
        }
    } else {
        $error = "Username tidak ditemukan!";
    }

    if ($login_berhasil && $data_user_benar) {
        // Simpan data sesi dari user yang BENAR tadi
        $_SESSION['id'] = $data_user_benar['id'];
        $_SESSION['username'] = $data_user_benar['username'];
        $_SESSION['role'] = $data_user_benar['role'];
        
        // Simpan cookie cadangan
        setcookie('user_role', $data_user_benar['role'], time() + 86400, '/');

        // Tentukan tujuan
        $url = 'index.php'; // Default admin
        if ($data_user_benar['role'] == 'guru') $url = '../guru/index.php';
        if ($data_user_benar['role'] == 'murid') $url = '../murid/index.php';

        // Redirect Paksa dengan JavaScript
        echo "<script>
            window.location.href = '$url';
        </script>";
        exit();
    } else {
        if (empty($error)) {
            $error = "Password salah! (Sudah dicoba ke semua akun '$u' yang ganda, tidak ada yang cocok).";
        }
    }
}

// Data untuk Debugging Tampilan
$list_user = mysqli_query($conn, "SELECT username, role, password FROM users LIMIT 10");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-2xl w-full max-w-md shadow-2xl">
        <h2 class="text-2xl font-bold text-center mb-2 text-gray-800">LOGIN SISTEM</h2>
        <p class="text-center text-xs text-gray-400 mb-6">Mode: Multi-Check User</p>
        
        <?php if($error): ?>
            <div class="bg-red-100 text-red-600 p-3 rounded mb-4 text-xs font-bold text-center border border-red-200">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">USERNAME</label>
                <input type="text" name="username" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" placeholder="Contoh: admin" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">PASSWORD</label>
                <input type="password" name="password" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" placeholder="Contoh: admin123" required>
            </div>
            <button type="submit" name="login" class="w-full bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-lg font-bold transition duration-200">MASUK SEKARANG</button>
        </form>

        <div class="mt-8 pt-4 border-t border-gray-100">
            <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">Isi Database (5 Data Teratas):</p>
            <div class="bg-yellow-50 p-2 rounded border border-yellow-100">
                <ul class="space-y-1">
                    <?php while($r = mysqli_fetch_assoc($list_user)): ?>
                    <li class="flex justify-between text-[10px] text-gray-600 border-b border-yellow-100 last:border-0 pb-1 last:pb-0">
                        <span class="font-bold text-blue-600"><?php echo $r['username']; ?></span>
                        <span><?php echo $r['role']; ?></span>
                        <span class="text-gray-400 font-mono"><?php echo substr($r['password'], 0, 6); ?>...</span>
                    </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
