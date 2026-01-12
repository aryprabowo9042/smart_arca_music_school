<?php
session_start();
require_once(__DIR__ . '/../koneksi.php');

// Pastikan yang login adalah guru
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'guru') {
    // Jika bukan guru, arahkan ke login
    echo "<script>window.location.href='../admin/login.php';</script>";
    exit();
}

$nama_guru = $_SESSION['username'];
$id_guru_login = 0;

// Cari ID Guru berdasarkan username session
$user_check = mysqli_query($conn, "SELECT id FROM users WHERE username='$nama_guru' LIMIT 1");
if($u = mysqli_fetch_assoc($user_check)) {
    $id_guru_login = $u['id'];
}

// 1. Ambil Jadwal Mengajar Guru ini saja
$query_jadwal = mysqli_query($conn, "SELECT jadwal.*, m.username as nama_murid 
                                     FROM jadwal 
                                     JOIN users m ON jadwal.id_murid = m.id 
                                     WHERE jadwal.id_guru = '$id_guru_login'
                                     ORDER BY FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'), jam ASC");

// 2. Hitung Total Honor yang sudah terkumpul (dari bagi hasil 50%)
$query_honor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) as total FROM keuangan WHERE id_guru = '$id_guru_login'"));
$total_honor = $query_honor['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru - Smart Arca</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f6; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 15px; margin-bottom: 20px; }
        .honor-box { background: #1a73e8; color: white; padding: 15px; border-radius: 8px; margin-bottom: 25px; text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #f8f9fa; }
        .btn { padding: 8px 12px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; font-size: 12px; font-weight: bold; }
        .logout { color: #dc3545; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div>
            <h2 style="margin:0;">Halo, Guru <?php echo htmlspecialchars($nama_guru); ?>!</h2>
            <small>Panel Pengajar Smart Arca</small>
        </div>
        <a href="../logout.php" class="logout">Keluar</a>
    </div>

    <div class="honor-box">
        <small>Estimasi Total Honor Mengajar Bapak/Ibu:</small>
        <h2 style="margin:5px 0 0 0;">Rp <?php echo number_format($total_honor, 0, ',', '.'); ?></h2>
    </div>

    <h3>Jadwal Mengajar Anda</h3>
    <table>
        <thead>
            <tr>
                <th>Hari / Jam</th>
                <th>Nama Murid</th>
                <th>Alat Musik</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if(mysqli_num_rows($query_jadwal) > 0) {
                while($j = mysqli_fetch_assoc($query_jadwal)) { ?>
                <tr>
                    <td><strong><?php echo $j['hari']; ?></strong><br><?php echo $j['jam']; ?></td>
                    <td><?php echo htmlspecialchars($j['nama_murid']); ?></td>
                    <td><span style="color:#1a73e8; font-weight:bold;"><?php echo $j['alat_musik']; ?></span></td>
                    <td>
                        <a href="absen.php?id_jadwal=<?php echo $j['id']; ?>" class="btn">ISI ABSEN & MATERI</a>
                    </td>
                </tr>
            <?php } } else { ?>
                <tr><td colspan="4" style="text-align:center; padding:20px; color:#999;">Anda belum memiliki jadwal mengajar.</td></tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
