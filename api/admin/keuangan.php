<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__ . '/../koneksi.php');

// 1. PROSES INPUT PEMBAYARAN LES (Bagi Hasil 50:50)
if (isset($_POST['bayar_les'])) {
    $id_guru = $_POST['id_guru'];
    $total_bayar = $_POST['jumlah'];
    $murid = mysqli_real_escape_string($conn, $_POST['nama_murid']);
    $bagi_hasil = $total_bayar / 2;

    mysqli_query($conn, "INSERT INTO keuangan (jenis, kategori, jumlah, keterangan) 
                         VALUES ('pemasukan', 'Manajemen', '$bagi_hasil', 'Bagi hasil 50% Les $murid')");
    
    mysqli_query($conn, "INSERT INTO keuangan (jenis, kategori, jumlah, id_guru, keterangan) 
                         VALUES ('pemasukan', 'Honor Guru', '$bagi_hasil', '$id_guru', 'Honor 50% Les $murid')");
    echo "<script>alert('Pembayaran Les Berhasil!'); window.location.href='keuangan.php';</script>";
}

// 2. PROSES INPUT PEMASUKAN LAIN (Pendaftaran, Saldo Awal, dll)
if (isset($_POST['input_pemasukan_lain'])) {
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori_masuk']);
    $jumlah = $_POST['jumlah_masuk'];
    $ket = mysqli_real_escape_string($conn, $_POST['ket_masuk']);

    mysqli_query($conn, "INSERT INTO keuangan (jenis, kategori, jumlah, keterangan) 
                         VALUES ('pemasukan', '$kategori', '$jumlah', '$ket')");
    echo "<script>alert('Pemasukan Berhasil Dicatat!'); window.location.href='keuangan.php';</script>";
}

// 3. PROSES INPUT PENGELUARAN (Sewa, Listrik, Gaji, dll)
if (isset($_POST['input_pengeluaran'])) {
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori_keluar']);
    $jumlah = $_POST['jumlah_keluar'];
    $ket = mysqli_real_escape_string($conn, $_POST['ket_keluar']);

    mysqli_query($conn, "INSERT INTO keuangan (jenis, kategori, jumlah, keterangan) 
                         VALUES ('pengeluaran', '$kategori', '$jumlah', '$ket')");
    echo "<script>alert('Pengeluaran Berhasil Dicatat!'); window.location.href='keuangan.php';</script>";
}

// 4. HITUNG PERHITUNGAN SALDO
$q_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) as total FROM keuangan WHERE jenis='pemasukan'"));
$q_keluar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) as total FROM keuangan WHERE jenis='pengeluaran'"));

$total_masuk = (float)($q_masuk['total'] ?? 0);
$total_keluar = (float)($q_keluar['total'] ?? 0);
$saldo_akhir = $total_masuk - $total_keluar;

$data_guru = mysqli_query($conn, "SELECT * FROM users WHERE role='guru'");
$riwayat = mysqli_query($conn, "SELECT keuangan.*, users.username as nama_guru FROM keuangan 
                                LEFT JOIN users ON keuangan.id_guru = users.id 
                                ORDER BY tanggal DESC LIMIT 20");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keuangan - Smart Arca</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 20px; font-size: 14px; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .dashboard-saldo { display: flex; gap: 15px; margin: 20px 0; }
        .card-saldo { flex: 1; padding: 20px; border-radius: 8px; color: white; text-align: center; }
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .form-box { background: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #ddd; }
        
        input, select { width: 100%; padding: 8px; margin: 8px 0; border-radius: 4px; border: 1px solid #ccc; box-sizing: border-box; }
        button { width: 100%; padding: 10px; border: none; border-radius: 4px; color: white; font-weight: bold; cursor: pointer; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #eee; }
        .text-masuk { color: green; font-weight: bold; }
        .text-keluar { color: red; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h2>Manajemen Kas & Saldo Berjalan</h2>
    <a href="index.php">← Kembali ke Dashboard</a>

    <div class="dashboard-saldo">
        <div class="card-saldo" style="background: #28a745;">
            <small>TOTAL PEMASUKAN (+)</small>
            <h2>Rp <?php echo number_format($total_masuk, 0, ',', '.'); ?></h2>
        </div>
        <div class="card-saldo" style="background: #dc3545;">
            <small>TOTAL PENGELUARAN (-)</small>
            <h2>Rp <?php echo number_format($total_keluar, 0, ',', '.'); ?></h2>
        </div>
        <div class="card-saldo" style="background: #1a73e8;">
            <small>SALDO AKHIR (BERJALAN)</small>
            <h2>Rp <?php echo number_format($saldo_akhir, 0, ',', '.'); ?></h2>
        </div>
    </div>

    <div class="form-grid">
        <div class="form-box">
            <h4 style="margin-top:0; color: #28a745;">Pemasukan (+)</h4>
            
            <form method="POST" style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px dashed #ccc;">
                <p style="margin:0; font-weight:bold; font-size:12px;">PAGUYUBAN LES (BAGI 50:50)</p>
                <input type="text" name="nama_murid" placeholder="Nama Murid" required>
                <select name="id_guru" required>
                    <option value="">-- Pilih Guru --</option>
                    <?php mysqli_data_seek($data_guru, 0); while($g = mysqli_fetch_assoc($data_guru)) { ?>
                        <option value="<?php echo $g['id']; ?>"><?php echo $g['username']; ?></option>
                    <?php } ?>
                </select>
                <input type="number" name="jumlah" placeholder="Jumlah Bayar Total" required>
                <button type="submit" name="bayar_les" style="background: #28a745;">CATAT BAYAR LES</button>
            </form>

            <form method="POST">
                <p style="margin:0; font-weight:bold; font-size:12px;">PEMASUKAN LAIN (PENDAFTARAN/DLL)</p>
                <select name="kategori_masuk" required>
                    <option>Pendaftaran</option>
                    <option>Saldo Bulan Lalu</option>
                    <option>Penjualan Alat/Buku</option>
                    <option>Lain-lain</option>
                </select>
                <input type="number" name="jumlah_masuk" placeholder="Jumlah" required>
                <input type="text" name="ket_masuk" placeholder="Keterangan Tambahan">
                <button type="submit" name="input_pemasukan_lain" style="background: #17a2b8;">CATAT PEMASUKAN LAIN</button>
            </form>
        </div>

        <div class="form-box">
            <h4 style="margin-top:0; color: #dc3545;">Pengeluaran (-)</h4>
            <form method="POST">
                <select name="kategori_keluar" required>
                    <option>Sewa Gedung</option>
                    <option>Listrik & Air</option>
                    <option>Gaji Karyawan</option>
                    <option>Alat Tulis & Kantor</option>
                    <option>Promosi/Iklan</option>
                    <option>Perawatan Alat Musik</option>
                    <option>Lain-lain</option>
                </select>
                <input type="number" name="jumlah_keluar" placeholder="Jumlah Pengeluaran" required>
                <input type="text" name="ket_keluar" placeholder="Keterangan (Contoh: Bayar Listrik Jan)">
                <button type="submit" name="input_pengeluaran" style="background: #dc3545;">CATAT PENGELUARAN</button>
            </form>
        </div>
    </div>

    <h4>Riwayat 20 Transaksi Terakhir</h4>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th>Keterangan</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($riwayat)) { ?>
            <tr>
                <td><small><?php echo date('d/m/y H:i', strtotime($row['tanggal'])); ?></small></td>
                <td><strong><?php echo $row['kategori']; ?></strong></td>
                <td><?php echo $row['keterangan']; ?> <?php echo ($row['nama_guru']) ? "<br><small>(Guru: ".$row['nama_guru'].")</small>" : ""; ?></td>
                <td class="<?php echo ($row['jenis'] == 'pemasukan') ? 'text-masuk' : 'text-keluar'; ?>">
                    <?php echo ($row['jenis'] == 'pemasukan') ? '+' : '-'; ?>
                    <?php echo number_format($row['jumlah'], 0, ',', '.'); ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
