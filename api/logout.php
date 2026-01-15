<?php
session_start();
session_unset();
session_destroy();

// Hapus semua Cookie login
$expire = time() - 3600;
setcookie('user_role', '', $expire, '/');
setcookie('user_id', '', $expire, '/');
setcookie('user_username', '', $expire, '/');

// Lempar ke halaman depan
header("Location: index.php");
exit();
