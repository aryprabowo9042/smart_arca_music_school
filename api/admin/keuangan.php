<?php
// 1. PROTEKSI HALAMAN
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// 2. KONEKSI DATABASE
require_once(__DIR__ . '/../koneksi.php');

// 3. PENGATURAN FILTER BULAN & TAHUN
$bulan_pilih = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun_pilih = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

$nama_bulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

// ==========================================
// 4. AMBIL DATA DARI DUA TABEL (ABSENSI & MANUAL)
// ==========================================

// A. Ambil Data SPP Les (Pemasukan Otomatis)
// Kita ambil dari absensi karena ini adalah sumber uang masuk per pertemuan
$sql_spp = "SELECT a.tanggal, u.username as keterangan, 'pemasukan' as jenis, a.nominal_bayar as nominal, 'SPP Les' as kategori
            FROM absensi a 
            JOIN jadwal j ON a.id_jadwal = j.id 
            JOIN users u ON j.id_murid = u.id 
            WHERE MONTH(a.tanggal) = '$bulan_pilih' AND YEAR(a.tanggal) = '$tahun_pilih'";

// B. Ambil Data Semua Transaksi Manual (Termasuk Honor Guru & Operasional)
// PERUBAHAN: Kita mengambil Honor Guru dari sini karena di sini nominalnya sudah AKUMULASI (misal 75rb)
$sql_manual = "SELECT tanggal, keterangan, jenis, nominal, kategori
               FROM transaksi_manual 
               WHERE MONTH(tanggal) = '$bulan_pilih' AND YEAR(tanggal) = '$tahun_pilih'";

// C. Gabungkan Data (Pemasukan dari Absensi + Pengeluaran/Lainnya dari Manual)
$sql_gabungan = "($sql_spp) UNION ($sql_manual) ORDER BY tanggal ASC";
$result_laporan = mysqli_query($conn, $sql_gabungan);

// ==========================================
// 5. HITUNG TOTAL UNTUK RINGKASAN
// ==========================================
$total_masuk = 0;
$total_keluar = 0;
$data_tabel = [];

if ($result_laporan) {
    while($row = mysqli_fetch_assoc($result_laporan)) {
        if($row['jenis'] == 'pemasukan') {
            $total_masuk += $row['nominal'];
        } else {
            $total_keluar += $row['nominal'];
        }
        $data_tabel[] = $row;
    }
}
$laba_bersih = $total_masuk - $total_keluar;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Bulanan - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @media print { 
            .no-print { display: none; } 
            body { background-color: white; }
            .print-border { border: 1px solid #e2e8f0; }
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen pb-20">

    <nav class="bg-indigo-900 text-white py-4 px-6 shadow-xl border-b-4 border-yellow-400 sticky top-0 z-50 flex justify-between items-center no-print">
        <div class="flex items-center gap-3">
            <a href="honor.php" class="hover:text-yellow-400 transition"><i class="fas fa-arrow-left"></i></a>
            <h1 class="font-black italic uppercase tracking-tighter leading-none">Smart Arca Report</h1>
        </div>
        <button onclick="window.print()" class="bg-yellow-400 text-indigo-900 px-4 py-2 rounded-xl text-[10px] font-black uppercase shadow-lg hover:bg-yellow-500">
            <i class="fas fa-print mr-1"></i> Cetak Laporan
        </button>
    </nav>

    <div class="max-w-6xl mx-auto p-6 md:p-10">
        
        <div class="text-center mb-10">
            <h2 class="text-3xl font-black text-slate-800 uppercase italic tracking-tighter leading-none">Laporan Keuangan Bulanan</h2>
            <p class="text-red-600 font-black uppercase text-xs mt-2 tracking-[0.3em]">Smart Arca Music School - Weleri</p>
            <div class="mt-4 inline-block bg-indigo-100 text-indigo-700 px-6 py-2 rounded-full font-black uppercase italic text-sm border-2 border-indigo-200">
                Periode: <?php echo $nama_bulan[$bulan_pilih]; ?> <?php echo $tahun_pilih; ?>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2rem] shadow-md mb-10 no-print border-2 border-slate-100">
            <form method="GET" class="flex flex-wrap items-end gap-6">
                <div class="flex-1 min-w-[150px]">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2 mb-2 block">Pilih Bulan</label>
                    <select name="bulan" class="w-full p-3 rounded-xl bg-slate-50 border-2 border-slate-50 outline-none font-bold italic focus:border-indigo-500 transition">
                        <?php foreach($nama_bulan as $key => $val): ?>
                            <option value="<?php echo $key; ?>" <?php echo ($bulan_pilih == $key) ? 'selected' : ''; ?>><?php echo $val; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2 mb-2 block">Pilih Tahun</label>
                    <select name="tahun" class="w-full p-3 rounded-xl bg-slate-50 border-2 border-slate-50 outline-none font-bold italic focus:border-indigo-500 transition">
                        <?php for($i=2025; $i<=2030; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo ($tahun_pilih == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <button type="submit" class="bg-indigo-600 text-white px-8 py-3.5 rounded-xl font-black uppercase italic text-xs shadow-lg hover:bg-indigo-700 transition">Tampilkan Data</button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border-b-8 border-green-500">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 leading-none">Total Pemasukan</p>
                <h3 class="text-2xl font-black text-green-600 italic leading-none">Rp <?php echo number_format($total_masuk, 0, ',', '.'); ?></h3>
            </div>
            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border-b-8 border-red-600">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 leading-none">Total Pengeluaran</p>
                <h3 class="text-2xl font-black text-red-700 italic leading-none">Rp <?php echo number_format($total_keluar, 0, ',', '.'); ?></h3>
            </div>
            <div class="bg-indigo-900 p-8 rounded-[2.5rem] shadow-xl text-white">
                <p class="text-[10px] font-black text-indigo-300 uppercase tracking-widest mb-1 leading-none">Laba Bersih (Profit)</p>
                <h3 class="text-2xl font-black text-yellow-400 italic leading-none">Rp <?php echo number_format($laba_bersih, 0, ',', '.'); ?></h3>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-100">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 font-black uppercase italic tracking-widest border-b">
                    <tr>
                        <th class="p-6">Tanggal</th>
                        <th class="p-6">Keterangan Transaksi</th>
                        <th class="p-6">Kategori</th>
                        <th class="p-6">Masuk (Rp)</th>
                        <th class="p-6">Keluar (Rp)</th>
                    </tr>
                </thead>
                <tbody class="font-bold text-slate-600">
                    <?php if(empty($data_tabel)): ?>
                        <tr><td colspan="5" class="p-20 text-center text-slate-300 italic font-black text-lg">Tidak ada data transaksi di bulan ini.</td></tr>
                    <?php endif; ?>
                    <?php foreach($data_tabel as $row): ?>
                    <tr class="border-b border-slate-50 hover:bg-slate-50 transition">
                        <td class="p-6 whitespace-nowrap"><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                        <td class="p-6 uppercase italic"><?php echo $row['keterangan']; ?></td>
                        <td class="p-6">
                            <span class="bg-slate-100 px-3 py-1 rounded-full text-[9px] uppercase tracking-tighter">
                                <?php echo $row['kategori']; ?>
                            </span>
                        </td>
                        <td class="p-6 font-black text-green-600">
                            <?php echo ($row['jenis'] == 'pemasukan') ? number_format($row['nominal'], 0, ',', '.') : '-'; ?>
                        </td>
                        <td class="p-6 font-black text-red-600">
                            <?php echo ($row['jenis'] == 'pengeluaran') ? number_format($row['nominal'], 0, ',', '.') : '-'; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-slate-900 text-white font-black italic uppercase">
                    <tr>
                        <td colspan="3" class="p-6 text-right tracking-widest">Saldo Akhir Bulan Ini:</td>
                        <td colspan="2" class="p-6 text-center text-yellow-400 text-lg">Rp <?php echo number_format($laba_bersih, 0, ',', '.'); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-8 text-center text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em]">
            Laporan ini dihasilkan secara otomatis oleh Smart Arca Management System &copy; 2026
        </div>
    </div>

</body>
</html>
