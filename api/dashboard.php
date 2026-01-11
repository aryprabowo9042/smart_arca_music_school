<?php
session_start();

// Cek apakah user sudah login?
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location: login.php");
    exit();
}

// Panggil koneksi (Path-nya langsung nama file, karena sejajar)
require_once(__DIR__ . '/koneksi.php');

// Ambil data user
$query = mysqli_query($conn, "SELECT * FROM users");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #0070f3; color: white; }
        .btn-logout { background: red; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>

    <h1>Selamat Datang, Admin!</h1>
    <p>Anda login sebagai: <strong><?php echo $_SESSION['username']; ?></strong></p>
    
    <a href="logout.php" class="btn-logout">LOGOUT KELUAR</a>

    <h3>Data Pengguna (Dari Database TiDB)</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Role</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($query)) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['username']; ?></td>
            <td><?php echo $row['role']; ?></td>
        </tr>
        <?php } ?>
    </table>

</body>
</html>
