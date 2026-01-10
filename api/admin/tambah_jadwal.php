<?php
session_start();
include '../includes/koneksi.php';

if ($_SESSION['status'] != "login" || $_SESSION['role'] != "admin") { header("Location: ../login.php"); exit(); }

if (isset($_POST['simpan'])) {
    $id_guru = $_POST['id_guru'];
    $id_murid = $_POST['id_murid'];
    $hari = $_POST['hari'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $ruangan = $_POST['ruangan'];

    $query = "INSERT INTO jadwal (id_guru, id_murid, hari, jam_mulai, jam_selesai, ruangan)
              VALUES ('$id_guru', '$id_murid', '$hari', '$jam_mulai', '$jam_selesai', '$ruangan')";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Jadwal berhasil dibuat!'); window.location='jadwal.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tambah Jadwal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="sidebar">
        <h2>Smart Arca Admin</h2>
        <a href="index.php">Dashboard</a>
        <a href="data_guru.php">Data Guru</a>
        <a href="data_murid.php">Data Murid</a>
        <a href="jadwal.php" style="background-color: #495057; color: white;">Jadwal Les</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Buat Jadwal Les Baru</h1>
        <div class="card" style="max-width: 600px;">
            <form action="" method="POST">
                
                <div class="form-group">
                    <label>Pilih Siswa</label>
                    <select name="id_murid" required style="width: 100%; padding: 8px;">
                        <option value="">-- Pilih Siswa --</option>
                        <?php
                        // Ambil data murid untuk dropdown
                        $q_murid = mysqli_query($koneksi, "SELECT * FROM users WHERE role='murid' ORDER BY nama_lengkap ASC");
                        while ($m = mysqli_fetch_assoc($q_murid)) {
                            echo "<option value='".$m['id']."'>".$m['nama_lengkap']." (".$m['kelas_musik'].")</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Pilih Guru Pengajar</label>
                    <select name="id_guru" required style="width: 100%; padding: 8px;">
                        <option value="">-- Pilih Guru --</option>
                        <?php
                        // Ambil data guru untuk dropdown
                        $q_guru = mysqli_query($koneksi, "SELECT * FROM users WHERE role='guru' ORDER BY nama_lengkap ASC");
                        while ($g = mysqli_fetch_assoc($q_guru)) {
                            echo "<option value='".$g['id']."'>".$g['nama_lengkap']."</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Hari</label>
                    <select name="hari" required style="width: 100%; padding: 8px;">
                        <option value="Senin">Senin</option>
                        <option value="Selasa">Selasa</option>
                        <option value="Rabu">Rabu</option>
                        <option value="Kamis">Kamis</option>
                        <option value="Jumat">Jumat</option>
                        <option value="Sabtu">Sabtu</option>
                        <option value="Minggu">Minggu</option>
                    </select>
                </div>

                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Jam Mulai</label>
                        <input type="time" name="jam_mulai" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Jam Selesai</label>
                        <input type="time" name="jam_selesai" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Ruangan</label>
                    <input type="text" name="ruangan" placeholder="Contoh: Studio 1 / R. Piano">
                </div>

                <button type="submit" name="simpan" class="btn btn-green">Simpan Jadwal</button>
                <a href="jadwal.php" class="btn btn-red">Batal</a>
            </form>
        </div>
    </div>
</body>
</html>