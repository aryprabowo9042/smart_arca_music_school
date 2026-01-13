<?php
session_start();
// Hapus semua data session
session_unset();
session_destroy();

// Hapus cookie jika ada (opsional)
if (isset($_COOKIE['user_login'])) {
    setcookie('user_login', '', time() - 3600, '/');
}

// Redirect paksa menggunakan JavaScript agar lebih aman di Vercel
echo "<script>window.location.replace('admin/login.php');</script>";
exit();
