<?php
session_start();
include '../includes/koneksi.php';

if ($_SESSION['role'] != 'admin') exit();

$id = $_GET['id'];

// 1. Ambil nama file dari database sebelum data dihapus
$q = mysqli_query($koneksi, "SELECT file_path FROM modul WHERE id='$id'");
$data = mysqli_fetch_assoc($q);

// Lokasi file
$file_to_delete = "../uploads/modul/" . $data['file_path'];

// 2. Hapus file fisik jika ada
if (file_exists($file_to_delete)) {
    unlink($file_to_delete);
}

// 3. Hapus data di database
mysqli_query($koneksi, "DELETE FROM modul WHERE id='$id'");

header("Location: modul.php");
?>