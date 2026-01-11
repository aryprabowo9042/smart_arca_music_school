<?php
session_start();
require_once(__DIR__ . '/../koneksi.php');

// Ambil data Guru & Murid untuk pilihan di Form
$data_guru  = mysqli_query($conn, "SELECT * FROM users WHERE role='guru'");
$data_murid = mysqli_query($conn, "SELECT * FROM users WHERE role='murid'");

if (isset($_POST['simpan_jadwal'])) {
    $id_guru    = $_POST['id_guru'];
    $id_murid   = $_POST['id_murid'];
    $alat_musik = $_POST['alat_musik'];
    $hari       = $_POST['hari'];
    $jam        = $_POST['jam'];

    $query = mysqli_query($conn, "INSERT INTO jadwal (id_guru, id_murid, alat_musik, hari, jam) 
                                  VALUES ('$id_guru', '$id_murid', '$alat_musik', '$hari', '$jam')");

    if ($query) {
        echo "<script>alert('Jadwal Berhasil Diatur!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Gagal Simpan Jadwal');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Atur Jadwal Les</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f4f7f6; }
        .card { background: white; padding: 25px; border-radius: 12px; max-width: 500px; margin: auto; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        select, input { width: 100%; padding: 10px; margin: 10px 0; border-radius: 5px; border: 1px solid #ddd; }
        button { width: 100%; padding: 12px; background: #1a73e8; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
<div class="card">
    <h2>Atur Jadwal Les Musik</h2>
    <form method="POST">
        <label>Pilih Guru:</label>
        <select name="id_guru" required>
            <?php while($g = mysqli_fetch_assoc($data_guru)) { echo "<option value='".$g['id']."'>".$g['username']."</option>"; } ?>
        </select>

        <label>Pilih Murid:</label>
        <select name="id_murid" required>
            <?php while($m = mysqli_fetch_assoc($data_murid)) { echo "<option value='".$m['id']."'>".$m['username']."</option>"; } ?>
        </select>

        <label>Alat Musik:</label>
        <select name="alat_musik" required>
            <option>Drum</option>
            <option>Piano/Keyboard</option>
            <option>Vokal</option>
            <option>Gitar Akustik</option>
            <option>Gitar Elektrik</option>
            <option>Bas</option>
        </select>

        <label>Hari:</label>
        <select name="hari" required>
            <option>Senin</option><option>Selasa</option><option>Rabu</option><option>Kamis</option><option>Jumat</option><option>Sabtu</option><option>Minggu</option>
        </select>

        <label>Jam Les:</label>
        <input type="time" name="jam" required>

        <button type="submit" name="simpan_jadwal">SIMPAN JADWAL</button>
    </form>
    <br><a href="index.php">Kembali</a>
</div>
</body>
</html>
