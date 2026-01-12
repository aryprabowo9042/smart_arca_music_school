<?php
session_start();
ob_start();
require_once(__DIR__ . '/../koneksi.php');

// 1. PROTEKSI GURU
$is_logged_in = (isset($_SESSION['status']) && $_SESSION['status'] == 'login') || isset($_COOKIE['user_login']);
$is_guru = (isset($_SESSION['role']) && $_SESSION['role'] == 'guru') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'guru');

if (!$is_logged_in || !$is_guru) {
    echo "<script>window.location.replace('../admin/login.php');</script>";
    exit();
}

// 2. AMBIL DATA LAMA
$id_absen = mysqli_real_escape_string($conn, $_GET['id'] ?? '');
if (empty($id_absen)) {
    header("Location: index.php");
    exit();
}

$query_lama = mysqli_query($conn, "SELECT a.*, m.username as nama_murid 
                                   FROM absensi a 
                                   JOIN jadwal j ON a.id_jadwal = j.id 
                                   JOIN users m ON j.id_murid = m.id 
                                   WHERE a.id = '$id_absen' LIMIT 1");
$data = mysqli_fetch_assoc($query_lama);

// 3. PROSES UPDATE
if (isset($_POST['update_absen'])) {
    $tanggal      = $_POST['tanggal'];
    $materi       = mysqli_real_escape_string($conn, $_POST['materi']);
    $perkembangan = mysqli_real_escape_string($conn, $_POST['perkembangan']);
    $link_materi  = mysqli_real_escape_string($conn, $_POST['link_materi']);
    $nominal      = (int)$_POST['nominal_bayar'];

    $update = mysqli_query($conn, "UPDATE absensi SET 
                                    tanggal = '$tanggal', 
                                    materi_ajar = '$materi', 
                                    perkembangan_murid = '$perkembangan', 
                                    file_materi = '$link_materi', 
                                    nominal_bayar = '$nominal' 
                                   WHERE id = '$id_absen'");

    if ($update) {
        $hak = $nominal * 0.5;
        echo "<script>
                alert('Data berhasil diperbarui! Estimasi Honor Baru (50%): Rp " . number_format($hak, 0, ',', '.') . "'); 
                window.location.href='index.php';
              </script>";
    } else {
        echo "<script>alert('Gagal memperbarui data.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Laporan - Smart Arca</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f9; padding: 20px; }
        .form-card { background: white; padding: 25px; border-radius: 15px; max-width: 500px; margin: auto; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        label { display: block; margin-top: 15px; font-weight: bold; font-size: 13px; color: #555; }
        input, textarea { width: 100%; padding: 12px; margin: 5px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        button { width: 100%; padding: 14px; background: #1a73e8; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 20px; }
        .btn-cancel { display: block; text-align: center; margin-top: 15px; color: #888; text-decoration: none; font-size: 13px; }
    </style>
</head>
<body>

<div class="form-card">
    <h3 style="margin-top:0; color:#1a73e8;">Edit Laporan & Pembayaran</h3>
    <p style="font-size:14px; background:#e8f0fe; padding:10px; border-radius:8px;">
        Murid: <strong><?php echo htmlspecialchars($data['nama_murid']); ?></strong>
    </p>

    <form method="POST">
        <label>Tanggal & Nominal Bayar (Rp):</label>
        <div style="display: flex; gap: 10px;">
            <input type="date" name="tanggal" value="<?php echo $data['tanggal']; ?>" required style="flex: 1;">
            <input type="number" name="nominal_bayar" value="<?php echo $data['nominal_bayar']; ?>" placeholder="Rp" required style="flex: 1;">
        </div>

        <label>Materi Pembelajaran:</label>
        <textarea name="materi" rows="3" required><?php echo htmlspecialchars($data['materi_ajar']); ?></textarea>
        
        <label>Catatan Perkembangan:</label>
        <textarea name="perkembangan" rows="3" required><?php echo htmlspecialchars($data['perkembangan_murid']); ?></textarea>
        
        <label>Link Materi (Opsional):</label>
        <input type="url" name="link_materi" value="<?php echo htmlspecialchars($data['file_materi']); ?>" placeholder="https://...">

        <button type="submit" name="update_absen">SIMPAN PERUBAHAN</button>
        <a href="index.php" class="btn-cancel">Batal</a>
    </form>
</div>

</body>
</html>
