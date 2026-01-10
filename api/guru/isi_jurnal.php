<?php
session_start();
include '../includes/koneksi.php';

// Cek apakah user adalah guru
if ($_SESSION['status'] != "login" || $_SESSION['role'] != "guru") { 
    header("Location: ../login.php"); 
    exit(); 
}

// Ambil ID Jadwal dari URL
$id_jadwal = $_GET['id'];

// Ambil Detail Jadwal (Siapa muridnya, alat musik apa)
$query_info = "SELECT jadwal.*, users.nama_lengkap AS nama_murid, users.kelas_musik 
               FROM jadwal 
               JOIN users ON jadwal.id_murid = users.id 
               WHERE jadwal.id='$id_jadwal'";
$result_info = mysqli_query($koneksi, $query_info);
$data_jadwal = mysqli_fetch_assoc($result_info);

// Jika jadwal tidak ditemukan (validasi)
if (!$data_jadwal) {
    echo "<script>alert('Data jadwal tidak ditemukan!'); window.location='jadwal_saya.php';</script>";
    exit();
}

// PROSES SIMPAN JURNAL
if (isset($_POST['simpan'])) {
    $tanggal = $_POST['tanggal'];
    $status = $_POST['status'];
    $catatan = $_POST['catatan'];

    $query_simpan = "INSERT INTO absensi (id_jadwal, tanggal, status, catatan_guru) 
                     VALUES ('$id_jadwal', '$tanggal', '$status', '$catatan')";

    if (mysqli_query($koneksi, $query_simpan)) {
        echo "<script>alert('Jurnal mengajar berhasil disimpan!'); window.location='jadwal_saya.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Isi Jurnal Mengajar</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="sidebar">
        <h2>Panel Guru</h2>
        <a href="index.php">Dashboard</a>
        <a href="jadwal_saya.php" style="background-color: #495057; color: white;">Jadwal Mengajar</a>
        <a href="keuangan.php">Input Pembayaran</a>
        <a href="dompet.php">Dompet Saya</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Isi Jurnal & Absensi</h1>
        <p>Silakan isi laporan hasil mengajar hari ini.</p>

        <div class="card" style="background: #e2e6ea; border-left: 5px solid #007bff; margin-bottom: 20px;">
            <h3>Detail Kelas</h3>
            <p>
                <b>Nama Siswa:</b> <?php echo $data_jadwal['nama_murid']; ?> <br>
                <b>Instrumen:</b> <?php echo $data_jadwal['kelas_musik']; ?> <br>
                <b>Jadwal Rutin:</b> <?php echo $data_jadwal['hari'] . ", " . $data_jadwal['jam_mulai']; ?>
            </p>
        </div>

        <div class="card">
            <form action="" method="POST">
                
                <div class="form-group">
                    <label>Tanggal Pertemuan</label>
                    <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required style="width: 100%; padding: 10px;">
                </div>

                <div class="form-group">
                    <label>Status Kehadiran Siswa</label>
                    <select name="status" required style="width: 100%; padding: 10px;">
                        <option value="hadir">Hadir</option>
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit</option>
                        <option value="alpa">Tanpa Keterangan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Catatan Mengajar (Jurnal)</label>
                    <textarea name="catatan" rows="5" required style="width: 100%; padding: 10px;" placeholder="Tuliskan materi yang diajarkan, PR, atau progres siswa hari ini..."></textarea>
                </div>

                <button type="submit" name="simpan" class="btn btn-green">Simpan Laporan</button>
                <a href="jadwal_saya.php" class="btn btn-red">Batal</a>
            </form>
        </div>

        <br>

        <div class="card">
            <h3>Riwayat Jurnal Kelas Ini</h3>
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #ddd;">
                        <th style="padding: 10px; text-align: left;">Tanggal</th>
                        <th style="padding: 10px; text-align: left;">Status</th>
                        <th style="padding: 10px; text-align: left;">Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $q_history = mysqli_query($koneksi, "SELECT * FROM absensi WHERE id_jadwal='$id_jadwal' ORDER BY tanggal DESC LIMIT 5");
                    while ($h = mysqli_fetch_assoc($q_history)) {
                    ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px;"><?php echo date('d-m-Y', strtotime($h['tanggal'])); ?></td>
                        <td style="padding: 10px;">
                            <?php if($h['status']=='hadir') echo '✅ Hadir'; 
                                  else if($h['status']=='sakit') echo '😷 Sakit';
                                  else echo '❌ Absen'; ?>
                        </td>
                        <td style="padding: 10px; color: #555;"><?php echo $h['catatan_guru']; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>