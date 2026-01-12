<?php
session_start();
ob_start();
require_once(__DIR__ . '/../koneksi.php');

$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'admin');
if (!$is_admin) { header("Location: login.php"); exit(); }

// QUERY TERBAIK: Mengambil rincian per pertemuan yang diinput Guru
$sql_rincian = "SELECT 
                    a.tanggal, 
                    u.username as nama_guru, 
                    m.username as nama_murid, 
                    a.nominal_bayar,
                    (a.nominal_bayar * 0.5) as hak_guru
                FROM absensi a
                JOIN jadwal j ON a.id_jadwal = j.id
                JOIN users u ON j.id_guru = u.id
                JOIN users m ON j.id_murid = m.id
                WHERE a.nominal_bayar > 0
                ORDER BY a.tanggal DESC";

$query_rincian = mysqli_query($conn, $sql_rincian);

// Hitung Total Omzet
$total_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal_bayar) as total FROM absensi"));
$total_omzet = $total_data['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rincian Pendapatan - Smart Arca</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header-stats { display: flex; gap: 20px; margin-bottom: 30px; }
        .stat { flex: 1; padding: 20px; border-radius: 12px; color: white; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 14px; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #f8f9fa; color: #666; }
        .badge-guru { background: #e8f0fe; color: #1a73e8; padding: 3px 8px; border-radius: 5px; font-size: 12px; }
    </style>
</head>
<body>
<div class="container">
    <a href="index.php" style="text-decoration:none; color:#1a73e8;">← Dashboard</a>
    <h2>Rincian Pembayaran Les (50/50)</h2>
    
    <div class="header-stats">
        <div class="stat" style="background:#1a73e8;">
            <small>Total Omzet Bruto</small><br>
            <strong style="font-size:22px;">Rp <?php echo number_format($total_omzet, 0, ',', '.'); ?></strong>
        </div>
        <div class="stat" style="background:#28a745;">
            <small>Profit Bersih Sekolah</small><br>
            <strong style="font-size:22px;">Rp <?php echo number_format($total_omzet * 0.5, 0, ',', '.'); ?></strong>
        </div>
    </div>

    <h3>Riwayat Pembayaran dari Guru</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Guru</th>
                <th>Murid</th>
                <th>Total Bayar</th>
                <th>Bagi Hasil Guru</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if (mysqli_num_rows($query_rincian) > 0) {
                while($row = mysqli_fetch_assoc($query_rincian)) { 
            ?>
            <tr>
                <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                <td><span class="badge-guru"><?php echo htmlspecialchars($row['nama_guru']); ?></span></td>
                <td><strong><?php echo htmlspecialchars($row['nama_murid']); ?></strong></td>
                <td>Rp <?php echo number_format($row['nominal_bayar'], 0, ',', '.'); ?></td>
                <td style="color:green;">Rp <?php echo number_format($row['hak_guru'], 0, ',', '.'); ?></td>
            </tr>
            <?php } } else { ?>
                <tr><td colspan="5" style="text-align:center; padding:30px; color:#999;">Belum ada rincian data. Pastikan Guru sudah menginput nominal di absensi.</td></tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
