<?php
session_start();
include '../includes/koneksi.php';

if ($_SESSION['status'] != "login" || $_SESSION['role'] != "guru") { header("Location: ../login.php"); exit(); }

// Ambil ID Jadwal dari URL
$id_jadwal = $_GET['id_jadwal'];

// Ambil info jadwal untuk ditampilkan di judul (Biar guru tau ini ngisi buat siapa)
$q_info = mysqli_query($koneksi, "SELECT users.nama_lengkap FROM jadwal JOIN users ON jadwal.id_murid = users.id WHERE jadwal.id='$id_jadwal'");
$d_info = mysqli_fetch_assoc($q_info);

// Proses Simpan Absensi
if (isset($_POST['simpan'])) {
    $tanggal = $_POST['tanggal'];
    $status = $_POST['status'];
    $catatan = $_POST['catatan'];

    $query = "INSERT INTO absensi (id_jadwal, tanggal, status, catatan_guru) 
              VALUES ('$id_jadwal', '$tanggal', '$status', '$catatan')";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Laporan mengajar berhasil disimpan!'); window.location='jadwal_saya.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Input Jurnal Mengajar</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="sidebar">
        <h2>Panel Guru</h2>
        <a href="index.php">Dashboard</a>
        <a href="jadwal_saya.php" style="background-color: #495057; color: white;">Jadwal Mengajar</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Jurnal Mengajar</h1>
        <p>Siswa: <b><?php echo $d_info['nama_lengkap']; ?></b></p>
        
        <div class="card" style="max-width: 600px;">
            <form action="" method="POST">
                
                <div class="form-group">
                    <label>Tanggal Pertemuan</label>
                    <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group">
                    <label>Status Kehadiran Siswa</label>
                    <select name="status" style="width: 100%; padding: 8px;">
                        <option value="hadir">Hadir</option>
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit</option>
                        <option value="alpa">Tanpa Keterangan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Catatan Guru / Materi Hari Ini</label>
                    <textarea name="catatan" rows="5" placeholder="Contoh: Hari ini belajar Scale C Major, teknik fingering sudah bagus. PR: Latih tempo 80bpm." required></textarea>
                </div>

                <button type="submit" name="simpan" class="btn btn-green">Simpan Laporan</button>
                <a href="jadwal_saya.php" class="btn btn-red">Batal</a>
            </form>
        </div>
    </div>

</body>
</html>