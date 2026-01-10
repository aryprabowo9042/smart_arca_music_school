<?php
session_start();
include '../includes/koneksi.php';

if ($_SESSION['status'] != "login" || $_SESSION['role'] != "admin") { header("Location: ../login.php"); exit(); }
function buatRupiah($angka){ return "Rp " . number_format($angka,0,',','.'); }

// PROSES SIMPAN PENGELUARAN
if (isset($_POST['simpan'])) {
    $tanggal = $_POST['tanggal'];
    $keterangan = $_POST['keterangan'];
    $jumlah = $_POST['jumlah'];
    $id_admin = $_SESSION['id_user'];

    $query = "INSERT INTO pengeluaran (tanggal, keterangan, jumlah, id_admin) VALUES ('$tanggal', '$keterangan', '$jumlah', '$id_admin')";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Pengeluaran tercatat!'); window.location='pengeluaran.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Pengeluaran Operasional</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="sidebar">
        <h2>Smart Arca Admin</h2>
        <a href="index.php">Dashboard</a>
        <a href="pembayaran.php">Pemasukan (SPP)</a>
        <a href="pengeluaran.php" style="background-color: #495057; color: white;">Pengeluaran</a>
        <a href="laporan_keuangan.php">Laporan Keuangan</a> <a href="../logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Pengeluaran Operasional</h1>
        
        <div style="margin-bottom: 20px;">
            <a href="pembayaran.php" class="btn btn-blue">Input Pemasukan</a>
            <a href="pengeluaran.php" class="btn btn-green">Input Pengeluaran</a>
            <a href="laporan_keuangan.php" class="btn btn-blue">Laporan & Saldo</a>
        </div>

        <div class="card" style="background-color: #f8d7da; border: 2px solid #f5c6cb;">
            <h2 style="margin-top: 0; color: #721c24;">- Catat Pengeluaran</h2>
            <form action="" method="POST">
                <div style="display: flex; gap: 15px;">
                    <div style="flex: 1;">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required style="width: 100%; padding: 8px;">
                    </div>
                    <div style="flex: 1;">
                        <label>Jumlah (Rp)</label>
                        <input type="number" name="jumlah" placeholder="Contoh: 50000" required style="width: 100%; padding: 8px;">
                    </div>
                </div>
                <div style="margin: 15px 0;">
                    <label>Keterangan</label>
                    <input type="text" name="keterangan" placeholder="Bayar Listrik, Beli Spidol, dll" required style="width: 100%; padding: 8px;">
                </div>
                <button type="submit" name="simpan" class="btn btn-red" style="width: 100%; padding: 10px;">SIMPAN PENGELUARAN</button>
            </form>
        </div>

        <br>

        <div class="card">
            <h3>Riwayat Pengeluaran</h3>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Jumlah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $q = mysqli_query($koneksi, "SELECT * FROM pengeluaran ORDER BY tanggal DESC");
                    while ($d = mysqli_fetch_assoc($q)) {
                    ?>
                    <tr>
                        <td><?php echo date('d-m-Y', strtotime($d['tanggal'])); ?></td>
                        <td><?php echo $d['keterangan']; ?></td>
                        <td style="color: red; font-weight: bold;"><?php echo buatRupiah($d['jumlah']); ?></td>
                        <td>
                            <a href="hapus_pengeluaran.php?id=<?php echo $d['id']; ?>" class="btn btn-red" onclick="return confirm('Hapus?')">Hapus</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>