<?php
session_start();
ob_start();

// PERBAIKAN UTAMA: Jalur ke koneksi.php yang benar di Vercel
require_once(__DIR__ . '/../koneksi.php');

// Proteksi agar hanya Murid yang bisa masuk
$is_logged_in = (isset($_SESSION['status']) && $_SESSION['status'] == 'login') || isset($_COOKIE['user_login']);
$is_murid = (isset($_SESSION['role']) && $_SESSION['role'] == 'murid') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'murid');

if (!$is_logged_in || !$is_murid) {
    echo "<script>window.location.replace('../admin/login.php');</script>";
    exit();
}

$display_name = $_SESSION['username'] ?? $_COOKIE['user_login'];

// Ambil riwayat belajar murid
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
    <title>Ruang Belajar Murid</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 20px; }
        .card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 15px; }
        .tag { background: #e8f0fe; color: #1a73e8; padding: 5px 10px; border-radius: 5px; font-size: 12px; font-weight: bold; }
        .btn-logout { color: red; text-decoration: none; font-weight: bold; float: right; }
    </style>
</head>
<body>
    <div style="max-width: 600px; margin: auto;">
        <a href="/api/logout.php" class="btn-logout">Logout</a>
        <h2>Halo, <?php echo htmlspecialchars($display_name); ?></h2>
        <p>Catatan perkembangan belajarmu di Smart Arca.</p>
        <hr>

        <?php if($query_belajar && mysqli_num_rows($query_belajar) > 0) {
            while($row = mysqli_fetch_assoc($query_belajar)) { ?>
            <div class="card">
                <span class="tag"><?php echo $row['alat_musik']; ?></span>
                <span style="float:right; color:#666; font-size:12px;"><?php echo date('d M Y', strtotime($row['tanggal'])); ?></span>
                <h4 style="margin: 15px 0 5px 0;">Materi: <?php echo htmlspecialchars($row['materi_ajar']); ?></h4>
                <p style="font-size:13px; color:#555;">Perkembangan: <?php echo htmlspecialchars($row['perkembangan_murid']); ?></p>
                
                <?php if(!empty($row['file_materi'])) { ?>
                    <div style="margin-top:10px; padding-top:10px; border-top:1px solid #eee;">
                        <a href="../uploads/modul/<?php echo $row['file_materi']; ?>" target="_blank" style="color:#1a73e8; font-weight:bold; font-size:12px; text-decoration:none;">📁 Lihat Modul Materi</a>
                    </div>
                <?php } ?>
            </div>
        <?php } } else { echo "<p style='text-align:center; color:#999;'>Belum ada catatan materi.</p>"; } ?>
    </div>
</body>
</html>
