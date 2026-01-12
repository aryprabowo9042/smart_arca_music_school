<?php
session_start();
ob_start();
require_once(__DIR__ . '/../koneksi.php');

// Proteksi Admin
$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'admin');
if (!$is_admin) { 
    header("Location: login.php"); 
    exit(); 
}

// Gunakan LEFT JOIN agar data pemasukan tetap muncul meskipun jadwal sudah dihapus
$sql = "SELECT 
            u.username as nama_guru, 
            SUM(a.nominal_bayar) as total_bruto,
            SUM(a.nominal_bayar) * 0.5 as hak_guru,
            SUM(a.nominal_bayar) * 0.5 as hak_sekolah,
            COUNT(a.id) as pertemuan
        FROM absensi a
        LEFT JOIN jadwal j ON a.id_jadwal = j.id
        LEFT JOIN users u ON j.id_guru = u.id
        GROUP BY u.id 
        HAVING total_bruto > 0";

$result = mysqli_query($conn, $sql);

// Hitung total omzet global
$total_query = mysqli_query($conn, "SELECT SUM(nominal_bayar) as total FROM absensi");
$total_data = mysqli_fetch_assoc($total_query);
$total_omzet = $total_data['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keuangan Smart Arca</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f0f2f5; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header-stats { display: flex; gap: 20px; margin-bottom: 30px; }
        .stat { flex: 1; padding: 20px; border-radius: 12px; color: white; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 15px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #f8f9fa; color: #666; font-size: 13px; }
        tr:hover { background: #fdfdfd; }
        .btn-back { display: inline-block; margin-bottom: 20px; text-decoration: none; color: #1a73e8; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <a href="index.php" class="btn-back">← Kembali ke Dashboard</a>
    <h2 style="margin-top:0;">Manajemen Honor & Profit (50/50)</h2>
    
    <div class="header-stats">
        <div class="stat" style="background:#1a73e8;">
            <small>Total Omzet Masuk</small><br>
            <strong style="font-size:20px;">Rp <?php echo number_format($total_omzet, 0, ',', '.'); ?></strong>
        </div>
        <div class="stat" style="background:#28a745;">
            <small>Profit Sekolah (50%)</small><br>
            <strong style="font-size:20px;">Rp <?php echo number_format($total_omzet * 0.5, 0, ',', '.'); ?></strong>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama Guru</th>
                <th style="text-align:center;">Total Sesi</th>
                <th>Total Setoran</th>
                <th>Hak Guru (50%)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) { 
                    $nama = $row['nama_guru'] ?? "Tanpa Nama (ID Terhapus)";
            ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($nama); ?></strong></td>
                <td style="text-align:center;"><?php echo $row['pertemuan']; ?></td>
                <td>Rp <?php echo number_format($row['total_bruto'], 0, ',', '.'); ?></td>
                <td style="color:green; font-weight:bold;">Rp <?php echo number_format($row['hak_guru'], 0, ',', '.'); ?></td>
            </tr>
            <?php 
                } 
            } else {
                echo "<tr><td colspan='4' style='text-align:center; padding:30px; color:#999;'>Belum ada data pemasukan tercatat.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
</body>
</html>
