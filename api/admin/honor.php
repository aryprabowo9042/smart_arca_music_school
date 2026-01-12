<?php
session_start();
ob_start();
require_once(__DIR__ . '/../koneksi.php');

// Proteksi Admin
$is_logged_in = (isset($_SESSION['status']) && $_SESSION['status'] == 'login') || isset($_COOKIE['user_login']);
$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'admin');

if (!$is_logged_in || !$is_admin) {
    echo "<script>window.location.replace('login.php');</script>";
    exit();
}

// Ambil Data Rekap per Guru
$sql = "SELECT u.id as id_guru, u.username as nama_guru, 
               SUM(a.nominal_bayar) as total_setoran,
               COUNT(a.id) as total_pertemuan,
               SUM(a.nominal_bayar) * 0.6 as hak_guru,
               SUM(a.nominal_bayar) * 0.4 as profit_sekolah
        FROM users u
        JOIN jadwal j ON u.id = j.id_guru
        JOIN absensi a ON j.id = a.id_jadwal
        WHERE u.role = 'guru'
        GROUP BY u.id";
$query = mysqli_query($conn, $sql);

// Hitung Total Keseluruhan untuk Dashboard Kecil
$total_all = mysqli_query($conn, "SELECT SUM(nominal_bayar) as bruto FROM absensi");
$row_all = mysqli_fetch_assoc($total_all);
$bruto_total = $row_all['bruto'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Honor Guru - Smart Arca</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 30px; }
        .stat-card { padding: 20px; border-radius: 10px; color: white; }
        .bg-blue { background: #1a73e8; }
        .bg-green { background: #28a745; }
        .bg-orange { background: #fd7e14; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #f8f9fa; font-size: 12px; text-transform: uppercase; color: #666; }
        .btn-back { text-decoration: none; color: #1a73e8; font-weight: bold; font-size: 14px; }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php" class="btn-back">← Kembali ke Dashboard</a>
    <h2 style="margin: 15px 0;">Manajemen Keuangan & Honor Guru</h2>

    <div class="stats-grid">
        <div class="stat-card bg-blue">
            <small>Total Omzet (Bruto)</small>
            <h3 style="margin:5px 0;">Rp <?php echo number_format($bruto_total, 0, ',', '.'); ?></h3>
        </div>
        <div class="stat-card bg-green">
            <small>Total Hak Guru (60%)</small>
            <h3 style="margin:5px 0;">Rp <?php echo number_format($bruto_total * 0.6, 0, ',', '.'); ?></h3>
        </div>
        <div class="stat-card bg-orange">
            <small>Profit Smart Arca (40%)</small>
            <h3 style="margin:5px 0;">Rp <?php echo number_format($bruto_total * 0.4, 0, ',', '.'); ?></h3>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama Guru</th>
                <th>Pertemuan</th>
                <th>Total Setoran Murid</th>
                <th>Hak Guru (60%)</th>
                <th>Profit Sekolah (40%)</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($query)) { ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($row['nama_guru']); ?></strong></td>
                <td><?php echo $row['total_pertemuan']; ?> Sesi</td>
                <td>Rp <?php echo number_format($row['total_setoran'], 0, ',', '.'); ?></td>
                <td style="color: #28a745; font-weight: bold;">Rp <?php echo number_format($row['hak_guru'], 0, ',', '.'); ?></td>
                <td style="color: #1a73e8;">Rp <?php echo number_format($row['profit_sekolah'], 0, ',', '.'); ?></td>
                <td>
                    <a href="detail_honor.php?id_guru=<?php echo $row['id_guru']; ?>" style="font-size: 11px; background: #eee; padding: 5px 10px; border-radius: 5px; text-decoration: none; color: #333;">Rincian</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
