<?php
session_start();

// Panggil koneksi (Naik satu tingkat ke folder api)
require_once(__DIR__ . '/../koneksi.php');

// Ambil data user untuk membuktikan database tetap connect
$query = mysqli_query($conn, "SELECT * FROM users");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin - Smart Arca</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f4f7f6; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #0070f3; color: white; }
        .status-ok { color: green; font-weight: bold; }
    </style>
</head>
<body>

    <div class="container">
        <h1>Dashboard Admin</h1>
        <p class="status-ok">✅ Koneksi Database: Aktif</p>
        
        <hr>

        <h3>Daftar Pengguna Sistem</h3>
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

        <br>
        <a href="../logout.php" style="color: red;">Keluar Sistem</a>
    </div>

</body>
</html>
