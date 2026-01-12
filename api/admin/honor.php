<?php
session_start();
require_once(__DIR__ . '/../koneksi.php');

$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'admin');
if (!$is_admin) { header("Location: login.php"); exit(); }

// 1. Ambil Total Omzet dari GURU
$q_guru = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal_bayar) as total FROM absensi"));
$omzet_guru = $q_guru['total'] ?? 0;

// 2. Ambil Total dari KEUANGAN LAINNYA (Manual)
$q_manual = mysqli_fetch_assoc(mysqli_query($conn, "SELECT 
    SUM(CASE WHEN jenis='masuk' THEN nominal ELSE 0 END) as masuk,
    SUM(CASE WHEN jenis='keluar' THEN nominal ELSE 0 END) as keluar 
    FROM keuangan"));
$masuk_manual = $q_manual['masuk'] ?? 0;
$keluar_manual = $q_manual['keluar'] ?? 0;

$total_bruto = $omzet_guru + $masuk_manual;
$profit_sekolah = ($omzet_guru * 0.5) + $masuk_manual - $keluar_manual;

// 3. QUERY GABUNGAN UNTUK TABEL RINCIAN (UNION)
$sql_union = "(SELECT tanggal, CONCAT('Laporan Guru: ', u.username, ' (Murid: ', m.username, ')') as ket, 'masuk' as tipe, nominal_bayar as nom 
               FROM absensi a 
               JOIN jadwal j ON a.id_jadwal = j.id 
               JOIN users u ON j.id_guru = u.id 
               JOIN users m ON j.id_murid = m.id 
               WHERE a.nominal_bayar > 0)
              UNION ALL
              (SELECT tanggal, keterangan as ket, jenis as tipe, nominal as nom FROM keuangan)
              ORDER BY tanggal DESC";

$query_rincian = mysqli_query($conn, $sql_union);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Keuangan Lengkap - Smart Arca</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .stats { display: flex; gap: 15px; margin-bottom: 25px; }
        .card { flex: 1; padding: 15px; border-radius: 10px; color: white; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 13px; }
        th, td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
        .masuk { color: green; font-weight: bold; }
        .keluar { color: red; font-weight: bold; }
        .tag { padding: 2px 6px; border-radius: 4px; font-size: 10px; text-transform: uppercase; color: white; }
    </style>
</head>
<body>
<div class="container">
    <a href="index.php" style="text-decoration:none; color:#1a73e8;">← Dashboard</a>
    <h2>Laporan Keuangan Konsolidasi</h2>
    
    <div class="stats">
        <div class="card" style="background:#1a73e8;"><small>Omzet Les (Guru)</small><br><strong>Rp <?php echo number_format($omzet_guru); ?></strong></div>
        <div class="card" style="background:#28a745;"><small>Pemasukan Lain</small><br><strong>Rp <?php echo number_format($masuk_manual); ?></strong></div>
        <div class="card" style="background:#dc3545;"><small>Total Pengeluaran</small><br><strong>Rp <?php echo number_format($keluar_manual); ?></strong></div>
        <div class="card" style="background:#6c757d;"><small>Profit Bersih</small><br><strong>Rp <?php echo number_format($profit_sekolah); ?></strong></div>
    </div>

    <h3>Riwayat Transaksi Gabungan</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Keterangan / Sumber</th>
                <th>Tipe</th>
                <th>Nominal</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($query_rincian)) { ?>
            <tr>
                <td><?php echo date('d/m/y', strtotime($row['tanggal'])); ?></td>
                <td><?php echo htmlspecialchars($row['ket']); ?></td>
                <td>
                    <span class="tag" style="background: <?php echo $row['tipe']=='masuk' ? '#28a745':'#dc3545'; ?>">
                        <?php echo $row['tipe']; ?>
                    </span>
                </td>
                <td class="<?php echo $row['tipe']; ?>">
                    Rp <?php echo number_format($row['nom']); ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
