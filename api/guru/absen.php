<?php
session_start();
ob_start();
require_once(__DIR__ . '/../koneksi.php');

// Proteksi Guru
$is_logged_in = (isset($_SESSION['status']) && $_SESSION['status'] == 'login') || isset($_COOKIE['user_login']);
$is_guru = (isset($_SESSION['role']) && $_SESSION['role'] == 'guru') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'guru');

if (!$is_logged_in || !$is_guru) {
    header("Location: ../admin/login.php");
    exit();
}

// Ambil ID Jadwal dari URL
$id_jadwal = mysqli_real_escape_string($conn, $_GET['id_jadwal']);

// Ambil info jadwal, murid, dan alat musik
$info_query = mysqli_query($conn, "SELECT jadwal.*, m.username as nama_murid 
                                   FROM jadwal 
                                   JOIN users m ON jadwal.id_murid = m.id 
                                   WHERE jadwal.id = '$id_jadwal' LIMIT 1");
$data = mysqli_fetch_assoc($info_query);

// PROSES SIMPAN ABSENSI
if (isset($_POST['simpan_absen'])) {
    $tanggal = $_POST['tanggal'];
    $materi = mysqli_real_escape_string($conn, $_POST['materi']);
    $perkembangan = mysqli_real_escape_string($conn, $_POST['perkembangan']);
    
    $insert = mysqli_query($conn, "INSERT INTO absensi (id_jadwal, tanggal, materi_ajar, perkembangan_murid, file_materi) 
                                   VALUES ('$id_jadwal', '$tanggal', '$materi', '$perkembangan', '$nama_file')");

    if ($insert) {
        echo "<script>alert('Laporan pembelajaran berhasil disimpan!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan laporan.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Materi Pembelajaran</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f9; padding: 20px; }
        .form-card { background: white; padding: 25px; border-radius: 15px; max-width: 500px; margin: auto; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h3 { color: #1a73e8; margin-top: 0; }
        input, textarea { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #28a745; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; }
        .back-btn { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>

<div class="form-card">
    <h3>Isi Laporan Pembelajaran</h3>
    <p style="font-size: 14px; color: #555;">
        Murid: <strong><?php echo htmlspecialchars($data['nama_murid']); ?></strong><br>
        Kelas: <strong><?php echo $data['alat_musik']; ?></strong>
    </p>
    <hr>
    
    <form method="POST" enctype="multipart/form-data">
        <label style="font-size: 13px;">Tanggal Pertemuan:</label>
        <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required>
        
        <label style="font-size: 13px;">Materi yang Diajarkan:</label>
        <textarea name="materi" rows="3" placeholder="Contoh: Belajar Chord C Mayor dan G Mayor" required></textarea>
        
        <label style="font-size: 13px;">Perkembangan Murid:</label>
        <textarea name="perkembangan" rows="3" placeholder="Contoh: Sudah lancar pindah jari, perlu latihan tempo." required></textarea>
        
        <label style="font-size: 13px;">Link Materi (Google Drive/Dropbox):</label>
        <input type="url" name="link_materi" placeholder="https://drive.google.com/...">
        
        <button type="submit" name="simpan_absen">SIMPAN LAPORAN</button>
        <a href="index.php" class="back-btn">← Batal & Kembali</a>
    </form>
</div>

</body>
</html>
