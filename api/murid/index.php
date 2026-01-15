<?php
// 1. CEK LOGIN MURID
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'murid') {
    header("Location: ../admin/login.php");
    exit();
}

require_once(__DIR__ . '/../koneksi.php');

$id_murid = $_COOKIE['user_id'];
$username = $_COOKIE['user_username'] ?? 'Siswa';

// 2. QUERY DATA ABSENSI & PEMBAYARAN
$sql = "SELECT a.*, j.alat_musik, g.username as nama_guru 
        FROM absensi a 
        JOIN jadwal j ON a.id_jadwal = j.id 
        JOIN users g ON j.id_guru = g.id 
        WHERE j.id_murid = '$id_murid' 
        ORDER BY a.tanggal DESC";
$query = mysqli_query($conn, $sql);

// 3. FUNGSI GAMBAR ALAT MUSIK OTOMATIS
function getInstrumentImage($alat) {
    $alat = strtolower($alat);
    if (strpos($alat, 'piano') !== false) return 'https://images.unsplash.com/photo-1520523839897-bd0b52f945a0?q=80&w=300';
    if (strpos($alat, 'gitar') !== false || strpos($alat, 'guitar') !== false) return 'https://images.unsplash.com/photo-1525201548942-d8b8967d0f52?q=80&w=300';
    if (strpos($alat, 'drum') !== false) return 'https://images.unsplash.com/photo-1543443258-92b04ad5ec6b?q=80&w=300';
    if (strpos($alat, 'vokal') !== false || strpos($alat, 'vocal') !== false) return 'https://images.unsplash.com/photo-1516280440614-37939bbacd81?q=80&w=300';
    if (strpos($alat, 'biola') !== false || strpos($alat, 'violin') !== false) return 'https://images.unsplash.com/photo-1465821508027-58156a2858b5?q=80&w=300';
    return 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?q=80&w=300'; // Default music image
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-pattern { background-color: #f8fafc; background-image: radial-gradient(#cbd5e1 0.5px, transparent 0.5px); background-size: 24px 24px; }
    </style>
</head>
<body class="bg-pattern min-h-screen pb-20">

    <nav class="bg-red-600 shadow-xl px-6 py-4 flex justify-between items-center mb-8 sticky top-0 z-50 border-b-4 border-yellow-400">
        <div class="flex items-center gap-3">
            <img src="../logo.png" onerror="this.src='https://ui-avatars.com/api/?name=SA&background=ffffff&color=dc2626&bold=true'" class="h-10 w-10 rounded-xl shadow-md">
            <div>
                <h1 class="text-white font-black text-lg italic tracking-tighter leading-none uppercase">Smart Arca</h1>
                <p class="text-[8px] text-yellow-300 font-bold uppercase tracking-[0.2em]">Student Portal</p>
            </div>
        </div>
        <a href="../logout.php" class="bg-yellow-400 hover:bg-yellow-500 text-red-700 px-4 py-2 rounded-xl text-xs font-black shadow-lg flex items-center gap-2 transition active:scale-95">
            <i class="fas fa-sign-out-alt"></i> KELUAR
        </a>
    </nav>

    <div class="max-w-5xl mx-auto px-4">
        
        <div class="bg-white rounded-[2.5rem] p-8 shadow-2xl border-t-8 border-red-600 mb-10 flex flex-col md:flex-row justify-between items-center gap-6 relative overflow-hidden">
            <div class="absolute -right-10 -top-10 text-slate-50 text-9xl rotate-12 opacity-50">
                <i class="fas fa-music"></i>
            </div>
            <div class="relative z-10 text-center md:text-left">
                <p class="text-red-600 font-bold text-xs uppercase tracking-widest mb-1">Selamat Datang,</p>
                <h2 class="text-4xl font-black text-slate-800 uppercase italic tracking-tighter"><?php echo htmlspecialchars($username); ?></h2>
                <div class="mt-4 flex flex-wrap gap-2 justify-center md:justify-start">
                    <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest italic border border-slate-200">#MusisiMasaDepan</span>
                </div>
            </div>
            <div class="bg-yellow-400 text-red-700 px-8 py-6 rounded-[2rem] border-4 border-white shadow-xl text-center relative z-10">
                <p class="text-[10px] font-black uppercase tracking-widest mb-1">Total Pertemuan</p>
                <p class="text-4xl font-black italic"><?php echo mysqli_num_rows($query); ?></p>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-100">
            <div class="p-6 bg-slate-50 border-b flex justify-between items-center">
                <h3 class="font-black text-slate-800 uppercase text-xs italic tracking-widest flex items-center gap-2">
                    <i class="fas fa-calendar-check text-red-600"></i> Lembar Absensi & Riwayat Kursus
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-white text-slate-400 text-[10px] uppercase font-black border-b tracking-[0.1em]">
                        <tr>
                            <th class="p-6">Instrumen</th>
                            <th class="p-6">Detail Sesi</th>
                            <th class="p-6">Materi Belajar</th>
                            <th class="p-6 text-right">Pembayaran</th>
                            <th class="p-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php if(mysqli_num_rows($query) == 0): ?>
                        <tr>
                            <td colspan="5" class="p-20 text-center">
                                <i class="fas fa-clipboard-list text-5xl text-slate-200 mb-4 block"></i>
                                <p class="text-slate-400 font-bold italic">Belum ada riwayat absensi.</p>
                            </td>
                        </tr>
                        <?php endif; ?>

                        <?php while($row = mysqli_fetch_assoc($query)): ?>
                        <tr class="hover:bg-red-50/50 transition-all group">
                            <td class="p-6">
                                <div class="flex items-center gap-4">
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-yellow-400 rounded-2xl rotate-6 scale-95 opacity-0 group-hover:opacity-100 transition-all"></div>
                                        <img src="<?php echo getInstrumentImage($row['alat_musik']); ?>" 
                                             class="w-16 h-16 rounded-2xl object-cover shadow-lg border-2 border-white relative z-10 transition transform group-hover:-translate-y-1">
                                    </div>
                                    <div>
                                        <p class="font-black text-slate-800 uppercase text-xs italic leading-none mb-1"><?php echo $row['alat_musik']; ?></p>
                                        <span class="text-[9px] bg-red-100 text-red-600 px-2 py-0.5 rounded-md font-bold uppercase">Selesai</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-6">
                                <p class="font-bold text-slate-700 text-xs"><?php echo date('d F Y', strtotime($row['tanggal'])); ?></p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter italic">Guru: <?php echo $row['nama_guru']; ?></p>
                            </td>
                            <td class="p-6">
                                <div class="max-w-[150px] md:max-w-xs">
                                    <p class="text-xs text-slate-500 leading-relaxed font-medium line-clamp-2 italic">"<?php echo htmlspecialchars($row['materi_ajar']); ?>"</p>
                                </div>
                            </td>
                            <td class="p-6 text-right">
                                <p class="font-black text-green-600 text-base leading-none mb-1">Rp <?php echo number_format($row['nominal_bayar'], 0, ',', '.'); ?></p>
                                <p class="text-[9px] font-black text-slate-300 uppercase italic">LUNAS</p>
                            </td>
                            <td class="p-6 text-center">
                                <a href="kuitansi.php?id=<?php echo $row['id']; ?>" target="_blank" 
                                   class="inline-flex items-center justify-center w-12 h-12 bg-yellow-400 text-red-700 rounded-2xl hover:bg-red-600 hover:text-white transition shadow-xl border-b-4 border-yellow-600 active:border-0 active:translate-y-1">
                                    <i class="fas fa-print text-xl"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <p class="mt-10 text-center text-[10px] text-slate-400 font-black uppercase tracking-[0.5em]">Smart Arca Music School &bull; Kendal</p>
    </div>

</body>
</html>
