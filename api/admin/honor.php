<?php
session_start();
ob_start();
require_once(__DIR__ . '/../koneksi.php');

$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'admin');
if (!$is_admin) { header("Location: login.php"); exit(); }

// PROSES INPUT MANUAL
if (isset($_POST['simpan_manual'])) {
    $tgl = $_POST['tanggal'];
    $ket = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $tip = $_POST['jenis'];
    $nom = (int)$_POST['nominal'];
    mysqli_query($conn, "INSERT INTO keuangan (tanggal, keterangan, jenis, nominal) VALUES ('$tgl', '$ket', '$tip', '$nom')");
    header("Location: honor.php"); exit();
}

// HITUNG STATISTIK
$q_guru = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal_bayar) as total FROM absensi"));
$omzet_guru = $q_guru['total'] ?? 0;

$q_manual = mysqli_fetch_assoc(mysqli_query($conn, "SELECT 
    SUM(CASE WHEN jenis='masuk' THEN nominal ELSE 0 END) as masuk,
    SUM(CASE WHEN jenis='keluar' THEN nominal ELSE 0 END) as keluar 
    FROM keuangan"));
$masuk_manual = $q_manual['masuk'] ?? 0;
$keluar_manual = $q_manual['keluar'] ?? 0;

$profit_bersih = ($omzet_guru * 0.5) + $masuk_manual - $keluar_manual;

// QUERY GABUNGAN UNTUK TABEL
$sql_union = "(SELECT a.id, a.tanggal, CONCAT('Les (', u.username, ')') as ket, 'masuk' as tipe, a.nominal_bayar as nom, 'ya' as is_kursus 
               FROM absensi a 
               JOIN jadwal j ON a.id_jadwal = j.id 
               JOIN users u ON j.id_murid = u.id)
              UNION ALL
              (SELECT id, tanggal, keterangan as ket, jenis as tipe, nominal as nom, 'tidak' as is_kursus 
               FROM keuangan)
              ORDER BY tanggal DESC LIMIT 50";
$query_rincian = mysqli_query($conn, $sql_union);
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keuangan - Smart Arca</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 15px; margin: 0; }
        .container { max-width: 900px; margin: auto; }
        .stats { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
        .card { flex: 1; min-width: 150px; padding: 15px; border-radius: 12px; color: white; text-align: center; }
        .form-box { background: white; padding: 20px; border-radius: 15px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; font-size: 13px; }
        .btn-kuitansi { background: #1a73e8; color: white; padding: 4px 8px; border-radius: 4px; text-decoration: none; font-size: 11px; font-weight: bold; }
        input, select, button { padding: 10px; margin: 5px 0; border: 1px solid #ddd; border-radius: 8px; width: 100%; box-sizing: border-box; }
        button { background: #1a73e8; color: white; border: none; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <a href="index.php" style="text-decoration:none; color:#1a73e8; font-weight:bold;">← Kembali</a>
    <h2>Laporan Keuangan & Kuitansi</h2>
    
    <div class="stats">
        <div class="card" style="background:#1a73e8;"><small>Omzet Les</small><br><strong>Rp <?php echo number_format($omzet_guru); ?></strong></div>
        <div class="card" style="background:#28a745;"><small>Profit Sekolah (50%)</small><br><strong>Rp <?php echo number_format($profit_bersih); ?></strong></div>
        <div class="card" style="background:#dc3545;"><small>Pengeluaran</small><br><strong>Rp <?php echo number_format($keluar_manual); ?></strong></div>
    </div>

    <div class="form-box">
        <form method="POST">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px;">
                <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required>
                <input type="text" name="keterangan" placeholder="Keterangan Ops" required>
                <select name="jenis"><option value="masuk">Masuk (+)</option><option value="keluar">Keluar (-)</option></select>
                <input type="number" name="nominal" placeholder="Nominal Rp" required>
                <button type="submit" name="simpan_manual">SIMPAN</button>
            </div>
        </form>
    </div>

    <table>
        <thead><tr style="background:#f8f9fa;"><th>Tanggal</th><th>Keterangan</th><th>Nominal</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($query_rincian)) { ?>
            <tr>
                <td><?php echo date('d/m/y', strtotime($row['tanggal'])); ?></td>
                <td><?php echo $row['ket']; ?></td>
                <td style="color:<?php echo $row['tipe']=='masuk'?'green':'red'; ?>; font-weight:bold;">Rp <?php echo number_format($row['nom']); ?></td>
                <td>
                    <?php if($row['is_kursus'] == 'ya') { ?>
                        <a href="../cetak_kuitansi.php?id=<?php echo $row['id']; ?>" target="_blank" class="btn-kuitansi">BUKTI BAYAR</a>
                    <?php } else { echo "-"; } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
