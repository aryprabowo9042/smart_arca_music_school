<?php
session_start();
session_destroy();
// Arahkan kembali ke halaman login di dalam folder admin
header("Location: admin/login.php");
exit();
?>
