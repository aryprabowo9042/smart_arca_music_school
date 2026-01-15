<?php
// api/logout.php
session_start();

// 1. Kosongkan semua data session
$_SESSION = array();

// 2. Hancurkan session di server
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// 3. HAPUS COOKIE SECARA AGRESIF (Penyebab utama gagal logout)
// Kita hapus dengan jalur '/' (Root) agar bersih total
$cookies_to_clear = ['user_role', 'user_id', 'user_username'];
foreach ($cookies_to_clear as $cookie_name) {
    setcookie($cookie_name, '', time() - 3600, '/');
}

// 4. Lempar ke halaman depan (Landing Page)
// Gunakan jalur relatif yang tepat dari folder /api/ ke /index.php
header("Location: ../index.php");
exit();
