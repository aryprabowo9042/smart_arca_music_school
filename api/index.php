<?php
session_start();
require_once(__DIR__ . '/../koneksi.php');

// Pastikan login sebagai murid
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'murid') {
    header("Location: ../admin/login.php"); exit();
}

$nama_murid = $_SESSION['username'];

// Ambil riwayat belajar murid ini
$query_belajar = mysqli_query($conn, "SELECT absensi.*, j.alat_musik, u.username as nama_guru 
                                      FROM absensi 
                                      JOIN jadwal j ON absensi.id_jadwal = j.id 
                                      JOIN users m ON j.id_murid = m.id 
                                      JOIN users u ON j.id_guru = u.id
                                      WHERE m.username = '$nama_murid'
                                      ORDER BY absensi.tanggal DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Murid - Smart Arca</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 15px; }
        .container { max-width: 800px; margin: auto; }
        .card { background: white; padding: 20px; border-radius: 12px; margin-bottom: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .tag { display: inline-block; padding: 4px 8px; background: #e8f0fe; color: #1a73e8; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .btn-download { display: inline-block; margin-top: 10px; padding: 8px 15px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; font-size: 13px; }
        .btn-preview { background: #6f42c1; }
    </style>
</head>
<body>
<div class="container">
    <h2>Progres Belajar: <?php echo $nama_murid; ?></h2>
    <a href="../logout.php" style="color:red; text-decoration:none; font-weight:bold;">Keluar</a>
    <hr>

    <?php while($row = mysqli_fetch_assoc($query_belajar)) { ?>
    <div class="card">
        <div style="display:flex; justify-content:space-between;">
            <span style="color:#666;"><?php echo date('d M Y', strtotime($row['tanggal'])); ?></span>
            <span class="tag"><?php echo $row['alat_musik']; ?></span>
        </div>
        <h3 style="margin:10px 0;">Guru: <?php echo $row['nama_guru']; ?></h3>
        <p><strong>Materi:</strong><br><?php echo nl2br(htmlspecialchars($row['materi_ajar'])); ?></p>
        <p><strong>Perkembangan:</strong><br><small><?php echo nl2br(htmlspecialchars($row['perkembangan_murid'])); ?></small></p>
        
        <?php if($row['file_materi']) { ?>
            <div style="border-top: 1px solid #eee; margin-top: 10px; padding-top: 10px;">
                <strong>Dokumen Materi:</strong><br>
                <a href="../uploads/<?php echo $row['file_materi']; ?>" target="_blank" class="btn-download btn-preview">Lihat Preview</a>
                <a href="../uploads/<?php echo $row['file_materi']; ?>" download class="btn-download">Download Materi</a>
            </div>
        <?php } ?>
    </div>
    <?php } ?>
</div>
</body>
</html>
