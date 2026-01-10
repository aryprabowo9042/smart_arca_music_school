<?php
session_start();
include '../includes/koneksi.php';

// Cek Admin
if ($_SESSION['status'] != "login" || $_SESSION['role'] != "admin") { 
    header("Location: ../login.php"); 
    exit(); 
}

$id = $_GET['id'];
$password_default = "123456"; // Password standar setelah di-reset

// Update database
$query = "UPDATE users SET password='$password_default' WHERE id='$id'";

if (mysqli_query($koneksi, $query)) {
    // Redirect kembali dengan pesan sukses
    echo "<script>
            alert('Password berhasil di-reset menjadi: 123456'); 
            window.location='data_murid.php';
          </script>";
} else {
    echo "Gagal mereset password: " . mysqli_error($koneksi);
}
?>