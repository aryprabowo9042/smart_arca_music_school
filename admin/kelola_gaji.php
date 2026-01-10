<?php
session_start();
include '../includes/koneksi.php';

if ($_SESSION['status'] != "login" || $_SESSION['role'] != "admin") { header("Location: ../login.php"); exit(); }
function buatRupiah($angka){ return "Rp " . number_format($angka,0,',','.'); }

// PROSES ACC PENARIKAN
if (isset($_GET['selesai_id'])) {
    $id_tarik = $_GET['selesai_id'];
    mysqli_query($koneksi, "UPDATE penarikan SET status='selesai' WHERE id='$id_tarik'");
    echo "<script>alert('Status diubah menjadi SELESAI. Silakan transfer uang manual ke guru.'); window.location='kelola_gaji.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Gaji Guru</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="sidebar">
        <h2>Smart Arca Admin</h2>
        <a href="index.php">Dashboard</a>
        <a href="data_guru.php">Data Guru</a>
        <a href="data_murid.php">Data Murid</a>
        <a href="jadwal.php">Jadwal Les</a>
        <a href="pembayaran.php">Keuangan</a>
        <a href="kelola_gaji.php" style="background-color: #495057; color: white;">Kelola Gaji Guru</a> <a href="modul.php">Modul Belajar</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Permintaan Penarikan Dana Guru</h1>
        <p>Daftar guru yang mengajukan penarikan saldo komisi.</p>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Guru</th>
                        <th>Jumlah Penarikan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT penarikan.*, users.nama_lengkap 
                              FROM penarikan 
                              JOIN users ON penarikan.id_guru = users.id 
                              ORDER BY penarikan.status ASC, penarikan.tanggal DESC";
                    $result = mysqli_query($koneksi, $query);
                    while ($data = mysqli_fetch_assoc($result)) {
                    ?>
                    <tr>
                        <td><?php echo date('d-m-Y H:i', strtotime($data['tanggal'])); ?></td>
                        <td><b><?php echo $data['nama_lengkap']; ?></b></td>
                        <td style="font-weight: bold; font-size: 1.1rem;"><?php echo buatRupiah($data['jumlah']); ?></td>
                        <td>
                            <?php if($data['status'] == 'pending'): ?>
                                <span style="background: orange; color: white; padding: 3px 8px; border-radius: 4px;">Pending</span>
                            <?php else: ?>
                                <span style="background: green; color: white; padding: 3px 8px; border-radius: 4px;">Selesai</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($data['status'] == 'pending'): ?>
                                <a href="kelola_gaji.php?selesai_id=<?php echo $data['id']; ?>" class="btn btn-green" onclick="return confirm('Apakah Anda sudah mentransfer uang ke guru ini?')">✅ Tandai Sudah Transfer</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>