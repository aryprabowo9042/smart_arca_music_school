<?php
session_start();
ob_start();
require_once(__DIR__ . '/../koneksi.php');

// Proteksi Guru
$is_logged_in = (isset($_SESSION['status']) && $_SESSION['status'] == 'login') || isset($_COOKIE['user_login']);
$is_guru = (isset($_SESSION['role']) && $_SESSION['role'] == 'guru') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'guru');

if (!$is_logged_in || !$is_guru) {
    echo "<script>window.location.replace('../admin/login.php');</script>";
    exit();
}

// Ambil ID Jadwal dari URL
$id_jadwal = mysqli_real_escape_string($conn, $_GET['id_jadwal'] ?? '');

if (empty($id_jadwal)) {
    header("Location: index.php");
    exit();
}

// Ambil info jadwal, murid, dan alat musik
$info_query = mysqli_query($conn, "SELECT jadwal.*, m.username as nama_murid 
                                   FROM jadwal 
                                   JOIN users m ON jadwal.id_murid = m.id 
                                   WHERE jadwal.id = '$id_jadwal' LIMIT 1");
$data = mysqli_fetch_assoc($info_query);

// PROSES SIMPAN ABSENSI (Tanpa Upload File agar tidak Forbidden)
if (isset($_POST['simpan_absen'])) {
    $tanggal = $_POST['tanggal'];
    $materi = mysqli_real_escape_string($conn, $_POST['materi']);
    $perkembangan = mysqli_real_escape_string($conn, $_POST['perkembangan']);
    $link_materi = mysqli_real_escape_string($conn, $_POST['link_materi']); // Mengambil link saja
    
    // Simpan link ke kolom file_materi di database
    $insert = mysqli_query($conn, "INSERT INTO absensi (id_jadwal, tanggal, materi_ajar, perkembangan_murid, file_materi) 
                                   VALUES ('$id_jadwal', '$tanggal', '$materi', '$perkembangan', '$link_materi')");

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
    <title>Input Laporan Pembelajaran</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f0f2f5; padding: 20px; margin: 0; }
        .form-card { background: white; padding: 30px; border-radius: 15px; max-width: 500px; margin: auto; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        h3 { color: #1a73e8; margin: 0 0 10px 0; }
        label { display: block; margin-top: 15px; font-size: 13px; font-weight: bold; color: #555; }
        input, textarea { width: 100%; padding: 12px; margin: 5px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-size: 14px; }
        input:focus, textarea:focus { border-color: #1a73e8; outline: none; }
        button { width: 100%; padding: 14px; background: #28a745; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 20px; font-size: 15px; }
        button:hover { background: #218838; }
        .back-btn { display: block; text-align: center; margin-top: 15px; color: #888; text-decoration: none; font-size: 13px; }
    </style>
</head>
<body>

<div class="form-card">
    <h3>Laporan Belajar</h3>
    <p style="font-size: 14px; color: #666; background: #f8f9fa; padding: 10px; border-radius: 8px;">
        Murid: <strong><?php echo htmlspecialchars($data['nama_murid']); ?></strong><br>
        Kelas: <strong><?php echo $data['alat_musik']; ?></strong>
    </p>
    
    <form method="POST">
        <label>Tanggal Pertemuan:</label>
        <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required>
        
        <label>Materi Pembelajaran:</label>
        <textarea name="materi" rows="3" placeholder="Apa yang dipelajari hari ini?" required></textarea>
        
        <label>Catatan Perkembangan:</label>
        <textarea name="perkembangan" rows="3" placeholder="Contoh: Sudah paham teknik dasar, perlu latihan jari manis." required></textarea>
        
        <label>Link Materi / Tugas (Opsional):</label>
        <input type="url" name="link_materi" placeholder="Contoh: Link Google Drive atau YouTube">
        <small style="color: #999; font-size: 11px;">Gunakan link jika ada file yang ingin dibagikan.</small>
        
        <button type="submit" name="simpan_absen">SIMPAN DATA</button>
        <a href="index.php" class="back-btn">← Batal & Kembali</a>
    </form>
</div>

</body>
</html>
