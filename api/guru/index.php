<?php
session_start();
ob_start();
require_once(__DIR__ . '/../koneksi.php');

$is_logged_in = (isset($_SESSION['status']) && $_SESSION['status'] == 'login') || isset($_COOKIE['user_login']);
$is_guru = (isset($_SESSION['role']) && $_SESSION['role'] == 'guru') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'guru');

if (!$is_logged_in || !$is_guru) {
    echo "<script>window.location.replace('../admin/login.php');</script>";
    exit();
}

$display_name = $_SESSION['username'] ?? $_COOKIE['user_login'];

// Ambil ID Guru
$u = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM users WHERE username='$display_name' LIMIT 1"));
$id_guru = $u['id'];

// Hitung Honor (50%)
$rekap_honor = mysqli_query($conn, "SELECT SUM(nominal_bayar) as total_masuk, COUNT(*) as total_pertemuan FROM absensi 
                                    JOIN jadwal ON absensi.id_jadwal = jadwal.id 
                                    WHERE jadwal.id_guru = '$id_guru'");
$honor = mysqli_fetch_assoc($rekap_honor);
$saldo_guru = ($honor['total_masuk'] ?? 0) * 0.5;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru - Smart Arca</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; padding: 20px; margin: 0; }
        .card { background: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 900px; margin: auto; }
        .saldo-box { background: #1a73e8; color: white; padding: 20px; border-radius: 12px; margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #f8f9fa; font-size: 12px; color: #666; }
        .btn { padding: 8px 15px; background: #1a73e8; color: white; text-decoration: none; border-radius: 6px; font-size: 12px; font-weight: bold; }
        .btn-edit { color: #1a73e8; font-size: 11px; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Halo, Guru <?php echo htmlspecialchars($display_name); ?></h2>
        <a href="/api/logout.php" style="color:#dc3545; font-weight:bold; text-decoration:none;">Logout</a>
    </div>

    <div class="saldo-box">
        <small>Estimasi Saldo Honor Anda (50%):</small>
        <h2 style="margin:5px 0;">Rp <?php echo number_format($saldo_guru, 0, ',', '.'); ?></h2>
        <small>Dari total <?php echo $honor['total_pertemuan']; ?> pertemuan yang tercatat</small>
    </div>

    <h3>Jadwal Mengajar Hari Ini</h3>
    <table>
        <thead>
            <tr>
                <th>Hari / Jam</th>
                <th>Nama Murid</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $q_jadwal = mysqli_query($conn, "SELECT jadwal.*, m.username as nama_murid FROM jadwal 
                                             JOIN users m ON jadwal.id_murid = m.id 
                                             WHERE id_guru = '$id_guru'");
            while($j = mysqli_fetch_assoc($q_jadwal)) { ?>
            <tr>
                <td><?php echo $j['hari']; ?>, <?php echo $j['jam']; ?></td>
                <td><?php echo htmlspecialchars($j['nama_murid']); ?></td>
                <td><a href="absen.php?id_jadwal=<?php echo $j['id']; ?>" class="btn">ISI LAPORAN & BAYAR</a></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <h3 style="margin-top:40px;">Riwayat Mengajar & Setoran</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Murid</th>
                <th>Setoran Murid</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $q_riwayat = mysqli_query($conn, "SELECT absensi.*, m.username as nama_murid FROM absensi 
                                              JOIN jadwal j ON absensi.id_jadwal = j.id 
                                              JOIN users m ON j.id_murid = m.id 
                                              WHERE j.id_guru = '$id_guru' ORDER BY tanggal DESC LIMIT 10");
            while($r = mysqli_fetch_assoc($q_riwayat)) { ?>
            <tr>
                <td><?php echo date('d/m/y', strtotime($r['tanggal'])); ?></td>
                <td><?php echo htmlspecialchars($r['nama_murid']); ?></td>
                <td>Rp <?php echo number_format($r['nominal_bayar'], 0, ',', '.'); ?></td>
                <td><a href="edit_absen.php?id=<?php echo $r['id']; ?>" class="btn-edit">EDIT</a></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
