<?php
session_start();
ob_start();
require_once(__DIR__ . '/../koneksi.php');

// Proteksi Double Check
$is_logged_in = (isset($_SESSION['status']) && $_SESSION['status'] == 'login') || isset($_COOKIE['user_login']);
$is_murid = (isset($_SESSION['role']) && $_SESSION['role'] == 'murid') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'murid');

if (!$is_logged_in || !$is_murid) {
    echo "<script>window.location.replace('../admin/login.php');</script>";
    exit();
}

$display_name = $_SESSION['username'] ?? $_COOKIE['user_login'];

// Ambil riwayat belajar dari tabel absensi
$query_belajar = mysqli_query($conn, "SELECT absensi.*, j.alat_musik, u.username as nama_guru 
                                      FROM absensi 
                                      JOIN jadwal j ON absensi.id_jadwal = j.id 
                                      JOIN users m ON j.id_murid = m.id 
                                      JOIN users u ON j.id_guru = u.id
                                      WHERE m.username = '$display_name'
                                      ORDER BY absensi.tanggal DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruang Murid - Smart Arca</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 15px; }
        .container { max-width: 600px; margin: auto; }
        .card { background: white; padding: 20px; border-radius: 12px; margin-bottom: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .tag { background: #e8f0fe; color: #1a73e8; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Halo, <?php echo htmlspecialchars($display_name); ?></h2>
        <a href="/api/logout.php" style="color:red; text-decoration:none; font-weight:bold;">Keluar</a>
    </div>
    <hr>
    <?php if(mysqli_num_rows($query_belajar) > 0) {
        while($row = mysqli_fetch_assoc($query_belajar)) { ?>
        <div class="card">
            <span class="tag"><?php echo $row['alat_musik']; ?></span>
            <small style="float:right; color:#888;"><?php echo date('d M Y', strtotime($row['tanggal'])); ?></small>
            <h4 style="margin:10px 0;">Materi: <?php echo htmlspecialchars($row['materi_ajar']); ?></h4>
            <p style="font-size:13px; color:#555;">Catatan Guru: <?php echo htmlspecialchars($row['perkembangan_murid']); ?></p>
            <?php if(!empty($row['file_materi'])) { ?>
                <a href="../uploads/modul/<?php echo $row['file_materi']; ?>" target="_blank" style="color:#1a73e8; font-size:12px; font-weight:bold;">Download Materi 📁</a>
            <?php } ?>
        </div>
    <?php } } else { echo "<p style='text-align:center; color:#999;'>Belum ada laporan belajar.</p>"; } ?>
</div>
</body>
</html>
