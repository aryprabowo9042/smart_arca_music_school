<?php
// 1. PROTEKSI HALAMAN
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'guru') {
    header("Location: ../index.php");
    exit();
}

require_once(__DIR__ . '/../koneksi.php');

$id_guru = $_COOKIE['user_id'];
$username_guru = $_COOKIE['user_username'] ?? 'Guru';

// ==========================================
// 2. LOGIKA SIMPAN JURNAL
// ==========================================
if (isset($_POST['absen'])) {
    $id_jadwal = (int)$_POST['id_jadwal'];
    $tgl = $_POST['tanggal_absen'];
    $nom = (int)$_POST['nominal_bayar']; 
    $materi = mysqli_real_escape_string($conn, $_POST['materi_les']);
    $perkembangan = mysqli_real_escape_string($conn, $_POST['refleksi_guru']);
    $mulai = $_POST['jam_mulai'];
    $selesai = $_POST['jam_selesai'];
    $id_edit = isset($_POST['id_edit']) ? (int)$_POST['id_edit'] : 0;

    if ($id_edit > 0) {
        $sql = "UPDATE `absensi` SET `tanggal` = '$tgl', `nominal_bayar` = '$nom', `materi_ajar'] = '$materi', `perkembangan_murid` = '$perkembangan', `jam_mulai` = '$mulai', `jam_selesai` = '$selesai' WHERE `id` = '$id_edit'";
    } else {
        // Tambahkan 'belum' secara default pada status_honor
        $sql = "INSERT INTO `absensi` (`id_jadwal`, `tanggal`, `nominal_bayar`, `materi_ajar`, `perkembangan_murid`, `jam_mulai`, `jam_selesai`, `status_honor`) 
                VALUES ('$id_jadwal', '$tgl', '$nom', '$materi', '$perkembangan', '$mulai', '$selesai', 'belum')";
    }
    mysqli_query($conn, $sql);
    header("Location: index.php?status=sukses"); exit();
}

// 3. LOGIKA TARIK HONOR (Update status menjadi 'proses')
if (isset($_POST['request_tarik'])) {
    $update_status = "UPDATE absensi a 
                     JOIN jadwal j ON a.id_jadwal = j.id 
                     SET a.status_honor = 'proses' 
                     WHERE j.id_guru = '$id_guru' AND a.status_honor = 'belum'";
    if(mysqli_query($conn, $update_status)) {
        header("Location: index.php?status=pending"); exit();
    }
}

// 4. HITUNG SALDO (Hanya yang statusnya 'belum')
$q_saldo = mysqli_query($conn, "SELECT SUM(`nominal_bayar`) as total FROM `absensi` a JOIN `jadwal` j ON a.`id_jadwal` = j.`id` WHERE j.`id_guru` = '$id_guru' AND a.status_honor = 'belum'");
$res_saldo = mysqli_fetch_assoc($q_saldo);
$total_hak = floor(($res_saldo['total'] ?? 0) * 0.5);

// 5. CEK APAKAH ADA YANG SEDANG DIPROSES
$q_proses = mysqli_query($conn, "SELECT SUM(`nominal_bayar`) as total_proses FROM `absensi` a JOIN `jadwal` j ON a.`id_jadwal` = j.`id` WHERE j.`id_guru` = '$id_guru' AND a.status_honor = 'proses'");
$res_proses = mysqli_fetch_assoc($q_proses);
$total_proses = floor(($res_proses['total_proses'] ?? 0) * 0.5);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Guru - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen pb-20">

    <nav class="bg-indigo-900 shadow-xl px-6 py-4 flex justify-between items-center mb-6 border-b-4 border-yellow-400 sticky top-0 z-50 text-white font-black italic uppercase">
        <div class="flex items-center gap-3">
            <i class="fas fa-chalkboard-teacher text-yellow-400"></i>
            <h1>Teacher Dashboard</h1>
        </div>
        <a href="../logout.php" class="bg-red-500 p-2 rounded-xl transition"><i class="fas fa-sign-out-alt"></i></a>
    </nav>

    <div class="max-w-5xl mx-auto px-4">
        
        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl mb-8 border-l-[12px] border-indigo-600 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex gap-4">
                <div class="bg-indigo-50 w-16 h-16 rounded-3xl flex items-center justify-center text-indigo-600 text-3xl"><i class="fas fa-wallet"></i></div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-2">Honor Tersedia</p>
                    <h2 class="text-4xl font-black text-slate-800 italic uppercase">Rp <?php echo number_format($total_hak, 0, ',', '.'); ?></h2>
                    <?php if($total_proses > 0): ?>
                        <p class="text-[9px] font-bold text-orange-500 uppercase mt-1 italic tracking-tight">* Rp <?php echo number_format($total_proses, 0, ',', '.'); ?> Sedang dalam proses pencairan</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <form method="POST">
                <?php if($total_hak > 0): ?>
                    <button type="submit" name="request_tarik" class="bg-yellow-400 hover:bg-yellow-300 text-indigo-900 font-black py-4 px-8 rounded-2xl uppercase italic text-xs shadow-xl transition transform active:scale-95 flex items-center gap-2 border-b-4 border-yellow-600">
                        <i class="fas fa-hand-holding-usd"></i> Tarik Honor
                    </button>
                <?php else: ?>
                    <button class="bg-slate-100 text-slate-400 font-black py-4 px-8 rounded-2xl uppercase italic text-xs cursor-not-allowed" disabled>Saldo Kosong</button>
                <?php endif; ?>
            </form>
        </div>

        </div>
</body>
</html>
