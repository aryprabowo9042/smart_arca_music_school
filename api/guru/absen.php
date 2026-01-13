<?php
// Cek Login
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'guru') {
    header("Location: ../admin/login.php");
    exit();
}

require_once(__DIR__ . '/../koneksi.php');

$id_jadwal = $_GET['id_jadwal'] ?? '';

// Ambil Detail Jadwal & Nama Murid
$q_detail = mysqli_query($conn, "
    SELECT j.*, m.username as nama_murid 
    FROM jadwal j 
    JOIN users m ON j.id_murid = m.id 
    WHERE j.id = '$id_jadwal'
");
$data = mysqli_fetch_assoc($q_detail);

if (!$data) {
    echo "Jadwal tidak ditemukan.";
    exit();
}

// PROSES SIMPAN ABSEN & PEMBAYARAN
if (isset($_POST['simpan_absen'])) {
    $tgl = $_POST['tanggal'];
    $materi = mysqli_real_escape_string($conn, $_POST['materi']);
    
    // Ini Nominal Pembayaran Les dari Murid
    $bayar = (int)$_POST['nominal_bayar']; 
    
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);

    // Simpan ke database absensi
    $insert = mysqli_query($conn, "
        INSERT INTO absensi (id_jadwal, tanggal, materi_ajar, nominal_bayar, catatan)
        VALUES ('$id_jadwal', '$tgl', '$materi', '$bayar', '$catatan')
    ");

    if ($insert) {
        // Redirect balik ke dashboard
        echo "<script>
            alert('Data Absensi & Pembayaran Berhasil Disimpan!');
            window.location.href = 'index.php';
        </script>";
        exit();
    } else {
        echo "<script>alert('Gagal menyimpan: " . mysqli_error($conn) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Absen & Bayar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen p-4 flex items-center justify-center">

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
        
        <div class="bg-blue-600 p-6 text-white">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-blue-200 text-xs font-bold uppercase mb-1">Jurnal Mengajar</p>
                    <h2 class="text-2xl font-bold"><?php echo htmlspecialchars($data['nama_murid']); ?></h2>
                    <p class="text-sm opacity-90"><?php echo $data['alat_musik']; ?> • <?php echo $data['jam']; ?></p>
                </div>
                <a href="index.php" class="bg-white/20 hover:bg-white/30 p-2 rounded-lg transition">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </div>

        <form method="POST" class="p-6 space-y-5">
            
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Tanggal Pertemuan</label>
                <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" class="w-full p-3 border border-gray-200 rounded-xl outline-none focus:border-blue-500 bg-gray-50">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Materi / Progress</label>
                <textarea name="materi" rows="3" class="w-full p-3 border border-gray-200 rounded-xl outline-none focus:border-blue-500" placeholder="Apa yang dipelajari hari ini?" required></textarea>
            </div>

            <div class="bg-green-50 p-4 rounded-xl border border-green-100">
                <label class="block text-xs font-bold text-green-700 uppercase mb-2">
                    <i class="fas fa-money-bill-wave mr-1"></i> Input Pembayaran Les
                </label>
                <div class="relative">
                    <span class="absolute left-4 top-3 text-green-600 font-bold">Rp</span>
                    <input type="number" name="nominal_bayar" class="w-full pl-10 p-3 border border-green-200 rounded-xl outline-none focus:ring-2 focus:ring-green-500 font-bold text-xl text-gray-700 bg-white" placeholder="0" value="0">
                </div>
                <p class="text-[10px] text-green-600 mt-2 leading-tight">
                    * Isi nominal jika murid membayar uang les hari ini.<br>
                    * Isi <strong>0</strong> jika murid tidak membayar (sudah lunas atau belum waktunya).
                </p>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Catatan (Opsional)</label>
                <input type="text" name="catatan" class="w-full p-3 border border-gray-200 rounded-xl outline-none focus:border-blue-500" placeholder="PR atau pesan khusus...">
            </div>

            <button type="submit" name="simpan_absen" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-blue-200 transition transform active:scale-95">
                SIMPAN JURNAL & BAYAR
            </button>

        </form>
    </div>

</body>
</html>
