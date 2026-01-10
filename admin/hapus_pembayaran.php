<?php
session_start();
include '../includes/koneksi.php';

if ($_SESSION['role'] != 'admin') exit();

$id = $_GET['id'];

// Logika: Jika transaksi dihapus, saldo guru juga harus dipotong kembali
$q = mysqli_query($koneksi, "SELECT id_guru_les, komisi_guru FROM pembayaran WHERE id='$id'");
$data = mysqli_fetch_assoc($q);

if($data['id_guru_les'] != 0 && $data['komisi_guru'] > 0) {
    $id_guru = $data['id_guru_les'];
    $komisi = $data['komisi_guru'];
    mysqli_query($koneksi, "UPDATE users SET saldo = saldo - $komisi WHERE id='$id_guru'");
}

mysqli_query($koneksi, "DELETE FROM pembayaran WHERE id='$id'");
header("Location: pembayaran.php");
?>