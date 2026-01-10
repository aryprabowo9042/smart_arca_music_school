<?php
session_start();
include '../includes/koneksi.php';

if ($_SESSION['status'] != "login" || $_SESSION['role'] != "admin") { header("Location: ../login.php"); exit(); }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Data Siswa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="sidebar">
        <h2>Smart Arca Admin</h2>
        <a href="index.php">Dashboard</a>
        <a href="data_guru.php">Data Guru</a>
        <a href="data_murid.php" style="background-color: #495057; color: white;">Data Murid</a>
        <a href="jadwal.php">Jadwal Les</a>
        <a href="absensi.php">Data Absensi</a>
        <a href="pembayaran.php">Keuangan</a>
        <a href="kelola_gaji.php">Kelola Gaji Guru</a>
        <a href="modul.php">Modul Belajar</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Data Siswa</h1>
        <a href="tambah_murid.php" class="btn btn-green">+ Tambah Siswa Baru</a>
        <br><br>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Password</th> <th>Kelas / Level</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE role='murid'");
                    while ($data = mysqli_fetch_assoc($query)) {
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $data['nama_lengkap']; ?></td>
                        <td><?php echo $data['username']; ?></td>
                        
                        <td style="font-family: monospace; color: #d63384;">
                            <?php echo $data['password']; ?>
                        </td>

                        <td>
                            <?php echo $data['kelas_musik']; ?> <br>
                            <small>(Level: <?php echo $data['level_musik']; ?>)</small>
                        </td>
                        <td>
                            <a href="edit_murid.php?id=<?php echo $data['id']; ?>" class="btn btn-blue" style="padding: 5px 8px; font-size: 12px;">Edit</a>
                            
                            <a href="reset_password_murid.php?id=<?php echo $data['id']; ?>" class="btn" style="background-color: #ffc107; color: #333; padding: 5px 8px; font-size: 12px; text-decoration: none; border-radius: 3px;" onclick="return confirm('Reset password siswa ini menjadi 123456?')">Reset Pass</a>

                            <a href="hapus_murid.php?id=<?php echo $data['id']; ?>" class="btn btn-red" style="padding: 5px 8px; font-size: 12px;" onclick="return confirm('Yakin ingin menghapus siswa ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>