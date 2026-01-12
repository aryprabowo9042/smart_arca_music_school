<?php
session_start();
// PERBAIKAN: Langsung ke file koneksi di folder api
require_once(__DIR__ . '/../koneksi.php');

// Proteksi halaman: Jika belum login atau bukan murid, arahkan ke login
if (!isset($_SESSION['status']) || $_SESSION['role'] !== 'murid') {
    header("Location: ../admin/login.php");
    exit();
}

$nama_murid = $_SESSION['username'];

// Ambil riwayat belajar
$query_belajar = mysqli_query($conn, "SELECT absensi.*, j.alat_musik, u.username as nama_guru 
                                      FROM absensi 
                                      JOIN jadwal j ON absensi.id_jadwal = j.id 
                                      JOIN users m ON j.id_murid = m.id 
                                      JOIN users u ON j.id_guru = u.id
                                      WHERE m.username = '$nama_murid'
                                      ORDER BY absensi.tanggal DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progres Belajar - Smart Arca</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 20px; margin: 0; }
        .container { max-width: 800px; margin: auto; }
        .card { background: white; padding: 20px; border-radius: 12px; margin-bottom: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-left: 5px solid #1a73e8; }
        .tag { background: #e8f0fe; color: #1a73e8; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .btn-logout { color: #dc3545; text-decoration: none; font-weight: bold; float: right; }
    </style>
</head>
<body>
    <div class="container">
        <a href="../logout.php" class="btn-logout">Logout</a>
        <h2>Halo, <?php echo htmlspecialchars($nama_murid); ?></h2>
        <p style="color: #666;">Riwayat materi dan perkembangan belajar Anda.</p>
        <hr>

        <?php if(mysqli_num_rows($query_belajar) > 0) {
            while($row = mysqli_fetch_assoc($query_belajar)) { ?>
            <div class="card">
                <span class="tag"><?php echo $row['alat_musik']; ?></span>
                <span style="float:right; color:#888; font-size: 13px;"><?php echo date('d M Y', strtotime($row['tanggal'])); ?></span>
                <h4 style="margin: 15px 0 5px 0;">Guru: <?php echo htmlspecialchars($row['nama_guru']); ?></h4>
                <p><strong>Materi:</strong><br><?php echo nl2br(htmlspecialchars($row['materi_ajar'])); ?></p>
                <p><strong>Catatan Guru:</strong><br><small><?php echo nl2br(htmlspecialchars($row['perkembangan_murid'])); ?></small></p>
                
                <?php if(!empty($row['file_materi'])) { ?>
                    <div style="margin-top:10px; padding-top:10px; border-top:1px solid #eee;">
                        <a href="../uploads/modul/<?php echo $row['file_materi']; ?>" target="_blank" style="background:#28a745; color:white; padding:8px 12px; border-radius:5px; text-decoration:none; font-size:12px; font-weight:bold;">Lihat Modul Materi</a>
                    </div>
                <?php } ?>
            </div>
        <?php } } else { echo "<p style='text-align:center; color:#999; margin-top:50px;'>Belum ada riwayat materi belajar.</p>"; } ?>
    </div>
</body>
</html>
