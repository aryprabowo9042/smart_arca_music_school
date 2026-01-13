<?php
// Cek Login
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'guru') {
    header("Location: ../admin/login.php");
    exit();
}

require_once(__DIR__ . '/../koneksi.php');

$id_jadwal = $_GET['id_jadwal'] ?? '';

// Ambil Detail Jadwal
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

// PROSES SIMPAN ABSEN
if (isset($_POST['simpan_absen'])) {
    $tgl = $_POST['tanggal'];
    $materi = mysqli_real_escape_string($conn, $_POST['materi']);
    $bayar = (int)$_POST['nominal_bayar']; // Nominal pembayaran hari ini
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);

    $insert = mysqli_query($conn, "
        INSERT INTO absensi (id_jadwal, tanggal, materi_ajar, nominal_bayar, catatan)
        VALUES ('$id_jadwal', '$tgl', '$materi', '$bayar', '$catatan')
    ");

    if ($insert) {
        // Beri notifikasi sukses dan kembali ke dashboard
        echo "<script>
            alert('Absensi & Pembayaran Berhasil Disimpan!');
            window.location.href = 'index.php';
        </script>";
        exit();
    } else {
        $error = "Gagal menyimpan: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Absen</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-4 flex items-center justify-center">

    <div class="bg-white p-6 rounded-2xl shadow-xl w-full max-w-md">
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h2 class="text-xl font-bold text-gray-800">Form Absensi</h2>
            <a href="index.php" class="text-sm text-gray-500">Batal</a>
        </div>

        <div class="bg-indigo-50 p-4 rounded-xl mb-6">
            <p class="text-xs text-gray-500 uppercase font-bold">Siswa</p>
            <p class="text-lg font-bold text-indigo-700"><?php echo $data['nama_murid']; ?></p>
            <p class="text-sm text-gray-600"><?php echo $data['alat_musik']; ?> • <?php echo $data['jam']; ?></p>
        </div>

        <form method="POST" class="space-y-4">
            
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Tanggal</label>
                <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" class="w-full p-3 border rounded-lg outline-none bg-gray-50">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Materi Yang Diajarkan</label>
                <textarea name="materi" rows="2" class="w-full p-3 border rounded-lg outline-none focus:border-indigo-500" placeholder="Contoh: Pengenalan Kunci C Major..." required></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Pembayaran Les (Jika Ada)</label>
                <div class="relative">
                    <span class="absolute left-3 top-3 text-gray-400 font-bold">Rp</span>
                    <input type="number" name="nominal_bayar" class="w-full pl-10 p-3 border rounded-lg outline-none focus:border-green-500 font-bold text-lg text-gray-800" placeholder="0" value="0">
                </div>
                <p class="text-[10px] text-gray-400 mt-1">*Isi jika siswa membayar uang les hari ini.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Catatan Tambahan (Opsional)</label>
                <input type="text" name="catatan" class="w-full p-3 border rounded-lg outline-none focus:border-indigo-500" placeholder="Catatan khusus...">
            </div>

            <button type="submit" name="simpan_absen" class="w-full bg-indigo-600 text-white font-bold py-4 rounded-xl shadow-lg hover:bg-indigo-700 transition mt-4">
                SIMPAN DATA
            </button>

        </form>
    </div>

</body>
</html>
