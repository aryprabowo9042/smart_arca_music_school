<?php
// --- FITUR KELUAR INTERNAL ---
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    setcookie('user_role', '', time() - 3600, '/');
    setcookie('user_id', '', time() - 3600, '/');
    setcookie('user_username', '', time() - 3600, '/');
    header("Location: ../../index.php"); // Kembali ke Landing Page
    exit();
}

// 1. CEK LOGIN MURID
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'murid') {
    header("Location: ../admin/login.php"); exit();
}

require_once(__DIR__ . '/../koneksi.php');
$id_murid = $_COOKIE['user_id'];
$username = $_COOKIE['user_username'] ?? 'Siswa';

// Query Data
$sql = "SELECT a.*, j.alat_musik, g.username as nama_guru FROM absensi a 
        JOIN jadwal j ON a.id_jadwal = j.id 
        JOIN users g ON j.id_guru = g.id 
        WHERE j.id_murid = '$id_murid' ORDER BY a.tanggal DESC";
$query = mysqli_query($conn, $sql);

function getInstrumentPic($alat) {
    $alat = strtolower($alat);
    if (strpos($alat, 'piano') !== false) return 'https://images.unsplash.com/photo-1520523839897-bd0b52f945a0?q=80&w=200';
    if (strpos($alat, 'gitar') !== false) return 'https://images.unsplash.com/photo-1525201548942-d8b8967d0f52?q=80&w=200';
    if (strpos($alat, 'drum') !== false) return 'https://images.unsplash.com/photo-1543443258-92b04ad5ec6b?q=80&w=200';
    return 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?q=80&w=200';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Portal Siswa - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-50 min-h-screen pb-10">

    <nav class="bg-red-600 shadow-lg px-6 py-4 flex justify-between items-center mb-8 border-b-4 border-yellow-400">
        <div class="flex items-center gap-3">
            <h1 class="text-white font-black text-lg italic tracking-tighter uppercase">Smart Arca</h1>
        </div>
        <a href="index.php?action=logout" class="bg-yellow-400 text-red-700 px-4 py-2 rounded-xl text-xs font-black shadow-lg flex items-center gap-2">
            <i class="fas fa-sign-out-alt"></i> KELUAR
        </a>
    </nav>

    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-[2rem] shadow-xl overflow-hidden border border-slate-100">
            <div class="p-6 bg-slate-50 border-b font-black text-slate-800 text-xs italic uppercase">Lembar Absensi Murid</div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-white text-slate-400 text-[10px] uppercase font-black border-b">
                        <tr><th class="p-5">Alat Musik</th><th class="p-5">Tanggal</th><th class="p-5">Materi</th><th class="p-5 text-right">Aksi</th></tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php while($row = mysqli_fetch_assoc($query)): ?>
                        <tr class="hover:bg-red-50 transition">
                            <td class="p-5 flex items-center gap-3">
                                <img src="<?php echo getInstrumentPic($row['alat_musik']); ?>" class="w-10 h-10 rounded-xl object-cover border">
                                <span class="font-black text-xs uppercase"><?php echo $row['alat_musik']; ?></span>
                            </td>
                            <td class="p-5 text-xs font-bold text-slate-500"><?php echo date('d/m/y', strtotime($row['tanggal'])); ?></td>
                            <td class="p-5 text-xs italic text-slate-400">"<?php echo $row['materi_ajar']; ?>"</td>
                            <td class="p-5 text-right">
                                <a href="../cetak_kuitansi.php?id=<?php echo $row['id']; ?>" target="_blank" class="bg-yellow-400 text-red-700 p-2 rounded-lg"><i class="fas fa-print"></i></a>
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
