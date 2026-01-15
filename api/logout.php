<?php
// api/logout.php
session_start();
session_unset();
session_destroy();

// Daftar semua nama cookie yang mungkin kita pakai
$cookies = ['user_role', 'user_id', 'user_username', 'PHPSESSID'];

foreach ($cookies as $c) {
    // Hapus untuk semua folder (/)
    setcookie($c, '', time() - 3600, '/');
    // Hapus khusus untuk folder api
    setcookie($c, '', time() - 3600, '/api/');
}

// Gunakan JavaScript untuk membersihkan sisa memori browser
echo "
<script>
    localStorage.clear();
    sessionStorage.clear();
    window.location.replace('/'); // Langsung ke folder root (Landing Page)
</script>
<p>Sedang keluar...</p>
";
exit();
