<?php
session_start();
ob_start();
require_once(__DIR__ . '/../koneksi.php');

$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'admin');
if (!$is_admin) { header("Location: login.php"); exit(); }

// 1. PROSES INPUT MANUAL ADMIN
if (isset($_POST['simpan_manual'])) {
    $tgl = $_POST['tanggal'];
    $ket = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $tip = $_POST['jenis'];
    $nom = (int)$_POST['nominal'];
    mysqli_query($conn, "INSERT INTO keuangan (tanggal, keterangan, jenis, nominal) VALUES ('$tgl', '$ket', '$tip', '$nom')");
    header("Location: honor.php");
}

// 2. HITUNG STATISTIK
$q_guru = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal_bayar) as total FROM absensi"));
$omzet_guru = $q_guru['total'] ?? 0;

$q_manual = mysqli_fetch_assoc(mysqli_query($conn, "SELECT 
    SUM(CASE WHEN jenis='masuk' THEN nominal ELSE 0 END) as masuk,
    SUM(CASE WHEN jenis='keluar' THEN nominal ELSE 0 END) as keluar 
    FROM keuangan"));
$masuk_manual = $q_manual['masuk'] ?? 0;
$keluar_manual = $q_manual['keluar'] ?? 0;

$profit_bersih = ($omzet_guru * 0.5) + $masuk_manual - $keluar_manual;

// 3. QUERY GABUNGAN (UNION)
$sql_union = "(SELECT tanggal, CONCAT('Laporan Guru (', u.username, ')') as ket, 'masuk' as tipe, nominal_bayar as nom FROM absensi a JOIN jadwal j ON a.id_jadwal = j.id JOIN users u ON j.id_guru = u.id WHERE a.nominal_bayar > 0)
              UNION ALL
              (SELECT tanggal, keterangan as ket, jenis as tipe, nominal as nom FROM keuangan)
              ORDER BY tanggal DESC LIMIT 50";
$query_rincian = mysqli_query($conn, $sql_union);
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keuangan Smart Arca</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 15px; margin: 0; }
        .container { max-width: 900px; margin: auto; }
        .stats { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
        .card { flex: 1; min-width: 150px; padding: 15px; border-radius: 12px; color: white; text-align: center; }
        .form-box { background: white; padding: 20px; border-radius: 15px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; font-size: 13px; }
        input, select, button { padding: 10px; margin: 5px 0; border: 1px solid #ddd; border-radius: 8px; width: 100%; box-sizing: border-box; }
        button { background: #1a73e8; color: white; font-weight: bold; border: none; cursor: pointer; }
    </style>
</head>
<body>
<div class="container">
    <a href="index.php" style="text-decoration:none; color:#1a73e8; font-weight:bold;">← Kembali</a>
    <h2>Laporan Keuangan Konsolidasi</h2>
    
    <div class="stats">
        <div class="card" style="background:#1a73e8;"><small>Omzet Guru</small><br><strong>Rp <?php echo number_format($omzet_guru); ?></strong></div>
        <div class="card" style="background:#28a745;"><small>Profit Sekolah</small><br><strong>Rp <?php echo number_format($profit_bersih); ?></strong></div>
        <div class="card" style="background:#dc3545;"><small>Pengeluaran</small><br><strong>Rp <?php echo number_format($keluar_manual); ?></strong></div>
    </div>

    <div class="form-box">
        <h4 style="margin:0 0 10px 0;">+ Input Keuangan Lainnya</h4>
        <form method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px;">
            <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required>
            <input type="text" name="keterangan" placeholder="Keterangan (Contoh: Beli Senar)" required>
            <select name="jenis">
                <option value="masuk">Pemasukan (+)</option>
                <option value="keluar">Pengeluaran (-)</option>
            </select>
            <input type="number" name="nominal" placeholder="Nominal Rp" required>
            <button type="submit" name="simpan_manual">SIMPAN</button>
        </form>
    </div>

    <table style="box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <thead>
            <tr style="background: #f8f9fa;">
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Jenis</th>
                <th>Nominal</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($query_rincian)) { ?>
            <tr>
                <td><?php echo date('d/m/y', strtotime($row['tanggal'])); ?></td>
                <td><?php echo htmlspecialchars($row['ket']); ?></td>
                <td><small style="padding:2px 6px; border-radius:4px; background:<?php echo $row['tipe']=='masuk'?'#d1e7dd':'#f8d7da'; ?>; color:<?php echo $row['tipe']=='masuk'?'#0f5132':'#842029'; ?>;"><?php echo strtoupper($row['tipe']); ?></small></td>
                <td style="font-weight:bold; color:<?php echo $row['tipe']=='masuk'?'#28a745':'#dc3545'; ?>;">Rp <?php echo number_format($row['nom']); ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
