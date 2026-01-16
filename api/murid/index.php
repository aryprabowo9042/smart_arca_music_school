<?php
// 1. PROTEKSI HALAMAN
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'murid') {
    // Jika bukan murid, tendang ke halaman login utama
    header("Location: ../api/index.php");
    exit();
}

require_once(__DIR__ . '/../koneksi.php');

$id_murid = $_COOKIE['user_id'];
$username = $_COOKIE['user_username'] ?? 'Siswa';

// 2. AMBIL DATA JADWAL MURID
$sql_jadwal = "SELECT j.*, u.username as nama_guru 
               FROM `jadwal` j 
               JOIN `users` u ON j.`id_guru` = u.`id` 
               WHERE j.`id_murid` = '$id_murid'";
$res_jadwal = mysqli_query($conn, $sql_jadwal);
$data_j = mysqli_fetch_assoc($res_jadwal);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen pb-20">

    <nav class="bg-indigo-900 shadow-xl px-6 py-4 flex justify-between items-center mb-6 border-b-4 border-yellow-400 sticky top-0 z-50 text-white">
        <div class="flex items-center gap-3">
            <div class="bg-yellow-400 p-2 rounded-lg text-indigo-900">
                <i class="fas fa-music"></i>
            </div>
            <h1 class="font-black text-lg italic uppercase tracking-tighter">Student Room</h1>
        </div>
        <a href="../api/index.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase transition shadow-lg flex items-center gap-2">
            Keluar <i class="fas fa-sign-out-alt"></i>
        </a>
    </nav>

    <div class="max-w-4xl mx-auto px-4">
        
        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl mb-10 border-l-[12px] border-indigo-600 flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 leading-none">Selamat Belajar,</p>
                <h2 class="text-4xl font-black text-slate-800 italic leading-none mb-3"><?php echo $username; ?></h2>
                <div class="flex gap-2">
                    <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-[10px] font-black uppercase"><?php echo $data_j['alat_musik'] ?? 'Musik'; ?></span>
                    <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-[10px] font-black uppercase"><?php echo $data_j['hari'] ?? '-'; ?></span>
                </div>
            </div>
            <div class="text-center md:text-right bg-slate-50 p-4 rounded-3xl border-2 border-dashed border-slate-200">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Guru Pengajar</p>
                <p class="text-xl font-black text-indigo-900 italic uppercase"><?php echo $data_j['nama_guru'] ?? 'Belum ada jadwal'; ?></p>
            </div>
        </div>

        <h2 class="text-xl font-black text-indigo-900 uppercase italic mb-6 leading-none">
            <i class="fas fa-book-open mr-2 text-yellow-500"></i> Catatan Perkembangan Belajar
        </h2>

        <div class="space-y-6">
            <?php 
            // Ambil data absensi/jurnal untuk murid ini melalui id_jadwal
            $id_j_murid = $data_j['id'] ?? 0;
            $sql_riwayat = "SELECT * FROM `absensi` 
                            WHERE `id_jadwal` = '$id_j_murid' 
                            ORDER BY `tanggal` DESC, `id` DESC";
            $res_riwayat = mysqli_query($conn, $sql_riwayat);

            if(mysqli_num_rows($res_riwayat) == 0):
            ?>
            <div class="bg-white p-10 rounded-[2.5rem] shadow-lg text-center border-2 border-dashed border-slate-100">
                <i class="fas fa-ghost text-slate-100 text-6xl mb-4"></i>
                <p class="text-slate-400 font-bold italic uppercase text-xs">Belum ada riwayat pertemuan yang tercatat.</p>
            </div>
            <?php 
            endif;
            while($h = mysqli_fetch_assoc($res_riwayat)): 
            ?>
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-100 p-8 hover:border-indigo-200 transition group">
                <div class="flex flex-col md:flex-row justify-between mb-6 gap-4 border-b border-slate-50 pb-4">
                    <div class="flex items-center gap-4">
                        <div class="bg-indigo-900 text-white w-14 h-14 rounded-2xl flex flex-col items-center justify-center leading-none">
                            <span class="text-[10px] font-black uppercase"><?php echo date('M', strtotime($h['tanggal'])); ?></span>
                            <span class="text-xl font-black"><?php echo date('d', strtotime($h['tanggal'])); ?></span>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Pertemuan Tanggal</p>
                            <p class="text-lg font-black text-slate-800 italic uppercase"><?php echo date('l, d F Y', strtotime($h['tanggal'])); ?></p>
                        </div>
                    </div>
                    <div class="md:text-right">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Durasi Les</p>
                        <p class="text-sm font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full inline-block">
                            <i class="far fa-clock mr-1"></i> <?php echo substr($h['jam_mulai'],0,5); ?> - <?php echo substr($h['jam_selesai'],0,5); ?> WIB
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-slate-50 p-6 rounded-3xl relative">
                        <i class="fas fa-quote-left text-slate-200 text-3xl absolute top-4 left-4"></i>
                        <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-2 relative z-10">Materi Hari Ini</p>
                        <p class="text-sm font-bold text-slate-700 italic relative z-10 leading-relaxed uppercase">
                            <?php echo htmlspecialchars($h['materi_ajar'] ?? '-'); ?>
                        </p>
                    </div>
                    <div class="bg-indigo-50 p-6 rounded-3xl border border-indigo-100">
                        <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-2">Pesan & Perkembangan</p>
                        <p class="text-sm italic text-indigo-900 leading-relaxed font-semibold">
                            "<?php echo htmlspecialchars($h['perkembangan_murid'] ?? '-'); ?>"
                        </p>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

</body>
</html>
