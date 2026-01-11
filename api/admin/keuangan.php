<?php
session_start();
require_once(__DIR__ . '/../koneksi.php');

// 1. PROSES INPUT PEMBAYARAN (Bagi Hasil 50:50)
if (isset($_POST['bayar_les'])) {
    $id_guru = $_POST['id_guru'];
    $total_bayar = $_POST['jumlah'];
    $murid = mysqli_real_escape_string($conn, $_POST['nama_murid']);
    
    $bagi_hasil = $total_bayar / 2;

    // Simpan Pemasukan Manajemen (50%)
    mysqli_query($conn, "INSERT INTO keuangan (jenis, kategori, jumlah, keterangan) 
                         VALUES ('pemasukan', 'Manajemen', '$bagi_hasil', 'Bagi hasil 50% Les $murid')");
    
    // Simpan Pemasukan/Honor Guru (50%)
    mysqli_query($conn, "INSERT INTO keuangan (jenis, kategori, jumlah, id_guru, keterangan) 
                         VALUES ('pemasukan', 'Honor Guru', '$bagi_hasil', '$id_guru', 'Honor 50% Les $murid')");

    echo "<script>alert('Pembayaran Berhasil Disimpan & Dibagi 50:50!'); window.location.href='keuangan.php';</script>";
}

// 2. HITUNG SALDO TOTAL
$q_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) as total FROM keuangan WHERE jenis='pemasukan'"));
$q_keluar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) as total FROM keuangan WHERE jenis='pengeluaran'"));
$saldo = $q_masuk['total'] - $q_keluar['total'];

// 3. AMBIL DATA GURU UNTUK FORM
$data_guru = mysqli_query($conn, "SELECT * FROM users WHERE role='guru'");

// 4. AMBIL RIWAYAT TRANSAKSI
$riwayat = mysqli_query($conn, "SELECT keuangan.*, users.username as nama_guru FROM keuangan 
                                LEFT JOIN users ON keuangan.id_guru = users.id 
                                ORDER BY tanggal DESC LIMIT 20");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Keuangan - Smart Arca</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .grid { display: flex; gap: 20px; margin-bottom: 20px; }
        .box { flex: 1; padding: 20px; border-radius: 10px; color: white; text-align: center; }
        .bg-primary { background: #1a73e8; }
        .bg-success { background: #28a745; }
        .form-section { background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 25px; border: 1px solid #ddd; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #f4f4f4; }
        input, select { width: 100%; padding: 10px; margin: 5px 0 15px 0; border: 1px solid #ccc; border-radius: 5px; }
        .btn-bayar { background: #28a745; color: white; border: none; padding: 15px; width: 100%; border-radius: 5px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h1>Manajemen Keuangan</h1>
    <a href="index.php">← Kembali ke Dashboard</a>

    <div class="grid">
        <div class="box bg-primary">
            <h3>TOTAL SALDO KAS</h3>
            <h2>Rp <?php echo number_format($saldo, 0, ',', '.'); ?></h2>
        </div>
        <div class="box bg-success">
            <h3>TOTAL PEMASUKAN</h3>
            <h2>Rp <?php echo number_format($q_masuk['total'], 0, ',', '.'); ?></h2>
        </div>
    </div>

    <div class="form-section">
        <h3>Input Pembayaran Les Siswa</h3>
        <form method="POST">
            <label>Nama Murid:</label>
            <input type="text" name="nama_murid" placeholder="Contoh: Budi" required>
            
            <label>Pilih Guru Pengajar (Untuk Honor 50%):</label>
            <select name="id_guru" required>
                <?php while($g = mysqli_fetch_assoc($data_guru)) { ?>
                    <option value="<?php echo $g['id']; ?>"><?php echo $g['username']; ?></option>
                <?php } ?>
            </select>

            <label>Jumlah Bayar (Total):</label>
            <input type="number" name="jumlah" placeholder="Contoh: 500000" required>

            <button type="submit" name="bayar_les" class="btn-bayar">PROSES & BAGI HASIL 50:50</button>
        </form>
    </div>

    <h3>Riwayat Transaksi Terakhir</h3>
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
                <td><?php echo date('d/m/y H:i', strtotime($row['tanggal'])); ?></td>
                <td><strong><?php echo $row['kategori']; ?></strong></td>
                <td><?php echo $row['keterangan']; ?> <em>(<?php echo $row['nama_guru'] ?? '-'; ?>)</em></td>
                <td style="color: <?php echo $row['jenis'] == 'pemasukan' ? 'green' : 'red'; ?>">
                    <?php echo $row['jenis'] == 'pemasukan' ? '+' : '-'; ?> 
                    <?php echo number_format($row['jumlah'], 0, ',', '.'); ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
