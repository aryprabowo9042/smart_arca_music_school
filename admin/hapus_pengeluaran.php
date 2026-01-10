<?php
session_start();
include '../includes/koneksi.php';

// Cek apakah user adalah admin
if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Ambil ID dari URL
$id = $_GET['id'];

// Hapus data dari database
$query = "DELETE FROM pengeluaran WHERE id='$id'";

if (mysqli_query($koneksi, $query)) {
    // Jika berhasil, kembali ke halaman pengeluaran
    header("Location: pengeluaran.php");
} else {
    echo "Gagal menghapus data: " . mysqli_error($koneksi);
}
?>