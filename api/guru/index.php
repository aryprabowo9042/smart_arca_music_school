<?php
// ... (Bagian atas: Cek Login & Koneksi tetap sama) ...
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    setcookie('user_role', '', time() - 3600, '/');
    header("Location: ../../index.php"); exit();
}
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'guru') {
    header("Location: ../admin/login.php"); exit();
}
require_once(__DIR__ . '/../koneksi.php');

$id_guru = $_COOKIE['user_id'];
$nama_guru = $_COOKIE['user_username'];

// --- LOGIKA MINTA PENCAIRAN ---
if (isset($_POST['minta_pencairan'])) {
    $nominal_tarik = (int)$_POST['nominal_tarik'];
    $tgl_skrg = date('Y-m-d');
    
    // Simpan ke tabel keuangan dengan status_konfirmasi = 0 (Pending)
    $sql_minta = "INSERT INTO keuangan (tanggal, nama_pelaku, id_user, keterangan, jenis, nominal, status_konfirmasi) 
                  VALUES ('$tgl_skrg', '$nama_guru', '$id_guru', 'Permintaan Pencairan Honor', 'keluar', '$nominal_tarik', 0)";
    
    if (mysqli_query($conn, $sql_minta)) {
        echo "<script>alert('Permintaan pencairan telah dikirim ke Admin!'); window.location='index.php';</script>";
    }
}

// ... (Query Hitung Saldo & Jadwal tetap sama) ...
$q_hak = mysqli_query($conn, "SELECT SUM(a.nominal_bayar) * 0.5 as total_hak FROM absensi a JOIN jadwal j ON a.id_jadwal = j.id WHERE j.id_guru = '$id_guru'");
$total_hak = mysqli_fetch_assoc($q_hak)['total_hak'] ?? 0;

// Total yang sudah cair (status=1)
$q_terima = mysqli_query($conn, "SELECT SUM(nominal) as total_terima FROM keuangan WHERE id_user = '$id_guru' AND jenis = 'keluar' AND status_konfirmasi = 1");
$total_terima = mysqli_fetch_assoc($q_terima)['total_terima'] ?? 0;

$saldo_honor = $total_hak - $total_terima;
?>

<div class="bg-indigo-600 p-6 rounded-b-[30px] shadow-lg text-white mb-6">
    <div class="flex justify-between items-start">
        <div>
            <p class="text-indigo-200 text-xs">Saldo Honor Anda</p>
            <h1 class="text-3xl font-bold">Rp <?php echo number_format($saldo_honor); ?></h1>
        </div>
        <a href="index.php?action=logout" class="bg-white/20 p-2 rounded-lg"><i class="fas fa-sign-out-alt"></i></a>
    </div>
    
    <?php if($saldo_honor > 0): ?>
    <form method="POST" class="mt-4">
        <input type="hidden" name="nominal_tarik" value="<?php echo $saldo_honor; ?>">
        <button type="submit" name="minta_pencairan" onclick="return confirm('Kirim permintaan pencairan sebesar Rp <?php echo number_format($saldo_honor); ?> ke Admin?')" class="w-full bg-white text-indigo-600 font-bold py-3 rounded-xl shadow-lg transition active:scale-95">
            <i class="fas fa-hand-holding-usd mr-2"></i> TARIK HONOR SEKARANG
        </button>
    </form>
    <?php endif; ?>
</div>

<div class="max-w-md mx-auto px-4">
    <h3 class="font-bold text-gray-700 mb-3 pl-3 border-l-4 border-yellow-500">Status Pencairan</h3>
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <?php 
        $q_status = mysqli_query($conn, "SELECT * FROM keuangan WHERE id_user = '$id_guru' AND status_konfirmasi = 0");
        if(mysqli_num_rows($q_status) > 0): 
            while($s = mysqli_fetch_assoc($q_status)): ?>
            <div class="p-4 flex justify-between items-center bg-yellow-50">
                <span class="text-xs font-bold text-yellow-700 uppercase">Menunggu Konfirmasi Admin</span>
                <span class="font-bold text-sm">Rp <?php echo number_format($s['nominal']); ?></span>
            </div>
        <?php endwhile; else: ?>
            <p class="p-4 text-center text-xs text-gray-400 italic">Tidak ada permintaan pending.</p>
        <?php endif; ?>
    </div>
</div>
