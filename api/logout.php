<?php
session_start();
// Hapus semua session
$_SESSION = array();
session_destroy();

// Hapus cookie login jika ada
if (isset($_COOKIE['user_login'])) {
    setcookie('user_login', '', time() - 3600, '/');
    setcookie('user_role', '', time() - 3600, '/');
}

// Redirect ke halaman login di folder admin
header("Location: /api/admin/login.php");
exit();
