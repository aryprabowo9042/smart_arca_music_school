<?php
session_start();
include '../includes/koneksi.php';

if ($_SESSION['status'] != "login" || $_SESSION['role'] != "admin") { header("Location: ../login.php"); exit(); }

// PROSES UPLOAD FILE
if (isset($_POST['upload'])) {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $kelas = $_POST['kelas']; // Ini yang penting!
    $level = $_POST['level'];
    
    // Ambil info file
    $nama_file = $_FILES['file_modul']['name'];
    $tmp_file = $_FILES['file_modul']['tmp_name'];
    
    // Beri nama unik agar tidak bentrok (tambah timestamp)
    $file_baru = time() . "_" . $nama_file;
    $path = "../uploads/modul/" . $file_baru;

    if ($nama_file != "") {
        // Coba pindahkan file ke folder
        if (move_uploaded_file($tmp_file, $path)) {
            $query = "INSERT INTO modul (judul, deskripsi, kelas_musik, level_target, file_path) 
                      VALUES ('$judul', '$deskripsi', '$kelas', '$level', '$file_baru')";
            
            if (mysqli_query($koneksi, $query)) {
                echo "<script>alert('Modul berhasil diunggah!'); window.location='modul.php';</script>";
            } else {
                echo "Gagal database: " . mysqli_error($koneksi);
            }
        } else {
            echo "<script>alert('Gagal upload. Cek folder uploads/modul apakah sudah dibuat?');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Modul</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="sidebar">
        <h2>Smart Arca Admin</h2>
        <a href="index.php">Dashboard</a>
        <a href="data_guru.php">Data Guru</a>
        <a href="data_murid.php">Data Murid</a>
        <a href="jadwal.php">Jadwal Les</a>
        <a href="pembayaran.php">Keuangan</a>
        <a href="modul.php" style="background-color: #495057; color: white;">Modul Belajar</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Modul Belajar Digital</h1>
        
        <div class="card" style="background: #e2e6ea; border: 1px solid #ccc;">
            <h3>+ Upload Materi Baru</h3>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Judul Materi</label>
                    <input type="text" name="judul" required placeholder="Contoh: Chord Dasar C Major">
                </div>
                
                <div class="form-group">
                    <label>Deskripsi Singkat</label>
                    <input type="text" name="deskripsi" required placeholder="Penjelasan isi modul">
                </div>

                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Untuk Kelas Musik</label>
                        <select name="kelas" required style="width: 100%; padding: 8px;">
                            <option value="Piano/Keyboard">Piano/Keyboard</option>
                            <option value="Gitar Klasik">Gitar Klasik</option>
                            <option value="Gitar Elektrik">Gitar Elektrik</option>
                            <option value="Drum">Drum</option>
                            <option value="Vokal">Vokal</option>
                            <option value="Theory">Music Theory</option>
                        </select>
                    </div>

                    <div class="form-group" style="flex: 1;">
                        <label>Target Level</label>
                        <select name="level" required style="width: 100%; padding: 8px;">
                            <option value="Basic">Basic</option>
                            <option value="Intermediate">Intermediate</option>
                            <option value="Advance">Advance</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>File Materi (PDF / Gambar)</label>
                    <input type="file" name="file_modul" required>
                </div>

                <button type="submit" name="upload" class="btn btn-green">Upload File</button>
            </form>
        </div>

        <br>

        <div class="card">
            <h3>Daftar Modul Tersedia</h3>
            <table>
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Kelas & Level</th>
                        <th>File</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = mysqli_query($koneksi, "SELECT * FROM modul ORDER BY id DESC");
                    while ($data = mysqli_fetch_assoc($query)) {
                    ?>
                    <tr>
                        <td>
                            <b><?php echo $data['judul']; ?></b><br>
                            <small><?php echo $data['deskripsi']; ?></small>
                        </td>
                        <td>
                            <?php echo $data['kelas_musik']; ?> <br> 
                            <span style="background:#ddd; padding:2px 5px; font-size:12px; border-radius:3px;"><?php echo $data['level_target']; ?></span>
                        </td>
                        <td>
                            <a href="../uploads/modul/<?php echo $data['file_path']; ?>" target="_blank" style="color: blue; text-decoration: underline;">Download</a>
                        </td>
                        <td>
                            <a href="hapus_modul.php?id=<?php echo $data['id']; ?>" class="btn btn-red" onclick="return confirm('Hapus file modul ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>