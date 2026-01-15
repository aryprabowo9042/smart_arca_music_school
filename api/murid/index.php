<?php
// api/murid/index.php

if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'murid') {
    header("Location: ../admin/login.php");
    exit();
}

require_once(__DIR__ . '/../koneksi.php');

$id_murid = $_COOKIE['user_id'];
$username = $_COOKIE['user_username'] ?? 'Siswa';

// Ambil riwayat absen/pembayaran
$sql = "SELECT a.*, j.alat_musik, g.username as nama_guru 
        FROM absensi a 
        JOIN jadwal j ON a.id_jadwal = j.id 
        JOIN users g ON j.id_guru = g.id 
        WHERE j.id_murid = '$id_murid' 
        ORDER BY a.tanggal DESC";
$query = mysqli_query($conn, $sql);

// Fungsi Gambar Alat Musik
function getInstrumentPic($alat) {
    $alat = strtolower($alat);
    if (strpos($alat, 'piano') !== false) return 'https://images.unsplash.com/photo-1520523839897-bd0b52f945a0?q=80&w=200';
    if (strpos($alat, 'gitar') !== false) return 'https://images.unsplash.com/photo-1525201548942-d8b8967d0f52?q=80&w=200';
    if (strpos($alat, 'drum') !== false) return 'https://images.unsplash.com/photo-1543443258-92b04ad5ec6b?q=80&w=200';
    if (strpos($alat, 'vokal') !== false) return 'https://images.unsplash.com/photo-1516280440614-37939bbacd81?q=80&w=200';
    return 'https://images.unsplash.com/photo-1514317011159-45ce51582b00?q=80&w=200';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Arca Music School - Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-50 min-h-screen">

    <nav class="bg-red-600 shadow-lg px-6 py-4 flex justify-between items-center sticky top-0 z-50 border-b-4 border-yellow-400">
        <div class="flex items-center gap-3">
            <img src="../logo.png" onerror="this.src='https://ui-avatars.com/api/?name=SA&background=ffffff&color=ef4444&bold=true'" class="h-10 w-10 rounded-lg">
            <h1 class="text-white font-black text-lg italic tracking-tighter uppercase">Smart Arca</h1>
        </div>
        <a href="index.php?action=logout" class="bg-yellow-400 text-red-700 px-4 py-2 rounded-xl text-xs font-black shadow-md"><i class="fas fa-sign-out-alt"></i></a>
    </nav>

    <div class="max-w-5xl mx-auto px-4 py-8">
        
        <div class="bg-white rounded-[2.5rem] p-8 shadow-xl border-t-8 border-red-600 mb-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="text-center md:text-left">
                <p class="text-red-600 font-bold text-xs uppercase tracking-widest mb-1">Selamat Datang,</p>
                <h2 class="text-3xl font-black text-slate-800 uppercase italic"><?php echo htmlspecialchars($username); ?></h2>
            </div>
            <div class="bg-yellow-100 text-yellow-700 px-6 py-4 rounded-3xl border-2 border-yellow-400 text-center">
                <p class="text-[10px] font-bold uppercase">Total Kehadiran</p>
                <p class="text-2xl font-black"><?php echo mysqli_num_rows($query); ?>x Pertemuan</p>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] shadow-2xl overflow-hidden border border-slate-100">
            <div class="p-6 bg-slate-50 border-b flex justify-between items-center">
                <h3 class="font-black text-slate-800 uppercase text-sm italic tracking-widest">Riwayat Absensi & Pembayaran</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-white text-slate-400 text-[10px] uppercase font-black border-b">
                        <tr>
                            <th class="p-5">Alat Musik</th>
                            <th class="p-5">Tanggal & Guru</th>
                            <th class="p-5">Materi</th>
                            <th class="p-5 text-right">Bayar</th>
                            <th class="p-5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php while($row = mysqli_fetch_assoc($query)): ?>
                        <tr class="hover:bg-red-50 transition-all group">
                            <td class="p-5">
                                <div class="flex items-center gap-3">
                                    <img src="<?php echo getInstrumentPic($row['alat_musik']); ?>" class="w-12 h-12 rounded-2xl object-cover shadow-md border-2 border-white">
                                    <span class="font-black text-slate-800 uppercase text-xs"><?php echo $row['alat_musik']; ?></span>
                                </div>
                            </td>
                            <td class="p-5">
                                <p class="font-bold text-slate-700 text-xs"><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></p>
                                <p class="text-[10px] text-red-500 font-bold uppercase tracking-tighter"><?php echo $row['nama_guru']; ?></p>
                            </td>
                            <td class="p-5 text-xs text-slate-500 italic max-w-xs truncate">
                                "<?php echo $row['materi_ajar']; ?>"
                            </td>
                            <td class="p-5 text-right font-black text-green-600">
                                Rp <?php echo number_format($row['nominal_bayar'], 0, ',', '.'); ?>
                            </td>
                            <td class="p-5 text-center">
                                <a href="kuitansi.php?id=<?php echo $row['id']; ?>" class="inline-flex items-center justify-center w-10 h-10 bg-yellow-400 text-red-700 rounded-xl hover:bg-red-600 hover:text-white transition shadow-lg border-b-4 border-yellow-600">
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
