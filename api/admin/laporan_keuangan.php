<?php
session_start();
include '../includes/koneksi.php';

if ($_SESSION['status'] != "login" || $_SESSION['role'] != "admin") { header("Location: ../login.php"); exit(); }
function buatRupiah($angka){ return "Rp " . number_format($angka,0,',','.'); }

// --- LOGIKA PENGGABUNGAN DATA (MERGE) ---
$transaksi = [];

// 1. Ambil Pemasukan (SPP)
$q_masuk = mysqli_query($koneksi, "SELECT * FROM pembayaran");
while($r = mysqli_fetch_assoc($q_masuk)){
    $transaksi[] = [
        'tanggal' => $r['tanggal'],
        'jenis' => 'masuk',
        'keterangan' => "Pemasukan: " . $r['keterangan'],
        'jumlah' => $r['jumlah']
    ];
}

// 2. Ambil Pengeluaran (Operasional)
$q_keluar = mysqli_query($koneksi, "SELECT * FROM pengeluaran");
while($r = mysqli_fetch_assoc($q_keluar)){
    $transaksi[] = [
        'tanggal' => $r['tanggal'],
        'jenis' => 'keluar',
        'keterangan' => "Biaya: " . $r['keterangan'],
        'jumlah' => $r['jumlah']
    ];
}

// 3. Ambil Pengeluaran (Gaji Guru / Withdraw) - Hanya yang status SELESAI
$q_gaji = mysqli_query($koneksi, "SELECT penarikan.*, users.nama_lengkap FROM penarikan JOIN users ON penarikan.id_guru = users.id WHERE status='selesai'");
while($r = mysqli_fetch_assoc($q_gaji)){
    $transaksi[] = [
        'tanggal' => date('Y-m-d', strtotime($r['tanggal'])), // Format tanggal penarikan biasanya datetime
        'jenis' => 'keluar',
        'keterangan' => "Gaji Guru: " . $r['nama_lengkap'],
        'jumlah' => $r['jumlah']
    ];
}

// 4. Urutkan Transaksi Berdasarkan Tanggal (Terlama ke Terbaru untuk Saldo Berjalan)
usort($transaksi, function($a, $b) {
    return strtotime($a['tanggal']) - strtotime($b['tanggal']);
});

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Laporan Keuangan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="sidebar">
        <h2>Smart Arca Admin</h2>
        <a href="index.php">Dashboard</a>
        <a href="pembayaran.php">Pemasukan (SPP)</a>
        <a href="pengeluaran.php">Pengeluaran</a>
        <a href="laporan_keuangan.php" style="background-color: #495057; color: white;">Laporan Keuangan</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Laporan Arus Kas & Saldo</h1>
        
        <div style="margin-bottom: 20px;">
            <a href="pembayaran.php" class="btn btn-blue">Input Pemasukan</a>
            <a href="pengeluaran.php" class="btn btn-green">Input Pengeluaran</a>
            <a href="cetak_laporan.php" target="_blank" class="btn" style="background: #ffc107; color: #000; float: right;">🖨️ Cetak Laporan PDF</a>
        </div>

        <div class="card">
            <table border="1" style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
                <thead>
                    <tr style="background: #f8f9fa;">
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th style="color: green;">Debet (Masuk)</th>
                        <th style="color: red;">Kredit (Keluar)</th>
                        <th style="background: #e2e6ea;">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $saldo = 0;
                    $total_masuk = 0;
                    $total_keluar = 0;

                    foreach ($transaksi as $t) {
                        $debet = 0;
                        $kredit = 0;

                        if($t['jenis'] == 'masuk'){
                            $debet = $t['jumlah'];
                            $saldo += $debet;
                            $total_masuk += $debet;
                        } else {
                            $kredit = $t['jumlah'];
                            $saldo -= $kredit;
                            $total_keluar += $kredit;
                        }
                    ?>
                    <tr>
                        <td style="text-align: center;"><?php echo $no++; ?></td>
                        <td><?php echo date('d-m-Y', strtotime($t['tanggal'])); ?></td>
                        <td><?php echo $t['keterangan']; ?></td>
                        <td style="text-align: right; color: green;">
                            <?php echo ($debet > 0) ? number_format($debet,0,',','.') : '-'; ?>
                        </td>
                        <td style="text-align: right; color: red;">
                            <?php echo ($kredit > 0) ? number_format($kredit,0,',','.') : '-'; ?>
                        </td>
                        <td style="text-align: right; font-weight: bold; background: #f1f3f5;">
                            <?php echo number_format($saldo,0,',','.'); ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr style="background: #343a40; color: white;">
                        <td colspan="3" style="text-align: right; font-weight: bold;">TOTAL AKHIR</td>
                        <td style="text-align: right; font-weight: bold; color: #28a745;"><?php echo number_format($total_masuk,0,',','.'); ?></td>
                        <td style="text-align: right; font-weight: bold; color: #ff6b6b;"><?php echo number_format($total_keluar,0,',','.'); ?></td>
                        <td style="text-align: right; font-weight: bold; font-size: 1.1em;"><?php echo number_format($saldo,0,',','.'); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</body>
</html>