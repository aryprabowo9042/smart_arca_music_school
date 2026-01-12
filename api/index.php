<?php
session_start();
require_once(__DIR__ . '/../koneksi.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'murid') {
    header("Location: ../admin/login.php"); exit();
}

$nama_murid = $_SESSION['username'];

// Ambil riwayat belajar & materi untuk murid yang sedang login
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
    <title>Ruang Belajar Murid - Smart Arca</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: auto; }
        .header { background: #1a73e8; color: white; padding: 20px; border-radius: 15px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; }
        .card { background: white; padding: 25px; border-radius: 15px; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-left: 6px solid #1a73e8; }
        .materi-box { background: #f8f9fa; padding: 15px; border-radius: 10px; border: 1px solid #eee; margin-top: 15px; }
        .btn { display: inline-block; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 13px; margin-top: 10px; margin-right: 5px; transition: 0.3s; }
        .btn-download { background: #28a745; color: white; }
        .btn-preview { background: #6f42c1; color: white; }
        .tag { background: #e8f0fe; color: #1a73e8; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div>
            <h2 style="margin:0;">Halo, <?php echo htmlspecialchars($nama_murid); ?>!</h2>
            <span>Selamat berlatih hari ini.</span>
        </div>
        <a href="../logout.php" style="color: white; text-decoration: none; font-weight: bold;">Logout</a>
    </div>

    <h3 style="color: #444;">Riwayat Materi & Absensi</h3>

    <?php if(mysqli_num_rows($query_belajar) > 0) { 
        while($row = mysqli_fetch_assoc($query_belajar)) { ?>
        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <span style="font-weight: bold; color: #666;"><?php echo date('l, d M Y', strtotime($row['tanggal'])); ?></span>
                <span class="tag"><?php echo $row['alat_musik']; ?></span>
            </div>
            
            <p style="margin: 15px 0 5px 0; color: #888; font-size: 12px;">GURU PENGUJI:</p>
            <h4 style="margin:0;"><?php echo htmlspecialchars($row['nama_guru']); ?></h4>

            <div class="materi-box">
                <strong>Materi Ajar:</strong><br>
                <?php echo nl2br(htmlspecialchars($row['materi_ajar'])); ?>
            </div>

            <p><strong>Catatan Guru:</strong><br>
            <small style="color: #555;"><?php echo nl2br(htmlspecialchars($row['perkembangan_murid'])); ?></small></p>

            <?php if(!empty($row['file_materi'])) { 
                $file_path = "../uploads/modul/" . $row['file_materi'];
            ?>
                <div style="border-top: 1px solid #eee; margin-top: 15px; padding-top: 15px;">
                    <span style="font-size: 13px; font-weight: bold;">📁 File Modul Materi:</span><br>
                    <a href="<?php echo $file_path; ?>" target="_blank" class="btn btn-preview">Buka / Preview</a>
                    <a href="<?php echo $file_path; ?>" download class="btn btn-download">Download Modul</a>
                </div>
            <?php } ?>
        </div>
    <?php } } else { ?>
        <div class="card" style="text-align: center; border-left: none; color: #999;">
            Belum ada catatan materi dari guru.
        </div>
    <?php } ?>
</div>

</body>
</html>
