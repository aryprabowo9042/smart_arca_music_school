<?php
session_start();
session_destroy();
// Kembali ke halaman login
header("location: login.php");
exit();
?>
