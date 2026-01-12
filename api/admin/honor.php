<?php
session_start();
require_once(__DIR__ . '/../koneksi.php');

$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'admin');
if (!$is_admin) { header("Location: login.php"); exit(); }

$sql = "SELECT u.username as nama_guru, 
               SUM(a.nominal_bayar) as total_bruto,
               SUM(a.nominal_bayar) * 0.5 as hak_guru,
               SUM(a.nominal_bayar) * 0.5 as hak_sekolah,
               COUNT(a.id) as pertemuan
        FROM absensi a
        JOIN jadwal j ON a.id_jadwal = j.id
        JOIN users u ON j.id_guru = u.id
        GROUP BY u.id";
$result = mysqli_query($conn, $sql);

$total_omzet = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal_bayar) as total FROM absensi"))['total'] ?? 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Keuangan Smart Arca</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: white; padding: 30px; border-radius: 15px; }
        .header-stats { display: flex; gap: 20px; margin-bottom: 30px; }
        .stat { flex: 1; padding: 20px; border-radius: 10px; color: white; text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
    </style>
</head>
<body>
<div class="container">
    <a href="index.php" style="text-decoration:none; color:#1a73e8;">← Dashboard</a>
    <h2>Manajemen Honor & Profit (50/50)</h2>
    
    <div class="header-stats">
        <div class="stat" style="background:#1a73e8;">
            <small>Total Omzet</small><br><strong>Rp <?php echo number_format($total_omzet, 0, ',', '.'); ?></strong>
        </div>
        <div class="stat" style="background:#28a745;">
            <small>Profit Sekolah (50%)</small><br><strong>Rp <?php echo number_format($total_omzet * 0.5, 0, ',', '.'); ?></strong>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Guru</th>
                <th>Sesi</th>
                <th>Total Setoran</th>
                <th>Honor Guru (50%)</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><strong><?php echo $row['nama_guru']; ?></strong></td>
                <td><?php echo $row['pertemuan']; ?></td>
                <td>Rp <?php echo number_format($row['total_bruto'], 0, ',', '.'); ?></td>
                <td style="color:green; font-weight:bold;">Rp <?php echo number_format($row['hak_guru'], 0, ',', '.'); ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
