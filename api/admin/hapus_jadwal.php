<?php
session_start();
include '../includes/koneksi.php';

if ($_SESSION['status'] != "login" || $_SESSION['role'] != "admin") {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'];
$query = "DELETE FROM jadwal WHERE id='$id'";

if (mysqli_query($koneksi, $query)) {
    echo "<script>alert('Jadwal berhasil dihapus!'); window.location='jadwal.php';</script>";
} else {
    echo "Gagal menghapus: " . mysqli_error($koneksi);
}
?>