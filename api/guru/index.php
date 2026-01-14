<?php
// 1. LOGIKA LOGOUT
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    setcookie('user_role', '', time() - 3600, '/');
    setcookie('user_id', '', time() - 3600, '/');
    setcookie('user_username', '', time() - 3600, '/');
    
    // Arahkan ke Landing Page
    header("Location: ../../index.php");
    exit();
}

// 2. CEK LOGIN
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'guru') {
    header("Location: ../admin/login.php");
    exit();
}

require_once(__DIR__ . '/../koneksi.php');

$id_guru = $_COOKIE['user_id'];
$nama_guru = $_COOKIE['user_username'] ?? 'Guru';

// TENTUKAN HARI INI
$hari_inggris = date('l');
$map_hari = [
    'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 
    'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
];
$hari_ini = $map_hari[$hari_inggris];

// AMBIL JADWAL
$q_jadwal = mysqli_query($conn, "
    SELECT j.*, m.username as nama_murid 
    FROM jadwal j 
    JOIN users m ON j.id_murid = m.id 
    WHERE j.id_guru = '$id_guru' AND j.hari = '$hari_ini'
    ORDER BY j.jam ASC
");

// HITUNG HONOR
$q_hak = mysqli_query($conn, "
    SELECT SUM(a.nominal_bayar) * 0.5 as total_hak
    FROM absensi a
    JOIN jadwal j ON a.id_jadwal = j.id
    WHERE j.id_guru = '$id_guru'
");
$total_hak = mysqli_fetch_assoc($q_hak)['total_hak'] ?? 0;

$q_terima = mysqli_query($conn, "
    SELECT SUM(nominal) as total_terima 
    FROM keuangan 
    WHERE nama_pelaku = '$nama_guru' AND jenis = 'keluar'
");
$total_terima = mysqli_fetch_assoc($q_terima)['total_terima'] ?? 0;

$saldo_belum_cair = $total_hak - $total_terima;

// RIWAYAT TERIMA HONOR
$q_riwayat_honor = mysqli_query($conn, "
    SELECT * FROM keuangan 
    WHERE nama_pelaku = '$nama_guru' AND jenis = 'keluar' 
    ORDER BY tanggal DESC LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen pb-20">

    <div class="bg-indigo-600 p-6 rounded-b-[30px] shadow-lg text-white mb-6">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-indigo-200 text-xs">Selamat Mengajar,</p>
                <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($nama_guru); ?></h1>
            </div>
            <a href="index.php?action=logout" class="bg-white/20 p-2 rounded-lg text-sm hover:bg-white/30 transition">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>

        <div class="mt-6 bg-white/10 backdrop-blur-md border border-white/20 p-4 rounded-xl flex justify-between items-center">
            <div>
                <p class="text-xs text-indigo-100">Honor Belum Dicairkan</p>
                <h2 class="text-2xl font-bold">Rp <?php echo number_format($saldo_belum_cair); ?></h2>
            </div>
            <div class="bg-white text-indigo-600 w-10 h-10 rounded-full flex items-center justify-center font-bold shadow">
                <i class="fas fa-wallet"></i>
            </div>
        </div>
    </div>

    <div class="max-w-md mx-auto px-4 space-y-6">
        
        <div>
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-bold text-gray-700 border-l-4 border-indigo-600 pl-3">Jadwal <?php echo $hari_ini; ?></h3>
                <span class="text-xs bg-gray-200 px-2 py-1 rounded text-gray-600"><?php echo date('d M Y'); ?></span>
            </div>

            <?php if(mysqli_num_rows($q_jadwal) > 0): ?>
                <div class="space-y-3">
                    <?php while($j = mysqli_fetch_assoc($q_jadwal)): ?>
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
                        <div>
                            <h4 class="font-bold text-gray-800"><?php echo $j['nama_murid']; ?></h4>
                            <p class="text-xs text-gray-500 mb-1"><?php echo $j['alat_musik']; ?> • <?php echo $j['jam']; ?></p>
                        </div>
                        <a href="absen.php?id_jadwal=<?php echo $j['id']; ?>" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-xs font-bold shadow-lg hover:bg-indigo-700 transition">
                            INPUT
                        </a>
                    </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="bg-white p-6 rounded-xl text-center shadow-sm">
                    <p class="text-gray-400 text-sm">Tidak ada jadwal les hari ini.</p>
                </div>
            <?php endif; ?>
        </div>

        <div>
            <h3 class="font-bold text-gray-700 border-l-4 border-green-500 pl-3 mb-3">Pencairan Terakhir</h3>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <?php if(mysqli_num_rows($q_riwayat_honor) > 0): ?>
                    <ul class="divide-y divide-gray-100">
                        <?php while($h = mysqli_fetch_assoc($q_riwayat_honor)): ?>
                        <li class="p-4 flex justify-between items-center">
                            <div>
                                <p class="text-xs text-gray-400"><?php echo date('d M Y', strtotime($h['tanggal'])); ?></p>
                                <p class="text-xs font-bold text-gray-600"><?php echo $h['keterangan']; ?></p>
                            </div>
                            <span class="text-green-600 font-bold text-sm">+ <?php echo number_format($h['nominal']); ?></span>
                        </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <div class="p-4 text-center text-xs text-gray-400">Belum ada data pencairan.</div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</body>
</html>
