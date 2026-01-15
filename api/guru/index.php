<?php
// 1. PRIORITAS UTAMA: LOGIKA LOGOUT
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    setcookie('user_role', '', time() - 3600, '/');
    setcookie('user_id', '', time() - 3600, '/');
    setcookie('user_username', '', time() - 3600, '/');
    header("Location: ../../index.php");
    exit();
}

// 2. CEK LOGIN GURU
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
    
    // Simpan ke tabel keuangan dengan status_konfirmasi = 0 (Pending)
    $sql_minta = "INSERT INTO keuangan (tanggal, nama_pelaku, id_user, keterangan, jenis, nominal, status_konfirmasi) 
                  VALUES ('$tgl_skrg', '$nama_guru', '$id_guru', 'Permintaan Pencairan Honor', 'keluar', '$nominal_tarik', 0)";
    
    if (mysqli_query($conn, $sql_minta)) {
        echo "<script>alert('Permintaan pencairan sebesar Rp " . number_format($nominal_tarik) . " telah dikirim!'); window.location='index.php';</script>";
        exit();
    }
}

// --- 4. HITUNG SALDO HONOR GURU (PERBAIKAN LOGIKA) ---

// a. Total Hak (50% dari total nominal_bayar di tabel absensi)
$q_hak = mysqli_query($conn, "
    SELECT SUM(a.nominal_bayar) * 0.5 as total_hak
    FROM absensi a
    JOIN jadwal j ON a.id_jadwal = j.id
    WHERE j.id_guru = '$id_guru'
");
$res_hak = mysqli_fetch_assoc($q_hak);
$total_hak = (float)($res_hak['total_hak'] ?? 0);

// b. Total Honor yang SUDAH DITARIK (Mencakup yang SUDAH CAIR [1] DAN yang SEDANG PROSES [0])
// Ini agar saldo langsung berkurang begitu klik tarik.
$q_ditarik = mysqli_query($conn, "
    SELECT SUM(nominal) as total_ditarik 
    FROM keuangan 
    WHERE id_user = '$id_guru' AND jenis = 'keluar' AND (status_konfirmasi = 1 OR status_konfirmasi = 0)
");
$res_ditarik = mysqli_fetch_assoc($q_ditarik);
$total_sudah_ditarik = (float)($res_ditarik['total_ditarik'] ?? 0);

// c. Sisa Saldo yang benar-benar tersedia untuk ditarik lagi
$saldo_honor = $total_hak - $total_sudah_ditarik;

// --- 5. DATA JADWAL HARI INI ---
$hari_inggris = date('l');
$map_hari = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
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
            <div><p class="text-indigo-200 text-xs font-bold uppercase tracking-widest">Portal Guru</p><h1 class="text-2xl font-bold"><?php echo htmlspecialchars($nama_guru); ?> 👋</h1></div>
            <a href="index.php?action=logout" class="bg-white/20 p-2 rounded-xl transition"><i class="fas fa-sign-out-alt"></i></a>
        </div>

        <div class="bg-white/10 backdrop-blur-lg border border-white/20 p-5 rounded-2xl">
            <p class="text-xs text-indigo-100 mb-1">Saldo Honor Tersedia</p>
            <h2 class="text-3xl font-black italic mb-4">Rp <?php echo number_format($saldo_honor, 0, ',', '.'); ?></h2>
            
            <div class="grid grid-cols-2 gap-3">
                <form method="POST">
                    <input type="hidden" name="nominal_tarik" value="<?php echo $saldo_honor; ?>">
                    <button type="submit" name="minta_pencairan" <?php echo ($saldo_honor <= 0) ? 'disabled' : ''; ?> class="w-full bg-indigo-500 hover:bg-indigo-400 disabled:bg-gray-400 text-white font-bold py-3 rounded-xl text-[10px] border border-white/20 transition">
                        <i class="fas fa-paper-plane mr-1"></i> TARIK HONOR
                    </button>
                </form>
                <a href="bayar.php" class="bg-green-500 hover:bg-green-400 text-white font-bold py-3 rounded-xl text-[10px] border border-white/20 flex items-center justify-center transition">
                    <i class="fas fa-money-bill-wave mr-1"></i> TERIMA SPP
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-md mx-auto px-4 space-y-6">
        <?php 
        $q_pending = mysqli_query($conn, "SELECT * FROM keuangan WHERE id_user = '$id_guru' AND status_konfirmasi = 0");
        if(mysqli_num_rows($q_pending) > 0): 
        ?>
        <div class="bg-orange-50 border-2 border-orange-200 p-4 rounded-2xl">
            <h3 class="text-orange-700 font-bold text-xs mb-3 uppercase tracking-tighter">Proses Pencairan (Pending)</h3>
            <?php while($p = mysqli_fetch_assoc($q_pending)): ?>
            <div class="bg-white p-3 rounded-xl flex justify-between items-center shadow-sm mb-2 last:mb-0">
                <span class="text-[10px] text-gray-400 font-bold"><?php echo date('d/m/Y', strtotime($p['tanggal'])); ?></span>
                <span class="font-bold text-gray-700">Rp <?php echo number_format($p['nominal'], 0, ',', '.'); ?></span>
            </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>

        <h3 class="font-bold text-gray-800 border-l-4 border-indigo-600 pl-3 uppercase text-sm">Jadwal <?php echo $hari_ini; ?></h3>
        <?php while($j = mysqli_fetch_assoc($q_jadwal)): ?>
        <div class="bg-white p-5 rounded-2xl shadow-sm border flex justify-between items-center group">
            <div>
                <h4 class="font-bold group-hover:text-indigo-600 transition"><?php echo htmlspecialchars($j['nama_murid']); ?></h4>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest"><?php echo htmlspecialchars($j['alat_musik']); ?> • <?php echo $j['jam']; ?></p>
            </div>
            <a href="absen.php?id_jadwal=<?php echo $j['id']; ?>" class="bg-indigo-600 text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-lg"><i class="fas fa-pencil-alt"></i></a>
        </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
