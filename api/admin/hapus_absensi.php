<?php
session_start();
include '../includes/koneksi.php';

if ($_SESSION['role'] != 'admin') exit();

$id = $_GET['id'];
mysqli_query($koneksi, "DELETE FROM absensi WHERE id='$id'");
header("Location: absensi.php");
?>