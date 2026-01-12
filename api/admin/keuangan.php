<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__ . '/../koneksi.php');

// 1. PROSES INPUT PEMBAYARAN
if (isset($_POST['bayar_les'])) {
    $id_guru = $_POST['id_guru'];
    $total_bayar = $_POST['jumlah'];
    $murid = mysqli_real_escape_string($conn, $_POST['nama_murid']);
    
    $bagi_hasil = $total_bayar / 2;

    mysqli_query($conn, "INSERT INTO keuangan (jenis, kategori, jumlah, keterangan) 
                         VALUES ('pemasukan', 'Manajemen', '$bagi_hasil', 'Bagi hasil 50% Les $murid')");
    
    mysqli_query($conn, "INSERT INTO keuangan (jenis, kategori, jumlah, id_guru, keterangan) 
                         VALUES ('pemasukan', 'Honor Guru', '$bagi_hasil', '$id_guru', 'Honor 50% Les $murid')");

    echo "<script>alert('Berhasil Disimpan!'); window.location.href='keuangan.php';</script>";
}

// 2. HITUNG SALDO (Perbaikan: Pakai (float) agar tidak error null)
$q_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) as total FROM keuangan WHERE jenis='pemasukan'"));
$q_keluar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) as total FROM keuangan WHERE jenis='pengeluaran'"));

$total_masuk = (float)($q_masuk['total'] ?? 0);
$total_keluar = (float)($q_keluar['total'] ?? 0);
$saldo = $total_masuk - $total_keluar;

$data_guru = mysqli_query($conn, "SELECT * FROM users WHERE role='guru'");
$riwayat = mysqli_query($conn, "SELECT keuangan.*, users.username as nama_guru FROM keuangan 
                                LEFT JOIN users ON keuangan.id_guru = users.id 
                                ORDER BY tanggal DESC LIMIT 10");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Keuangan - Smart Arca</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .grid { display: flex; gap: 15px; margin: 20px 0; }
        .box { flex: 1; padding: 15px; border-radius: 8px; color: white; text-align: center; }
        .form-input { background: #f9f9f9; padding: 15px; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 20px; }
        input, select { width: 100%; padding: 10px; margin: 8px 0; border-radius: 4px; border: 1px solid #ccc; box-sizing: border-box; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
    </style>
</head>
<body>

<div class="container">
    <h2>Manajemen Keuangan & Honor</h2>
    <a href="index.php">← Kembali ke Dashboard</a>

    <div class="grid">
        <div class="box" style="background: #1a73e8;">
            <small>SALDO KAS</small>
            <div><strong>Rp <?php echo number_format($saldo, 0, ',', '.'); ?></strong></div>
        </div>
        <div class="box" style="background: #28a745;">
            <small>TOTAL PEMASUKAN</small>
            <div><strong>Rp <?php echo number_format($total_masuk, 0, ',', '.'); ?></strong></div>
        </div>
    </div>

    <div class="form-input">
        <h4>Input Pembayaran Les</h4>
        <form method="POST">
            <input type="text" name="nama_murid" placeholder="Nama Murid" required>
            <select name="id_guru" required>
                <option value="">-- Pilih Guru --</option>
                <?php while($g = mysqli_fetch_assoc($data_guru)) { ?>
                    <option value="<?php echo $g['id']; ?>"><?php echo $g['username']; ?></option>
                <?php } ?>
            </select>
            <input type="number" name="jumlah" placeholder="Jumlah Total (Misal: 500000)" required>
            <button type="submit" name="bayar_les" style="width:100%; padding:10px; background:#28a745; color:white; border:none; cursor:pointer; font-weight:bold;">SIMPAN & BAGI 50:50</button>
        </form>
    </div>

    <h4>Riwayat Transaksi</h4>
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Keterangan</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($riwayat)) { ?>
            <tr>
                <td><?php echo $row['kategori']; ?></td>
                <td><?php echo $row['keterangan']; ?></td>
                <td style="color: green;">+<?php echo number_format((float)$row['jumlah'], 0, ',', '.'); ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
