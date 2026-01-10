<?php
session_start();
include '../includes/koneksi.php';

if ($_SESSION['status'] != "login" || $_SESSION['role'] != "guru") { header("Location: ../login.php"); exit(); }

$id_guru = $_SESSION['id_user'];
function buatRupiah($angka){ return "Rp " . number_format($angka,0,',','.'); }

// AMBIL SALDO SAAT INI
$q_saldo = mysqli_query($koneksi, "SELECT saldo FROM users WHERE id='$id_guru'");
$d_saldo = mysqli_fetch_assoc($q_saldo);
$saldo_sekarang = $d_saldo['saldo'];

// PROSES TARIK DANA
if (isset($_POST['tarik'])) {
    $jumlah_tarik = $_POST['jumlah'];

    if ($jumlah_tarik > $saldo_sekarang) {
        echo "<script>alert('Saldo tidak cukup!');</script>";
    } else {
        // 1. Catat di tabel penarikan
        $q_insert = "INSERT INTO penarikan (id_guru, jumlah, status) VALUES ('$id_guru', '$jumlah_tarik', 'pending')";
        mysqli_query($koneksi, $q_insert);

        // 2. Potong Saldo Guru Langsung (Supaya tidak ditarik dobel)
        mysqli_query($koneksi, "UPDATE users SET saldo = saldo - $jumlah_tarik WHERE id='$id_guru'");

        echo "<script>alert('Permintaan penarikan berhasil dikirim! Menunggu persetujuan Admin.'); window.location='dompet.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dompet & Gaji</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="sidebar">
        <h2>Panel Guru</h2>
        <a href="index.php">Dashboard</a>
        <a href="jadwal_saya.php">Jadwal Mengajar</a>
        <a href="keuangan.php">Input Pembayaran</a>
        <a href="dompet.php" style="background-color: #495057; color: white;">Dompet Saya</a> <a href="../logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Dompet Saya</h1>
        
        <div class="card" style="background: linear-gradient(135deg, #28a745, #218838); color: white; text-align: center; padding: 30px;">
            <p style="font-size: 1.2rem;">Saldo Aktif (Komisi 50%)</p>
            <h1 style="font-size: 3.5rem; margin: 10px 0;"><?php echo buatRupiah($saldo_sekarang); ?></h1>
        </div>

        <br>

        <div class="card">
            <h3>Ajukan Penarikan Dana</h3>
            <form action="" method="POST">
                <div class="form-group">
                    <label>Jumlah Penarikan (Rp)</label>
                    <input type="number" name="jumlah" max="<?php echo $saldo_sekarang; ?>" required style="width: 100%; padding: 10px;">
                </div>
                <button type="submit" name="tarik" class="btn btn-green">Tarik Dana Sekarang</button>
            </form>
        </div>

        <br>

        <div class="card">
            <h3>Riwayat Penarikan</h3>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal Request</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $q_riwayat = mysqli_query($koneksi, "SELECT * FROM penarikan WHERE id_guru='$id_guru' ORDER BY id DESC");
                    while ($r = mysqli_fetch_assoc($q_riwayat)) {
                        $status_color = ($r['status'] == 'selesai') ? 'green' : (($r['status'] == 'pending') ? 'orange' : 'red');
                    ?>
                    <tr>
                        <td><?php echo date('d-m-Y H:i', strtotime($r['tanggal'])); ?></td>
                        <td><?php echo buatRupiah($r['jumlah']); ?></td>
                        <td style="color: <?php echo $status_color; ?>; font-weight: bold;">
                            <?php echo strtoupper($r['status']); ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>