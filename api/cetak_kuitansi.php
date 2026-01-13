<?php
session_start();
require_once(__DIR__ . '/koneksi.php');

// Ambil ID Absensi dari URL
$id_absen = mysqli_real_escape_string($conn, $_GET['id'] ?? '');

// Query lengkap untuk mengambil data kursus
$sql = "SELECT a.*, j.alat_musik, g.username as nama_guru, m.username as nama_murid 
        FROM absensi a 
        JOIN jadwal j ON a.id_jadwal = j.id 
        JOIN users g ON j.id_guru = g.id 
        JOIN users m ON j.id_murid = m.id 
        WHERE a.id = '$id_absen'";

$result = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($result);

if (!$data) { 
    echo "<script>alert('Data kuitansi tidak ditemukan!'); window.close();</script>"; 
    exit(); 
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi_<?php echo $data['nama_murid']; ?>_<?php echo $data['id']; ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; padding: 20px; margin: 0; }
        .kuitansi-wrapper { 
            width: 750px; margin: auto; background: white; padding: 40px; 
            border: 1px solid #ddd; position: relative; box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #1a73e8; padding-bottom: 20px; }
        .logo-section { display: flex; align-items: center; }
        .logo-circle { width: 70px; height: 70px; background: #1a73e8; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 10px; text-align: center; margin-right: 15px; border: 3px solid #e8f0fe; }
        .brand h1 { margin: 0; color: #1a73e8; font-size: 24px; letter-spacing: 1px; }
        .brand p { margin: 0; font-size: 12px; color: #666; }
        
        .title-kuitansi { text-align: center; margin: 30px 0; }
        .title-kuitansi h2 { margin: 0; text-decoration: underline; color: #333; }
        .title-kuitansi p { margin: 5px 0; font-size: 14px; font-weight: bold; color: #1a73e8; }

        .content { margin-bottom: 40px; }
        .info-row { display: flex; margin-bottom: 12px; font-size: 15px; border-bottom: 1px dotted #eee; padding-bottom: 5px; }
        .info-label { width: 200px; color: #555; }
        .info-value { flex: 1; font-weight: bold; color: #000; text-transform: uppercase; }

        .footer { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 50px; }
        .nominal-box { background: #e8f0fe; border: 2px dashed #1a73e8; padding: 15px 30px; border-radius: 10px; }
        .nominal-box span { font-size: 14px; display: block; color: #1a73e8; }
        .nominal-box strong { font-size: 24px; color: #1a73e8; }

        .ttd { text-align: center; width: 200px; }
        .ttd p { margin: 0; font-size: 14px; }
        .ttd .space { height: 80px; }

        /* STEMPEL LUNAS */
        .stempel {
            position: absolute; bottom: 80px; right: 180px; width: 150px; height: 70px;
            border: 4px double #d9534f; color: #d9534f; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 28px; font-weight: 900; text-transform: uppercase;
            transform: rotate(-20deg); opacity: 0.7; pointer-events: none;
            letter-spacing: 3px; font-family: 'Arial Black', sans-serif;
        }

        .btn-print { 
            display: block; width: 200px; margin: 0 auto 20px auto; padding: 10px; 
            background: #28a745; color: white; text-align: center; border: none; 
            border-radius: 5px; cursor: pointer; font-weight: bold;
        }

        @media print {
            body { background: white; padding: 0; }
            .kuitansi-wrapper { box-shadow: none; border: 1px solid #000; width: 100%; }
            .btn-print { display: none; }
        }
    </style>
</head>
<body>

<button class="btn-print" onclick="window.print()">CETAK / SIMPAN PDF</button>

<div class="kuitansi-wrapper">
    <div class="header">
        <div class="logo-section">
            <div class="logo-circle">SMART<br>ARCA</div>
            <div class="brand">
                <h1>SMART ARCA</h1>
                <p>Music School & Education Center</p>
                <p>Batam, Kepulauan Riau - Indonesia</p>
            </div>
        </div>
        <div style="text-align: right;">
            <p style="margin:0; font-size:12px; color:#666;">Tanggal Cetak:</p>
            <p style="margin:0; font-size:14px; font-weight:bold;"><?php echo date('d/m/Y'); ?></p>
        </div>
    </div>

    <div class="title-kuitansi">
        <h2>KUITANSI PEMBAYARAN</h2>
        <p>No. INV/SA/<?php echo date('Y', strtotime($data['tanggal'])); ?>/<?php echo $data['id']; ?></p>
    </div>

    <div class="content">
        <div class="info-row">
            <div class="info-label">Telah Diterima Dari</div>
            <div class="info-value">: <?php echo $data['nama_murid']; ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Untuk Pembayaran</div>
            <div class="info-value">: KURSUS MUSIK (<?php echo $data['alat_musik']; ?>)</div>
        </div>
        <div class="info-row">
            <div class="info-label">Guru Pengajar</div>
            <div class="info-value">: <?php echo $data['nama_guru']; ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Materi / Catatan</div>
            <div class="info-value">: <?php echo $data['materi_ajar']; ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal Pelaksanaan</div>
            <div class="info-value">: <?php echo date('d F Y', strtotime($data['tanggal'])); ?></div>
        </div>
    </div>

    <div class="footer">
        <div class="nominal-box">
            <span>Jumlah Pembayaran:</span>
            <strong>Rp <?php echo number_format($data['nominal_bayar'], 0, ',', '.'); ?>,-</strong>
        </div>
        <div class="ttd">
            <p>Batam, <?php echo date('d F Y'); ?></p>
            <div class="space"></div>
            <p><strong>ADMIN SMART ARCA</strong></p>
        </div>
    </div>

    <div class="stempel">LUNAS</div>
</div>

</body>
</html>
