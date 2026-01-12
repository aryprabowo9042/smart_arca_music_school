<?php
session_start();
ob_start();
require_once(__DIR__ . '/../koneksi.php');

// Proteksi Admin
$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'admin');
if (!$is_admin) { header("Location: login.php"); exit(); }

// QUERY REKAP: Mengambil data omzet dari tabel absensi secara langsung
$sql = "SELECT 
            u.username as nama_guru, 
            COUNT(a.id) as pertemuan,
            SUM(a.nominal_bayar) as total_bruto,
            SUM(a.nominal_bayar) * 0.5 as hak_guru
        FROM absensi a
        LEFT JOIN jadwal j ON a.id_jadwal = j.id
        LEFT JOIN users u ON j.id_guru = u.id
        GROUP BY j.id_guru 
        HAVING total_bruto > 0";

$result = mysqli_query($conn, $sql);

// Query Total Omzet (Yang sudah berhasil muncul 50.000)
$total_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal_bayar) as total FROM absensi"));
$total_omzet = $total_data['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keuangan Smart Arca</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header-stats { display: flex; gap: 20px; margin-bottom: 30px; }
        .stat { flex: 1; padding: 20px; border-radius: 12px; color: white; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #f8f9fa; color: #666; font-size: 13px; }
    </style>
</head>
<body>
<div class="container">
    <a href="index.php" style="text-decoration:none; color:#1a73e8;">← Dashboard</a>
    <h2>Laporan Keuangan (50/50)</h2>
    
    <div class="header-stats">
        <div class="stat" style="background:#1a73e8;">
            <small>Total Omzet</small><br>
            <strong style="font-size:22px;">Rp <?php echo number_format($total_omzet, 0, ',', '.'); ?></strong>
        </div>
        <div class="stat" style="background:#28a745;">
            <small>Profit Sekolah (50%)</small><br>
            <strong style="font-size:22px;">Rp <?php echo number_format($total_omzet * 0.5, 0, ',', '.'); ?></strong>
        </div>
    </div>

    <h3>Rincian per Guru</h3>
    <table>
        <thead>
            <tr>
                <th>Nama Guru</th>
                <th>Sesi</th>
                <th>Setoran</th>
                <th>Honor Guru (50%)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) { 
                    $nama = $row['nama_guru'] ?? "Guru (ID: ".($row['id_guru'] ?? 'N/A').")";
            ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($nama); ?></strong></td>
                <td><?php echo $row['pertemuan']; ?> Sesi</td>
                <td>Rp <?php echo number_format($row['total_bruto'], 0, ',', '.'); ?></td>
                <td style="color:green; font-weight:bold;">Rp <?php echo number_format($row['hak_guru'], 0, ',', '.'); ?></td>
            </tr>
            <?php } } else { ?>
                <tr><td colspan="4" style="text-align:center; padding:20px; color:#999;">Data rincian tidak ditemukan. Pastikan data Jadwal masih ada.</td></tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
