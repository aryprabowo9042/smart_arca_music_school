<?php
// api/murid/kuitansi.php
require_once(__DIR__ . '/../koneksi.php');

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
    <title>Kuitansi_Smart_Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>@media print { .no-print { display: none; } }</style>
</head>
<body class="bg-gray-200 p-5 md:p-20">
    <div class="max-w-xl mx-auto bg-white p-8 rounded-[2rem] shadow-2xl border-t-8 border-red-600 relative overflow-hidden">
        <div class="flex justify-between items-start mb-10">
            <div>
                <h1 class="text-3xl font-black text-red-600 italic leading-none">SMART ARCA</h1>
                <p class="text-[10px] font-bold text-yellow-500 uppercase tracking-widest">Music School Receipt</p>
            </div>
            <div class="text-right">
                <p class="text-[8px] text-gray-400 font-bold uppercase">No. Invoice</p>
                <p class="text-xs font-black">#INV/<?php echo $d['id']; ?>/<?php echo date('my'); ?></p>
            </div>
        </div>

        <div class="border-y-2 border-dashed border-gray-100 py-6 mb-6 space-y-3">
            <div class="flex justify-between"><span class="text-[10px] text-gray-400 font-bold uppercase">Siswa:</span><span class="font-black text-sm uppercase"><?php echo $d['nama_murid']; ?></span></div>
            <div class="flex justify-between"><span class="text-[10px] text-gray-400 font-bold uppercase">Instrumen:</span><span class="font-bold text-sm uppercase text-red-600"><?php echo $d['alat_musik']; ?></span></div>
            <div class="flex justify-between"><span class="text-[10px] text-gray-400 font-bold uppercase">Guru:</span><span class="font-bold text-sm"><?php echo $d['nama_guru']; ?></span></div>
        </div>

        <div class="bg-slate-50 p-4 rounded-2xl mb-10">
            <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Keterangan Materi:</p>
            <p class="text-sm italic">"<?php echo $d['materi_ajar']; ?>"</p>
        </div>

        <div class="bg-red-600 p-6 rounded-2xl flex justify-between items-center text-yellow-400 shadow-xl mb-10">
            <span class="font-black">TOTAL</span>
            <span class="text-3xl font-black italic">Rp <?php echo number_format($d['nominal_bayar'], 0, ',', '.'); ?></span>
        </div>

        <div class="flex justify-between items-end">
            <div class="text-[9px] text-gray-300 italic">Dicetak pada: <?php echo date('d/m/Y H:i'); ?></div>
            <div class="text-center border-t border-gray-100 pt-2 px-6">
                <p class="text-[10px] text-gray-400 mb-10">Admin,</p>
                <p class="font-bold text-xs uppercase">FIA</p>
            </div>
        </div>

        <button onclick="window.print()" class="no-print w-full mt-10 bg-slate-800 text-white font-bold py-4 rounded-2xl">CETAK KUITANSI</button>
    </div>
</body>
</html>
