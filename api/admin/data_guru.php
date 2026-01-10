<?php
session_start();
include '../includes/koneksi.php';

if ($_SESSION['status'] != "login" || $_SESSION['role'] != "admin") {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Data Guru</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="sidebar">
        <h2>Smart Arca Admin</h2>
        <a href="index.php">Dashboard</a>
        <a href="data_guru.php" style="background-color: #495057; color: white;">Data Guru</a>
        <a href="data_murid.php">Data Murid</a>
        <a href="jadwal.php">Jadwal Les</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Data Guru</h1>
        <a href="tambah_guru.php" class="btn btn-green">+ Tambah Guru Baru</a>
        <br><br>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>No HP</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE role='guru'");
                    while ($data = mysqli_fetch_assoc($query)) {
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $data['nama_lengkap']; ?></td>
                        <td><?php echo $data['username']; ?></td>
                        <td><?php echo $data['no_hp']; ?></td>
                        <td>
                            <a href="edit_guru.php?id=<?php echo $data['id']; ?>" class="btn btn-blue">Edit</a>
                            <a href="hapus_guru.php?id=<?php echo $data['id']; ?>" class="btn btn-red" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>