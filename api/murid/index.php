<?php
// 1. PROTEKSI HALAMAN
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'murid') {
    header("Location: ../"); 
    exit();
}

// 2. KONEKSI (DIUBAH KE ../ AGAR SESUAI FOLDER API)
require_once(__DIR__ . '/../koneksi.php');

$id_murid = $_COOKIE['user_id'];
$username = $_COOKIE['user_username'] ?? 'Siswa';

// 3. AMBIL DATA JADWAL
$sql_jadwal = "SELECT j.*, u.username as nama_guru 
               FROM `jadwal` j 
               JOIN `users` u ON j.`id_guru` = u.`id` 
               WHERE j.`id_murid` = '$id_murid' LIMIT 1";
$res_jadwal = mysqli_query($conn, $sql_jadwal);
$data_j = mysqli_fetch_assoc($res_jadwal);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Murid - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen pb-20">

    <nav class="bg-indigo-900 shadow-xl px-6 py-4 flex justify-between items-center mb-6 border-b-4 border-yellow-400 sticky top-0 z-50 text-white font-black italic uppercase">
        <div class="flex items-center gap-3">
            <i class="fas fa-music text-yellow-400"></i>
            <h1 class="tracking-tighter uppercase">Student Room</h1>
        </div>
        
        <a href="../" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-xl text-[10px] font-black transition shadow-lg flex items-center gap-2">
            Keluar <i class="fas fa-sign-out-alt"></i>
        </a>
    </nav>

    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl mb-10 border-l-[12px] border-indigo-600 flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 leading-none">Selamat Belajar,</p>
                <h2 class="text-4xl font-black text-slate-800 italic leading-none mb-3 uppercase tracking-tighter"><?php echo $username; ?></h2>
                <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-[10px] font-black uppercase"><?php echo $data_j['alat_musik'] ?? 'General Music'; ?></span>
            </div>
            <div class="bg-slate-50 p-5 rounded-3xl border-2 border-dashed border-slate-200 text-right">
                <p class="text-[9px] font-black text-slate-400 uppercase leading-none mb-1">Guru Pengajar</p>
                <p class="text-xl font-black text-indigo-900 italic uppercase"><?php echo $data_j['nama_guru'] ?? 'Guru Smart Arca'; ?></p>
            </div>
        </div>

        <h2 class="text-xl font-black text-indigo-900 uppercase italic mb-6"><i class="fas fa-history mr-2 text-yellow-500"></i> Riwayat Pertemuan</h2>

        <div class="space-y-6">
            <?php 
            $id_j_murid = $data_j['id'] ?? 0;
            $sql_riwayat = "SELECT * FROM `absensi` WHERE `id_jadwal` = '$id_j_murid' ORDER BY `tanggal` DESC";
            $res_riwayat = mysqli_query($conn, $sql_riwayat);

            if(mysqli_num_rows($res_riwayat) == 0):
            ?>
                <div class="text-center p-10 bg-white rounded-[2rem] text-slate-300 font-bold uppercase italic">Belum ada riwayat pertemuan.</div>
            <?php 
            endif;
            while($h = mysqli_fetch_assoc($res_riwayat)): 
            ?>
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-100 p-8">
                <div class="flex flex-col md:flex-row justify-between mb-4 border-b pb-4 border-slate-50">
                    <div class="flex items-center gap-4">
                        <div class="bg-indigo-900 text-white w-14 h-14 rounded-2xl flex flex-col items-center justify-center leading-none">
                            <span class="text-[10px] font-black"><?php echo date('M', strtotime($h['tanggal'])); ?></span>
                            <span class="text-xl font-black"><?php echo date('d', strtotime($h['tanggal'])); ?></span>
                        </div>
                        <h3 class="text-lg font-black text-slate-800 uppercase italic"><?php echo date('l, d F Y', strtotime($h['tanggal'])); ?></h3>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-slate-50 p-4 rounded-2xl">
                        <p class="text-[9px] font-black text-indigo-400 uppercase mb-1">Materi</p>
                        <p class="text-sm font-bold text-slate-700 italic uppercase"><?php echo htmlspecialchars($h['materi_ajar'] ?? '-'); ?></p>
                    </div>
                    <div class="bg-indigo-50 p-4 rounded-2xl border border-indigo-100">
                        <p class="text-[9px] font-black text-indigo-400 uppercase mb-1">Pesan Guru</p>
                        <p class="text-sm italic text-indigo-900">"<?php echo htmlspecialchars($h['perkembangan_murid'] ?? '-'); ?>"</p>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
