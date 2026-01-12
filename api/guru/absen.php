<?php
session_start();
ob_start();
require_once(__DIR__ . '/../koneksi.php');

// Proteksi Guru
$is_logged_in = (isset($_SESSION['status']) && $_SESSION['status'] == 'login') || isset($_COOKIE['user_login']);
$is_guru = (isset($_SESSION['role']) && $_SESSION['role'] == 'guru') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'guru');

if (!$is_logged_in || !$is_guru) {
    echo "<script>window.location.replace('../admin/login.php');</script>";
    exit();
}

// Ambil ID Jadwal dari URL
$id_jadwal = mysqli_real_escape_string($conn, $_GET['id_jadwal'] ?? '');
if (empty($id_jadwal)) { header("Location: index.php"); exit(); }

// Info Murid & Jadwal
$info_query = mysqli_query($conn, "SELECT jadwal.*, m.username as nama_murid 
                                   FROM jadwal 
                                   JOIN users m ON jadwal.id_murid = m.id 
                                   WHERE jadwal.id = '$id_jadwal' LIMIT 1");
$data = mysqli_fetch_assoc($info_query);

// PROSES SIMPAN
if (isset($_POST['simpan_absen'])) {
    $tanggal      = $_POST['tanggal'];
    $materi       = mysqli_real_escape_string($conn, $_POST['materi']);
    $perkembangan = mysqli_real_escape_string($conn, $_POST['perkembangan']);
    $link_materi  = mysqli_real_escape_string($conn, $_POST['link_materi']);
    $nominal      = (int)$_POST['nominal_bayar']; // Input Uang

    $insert = mysqli_query($conn, "INSERT INTO absensi (id_jadwal, tanggal, materi_ajar, perkembangan_murid, file_materi, nominal_bayar) 
                                   VALUES ('$id_jadwal', '$tanggal', '$materi', '$perkembangan', '$link_materi', '$nominal')");

    if ($insert) {
        $bagi_guru = $nominal * 0.6; // Contoh 60%
        echo "<script>
                alert('Laporan Berhasil! Estimasi Honor Anda dari sesi ini: Rp " . number_format($bagi_guru, 0, ',', '.') . "'); 
                window.location.href='index.php';
              </script>";
    } else {
        echo "<script>alert('Gagal simpan!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absen & Pembayaran - Smart Arca</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f9; padding: 20px; }
        .form-card { background: white; padding: 30px; border-radius: 15px; max-width: 500px; margin: auto; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .info-pembayaran { background: #fff3cd; border: 1px solid #ffeeba; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; color: #856404; }
        label { display: block; margin-top: 15px; font-weight: bold; font-size: 13px; }
        input, textarea { width: 100%; padding: 12px; margin: 5px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        button { width: 100%; padding: 14px; background: #28a745; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 20px; }
        .back { display: block; text-align: center; margin-top: 15px; color: #888; text-decoration: none; font-size: 13px; }
    </style>
</head>
<body>

<div class="form-card">
    <h3 style="margin-top:0; color:#1a73e8;">Input Pertemuan & Pembayaran</h3>
    <p style="font-size:14px;">Murid: <strong><?php echo htmlspecialchars($data['nama_murid']); ?></strong> (<?php echo $data['alat_musik']; ?>)</p>
    
    <div class="info-pembayaran">
        <strong>Konfirmasi Bagi Hasil:</strong><br>
        Sesuai kesepakatan, Guru menerima 60% dari setoran murid. Dana akan divalidasi oleh Admin.
    </div>

    <form method="POST">
        <label>Tanggal & Nominal:</label>
        <div style="display: flex; gap: 10px;">
            <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required style="flex: 1;">
            <input type="number" name="nominal_bayar" placeholder="Rp Bayar" required style="flex: 1;">
        </div>

        <label>Materi Pembelajaran:</label>
        <textarea name="materi" rows="3" placeholder="Apa yang dipelajari?" required></textarea>
        
        <label>Catatan Perkembangan:</label>
        <textarea name="perkembangan" rows="3" placeholder="Progres murid hari ini..." required></textarea>
        
        <label>Link Materi (Opsional):</label>
        <input type="url" name="link_materi" placeholder="https://drive.google.com/...">

        <button type="submit" name="simpan_absen" onclick="return confirm('Pastikan Nominal Pembayaran Murid sudah benar. Lanjutkan?')">SIMPAN & KONFIRMASI SALDO</button>
        <a href="index.php" class="back">← Kembali</a>
    </form>
</div>

</body>
</html>
