<?php
session_start();
require_once(__DIR__ . '/../koneksi.php');

// Proteksi: Hanya Murid yang bisa akses
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'murid') {
    header("Location: ../admin/login.php");
    exit();
}

$id_murid = $_SESSION['id'];
$username = $_SESSION['username'];

// Ambil riwayat les murid ini
$sql = "SELECT a.*, j.alat_musik, g.username as nama_guru 
        FROM absensi a 
        JOIN jadwal j ON a.id_jadwal = j.id 
        JOIN users g ON j.id_guru = g.id 
        WHERE j.id_murid = '$id_murid' 
        ORDER BY a.tanggal DESC";
$query = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siswa - Smart Arca</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; padding: 15px; margin: 0; }
        .header { background: #1a73e8; color: white; padding: 25px 20px; border-radius: 0 0 25px 25px; margin: -15px -15px 20px -15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .card { background: white; padding: 18px; border-radius: 15px; margin-bottom: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border: 1px solid #eee; }
        .instrumen-badge { background: #e8f0fe; color: #1a73e8; padding: 4px 10px; border-radius: 8px; font-size: 12px; font-weight: bold; }
        .btn-kuitansi { display: inline-block; background: #1a73e8; color: white; padding: 10px 15px; border-radius: 10px; text-decoration: none; font-weight: bold; margin-top: 12px; font-size: 13px; text-align: center; width: 100%; box-sizing: border-box; }
        .logout-link { display: block; text-align: center; color: #dc3545; margin-top: 30px; text-decoration: none; font-weight: bold; font-size: 14px; padding: 10px; border: 1px solid #dc3545; border-radius: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0;">Halo, <?php echo htmlspecialchars($username); ?>! 👋</h2>
        <p style="margin:5px 0 0 0; font-size:13px; opacity:0.9;">Selamat datang di Smart Arca Music School</p>
    </div>

    <h4 style="color: #333; margin-left: 5px;">Riwayat Les & Pembayaran</h4>

    <?php if(mysqli_num_rows($query) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($query)) { ?>
        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items: center; margin-bottom: 10px;">
                <span class="instrumen-badge"><?php echo $row['alat_musik']; ?></span>
                <small style="color: #888;"><?php echo date('d M Y', strtotime($row['tanggal'])); ?></small>
            </div>
            <p style="margin:5px 0; font-size:14px;"><strong>Guru:</strong> <?php echo $row['nama_guru']; ?></p>
            <p style="margin:5px 0; font-size:14px; color:#666;"><strong>Materi:</strong> <?php echo $row['materi_ajar']; ?></p>
            
            <div style="border-top:1px solid #f0f0f0; margin-top:12px; padding-top:12px; display:flex; justify-content:space-between; align-items:center;">
                <span style="font-weight:bold; color: #28a745; font-size: 16px;">Rp <?php echo number_format($row['nominal_bayar'], 0, ',', '.'); ?></span>
            </div>
            <a href="../cetak_kuitansi.php?id=<?php echo $row['id']; ?>" target="_blank" class="btn-kuitansi">📄 LIHAT BUKTI BAYAR</a>
        </div>
        <?php } ?>
    <?php else: ?>
        <div class="card" style="text-align: center; color: #999; padding: 40px 20px;">
            Belum ada riwayat pertemuan les.
        </div>
    <?php endif; ?>

    <a href="../admin/index.php?action=logout" class="logout-link">KELUAR APLIKASI</a>
</body>
</html>
