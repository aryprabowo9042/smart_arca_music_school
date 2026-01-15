<?php
// 1. PRIORITAS UTAMA: LOGIKA LOGOUT
// Ditaruh paling atas agar proses keluar tidak terhalang pengecekan lain
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    setcookie('user_role', '', time() - 3600, '/');
    setcookie('user_id', '', time() - 3600, '/');
    setcookie('user_username', '', time() - 3600, '/');
    
    // Arahkan kembali ke Landing Page (Halaman Utama)
    header("Location: ../../index.php");
    exit();
}

// 2. CEK LOGIN GURU
// Jika bukan guru, lempar ke halaman login admin
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'guru') {
    header("Location: ../admin/login.php");
    exit();
}

require_once(__DIR__ . '/../koneksi.php');

$id_guru = $_COOKIE['user_id'];
$nama_guru = $_COOKIE['user_username'] ?? 'Guru';

// --- 3. LOGIKA PROSES TARIK HONOR ---
if (isset($_POST['minta_pencairan'])) {
    $nominal_tarik = (int)$_POST['nominal_tarik'];
    $tgl_skrg = date('Y-m-d');
    
    // Simpan ke tabel keuangan dengan status_konfirmasi = 0 (Pending/Menunggu Admin)
    $sql_minta = "INSERT INTO keuangan (tanggal, nama_pelaku, id_user, keterangan, jenis, nominal, status_konfirmasi) 
                  VALUES ('$tgl_skrg', '$nama_guru', '$id_guru', 'Permintaan Pencairan Honor', 'keluar', '$nominal_tarik', 0)";
    
    if (mysqli_query($conn, $sql_minta)) {
        echo "<script>alert('Permintaan pencairan honor telah dikirim!'); window.location='index.php';</script>";
        exit();
    }
}

// --- 4. HITUNG SALDO HONOR GURU ---
// a. Total Hak (50% dari total nominal_bayar di tabel absensi yang diajar guru ini)
$q_hak = mysqli_query($conn, "
    SELECT SUM(a.nominal_bayar) * 0.5 as total_hak
    FROM absensi a
    JOIN jadwal j ON a.id_jadwal = j.id
    WHERE j.id_guru = '$id_guru'
");
$res_hak = mysqli_fetch_assoc($q_hak);
$total_hak = (float)($res_hak['total_hak'] ?? 0);

// b. Total Honor yang SUDAH Diterima (Sudah dikonfirmasi Admin / status = 1)
$q_terima = mysqli_query($conn, "
    SELECT SUM(nominal) as total_terima 
    FROM keuangan 
    WHERE id_user = '$id_guru' AND jenis = 'keluar' AND status_konfirmasi = 1
");
$res_terima = mysqli_fetch_assoc($q_terima);
$total_terima = (float)($res_terima['total_terima'] ?? 0);

// c. Sisa Saldo yang bisa ditarik
$saldo_honor = $total_hak - $total_terima;

// --- 5. DATA JADWAL HARI INI ---
$hari_inggris = date('l');
$map_hari = [
    'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 
    'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
];
$hari_ini = $map_hari[$hari_inggris];

$q_jadwal = mysqli_query($conn, "
    SELECT j.*, m.username as nama_murid 
    FROM jadwal j 
    JOIN users m ON j.id_murid = m.id 
    WHERE j.id_guru = '$id_guru' AND j.hari = '$hari_ini'
    ORDER BY j.jam ASC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen pb-20">

    <div class="bg-indigo-700 p-6 rounded-b-[40px] shadow-xl text-white mb-8">
        <div class="flex justify-between items-start mb-6">
            <div>
                <p class="text-indigo-200 text-xs font-bold uppercase tracking-widest">Portal Guru</p>
                <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($nama_guru); ?> 👋</h1>
            </div>
            <a href="index.php?action=logout" class="bg-white/20 hover:bg-white/30 p-2 rounded-xl transition">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>

        <div class="bg-white/10 backdrop-blur-lg border border-white/20 p-5 rounded-2xl shadow-inner">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <p class="text-xs text-indigo-100">Estimasi Saldo Honor</p>
                    <h2 class="text-3xl font-black italic">Rp <?php echo number_format($saldo_honor, 0, ',', '.'); ?></h2>
                </div>
                <div class="bg-white text-indigo-700 w-12 h-12 rounded-full flex items-center justify-center shadow-lg">
                    <i class="fas fa-wallet text-xl"></i>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-3">
                <form method="POST">
                    <input type="hidden" name="nominal_tarik" value="<?php echo $saldo_honor; ?>">
                    <button type="submit" name="minta_pencairan" <?php echo ($saldo_honor <= 0) ? 'disabled' : ''; ?> class="w-full h-full bg-indigo-500 hover:bg-indigo-400 disabled:bg-gray-400 text-white font-bold py-3 rounded-xl text-[10px] border border-white/20 transition">
                        <i class="fas fa-paper-plane mr-1"></i> TARIK HONOR
                    </button>
                </form>
                
                <a href="bayar.php" class="bg-green-500 hover:bg-green-400 text-white font-bold py-3 rounded-xl text-[10px] border border-white/20 flex items-center justify-center transition">
                    <i class="fas fa-money-bill-wave mr-1"></i> TERIMA SPP
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-md mx-auto px-4 space-y-8">

        <?php 
        $q_pending = mysqli_query($conn, "SELECT * FROM keuangan WHERE id_user = '$id_guru' AND status_konfirmasi = 0");
        if(mysqli_num_rows($q_pending) > 0): 
        ?>
        <div class="bg-orange-50 border-2 border-orange-200 p-4 rounded-2xl animate-pulse">
            <h3 class="text-orange-700 font-bold text-xs mb-3 flex items-center gap-2">
                <i class="fas fa-clock"></i> MENUNGGU KONFIRMASI ADMIN
            </h3>
            <?php while($p = mysqli_fetch_assoc($q_pending)): ?>
            <div class="bg-white p-3 rounded-xl flex justify-between items-center shadow-sm mb-2 last:mb-0">
                <span class="text-[10px] text-gray-400 uppercase font-bold"><?php echo date('d M Y', strtotime($p['tanggal'])); ?></span>
                <span class="font-bold text-gray-700">Rp <?php echo number_format($p['nominal'], 0, ',', '.'); ?></span>
            </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>

        <div>
            <div class="flex justify-between items-center mb-4 border-l-4 border-indigo-600 pl-3">
                <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wider">Jadwal Mengajar (<?php echo $hari_ini; ?>)</h3>
                <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-full"><?php echo date('d M'); ?></span>
            </div>

            <?php if(mysqli_num_rows($q_jadwal) > 0): ?>
                <div class="space-y-4">
                    <?php while($j = mysqli_fetch_assoc($q_jadwal)): ?>
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center group hover:border-indigo-300 transition-all">
                        <div>
                            <h4 class="font-bold text-gray-800 group-hover:text-indigo-600 transition"><?php echo htmlspecialchars($j['nama_murid']); ?></h4>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-[10px] font-bold text-gray-400"><i class="far fa-clock mr-1"></i><?php echo $j['jam']; ?></span>
                                <span class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest"><?php echo htmlspecialchars($j['alat_musik']); ?></span>
                            </div>
                        </div>
                        <a href="absen.php?id_jadwal=<?php echo $j['id']; ?>" class="bg-indigo-600 text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-lg hover:bg-indigo-700 transition">
                            <i class="fas fa-pencil-alt text-sm"></i>
                        </a>
                    </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="bg-white p-10 rounded-2xl text-center shadow-sm border-2 border-dashed border-gray-200">
                    <i class="fas fa-calendar-check text-4xl text-gray-100 mb-3"></i>
                    <p class="text-gray-400 text-xs">Tidak ada jadwal hari ini.</p>
                </div>
            <?php endif; ?>
        </div>

        <div>
            <h3 class="font-bold text-gray-700 text-xs mb-3 border-l-4 border-green-500 pl-3 uppercase tracking-wider">Pencairan Honor Terakhir</h3>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <?php 
                $q_history = mysqli_query($conn, "SELECT * FROM keuangan WHERE id_user = '$id_guru' AND status_konfirmasi = 1 AND jenis = 'keluar' ORDER BY tanggal DESC LIMIT 5");
                if(mysqli_num_rows($q_history) > 0): 
                ?>
                    <table class="w-full text-left text-xs">
                        <tbody class="divide-y divide-gray-50">
                            <?php while($h = mysqli_fetch_assoc($q_history)): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="p-4 text-gray-400"><?php echo date('d/m', strtotime($h['tanggal'])); ?></td>
                                <td class="p-4 font-bold text-gray-700">Honor Cair</td>
                                <td class="p-4 text-right font-black text-green-600">Rp <?php echo number_format($h['nominal'], 0, ',', '.'); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="p-6 text-center text-[10px] text-gray-300 italic">Belum ada riwayat pencairan.</p>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <div class="mt-12 text-center pb-8">
        <p class="text-[10px] text-gray-300 font-bold uppercase tracking-[0.3em]">Smart Arca Music School</p>
    </div>

</body>
</html>
