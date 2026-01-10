<?php
session_start();
include '../includes/koneksi.php';

if ($_SESSION['status'] != "login" || $_SESSION['role'] != "admin") {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'];

// Hapus data dari tabel users
$query = "DELETE FROM users WHERE id='$id'";

if (mysqli_query($koneksi, $query)) {
    echo "<script>alert('Data murid berhasil dihapus!'); window.location='data_murid.php';</script>";
} else {
    echo "Gagal menghapus: " . mysqli_error($koneksi);
}
?>