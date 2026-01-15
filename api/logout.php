<?php
// api/logout.php
session_start();

// 1. Hapus semua data di server
$_SESSION = array();
session_destroy();

// 2. Hapus semua Cookie (user_role, user_id, user_username)
// Kita gunakan waktu mundur (expired) agar browser langsung menghapusnya
$expire = time() - 3600;
setcookie('user_role', '', $expire, '/');
setcookie('user_id', '', $expire, '/');
setcookie('user_username', '', $expire, '/');

// 3. Gunakan JavaScript untuk memastikan browser benar-benar keluar
// Ini jauh lebih ampuh daripada header PHP saja
echo "
<!DOCTYPE html>
<html>
<body>
    <script>
        // Hapus sisa-sisa memori di browser
        localStorage.clear();
        sessionStorage.clear();
        
        // Paksa pindah ke halaman depan
        window.location.replace('../index.php');
    </script>
    <p>Mohon tunggu, sedang keluar...</p>
</body>
</html>
";
exit();
