<?php
session_start();
include '../includes/koneksi.php';

if ($_SESSION['status'] != "login" || $_SESSION['role'] != "admin") { header("Location: ../login.php"); exit(); }
function buatRupiah($angka){ return "Rp " . number_format($angka,0,',','.'); }

// --- LOGIKA SAMA PERSIS SEPERTI LAPORAN ---
$transaksi = [];
$q_masuk = mysqli_query($koneksi, "SELECT * FROM pembayaran");
while($r = mysqli_fetch_assoc($q_masuk)){ $transaksi[] = ['tanggal'=>$r['tanggal'], 'jenis'=>'masuk', 'keterangan'=>"Pemasukan: ".$r['keterangan'], 'jumlah'=>$r['jumlah']]; }

$q_keluar = mysqli_query($koneksi, "SELECT * FROM pengeluaran");
while($r = mysqli_fetch_assoc($q_keluar)){ $transaksi[] = ['tanggal'=>$r['tanggal'], 'jenis'=>'keluar', 'keterangan'=>"Biaya: ".$r['keterangan'], 'jumlah'=>$r['jumlah']]; }

$q_gaji = mysqli_query($koneksi, "SELECT penarikan.*, users.nama_lengkap FROM penarikan JOIN users ON penarikan.id_guru = users.id WHERE status='selesai'");
while($r = mysqli_fetch_assoc($q_gaji)){ $transaksi[] = ['tanggal'=>date('Y-m-d', strtotime($r['tanggal'])), 'jenis'=>'keluar', 'keterangan'=>"Gaji Guru: ".$r['nama_lengkap'], 'jumlah'=>$r['jumlah']]; }

usort($transaksi, function($a, $b) { return strtotime($a['tanggal']) - strtotime($b['tanggal']); });
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cetak Laporan Keuangan</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background-color: #eee; }
        h1, h3 { text-align: center; margin: 5px 0; }
        hr { margin-bottom: 20px; }
    </style>
</head>
<body onload="window.print()">

    <h1>SMART ARCA MUSIC SCHOOL</h1>
    <h3>LAPORAN KEUANGAN & ARUS KAS</h3>
    <p style="text-align: center;">Per Tanggal: <?php echo date('d-m-Y H:i'); ?></p>
    <hr>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Debet (Masuk)</th>
                <th>Kredit (Keluar)</th>
                <th>Saldo</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1; $saldo = 0; $total_masuk = 0; $total_keluar = 0;
            foreach ($transaksi as $t) {
                if($t['jenis'] == 'masuk'){ $debet = $t['jumlah']; $kredit = 0; $saldo += $debet; $total_masuk += $debet; } 
                else { $kredit = $t['jumlah']; $debet = 0; $saldo -= $kredit; $total_keluar += $kredit; }
            ?>
            <tr>
                <td style="text-align:center;"><?php echo $no++; ?></td>
                <td style="text-align:center;"><?php echo date('d/m/Y', strtotime($t['tanggal'])); ?></td>
                <td><?php echo $t['keterangan']; ?></td>
                <td style="text-align:right;"><?php echo ($debet > 0) ? number_format($debet,0,',','.') : '-'; ?></td>
                <td style="text-align:right;"><?php echo ($kredit > 0) ? number_format($kredit,0,',','.') : '-'; ?></td>
                <td style="text-align:right; font-weight:bold;"><?php echo number_format($saldo,0,',','.'); ?></td>
            </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr style="background: #eee;">
                <td colspan="3" style="text-align: right; font-weight: bold;">TOTAL</td>
                <td style="text-align: right; font-weight: bold;"><?php echo number_format($total_masuk,0,',','.'); ?></td>
                <td style="text-align: right; font-weight: bold;"><?php echo number_format($total_keluar,0,',','.'); ?></td>
                <td style="text-align: right; font-weight: bold;"><?php echo number_format($saldo,0,',','.'); ?></td>
            </tr>
        </tfoot>
    </table>

</body>
</html>