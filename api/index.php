<?php
session_start();
ob_start();

// 1. KONEKSI KE DATABASE
require_once(__DIR__ . '/../koneksi.php');

// 2. SISTEM DOUBLE CHECK PROTEKSI (Agar tidak mental ke login)
$is_logged_in = (isset($_SESSION['status']) && $_SESSION['status'] == 'login') || isset($_COOKIE['user_login']);
$is_murid = (isset($_SESSION['role']) && $_SESSION['role'] == 'murid') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'murid');

if (!$is_logged_in || !$is_murid) {
    echo "<script>window.location.replace('../admin/login.php');</script>";
    exit();
}

// Gunakan nama dari session, jika kosong ambil dari cookie
$display_name = $_SESSION['username'] ?? $_COOKIE['user_login'];

// 3. AMBIL DATA RIWAYAT BELAJAR (ABSENSI)
// Kita mengambil data materi dan perkembangan yang diisi oleh guru
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
    <title>Ruang Murid - Smart Arca</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f0f2f5; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 600px; margin: auto; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; background: white; padding: 15px 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .header h2 { margin: 0; font-size: 20px; color: #1a73e8; }
        .btn-logout { color: #dc3545; text-decoration: none; font-weight: bold; font-size: 14px; border: 1px solid #dc3545; padding: 5px 12px; border-radius: 8px; transition: 0.3s; }
        .btn-logout:hover { background: #dc3545; color: white; }

        .card { background: white; padding: 20px; border-radius: 15px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-left: 5px solid #1a73e8; position: relative; }
        .date-tag { position: absolute; top: 20px; right: 20px; font-size: 11px; color: #999; font-weight: bold; }
        .instrument-badge { background: #e8f0fe; color: #1a73e8; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; display: inline-block; margin-bottom: 10px; }
        
        h4 { margin: 10px 0 5px 0; font-size: 17px; color: #222; }
        .guru-name { font-size: 13px; color: #666; margin-bottom: 15px; }
        .note-box { background: #f8f9fa; padding: 12px; border-radius: 8px; font-size: 14px; line-height: 1.5; color: #444; border-left: 3px solid #ddd; }
        
        .link-box { margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee; }
        .btn-link { display: inline-block; background: #1a73e8; color: white; text-decoration: none; padding: 8px 15px; border-radius: 8px; font-size: 12px; font-weight: bold; }
        .btn-link:hover { background: #1557b0; }
        
        .empty-state { text-align: center; padding: 50px 20px; color: #999; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div>
            <h2>Halo, <?php echo htmlspecialchars($display_name); ?>!</h2>
            <small style="color: #888;">Selamat datang di Ruang Belajar</small>
        </div>
        <a href="/api/logout.php" class="btn-logout">Logout</a>
    </div>

    <h3 style="font-size: 16px; color: #555; margin-bottom: 15px;">Riwayat Pembelajaran</h3>

    <?php 
    if ($query_belajar && mysqli_num_rows($query_belajar) > 0) {
        while ($row = mysqli_fetch_assoc($query_belajar)) { 
    ?>
        <div class="card">
            <div class="date-tag"><?php echo date('d M Y', strtotime($row['tanggal'])); ?></div>
            <div class="instrument-badge"><?php echo strtoupper($row['alat_musik']); ?></div>
            
            <h4>Materi: <?php echo htmlspecialchars($row['materi_ajar']); ?></h4>
            <div class="guru-name">Oleh Guru: <strong><?php echo htmlspecialchars($row['nama_guru']); ?></strong></div>
            
            <div class="note-box">
                <strong>Catatan Guru:</strong><br>
                <?php echo nl2br(htmlspecialchars($row['perkembangan_murid'])); ?>
            </div>

            <?php if (!empty($row['file_materi'])) { ?>
                <div class="link-box">
                    <a href="<?php echo $row['file_materi']; ?>" target="_blank" class="btn-link">
                        🔗 BUKA LINK MATERI / TUGAS
                    </a>
                </div>
            <?php } ?>
        </div>
    <?php 
        } 
    } else { 
    ?>
        <div class="empty-state">
            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" style="opacity: 0.2; margin-bottom: 15px;">
            <p>Belum ada catatan materi dari guru.<br>Tetap semangat berlatih musik!</p>
        </div>
    <?php } ?>
</div>

</body>
</html>
