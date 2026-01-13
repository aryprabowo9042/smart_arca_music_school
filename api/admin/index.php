<?php
session_start();
ob_start();

// Jika tombol logout ditekan
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    echo "<script>window.location.replace('login.php');</script>";
    exit();
}

// PROTEKSI SEDERHANA (Jika session kosong, baru lempar ke login)
if (!isset($_SESSION['role'])) {
    echo "<script>window.location.replace('login.php');</script>";
    exit();
}

require_once(__DIR__ . '/../koneksi.php');
// ... sisa kode HTML dashboard Bapak ...
?>
<h1 class="p-10 font-bold text-2xl text-center">SELAMAT DATANG ADMIN</h1>
<div class="text-center">
    <a href="index.php?action=logout" class="text-red-500 font-bold">LOGOUT</a>
</div>
