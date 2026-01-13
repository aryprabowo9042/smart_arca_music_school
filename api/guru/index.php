<?php
session_start();
require_once(__DIR__ . '/../koneksi.php');

// Proteksi Guru
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'guru') {
    header("Location: ../admin/login.php");
    exit();
}

$id_guru = $_SESSION['id'];
$nama_guru = $_SESSION['username'];

// Hitung Saldo Honor (50% dari nominal bayar)
$q_honor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal_bayar) * 0.5 as saldo FROM absensi a JOIN jadwal j ON a.id_jadwal = j.id WHERE j.id_guru = '$id_guru'"));
$saldo = $q_honor['saldo'] ?? 0;

// Ambil Jadwal Hari Ini
$hari_ini = date('l'); 
$hari_map = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
$hari_indo = $hari_map[$hari_ini];

$query_jadwal = mysqli_query($conn, "SELECT j.*, m.username as nama_murid FROM jadwal j JOIN users m ON j.id_murid = m.id WHERE j.id_guru = '$id_guru' AND j.hari = '$hari_indo'");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guru Dashboard - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen pb-10">

    <div class="bg-blue-700 text-white px-6 pt-8 pb-16 rounded-b-[40px] shadow-lg">
        <div class="flex justify-between items-center mb-6">
            <div>
                <p class="text-blue-200 text-sm">Selamat mengajar,</p>
                <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($nama_guru); ?></h1>
            </div>
            <a href="../admin/index.php?action=logout" class="bg-white/20 p-2 rounded-full hover:bg-white/30 transition">
                <i class="fas fa-power-off"></i>
            </a>
        </div>
        
        <div class="bg-white text-gray-800 p-6 rounded-3xl shadow-xl flex justify-between items-center transform translate-y-8">
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Estimasi Honor Anda</p>
                <h2 class="text-3xl font-black text-blue-700">Rp <?php echo number_format($saldo); ?></h2>
            </div>
            <div class="bg-blue-100 text-blue-600 p-4 rounded-2xl text-2xl">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
        </div>
    </div>

    <div class="max-w-md mx-auto px-6 mt-16">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-gray-700">Jadwal Hari Ini (<?php echo $hari_indo; ?>)</h3>
            <a href="absen.php" class="text-blue-600 text-sm font-semibold">Lihat Semua</a>
        </div>

        <div class="space-y-4">
            <?php if(mysqli_num_rows($query_jadwal) > 0): ?>
                <?php while($j = mysqli_fetch_assoc($query_jadwal)): ?>
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center">
                    <div>
                        <h4 class="font-bold text-gray-800"><?php echo $j['nama_murid']; ?></h4>
                        <p class="text-sm text-gray-400"><i class="far fa-clock mr-1"></i> <?php echo $j['jam']; ?> • <?php echo $j['alat_musik']; ?></p>
                    </div>
                    <a href="absen.php?id_jadwal=<?php echo $j['id']; ?>" class="bg-blue-600 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-md shadow-blue-200">
                        ABSEN
                    </a>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-10">
                    <i class="fas fa-calendar-day text-4xl text-gray-200 mb-3 block"></i>
                    <p class="text-gray-400 text-sm">Tidak ada jadwal mengajar hari ini.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="mt-8">
            <h3 class="font-bold text-gray-700 mb-4">Aksi Cepat</h3>
            <div class="grid grid-cols-2 gap-4">
                <a href="riwayat_honor.php" class="bg-indigo-50 p-4 rounded-2xl flex flex-col items-center gap-2">
                    <i class="fas fa-history text-indigo-500"></i>
                    <span class="text-xs font-bold text-indigo-700">Riwayat Honor</span>
                </a>
                <a href="profil.php" class="bg-green-50 p-4 rounded-2xl flex flex-col items-center gap-2">
                    <i class="fas fa-user-circle text-green-500"></i>
                    <span class="text-xs font-bold text-green-700">Ubah Password</span>
                </a>
            </div>
        </div>
    </div>

</body>
</html>
