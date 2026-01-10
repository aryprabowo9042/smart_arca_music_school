<?php
session_start();
include '../includes/koneksi.php';

// Cek Admin
if ($_SESSION['status'] != "login" || $_SESSION['role'] != "admin") {
    header("Location: ../login.php");
    exit();
}

// Ambil ID dari URL
$id = $_GET['id'];

// Hapus data
$query = "DELETE FROM users WHERE id='$id'";
if (mysqli_query($koneksi, $query)) {
    echo "<script>alert('Data berhasil dihapus!'); window.location='data_guru.php';</script>";
} else {
    echo "Gagal menghapus: " . mysqli_error($koneksi);
}
?>