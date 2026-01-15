<?php
// api/logout.php
session_start();
session_unset();
session_destroy();

// Hapus semua cookie dengan jalur '/' agar bersih total di semua folder
$expire = time() - 3600;
setcookie('user_role', '', $expire, '/');
setcookie('user_id', '', $expire, '/');
setcookie('user_username', '', $expire, '/');

// Gunakan JavaScript sebagai pengaman tambahan jika Header PHP gagal
echo "
<script>
    localStorage.clear();
    sessionStorage.clear();
    window.location.replace('/'); // Paksa kembali ke halaman utama (root)
</script>
";
exit();
