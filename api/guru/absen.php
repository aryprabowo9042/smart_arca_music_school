<?php
session_start();
require_once(__DIR__ . '/../koneksi.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'guru') {
    header("Location: ../admin/login.php"); exit();
}

$id_jadwal = $_GET['id_jadwal'] ?? 0;
$info_jadwal = mysqli_query($conn, "SELECT jadwal.*, m.username as nama_murid FROM jadwal JOIN users m ON jadwal.id_murid = m.id WHERE jadwal.id = '$id_jadwal' LIMIT 1");
$det = mysqli_fetch_assoc($info_jadwal);

if (isset($_POST['simpan_absen'])) {
    $tanggal = $_POST['tanggal'];
    $materi  = mysqli_real_escape_string($conn, $_POST['materi']);
    $perkembangan = mysqli_real_escape_string($conn, $_POST['perkembangan']);
    $status = $_POST['status_hadir'];
    
    // Logika Upload File
    $nama_file = null;
    if (!empty($_FILES['dokumen']['name'])) {
        $nama_file = time() . "_" . $_FILES['dokumen']['name'];
        $tmp_file  = $_FILES['dokumen']['tmp_name'];
        $path      = __DIR__ . "/../uploads/" . $nama_file;
        move_uploaded_file($tmp_file, $path);
    }

    $query = mysqli_query($conn, "INSERT INTO absensi (id_jadwal, tanggal, materi_ajar, perkembangan_murid, status_hadir, file_materi) 
                                  VALUES ('$id_jadwal', '$tanggal', '$materi', '$perkembangan', '$status', '$nama_file')");

    if ($query) {
        echo "<script>alert('Laporan & Materi Berhasil Terkirim!'); window.location.href='index.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Materi - Smart Arca</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f6; padding: 15px; }
        .card { max-width: 500px; margin: auto; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        input, select, textarea { width: 100%; padding: 10px; margin-top: 5px; border-radius: 6px; border: 1px solid #ddd; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #1a73e8; color: white; border: none; border-radius: 6px; margin-top: 20px; cursor: pointer; font-weight: bold; }
        .file-info { font-size: 11px; color: #666; margin-top: 5px; }
    </style>
</head>
<body>
<div class="card">
    <h2>Laporan & Upload Materi</h2>
    <p>Murid: <strong><?php echo $det['nama_murid']; ?></strong></p>
    <form method="POST" enctype="multipart/form-data">
        <label>Tanggal:</label>
        <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required>

        <label>Materi Ajar:</label>
        <textarea name="materi" required></textarea>

        <label>Perkembangan Murid:</label>
        <textarea name="perkembangan" required></textarea>

        <label>Status Hadir:</label>
        <select name="status_hadir"><option>Hadir</option><option>Izin</option><option>Sakit</option></select>

        <label style="display:block; margin-top:15px; font-weight:bold;">Upload Dokumen Materi (PDF/JPG):</label>
        <input type="file" name="dokumen" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
        <div class="file-info">*File ini akan tampil di halaman murid untuk didownload.</div>

        <button type="submit" name="simpan_absen">SIMPAN & KIRIM KE MURID</button>
    </form>
    <a href="index.php" style="display:block; text-align:center; margin-top:15px; color:#666; text-decoration:none;">← Batal</a>
</div>
</body>
</html>
