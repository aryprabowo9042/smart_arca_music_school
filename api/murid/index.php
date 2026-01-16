<?php
// 1. PROTEKSI HALAMAN
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'murid') {
    // Jika bukan murid, kembali ke halaman login utama
    header("Location: ../index.php");
    exit();
}

require_once(__DIR__ . '/../../koneksi.php');

$id_murid = $_COOKIE['user_id'];
$username = $_COOKIE['user_username'] ?? 'Siswa';

// 2. AMBIL DATA JADWAL & GURU MURID INI
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Murid - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen pb-20">

    <nav class="bg-indigo-900 shadow-xl px-6 py-4 flex justify-between items-center mb-6 border-b-4 border-yellow-400 sticky top-0 z-50 text-white">
        <div class="flex items-center gap-3">
            <div class="bg-yellow-400 p-2 rounded-lg text-indigo-900 shadow-md">
                <i class="fas fa-music"></i>
            </div>
            <h1 class="font-black text-lg italic uppercase tracking-tighter">Student Room</h1>
        </div>
        
        <a href="../index.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase transition shadow-lg flex items-center gap-2 active:scale-95">
            Keluar <i class="fas fa-sign-out-alt"></i>
        </a>
    </nav>

    <div class="max-w-4xl mx-auto px-4">
        
        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl mb-10 border-l-[12px] border-indigo-600 flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 leading-none">Selamat Belajar,</p>
                <h2 class="text-4xl font-black text-slate-800 italic leading-none mb-3 tracking-tighter"><?php echo $username; ?></h2>
                <div class="flex gap-2">
                    <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-[10px] font-black uppercase"><?php echo $data_j['alat_musik'] ?? 'General Music'; ?></span>
                    <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-[10px] font-black uppercase"><?php echo $data_j['hari'] ?? '-'; ?></span>
                </div>
            </div>
            <div class="text-center md:text-right bg-slate-50 p-5 rounded-3xl border-2 border-dashed border-slate-200">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 leading-none">Guru Pengajar</p>
                <p class="text-xl font-black text-indigo-900 italic uppercase leading-none mt-1"><?php echo $data_j['nama_guru'] ?? 'Belum Ditentukan'; ?></p>
            </div>
        </div>

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-black text-indigo-900 uppercase italic leading-none">
                <i class="fas fa-book-open mr-2 text-yellow-500"></i> Catatan Pertemuan
            </h2>
        </div>

        <div class="space-y-6">
            <?php 
            // Mengambil riwayat dari tabel absensi berdasarkan id_jadwal murid
            $id_j_murid = $data_j['id'] ?? 0;
            $sql_riwayat = "SELECT * FROM `absensi` 
                            WHERE `id_jadwal` = '$id_j_murid' 
                            ORDER BY `tanggal` DESC, `id` DESC";
            $res_riwayat = mysqli_query($conn, $sql_riwayat);

            if(mysqli_num_rows($res_riwayat) == 0):
            ?>
            <div class="bg-white p-12 rounded-[2.5rem] shadow-lg text-center border-2 border-dashed border-slate-100">
                <i class="fas fa-calendar-day text-slate-100 text-6xl mb-4"></i>
                <p class="text-slate-400 font-bold italic uppercase text-xs">Belum ada catatan belajar yang diinput oleh guru.</p>
            </div>
            <?php 
            endif;
            while($h = mysqli_fetch_assoc($res_riwayat)): 
                // Proteksi jam kosong
                $mulai = !empty($h['jam_mulai']) ? substr($h['jam_mulai'], 0, 5) : "--:--";
                $selesai = !empty($h['jam_selesai']) ? substr($h['jam_selesai'], 0, 5) : "--:--";
            ?>
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-100 p-8 hover:border-indigo-200 transition group relative overflow-hidden">
                <div class="flex flex-col md:flex-row justify-between mb-6 gap-4 border-b border-slate-50 pb-4 relative z-10">
                    <div class="flex items-center gap-4">
                        <div class="bg-indigo-900 text-white w-14 h-14 rounded-2xl flex flex-col items-center justify-center leading-none shadow-lg">
                            <span class="text-[10px] font-black uppercase"><?php echo date('M', strtotime($h['tanggal'])); ?></span>
                            <span class="text-xl font-black"><?php echo date('d', strtotime($h['tanggal'])); ?></span>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Pertemuan</p>
                            <p class="text-lg font-black text-slate-800 italic uppercase"><?php echo date('l, d F Y', strtotime($h['tanggal'])); ?></p>
                        </div>
                    </div>
                    <div class="md:text-right">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Waktu Belajar</p>
                        <p class="text-sm font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full inline-block border border-indigo-100">
                            <i class="far fa-clock mr-1"></i> <?php echo $mulai; ?> - <?php echo $selesai; ?> WIB
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
                    <div class="bg-slate-50 p-6 rounded-3xl relative">
                        <i class="fas fa-music text-slate-200 text-3xl absolute top-4 right-4 opacity-50"></i>
                        <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-2">Materi Yang Dipelajari</p>
                        <p class="text-sm font-bold text-slate-700 italic leading-relaxed uppercase tracking-tighter">
                            <?php echo htmlspecialchars($h['materi_ajar'] ?? '-'); ?>
                        </p>
                    </div>
                    <div class="bg-indigo-50 p-6 rounded-3xl border border-indigo-100">
                        <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-2">Catatan Dari Guru</p>
                        <p class="text-sm italic text-indigo-900 leading-relaxed font-semibold">
                            "<?php echo htmlspecialchars($h['perkembangan_murid'] ?? 'Terus semangat berlatih!'); ?>"
                        </p>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

</body>
</html>
