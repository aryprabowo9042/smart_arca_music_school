<?php
session_start();
// PERBAIKAN: Jalur koneksi disesuaikan dengan folder api/
require_once(__DIR__ . '/../koneksi.php');

// Pengecekan Login
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'murid') {
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
    <title>Ruang Belajar Murid</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 20px; }
        .card { background: white; padding: 20px; border-radius: 12px; margin-bottom: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .tag { background: #e8f0fe; color: #1a73e8; padding: 5px 10px; border-radius: 5px; font-size: 12px; font-weight: bold; }
        .btn-logout { color: red; text-decoration: none; font-weight: bold; float: right; }
    </style>
</head>
<body>
    <div style="max-width: 800px; margin: auto;">
        <a href="../logout.php" class="btn-logout">Logout</a>
        <h2>Progres Belajar: <?php echo htmlspecialchars($nama_murid); ?></h2>
        <hr>

        <?php if(mysqli_num_rows($query_belajar) > 0) {
            while($row = mysqli_fetch_assoc($query_belajar)) { ?>
            <div class="card">
                <span class="tag"><?php echo $row['alat_musik']; ?></span>
                <span style="float:right; color:#666;"><?php echo date('d M Y', strtotime($row['tanggal'])); ?></span>
                <h4 style="margin: 15px 0 5px 0;">Guru: <?php echo $row['nama_guru']; ?></h4>
                <p><strong>Materi:</strong><br><?php echo nl2br(htmlspecialchars($row['materi_ajar'])); ?></p>
                
                <?php if(!empty($row['file_materi'])) { ?>
                    <div style="margin-top:10px; padding-top:10px; border-top:1px solid #eee;">
                        <a href="../uploads/modul/<?php echo $row['file_materi']; ?>" target="_blank" style="background:#6f42c1; color:white; padding:8px 12px; border-radius:5px; text-decoration:none; font-size:12px;">Buka Modul Materi</a>
                    </div>
                <?php } ?>
            </div>
        <?php } } else { echo "<p>Belum ada catatan belajar.</p>"; } ?>
    </div>
</body>
</html>
