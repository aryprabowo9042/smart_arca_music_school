<?php
session_start();
ob_start();
// PERBAIKAN: Jalur koneksi harus langsung ke ../koneksi.php (bukan folder includes)
require_once(__DIR__ . '/../koneksi.php');

// Proteksi Double Check (Session + Cookie) agar tidak mental
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
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruang Belajar - Smart Arca</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; padding: 15px; margin: 0; }
        .container { max-width: 600px; margin: auto; }
        .card { background: white; padding: 20px; border-radius: 15px; margin-bottom: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 5px solid #1a73e8; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .tag { background: #e8f0fe; color: #1a73e8; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .logout-btn { color: #dc3545; text-decoration: none; font-weight: bold; font-size: 14px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div>
            <h2 style="margin:0;">Halo, <?php echo htmlspecialchars($display_name); ?>!</h2>
            <small style="color: #666;">Progres Belajar Musikmu</small>
        </div>
        <a href="/api/logout.php" class="logout-btn">Logout</a>
    </div>

    <?php if($query_belajar && mysqli_num_rows($query_belajar) > 0) {
        while($row = mysqli_fetch_assoc($query_belajar)) { ?>
        <div class="card">
            <div style="display:flex; justify-content:space-between;">
                <span class="tag"><?php echo $row['alat_musik']; ?></span>
                <small style="color:#999;"><?php echo date('d M Y', strtotime($row['tanggal'])); ?></small>
            </div>
            <h4 style="margin: 15px 0 5px 0; color: #333;">Materi: <?php echo htmlspecialchars($row['materi_ajar']); ?></h4>
            <p style="font-size:13px; color:#666; margin-bottom: 15px;">Catatan Guru: <?php echo htmlspecialchars($row['perkembangan_murid']); ?></p>
            
            <?php if(!empty($row['file_materi'])) { ?>
                <div style="border-top: 1px solid #eee; padding-top: 10px;">
                    <a href="../uploads/modul/<?php echo $row['file_materi']; ?>" target="_blank" style="color:#1a73e8; font-size:12px; font-weight:bold; text-decoration:none;">📁 Download / Preview Materi</a>
                </div>
            <?php } ?>
        </div>
    <?php } } else { ?>
        <div style="text-align:center; padding: 50px 20px; color: #999;">
            <p>Belum ada catatan materi mengajar.</p>
        </div>
    <?php } ?>
</div>
</body>
</html>
