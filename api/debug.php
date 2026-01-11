<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>--- Smart Arca Debugging System ---</h2>";

// 1. Cek Folder Saat Ini
echo "Lokasi Folder API: " . __DIR__ . "<br>";

// 2. Cek apakah koneksi.php ada di sini
$file_koneksi = __DIR__ . '/koneksi.php';
if (file_exists($file_koneksi)) {
    echo "✅ File koneksi.php DITEMUKAN di: $file_koneksi <br>";
} else {
    echo "❌ File koneksi.php TIDAK DITEMUKAN di: $file_koneksi <br>";
}

// 3. Tes Koneksi Database
echo "<h3>--- Mengetes Koneksi TiDB ---</h3>";
include "koneksi.php";

if ($conn) {
    echo "✅ KONEKSI DATABASE BERHASIL!<br>";
    
    // 4. Cek Data di Tabel Users
    $query = mysqli_query($conn, "SELECT * FROM users");
    if ($query) {
        $jumlah = mysqli_num_rows($query);
        echo "✅ Tabel 'users' DITEMUKAN. <br>";
        echo "✅ Jumlah data user: " . $jumlah . " baris.<br>";
        
        while($row = mysqli_fetch_assoc($query)) {
            echo "- User: " . $row['username'] . " (Role: " . $row['role'] . ")<br>";
        }
    } else {
        echo "❌ Tabel 'users' TIDAK DITEMUKAN atau Error Query: " . mysqli_error($conn) . "<br>";
    }
} else {
    echo "❌ KONEKSI DATABASE GAGAL.<br>";
}
?>