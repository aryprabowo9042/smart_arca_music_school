<?php
session_start();
include '../includes/koneksi.php';

if ($_SESSION['status'] != "login" || $_SESSION['role'] != "admin") { header("Location: ../login.php"); exit(); }

$id = $_GET['id'];

// Ambil data absensi lama + Nama Murid (untuk judul)
$query = "SELECT absensi.*, murid.nama_lengkap AS nama_murid 
          FROM absensi 
          JOIN jadwal ON absensi.id_jadwal = jadwal.id 
          JOIN users AS murid ON jadwal.id_murid = murid.id 
          WHERE absensi.id='$id'";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);

// Proses Update
if (isset($_POST['update'])) {
    $tanggal = $_POST['tanggal'];
    $status = $_POST['status'];
    $catatan = $_POST['catatan'];

    $sql = "UPDATE absensi SET tanggal='$tanggal', status='$status', catatan_guru='$catatan' WHERE id='$id'";
    
    if (mysqli_query($koneksi, $sql)) {
        echo "<script>alert('Data absensi berhasil diperbarui!'); window.location='absensi.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Absensi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="sidebar">
        <h2>Smart Arca Admin</h2>
        <a href="index.php">Dashboard</a>
        <a href="absensi.php" style="background-color: #495057; color: white;">Data Absensi</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Edit Absensi Siswa</h1>
        <p>Siswa: <b><?php echo $data['nama_murid']; ?></b></p>
        
        <div class="card" style="max-width: 600px;">
            <form action="" method="POST">
                
                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" value="<?php echo $data['tanggal']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Status Kehadiran</label>
                    <select name="status" style="width: 100%; padding: 8px;">
                        <option value="hadir" <?php if($data['status']=='hadir') echo 'selected'; ?>>Hadir</option>
                        <option value="izin" <?php if($data['status']=='izin') echo 'selected'; ?>>Izin</option>
                        <option value="sakit" <?php if($data['status']=='sakit') echo 'selected'; ?>>Sakit</option>
                        <option value="alpa" <?php if($data['status']=='alpa') echo 'selected'; ?>>Alpa (Tanpa Ket)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Catatan Guru / Jurnal Belajar</label>
                    <textarea name="catatan" rows="5" required><?php echo $data['catatan_guru']; ?></textarea>
                </div>

                <button type="submit" name="update" class="btn btn-blue">Update Data</button>
                <a href="absensi.php" class="btn btn-red">Batal</a>
            </form>
        </div>
    </div>
</body>
</html>