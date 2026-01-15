<?php
require_once(__DIR__ . '/koneksi.php');

$id = $_GET['id'];
$q = mysqli_query($conn, "SELECT a.*, j.alat_musik, m.username as nama_murid, g.username as nama_guru 
                          FROM absensi a 
                          JOIN jadwal j ON a.id_jadwal = j.id 
                          JOIN users m ON j.id_murid = m.id 
                          JOIN users g ON j.id_guru = g.id 
                          WHERE a.id = '$id'");
$d = mysqli_fetch_assoc($q);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kuitansi_<?php echo $d['nama_murid']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print { .no-print { display: none; } }
    </style>
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-2xl mx-auto bg-white p-8 border-t-8 border-red-600 shadow-xl relative overflow-hidden">
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-3xl font-black text-red-600 italic">SMART ARCA</h1>
                <p class="text-xs font-bold text-yellow-500 uppercase tracking-widest">Music School Receipt</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-400 font-bold uppercase">No. Kuitansi</p>
                <p class="font-black">#INV-<?php echo $d['id']; ?><?php echo date('md'); ?></p>
            </div>
        </div>

        <div class="space-y-4 mb-10 border-l-4 border-yellow-400 pl-6">
            <div><p class="text-[10px] uppercase font-bold text-gray-400">Telah Terima Dari</p><p class="font-bold text-xl uppercase"><?php echo $d['nama_murid']; ?></p></div>
            <div><p class="text-[10px] uppercase font-bold text-gray-400">Untuk Pembayaran</p><p class="font-medium">Kursus <?php echo $d['alat_musik']; ?> (Materi: <?php echo $d['materi_ajar']; ?>)</p></div>
            <div><p class="text-[10px] uppercase font-bold text-gray-400">Guru Pembimbing</p><p class="font-bold"><?php echo $d['nama_guru']; ?></p></div>
        </div>

        <div class="bg-red-600 p-6 rounded-2xl flex justify-between items-center text-yellow-400 mb-8">
            <span class="font-black text-lg">TOTAL BAYAR</span>
            <span class="text-3xl font-black italic">Rp <?php echo number_format($d['nominal_bayar'], 0, ',', '.'); ?></span>
        </div>

        <div class="flex justify-between items-end">
            <p class="text-[10px] text-gray-400 italic italic">Dicetak pada: <?php echo date('d/m/Y H:i'); ?></p>
            <div class="text-center">
                <p class="text-xs mb-16">Admin Smart Arca,</p>
                <p class="font-bold border-t border-gray-200 pt-2 px-4">FIA</p>
            </div>
        </div>

        <div class="mt-8 no-print">
            <button onclick="window.print()" class="w-full bg-slate-800 text-white font-bold py-3 rounded-xl shadow-lg">CETAK SEKARANG</button>
        </div>
    </div>
</body>
</html>
