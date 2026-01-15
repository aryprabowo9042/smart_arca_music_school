<?php
// 1. LOGIKA LOGOUT
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    setcookie('user_role', '', time() - 3600, '/');
    setcookie('user_id', '', time() - 3600, '/');
    setcookie('user_username', '', time() - 3600, '/');
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

// --- 3. LOGIKA TARIK HONOR ---
if (isset($_POST['minta_pencairan'])) {
    $nominal_tarik = (int)$_POST['nominal_tarik'];
    $tgl_skrg = date('Y-m-d');
    $sql_minta = "INSERT INTO keuangan (tanggal, nama_pelaku, id_user, keterangan, jenis, nominal, status_konfirmasi) 
                  VALUES ('$tgl_skrg', '$nama_guru', '$id_guru', 'Permintaan Pencairan Honor', 'keluar', '$nominal_tarik', 0)";
    if (mysqli_query($conn, $sql_minta)) {
        echo "<script>alert('Permintaan terkirim!'); window.location='index.php';</script>";
        exit();
    }
}

// --- 4. HITUNG SALDO ---
$q_hak = mysqli_query($conn, "SELECT SUM(a.nominal_bayar) * 0.5 as total_hak FROM absensi a JOIN jadwal j ON a.id_jadwal = j.id WHERE j.id_guru = '$id_guru'");
$total_hak = mysqli_fetch_assoc($q_hak)['total_hak'] ?? 0;

$q_terima = mysqli_query($conn, "SELECT SUM(nominal) as total_terima FROM keuangan WHERE id_user = '$id_guru' AND jenis = 'keluar' AND status_konfirmasi = 1");
$total_terima = mysqli_fetch_assoc($q_terima)['total_terima'] ?? 0;

$saldo_honor = $total_hak - $total_terima;

// --- 5. JADWAL HARI INI ---
$hari_inggris = date('l');
$map_hari = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
$hari_ini = $map_hari[$hari_inggris];
$q_jadwal = mysqli_query($conn, "SELECT j.*, m.username as nama_murid FROM jadwal j JOIN users m ON j.id_murid = m.id WHERE j.id_guru = '$id_guru' AND j.hari = '$hari_ini' ORDER BY j.jam ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen pb-20">
    <div class="bg-indigo-700 p-6 rounded-b-[40px] shadow-xl text-white mb-8">
        <div class="flex justify-between items-start mb-6">
            <div><p class="text-indigo-200 text-xs font-bold uppercase">Portal Guru</p><h1 class="text-2xl font-bold"><?php echo $nama_guru; ?> 👋</h1></div>
            <a href="index.php?action=logout" class="bg-white/20 p-2 rounded-xl"><i class="fas fa-sign-out-alt"></i></a>
        </div>
        <div class="bg-white/10 backdrop-blur-lg border border-white/20 p-5 rounded-2xl">
            <p class="text-xs text-indigo-100 mb-1">Saldo Honor</p>
            <h2 class="text-3xl font-black italic mb-4">Rp <?php echo number_format($saldo_honor); ?></h2>
            
            <div class="grid grid-cols-2 gap-3">
                <form method="POST">
                    <input type="hidden" name="nominal_tarik" value="<?php echo $saldo_honor; ?>">
                    <button type="submit" name="minta_pencairan" class="w-full bg-indigo-500 hover:bg-indigo-400 text-white font-bold py-3 rounded-xl text-xs border border-white/20">
                        <i class="fas fa-paper-plane mr-1"></i> TARIK HONOR
                    </button>
                </form>
                <a href="terima_bayar.php" class="w-full bg-green-500 hover:bg-green-400 text-white font-bold py-3 rounded-xl text-xs border border-white/20 flex items-center justify-center">
                    <i class="fas fa-money-bill-wave mr-1"></i> TERIMA SPP
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-md mx-auto px-4 space-y-6">
        <h3 class="font-bold text-gray-800 border-l-4 border-indigo-600 pl-3">Jadwal <?php echo $hari_ini; ?></h3>
        <?php while($j = mysqli_fetch_assoc($q_jadwal)): ?>
        <div class="bg-white p-5 rounded-2xl shadow-sm border flex justify-between items-center">
            <div><h4 class="font-bold"><?php echo $j['nama_murid']; ?></h4><p class="text-[10px] text-gray-400"><?php echo $j['alat_musik']; ?> • <?php echo $j['jam']; ?></p></div>
            <a href="absen.php?id_jadwal=<?php echo $j['id']; ?>" class="bg-indigo-600 text-white w-10 h-10 rounded-xl flex items-center justify-center"><i class="fas fa-pen-nib"></i></a>
        </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
