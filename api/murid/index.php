<?php
// 1. CEK LOGIN MURID
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'murid') {
    header("Location: ../admin/login.php");
    exit();
}

require_once(__DIR__ . '/../koneksi.php');

$id_murid = $_COOKIE['user_id'];
$username = $_COOKIE['user_username'] ?? 'Siswa';

// 2. QUERY DATA ABSEN & PEMBAYARAN (TABEL)
$sql = "SELECT a.*, j.alat_musik, g.username as nama_guru 
        FROM absensi a 
        JOIN jadwal j ON a.id_jadwal = j.id 
        JOIN users g ON j.id_guru = g.id 
        WHERE j.id_murid = '$id_murid' 
        ORDER BY a.tanggal DESC";
$query = mysqli_query($conn, $sql);

// Fungsi Map Gambar Alat Musik
function getInstrumentImg($alat) {
    $alat = strtolower($alat);
    if (strpos($alat, 'piano') !== false) return 'https://images.unsplash.com/photo-1520523839897-bd0b52f945a0?q=80&w=400';
    if (strpos($alat, 'gitar') !== false) return 'https://images.unsplash.com/photo-1525201548942-d8b8967d0f52?q=80&w=400';
    if (strpos($alat, 'drum') !== false) return 'https://images.unsplash.com/photo-1543443258-92b04ad5ec6b?q=80&w=400';
    return 'https://images.unsplash.com/photo-1516280440614-37939bbacd81?q=80&w=400'; // Default Vocal
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Murid - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-50 min-h-screen pb-10">

    <nav class="bg-red-600 shadow-lg px-6 py-4 flex justify-between items-center mb-6 sticky top-0 z-50 border-b-4 border-yellow-400">
        <div class="flex items-center gap-3">
            <img src="../logo.png" onerror="this.src='https://ui-avatars.com/api/?name=SA&background=ffffff&color=dc2626&bold=true'" class="h-10 w-10 rounded-lg">
            <h1 class="text-white font-black text-lg italic tracking-tighter uppercase">Smart Arca</h1>
        </div>
        <a href="index.php?action=logout" class="bg-yellow-400 text-red-700 p-2 rounded-xl text-xs font-black shadow-md"><i class="fas fa-sign-out-alt"></i></a>
    </nav>

    <div class="max-w-4xl mx-auto px-4">
        
        <div class="bg-gradient-to-br from-red-600 to-red-800 rounded-[2.5rem] p-8 text-white shadow-2xl mb-8 relative overflow-hidden border-b-8 border-yellow-500">
            <div class="relative z-10">
                <p class="text-red-100 text-xs font-bold uppercase tracking-[0.3em] mb-2">Student Dashboard</p>
                <h2 class="text-3xl font-black mb-1">Halo, <?php echo htmlspecialchars($username); ?>!</h2>
                <p class="text-red-200 text-sm italic">"Teruslah berlatih, harmoni indah menantimu."</p>
            </div>
            <i class="fas fa-music absolute -right-10 -bottom-10 text-9xl text-white/10 rotate-12"></i>
        </div>

        <div class="bg-white rounded-[2rem] shadow-xl overflow-hidden border border-slate-100">
            <div class="p-6 border-b flex justify-between items-center bg-slate-50/50">
                <h3 class="font-black text-slate-800 uppercase text-sm tracking-widest italic">Riwayat Pertemuan & Pembayaran</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-400 text-[10px] uppercase font-black border-b">
                        <tr>
                            <th class="p-5">Kelas & Guru</th>
                            <th class="p-5">Materi</th>
                            <th class="p-5">Pembayaran</th>
                            <th class="p-5 text-center">Kuitansi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php while($row = mysqli_fetch_assoc($query)): ?>
                        <tr class="hover:bg-red-50/30 transition">
                            <td class="p-5">
                                <div class="flex items-center gap-4">
                                    <img src="<?php echo getInstrumentImg($row['alat_musik']); ?>" class="w-12 h-12 rounded-xl object-cover shadow-md border-2 border-white">
                                    <div>
                                        <p class="font-black text-slate-800 uppercase text-xs"><?php echo $row['alat_musik']; ?></p>
                                        <p class="text-[10px] text-red-500 font-bold"><?php echo $row['nama_guru']; ?></p>
                                        <p class="text-[9px] text-slate-400"><?php echo date('d M Y', strtotime($row['tanggal'])); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-5">
                                <p class="text-xs text-slate-600 leading-relaxed italic">"<?php echo htmlspecialchars($row['materi_ajar']); ?>"</p>
                            </td>
                            <td class="p-5">
                                <p class="font-black text-green-600 text-sm">Rp <?php echo number_format($row['nominal_bayar'], 0, ',', '.'); ?></p>
                                <span class="text-[8px] bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-black uppercase">Lunas</span>
                            </td>
                            <td class="p-5 text-center">
                                <a href="../cetak_kuitansi.php?id=<?php echo $row['id']; ?>" target="_blank" class="inline-flex items-center justify-center w-10 h-10 bg-yellow-400 text-red-700 rounded-xl hover:bg-red-600 hover:text-white transition shadow-md">
                                    <i class="fas fa-print"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
