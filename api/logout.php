<?php
// 1. Mulai session
session_start();

// 2. Hapus semua data session
session_unset();

// 3. Hancurkan session
session_destroy();

// 4. Arahkan kembali ke halaman utama atau halaman login
header("Location: /index.php?pesan=logout");
exit();
?>