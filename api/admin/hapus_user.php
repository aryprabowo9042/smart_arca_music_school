<?php
session_start();
require_once(__DIR__ . '/../koneksi.php');

// Ambil ID dari URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Jalankan perintah hapus
    $query = mysqli_query($conn, "DELETE FROM users WHERE id='$id'");

    if ($query) {
        echo "<script>alert('Data Berhasil Dihapus!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Gagal Menghapus Data!'); window.location.href='index.php';</script>";
    }
} else {
    header("location: index.php");
}
?>
