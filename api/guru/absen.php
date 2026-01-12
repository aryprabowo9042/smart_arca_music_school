<?php
session_start();
require_once(__DIR__ . '/../koneksi.php');

// Cek Role Guru
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'guru') {
    header("Location: ../admin/login.php");
    exit();
}

// Ambil ID Jadwal dari URL
$id_jadwal = $_GET['id_jadwal'] ?? 0;

// Ambil info detail jadwal untuk ditampilkan di judul (Nama Murid & Alat Musik)
$info_jadwal = mysqli_query($conn, "SELECT jadwal.*, m.username as nama_murid 
                                    FROM jadwal 
                                    JOIN users m ON jadwal.id_murid = m.id 
                                    WHERE jadwal.id = '$id_jadwal' LIMIT 1");
$det = mysqli_fetch_assoc($info_jadwal);

if (!$det) { die("Data jadwal tidak ditemukan."); }

// PROSES SIMPAN ABSEN
if (isset($_POST['simpan_absen'])) {
    $tanggal = $_POST['tanggal'];
    $materi  = mysqli_real_escape_string($conn, $_POST['materi']);
    $perkembangan = mysqli_real_escape_string($conn, $_POST['perkembangan']);
    $status = $_POST['status_hadir'];

    $query = mysqli_query($conn, "INSERT INTO absensi (id_jadwal, tanggal, materi_ajar, perkembangan_murid, status_hadir) 
                                  VALUES ('$id_jadwal', '$tanggal', '$materi', '$perkembangan', '$status')");

    if ($query) {
        echo "<script>alert('Laporan Mengajar Berhasil Disimpan!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan laporan.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Isi Absen & Materi - Smart Arca</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f6; padding: 15px; }
        .card { max-width: 500px; margin: auto; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h2 { color: #1a73e8; margin-top: 0; }
        label { display: block; margin-top: 15px; font-weight: bold; font-size: 14px; }
        input, select, textarea { width: 100%; padding: 10px; margin-top: 5px; border-radius: 6px; border: 1px solid #ddd; box-sizing: border-box; }
        textarea { height: 100px; }
        button { width: 100%; padding: 12px; background: #28a745; color: white; border: none; border-radius: 6px; margin-top: 20px; cursor: pointer; font-weight: bold; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; }
    </style>
</head>
<body>

<div class="card">
    <h2>Laporan Mengajar</h2>
    <p>Murid: <strong><?php echo $det['nama_murid']; ?></strong><br>
       Alat Musik: <strong><?php echo $det['alat_musik']; ?></strong></p>
    <hr>

    <form method="POST">
        <label>Tanggal Pertemuan:</label>
        <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required>

        <label>Status Kehadiran Murid:</label>
        <select name="status_hadir">
            <option value="Hadir">Hadir</option>
            <option value="Izin">Izin</option>
            <option value="Sakit">Sakit</option>
            <option value="Tanpa Keterangan">Tanpa Keterangan (Alpha)</option>
        </select>

        <label>Materi yang Diajarkan:</label>
        <textarea name="materi" placeholder="Contoh: Belajar Chord Major dan Lagu 'Twinkle Twinkle'..." required></textarea>

        <label>Catatan Perkembangan Murid:</label>
        <textarea name="perkembangan" placeholder="Contoh: Sudah lancar pindah jari, tapi tempo masih lambat..." required></textarea>

        <button type="submit" name="simpan_absen">SIMPAN LAPORAN</button>
    </form>
    
    <a href="index.php" class="back-link">← Kembali ke Jadwal</a>
</div>

</body>
</html>
