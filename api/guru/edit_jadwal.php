<?php
session_start();
include '../includes/koneksi.php';

if ($_SESSION['status'] != "login" || $_SESSION['role'] != "guru") { header("Location: ../login.php"); exit(); }

$id_guru_login = $_SESSION['id_user'];
$id_jadwal = $_GET['id'];

// Ambil Data Lama & Validasi Keamanan (Pastikan jadwal ini milik guru yg login)
$query_lama = mysqli_query($koneksi, "SELECT * FROM jadwal WHERE id='$id_jadwal' AND id_guru='$id_guru_login'");

// Jika jadwal tidak ditemukan atau milik guru lain
if (mysqli_num_rows($query_lama) == 0) {
    echo "<script>alert('Anda tidak memiliki akses ke jadwal ini!'); window.location='jadwal_saya.php';</script>";
    exit();
}

$data = mysqli_fetch_assoc($query_lama);

// Proses Update
if (isset($_POST['update'])) {
    $hari = $_POST['hari'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $ruangan = $_POST['ruangan'];

    $query = "UPDATE jadwal SET 
              hari='$hari', 
              jam_mulai='$jam_mulai', 
              jam_selesai='$jam_selesai', 
              ruangan='$ruangan' 
              WHERE id='$id_jadwal'";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Perubahan jadwal berhasil disimpan!'); window.location='jadwal_saya.php';</script>";
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
        <h2>Panel Guru</h2>
        <a href="index.php">Dashboard</a>
        <a href="jadwal_saya.php" style="background-color: #495057; color: white;">Jadwal Mengajar</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Ubah Jadwal Les</h1>
        <p>Ubah waktu atau ruangan untuk kelas ini.</p>

        <div class="card" style="max-width: 600px;">
            <form action="" method="POST">
                
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
                <a href="jadwal_saya.php" class="btn btn-red">Batal</a>
            </form>
        </div>
    </div>
</body>
</html>