<?php
session_start();
include '../includes/koneksi.php';

if ($_SESSION['status'] != "login" || $_SESSION['role'] != "murid") { header("Location: ../login.php"); exit(); }

$id_murid = $_SESSION['id_user'];

// 1. Cek Murid ini kelas apa & level apa
$q_murid = mysqli_query($koneksi, "SELECT kelas_musik, level_musik FROM users WHERE id='$id_murid'");
$data_murid = mysqli_fetch_assoc($q_murid);

$kelas_saya = $data_murid['kelas_musik'];
$level_saya = $data_murid['level_musik'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Materi Belajar</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="sidebar">
        <h2>Area Siswa</h2>
        <a href="index.php">Dashboard</a>
        <a href="jadwal_saya.php">Jadwal Les</a>
        <a href="rekap_belajar.php">Rekap Hasil Belajar</a>
        <a href="pembayaran_saya.php">Riwayat Pembayaran</a>
        <a href="modul_saya.php" style="background-color: #495057; color: white;">Materi Belajar</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Materi Belajar Digital</h1>
        
        <div style="background: #d4edda; padding: 15px; border-radius: 5px; color: #155724; margin-bottom: 20px;">
            Anda terdaftar di kelas <b><?php echo $kelas_saya; ?></b> (Level: <?php echo $level_saya; ?>). <br>
            Berikut adalah materi yang sesuai untuk Anda.
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Judul Materi</th>
                        <th>Deskripsi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // 2. Query hanya modul yang cocok dengan kelas & level murid
                    $query = "SELECT * FROM modul 
                              WHERE kelas_musik = '$kelas_saya' 
                              AND level_target = '$level_saya' 
                              ORDER BY id DESC";
                    
                    $result = mysqli_query($koneksi, $query);

                    // Jika tidak ada modul yang cocok
                    if(mysqli_num_rows($result) == 0){
                        echo "<tr><td colspan='3' style='text-align:center; padding: 20px;'>Belum ada materi untuk kelas ini.</td></tr>";
                    }

                    while ($data = mysqli_fetch_assoc($result)) {
                    ?>
                    <tr>
                        <td><b><?php echo $data['judul']; ?></b></td>
                        <td><?php echo $data['deskripsi']; ?></td>
                        <td>
                            <a href="../uploads/modul/<?php echo $data['file_path']; ?>" target="_blank" class="btn btn-blue" style="text-decoration:none; padding:5px 10px; border-radius:3px;">Buka / Download</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>