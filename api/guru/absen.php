<?php
session_start();
ob_start();
require_once(__DIR__ . '/../koneksi.php');

$is_logged_in = (isset($_SESSION['status']) && $_SESSION['status'] == 'login') || isset($_COOKIE['user_login']);
$is_guru = (isset($_SESSION['role']) && $_SESSION['role'] == 'guru') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'guru');

if (!$is_logged_in || !$is_guru) {
    echo "<script>window.location.replace('../admin/login.php');</script>";
    exit();
}

$id_jadwal = mysqli_real_escape_string($conn, $_GET['id_jadwal'] ?? '');
$info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT j.*, m.username FROM jadwal j JOIN users m ON j.id_murid = m.id WHERE j.id = '$id_jadwal'"));

if (isset($_POST['simpan'])) {
    $tgl = $_POST['tanggal'];
    $mtr = mysqli_real_escape_string($conn, $_POST['materi']);
    $prk = mysqli_real_escape_string($conn, $_POST['perkembangan']);
    $lnk = mysqli_real_escape_string($conn, $_POST['link']);
    $nom = (int)$_POST['nominal'];

    $sql = "INSERT INTO absensi (id_jadwal, tanggal, materi_ajar, perkembangan_murid, file_materi, nominal_bayar) 
            VALUES ('$id_jadwal', '$tgl', '$mtr', '$prk', '$lnk', '$nom')";
    
    if (mysqli_query($conn, $sql)) {
        $hak = $nom * 0.5;
        echo "<script>alert('Berhasil! Estimasi Honor Anda (50%): Rp " . number_format($hak, 0, ',', '.') . "'); window.location.href='index.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Absen</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f9; padding: 20px; }
        .box { background: white; padding: 25px; border-radius: 15px; max-width: 500px; margin: auto; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        input, textarea { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #28a745; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
<div class="box">
    <h3>Input Pembelajaran & Bayar</h3>
    <p>Murid: <strong><?php echo $info['username']; ?></strong></p>
    <form method="POST">
        <label style="font-size:12px;">Tanggal & Nominal Bayar:</label>
        <div style="display:flex; gap:10px;">
            <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required>
            <input type="number" name="nominal" placeholder="Rp Bayar" required>
        </div>
        <textarea name="materi" placeholder="Materi yang diajarkan" required></textarea>
        <textarea name="perkembangan" placeholder="Catatan perkembangan murid" required></textarea>
        <input type="url" name="link" placeholder="Link Materi (Opsional)">
        <button type="submit" name="simpan">SIMPAN & KONFIRMASI SALDO</button>
        <a href="index.php" style="display:block; text-align:center; margin-top:15px; color:#666; font-size:13px; text-decoration:none;">Batal</a>
    </form>
</div>
</body>
</html>
