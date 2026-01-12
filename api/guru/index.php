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

// Hitung Total Honor Guru (Contoh bagi hasil 60%)
$rekap_honor = mysqli_query($conn, "SELECT SUM(nominal_bayar) as total_masuk, COUNT(*) as total_pertemuan FROM absensi 
                                    JOIN jadwal ON absensi.id_jadwal = jadwal.id 
                                    WHERE jadwal.id_guru = '$id_guru'");
$honor = mysqli_fetch_assoc($rekap_honor);
$saldo_guru = $honor['total_masuk'] * 0.6; // Sesuaikan persentase di sini
?>
<!DOCTYPE html>
<html>
<head>
    <title>Panel Guru - Smart Arca</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f6; padding: 20px; }
        .card { background: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .saldo-box { background: #1a73e8; color: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        .btn-edit { color: #1a73e8; text-decoration: none; font-size: 12px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Halo, Guru <?php echo $display_name; ?></h2>
        <div class="saldo-box">
            <small>Estimasi Saldo Honor Anda (60%):</small>
            <h2 style="margin:5px 0;">Rp <?php echo number_format($saldo_guru, 0, ',', '.'); ?></h2>
            <small>Dari <?php echo $honor['total_pertemuan']; ?> Pertemuan</small>
        </div>
        <a href="/api/logout.php" style="color:red;">Logout</a>
    </div>

    <h3>Rekap Hasil Mengajar & Pembayaran</h3>
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Murid</th>
                    <th>Materi</th>
                    <th>Pembayaran Murid</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $q = mysqli_query($conn, "SELECT absensi.*, m.username as nama_murid FROM absensi 
                                          JOIN jadwal j ON absensi.id_jadwal = j.id 
                                          JOIN users m ON j.id_murid = m.id 
                                          WHERE j.id_guru = '$id_guru' ORDER BY tanggal DESC");
                while($row = mysqli_fetch_assoc($q)) { ?>
                <tr>
                    <td><?php echo date('d/m/y', strtotime($row['tanggal'])); ?></td>
                    <td><?php echo $row['nama_murid']; ?></td>
                    <td><?php echo $row['materi_ajar']; ?></td>
                    <td>Rp <?php echo number_format($row['nominal_bayar'], 0, ',', '.'); ?></td>
                    <td><a href="edit_absen.php?id=<?php echo $row['id']; ?>" class="btn-edit">EDIT</a></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
