<?php
session_start();
include '../includes/koneksi.php';

if ($_SESSION['status'] != "login" || $_SESSION['role'] != "admin") { header("Location: ../login.php"); exit(); }

$id_jadwal = $_GET['id'];

// Ambil Data Lama
$query_lama = mysqli_query($koneksi, "SELECT * FROM jadwal WHERE id='$id_jadwal'");
$data = mysqli_fetch_assoc($query_lama);

// Proses Update
if (isset($_POST['update'])) {
    $id_murid = $_POST['id_murid'];
    $id_guru = $_POST['id_guru'];
    $hari = $_POST['hari'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $ruangan = $_POST['ruangan'];

    $query = "UPDATE jadwal SET 
              id_murid='$id_murid', 
              id_guru='$id_guru', 
              hari='$hari', 
              jam_mulai='$jam_mulai', 
              jam_selesai='$jam_selesai', 
              ruangan='$ruangan' 
              WHERE id='$id_jadwal'";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Jadwal berhasil diupdate!'); window.location='jadwal.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Jadwal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="sidebar">
        <h2>Smart Arca Admin</h2>
        <a href="index.php">Dashboard</a>
        <a href="jadwal.php" style="background-color: #495057; color: white;">Jadwal Les</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Edit Jadwal Les</h1>
        <div class="card" style="max-width: 600px;">
            <form action="" method="POST">
                
                <div class="form-group">
                    <label>Nama Siswa</label>
                    <select name="id_murid" required style="width: 100%; padding: 10px;">
                        <?php
                        $m = mysqli_query($koneksi, "SELECT * FROM users WHERE role='murid'");
                        while ($r = mysqli_fetch_assoc($m)) {
                            $selected = ($r['id'] == $data['id_murid']) ? 'selected' : '';
                            echo "<option value='".$r['id']."' $selected>".$r['nama_lengkap']."</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Guru Pengajar</label>
                    <select name="id_guru" required style="width: 100%; padding: 10px;">
                        <?php
                        $g = mysqli_query($koneksi, "SELECT * FROM users WHERE role='guru'");
                        while ($r = mysqli_fetch_assoc($g)) {
                            $selected = ($r['id'] == $data['id_guru']) ? 'selected' : '';
                            echo "<option value='".$r['id']."' $selected>".$r['nama_lengkap']."</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Hari</label>
                    <select name="hari" required style="width: 100%; padding: 10px;">
                        <?php
                        $days = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
                        foreach($days as $day){
                            $sel = ($day == $data['hari']) ? 'selected' : '';
                            echo "<option value='$day' $sel>$day</option>";
                        }
                        ?>
                    </select>
                </div>

                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex:1;">
                        <label>Jam Mulai</label>
                        <input type="time" name="jam_mulai" value="<?php echo $data['jam_mulai']; ?>" required>
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Jam Selesai</label>
                        <input type="time" name="jam_selesai" value="<?php echo $data['jam_selesai']; ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Ruangan</label>
                    <input type="text" name="ruangan" value="<?php echo $data['ruangan']; ?>" required>
                </div>

                <button type="submit" name="update" class="btn btn-blue">Simpan Perubahan</button>
                <a href="jadwal.php" class="btn btn-red">Batal</a>
            </form>
        </div>
    </div>
</body>
</html>