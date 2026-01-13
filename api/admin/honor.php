<?php
session_start();
ob_start();

// Cek Login
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

require_once(__DIR__ . '/../koneksi.php');

// --- PROSES SIMPAN TRANSAKSI ---
if (isset($_POST['simpan_transaksi'])) {
    $tgl  = $_POST['tanggal'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama_pelaku']);
    $ket  = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $tip  = $_POST['jenis']; // Nilainya 'masuk' atau 'keluar'
    $nom  = (int)$_POST['nominal'];

    // Query Insert dengan Nama Pelaku
    $sql_insert = "INSERT INTO keuangan (tanggal, nama_pelaku, keterangan, jenis, nominal) 
                   VALUES ('$tgl', '$nama', '$ket', '$tip', '$nom')";
    
    if (mysqli_query($conn, $sql_insert)) {
        header("Location: honor.php"); // Refresh halaman
        exit();
    } else {
        echo "<script>alert('Gagal menyimpan: " . mysqli_error($conn) . "');</script>";
    }
}

// --- HITUNG KEUANGAN ---

// 1. Total Pemasukan dari SPP/Les (Otomatis dari Absensi)
$q_spp = mysqli_query($conn, "SELECT SUM(nominal_bayar) as total FROM absensi");
$total_spp = mysqli_fetch_assoc($q_spp)['total'] ?? 0;

// 2. Total Pemasukan Manual (Lain-lain)
$q_masuk = mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan WHERE jenis='masuk'");
$total_masuk_manual = mysqli_fetch_assoc($q_masuk)['total'] ?? 0;

// 3. Total Pengeluaran Manual (Gaji Guru, Listrik, dll)
$q_keluar = mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan WHERE jenis='keluar'");
$total_keluar = mysqli_fetch_assoc($q_keluar)['total'] ?? 0;

// 4. Hitung Saldo Akhir
// Rumus: (Semua Uang Masuk) - (Semua Uang Keluar)
$total_pendapatan = $total_spp + $total_masuk_manual;
$saldo_akhir = $total_pendapatan - $total_keluar;


// --- LOGIKA HONOR GURU (Estimasi) ---
// Kita hitung berapa honor yang 'seharusnya' dibayar berdasarkan absensi (misal 50% bagi hasil)
$list_guru = mysqli_query($conn, "
    SELECT u.username, SUM(a.nominal_bayar) * 0.5 as estimasi_honor
    FROM absensi a
    JOIN jadwal j ON a.id_jadwal = j.id
    JOIN users u ON j.id_guru = u.id
    GROUP BY u.id
");

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keuangan - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen pb-10">

    <nav class="bg-white shadow-sm px-6 py-4 flex justify-between items-center mb-6 sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <a href="index.php" class="text-gray-500 hover:text-blue-600"><i class="fas fa-arrow-left"></i></a>
            <h1 class="text-xl font-bold text-gray-800">Manajemen Keuangan</h1>
        </div>
        <div class="bg-blue-100 text-blue-700 px-4 py-2 rounded-lg font-bold">
            Saldo: Rp <?php echo number_format($saldo_akhir, 0, ',', '.'); ?>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="space-y-6">
            
            <div class="bg-white p-6 rounded-2xl shadow-sm space-y-4">
                <h3 class="font-bold text-gray-700 border-b pb-2">Ringkasan Aset</h3>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">Total Pemasukan Les</span>
                    <span class="font-bold text-green-600">+ Rp <?php echo number_format($total_spp); ?></span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">Pemasukan Lain</span>
                    <span class="font-bold text-green-600">+ Rp <?php echo number_format($total_masuk_manual); ?></span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">Total Pengeluaran</span>
                    <span class="font-bold text-red-500">- Rp <?php echo number_format($total_keluar); ?></span>
                </div>
                <div class="border-t pt-2 flex justify-between items-center text-lg">
                    <span class="font-bold text-gray-800">Sisa Saldo</span>
                    <span class="font-bold text-blue-600">Rp <?php echo number_format($saldo_akhir); ?></span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h3 class="font-bold text-gray-700 mb-4 flex items-center gap-2">
                    <i class="fas fa-chalkboard-teacher text-purple-500"></i> Estimasi Honor Guru
                </h3>
                <p class="text-xs text-gray-400 mb-4">Angka ini adalah 50% dari total les yang diajar. Klik 'Bayar' untuk mencatat pengeluaran.</p>
                
                <div class="space-y-3">
                    <?php while($g = mysqli_fetch_assoc($list_guru)): ?>
                    <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                        <div>
                            <p class="font-bold text-sm text-gray-700"><?php echo $g['username']; ?></p>
                            <p class="text-xs text-gray-500">Hak: Rp <?php echo number_format($g['estimasi_honor']); ?></p>
                        </div>
                        <button onclick="bayarHonor('<?php echo $g['username']; ?>', '<?php echo $g['estimasi_honor']; ?>')" 
                                class="bg-purple-100 text-purple-700 hover:bg-purple-600 hover:text-white px-3 py-1 rounded-lg text-xs font-bold transition">
                            Bayar
                        </button>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

        </div>

        <div class="md:col-span-2 space-y-6">

            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h3 class="font-bold text-gray-700 mb-4">Input Transaksi Baru</h3>
                <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    
                    <div>
                        <label class="text-xs font-bold text-gray-400">Tanggal</label>
                        <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-gray-400">Jenis Transaksi</label>
                        <select name="jenis" id="jenis_transaksi" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="keluar">🔴 Pengeluaran (Uang Keluar)</option>
                            <option value="masuk">🟢 Pemasukan (Uang Masuk)</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-xs font-bold text-gray-400">Nama Pemberi / Penerima</label>
                        <input type="text" name="nama_pelaku" id="nama_pelaku" placeholder="Contoh: Pak Budi / Toko Alat Tulis" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-xs font-bold text-gray-400">Keterangan</label>
                        <input type="text" name="keterangan" id="keterangan" placeholder="Contoh: Honor Mengajar Bulan Mei / Beli Spidol" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-xs font-bold text-gray-400">Nominal (Rp)</label>
                        <input type="number" name="nominal" id="nominal" placeholder="0" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-bold text-lg" required>
                    </div>

                    <button type="submit" name="simpan_transaksi" class="md:col-span-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition">
                        SIMPAN DATA
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="p-4 border-b">
                    <h3 class="font-bold text-gray-700">Riwayat Transaksi Manual</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-500">
                            <tr>
                                <th class="p-4">Tanggal</th>
                                <th class="p-4">Nama</th>
                                <th class="p-4">Keterangan</th>
                                <th class="p-4 text-right">Masuk</th>
                                <th class="p-4 text-right">Keluar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php 
                            $riwayat = mysqli_query($conn, "SELECT * FROM keuangan ORDER BY tanggal DESC LIMIT 20");
                            while($r = mysqli_fetch_assoc($riwayat)): 
                            ?>
                            <tr class="hover:bg-gray-50">
                                <td class="p-4"><?php echo date('d/m/y', strtotime($r['tanggal'])); ?></td>
                                <td class="p-4 font-bold text-gray-700"><?php echo htmlspecialchars($r['nama_pelaku'] ?? '-'); ?></td>
                                <td class="p-4 text-gray-500"><?php echo htmlspecialchars($r['keterangan']); ?></td>
                                <td class="p-4 text-right font-bold text-green-600">
                                    <?php echo ($r['jenis'] == 'masuk') ? number_format($r['nominal']) : '-'; ?>
                                </td>
                                <td class="p-4 text-right font-bold text-red-500">
                                    <?php echo ($r['jenis'] == 'keluar') ? number_format($r['nominal']) : '-'; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script>
        function bayarHonor(namaGuru, nominal) {
            // Isi otomatis form
            document.getElementById('nama_pelaku').value = namaGuru;
            document.getElementById('keterangan').value = 'Pembayaran Honor ' + namaGuru;
            document.getElementById('nominal').value = nominal;
            
            // Set jenis ke Pengeluaran
            document.getElementById('jenis_transaksi').value = 'keluar';
            
            // Scroll ke form
            document.getElementById('nama_pelaku').focus();
            
            // Opsional: Kasih alert kecil
            alert('Form telah diisi untuk pembayaran honor ' + namaGuru + '. Silakan cek nominal dan klik Simpan.');
        }
    </script>

</body>
</html>
