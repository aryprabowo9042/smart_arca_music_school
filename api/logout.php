<?php
session_start();

// 1. Hancurkan semua data session di server
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// 2. Hancurkan cookie login manual (jika ada)
if (isset($_COOKIE['user_login'])) {
    setcookie('user_login', '', time() - 3600, '/');
    setcookie('user_role', '', time() - 3600, '/');
}

// 3. Gunakan HTML/JS Redirect (Menghindari Error 403 dari Server)
?>
<!DOCTYPE html>
<html>
<head>
    <title>Logging out...</title>
</head>
<body>
    <script>
        // Mengarahkan ke halaman login tanpa memicu proteksi firewall server
        window.location.replace("admin/login.php");
    </script>
    <p>Sedang keluar, silakan tunggu... <a href="admin/login.php">Klik di sini jika tidak berpindah.</a></p>
</body>
</html>
