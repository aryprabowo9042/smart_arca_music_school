<?php
session_start();
require_once(__DIR__ . '/../koneksi.php');

$id = $_GET['id'];
$q = mysqli_query($conn, "SELECT * FROM absensi WHERE id='$id'");
$data = mysqli_fetch_assoc($q);

if (isset($_POST['update'])) {
    $tgl = $_POST['tanggal'];
    $mtr = mysqli_real_escape_string($conn, $_POST['materi']);
    $nom = $_POST['nominal_bayar'];

    mysqli_query($conn, "UPDATE absensi SET tanggal='$tgl', materi_ajar='$mtr', nominal_bayar='$nom' WHERE id='$id'");
    header("Location: index.php");
}
?>
