<?php
// 1. KONEKSI (File ini di folder api, jadi koneksi.php ada di folder yang sama)
require_once('koneksi.php');

$id_absen = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. AMBIL DATA LENGKAP
$sql = "SELECT a.*, u_m.username as nama_murid, u_g.username as nama_guru, j.alat_musik 
        FROM absensi a 
        JOIN jadwal j ON a.id_jadwal = j.id 
        JOIN users u_m ON j.id_murid = u_m.id 
        JOIN users u_g ON j.id_guru = u_g.id 
        WHERE a.id = '$id_absen' LIMIT 1";

$res = mysqli_query($conn, $sql);
$d = mysqli_fetch_assoc($res);

if(!$d) die("Kuitansi tidak ditemukan.");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kuitansi #<?php echo $d['id']; ?> - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print { .no-print { display: none; } }
        body { font-family: 'Courier New', Courier, monospace; }
    </style>
</head>
<body class="bg-slate-100 p-4 md:p-10">

    <div class="max-w-xl mx-auto bg-white p-8 shadow-sm border-t-8 border-red-700 relative overflow-hidden">
        
        <div class="absolute inset-0 flex items-center justify-center opacity-[0.05] pointer-events-none">
            <h1 class="text-9xl font-black -rotate-12 border-8 border-red-700 p-4">LUNAS</h1>
        </div>

        <div class="flex justify-between items-start mb-10 border-b-2 border-dashed pb-6 relative z-10">
            <div>
                <h1 class="text-2xl font-black uppercase italic tracking-tighter text-red-700">Smart Arca</h1>
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em]">Music School Management</p>
            </div>
            <div class="text-right">
                <h2 class="text-lg font-black uppercase italic">Kuitansi Resmi</h2>
                <p class="text-[9px] font-bold text-slate-400 uppercase">No: ARC/<?php echo date('Y/m', strtotime($d['tanggal'])); ?>/<?php echo $d['id']; ?></p>
            </div>
        </div>

        <div class="space-y-5 text-xs font-bold uppercase relative z-10">
            <div class="flex justify-between border-b border-slate-50 pb-2">
                <span class="text-slate-400 italic font-normal">Telah Diterima Dari:</span>
                <span class="text-slate-800"><?php echo $d['nama_murid']; ?></span>
            </div>
            <div class="flex justify-between border-b border-slate-50 pb-2">
                <span class="text-slate-400 italic font-normal">Untuk Pembayaran:</span>
                <span class="text-slate-800">Les Musik (<?php echo $d['alat_musik']; ?>)</span>
            </div>
            <div class="flex justify-between border-b border-slate-50 pb-2">
                <span class="text-slate-400 italic font-normal">Guru Pengajar:</span>
                <span class="text-slate-800"><?php echo $d['nama_guru']; ?></span>
            </div>
            <div class="flex justify-between border-b border-slate-50 pb-2">
                <span class="text-slate-400 italic font-normal">Tanggal Pertemuan:</span>
                <span class="text-slate-800"><?php echo date('d F Y', strtotime($d['tanggal'])); ?></span>
            </div>
            
            <div class="mt-10 bg-yellow-50 p-6 rounded-2xl border-2 border-dashed border-yellow-400">
                <div class="flex justify-between items-center">
                    <span class="text-red-700 italic font-black text-sm">JUMLAH TERBILANG:</span>
                    <span class="text-2xl font-black text-red-700">Rp <?php echo number_format($d['nominal_bayar'], 0, ',', '.'); ?></span>
                </div>
            </div>
        </div>

        <div class="mt-12 flex justify-between items-end relative z-10">
            <div class="text-[8px] text-slate-400 leading-relaxed italic">
                * Kuitansi ini adalah bukti pembayaran sah.<br>
                * Dicetak otomatis oleh Smart Arca System pada <?php echo date('d/m/Y H:i'); ?>.
            </div>
            <div class="text-center">
                <p class="text-[9px] uppercase font-bold mb-10 text-slate-400">Kasir / Administrasi</p>
                <div class="border-b-2 border-slate-800 w-32 mx-auto"></div>
                <p class="text-[10px] mt-2 font-black uppercase italic text-red-700">Smart Arca Music</p>
            </div>
        </div>

        <div class="mt-12 flex gap-4 no-print justify-center">
            <button onclick="window.print()" class="bg-red-700 text-white px-8 py-3 rounded-xl font-black uppercase text-[10px] shadow-xl hover:bg-red-800 transition">
                <i class="fas fa-print mr-2"></i> Cetak Sekarang
            </button>
            <a href="murid/index.php" class="bg-slate-200 text-slate-600 px-8 py-3 rounded-xl font-black uppercase text-[10px]">
                Kembali
            </a>
        </div>
    </div>

</body>
</html>
