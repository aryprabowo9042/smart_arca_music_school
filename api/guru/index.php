<?php
// 1. PROTEKSI HALAMAN
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'guru') {
    header("Location: ../admin/login.php");
    exit();
}

require_once(__DIR__ . '/../koneksi.php');

$id_guru = $_COOKIE['user_id'];

// ==========================================
// 2. LOGIKA SIMPAN JURNAL (SETIAP PERTEMUAN)
// ==========================================
if (isset($_POST['absen'])) {
    $id_jadwal = (int)$_POST['id_jadwal'];
    $tgl = $_POST['tanggal_absen']; // Tanggal dipilih manual atau default hari ini
    $nom = (int)$_POST['nominal_bayar']; 
    $materi = mysqli_real_escape_string($conn, $_POST['materi_les']);
    $perkembangan = mysqli_real_escape_string($conn, $_POST['refleksi_guru']);
    $mulai = $_POST['jam_mulai'];
    $selesai = $_POST['jam_selesai'];
    $id_edit = isset($_POST['id_edit']) ? (int)$_POST['id_edit'] : 0;

    if ($id_edit > 0) {
        $sql = "UPDATE `absensi` SET 
                `tanggal` = '$tgl',
                `nominal_bayar` = '$nom', 
                `materi_ajar` = '$materi', 
                `perkembangan_murid` = '$perkembangan', 
                `jam_mulai` = '$mulai', 
                `jam_selesai` = '$selesai' 
                WHERE `id` = '$id_edit'";
    } else {
        // INSERT BARU (Setiap pertemuan jadi baris baru di DB)
        $sql = "INSERT INTO `absensi` (`id_jadwal`, `tanggal`, `nominal_bayar`, `materi_ajar`, `perkembangan_murid`, `jam_mulai`, `jam_selesai`) 
                VALUES ('$id_jadwal', '$tgl', '$nom', '$materi', '$perkembangan', '$mulai', '$selesai')";
    }
    
    if(mysqli_query($conn, $sql)) {
        header("Location: index.php?status=sukses"); 
        exit();
    } else {
        die("Error: " . mysqli_error($conn)); 
    }
}

// 3. HITUNG TOTAL HONOR GURU
$q_saldo = mysqli_query($conn, "SELECT SUM(`nominal_bayar`) as total FROM `absensi` a JOIN `jadwal` j ON a.`id_jadwal` = j.`id` WHERE j.`id_guru` = '$id_guru'");
$res_saldo = mysqli_fetch_assoc($q_saldo);
$total_hak = floor(($res_saldo['total'] ?? 0) * 0.5);
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
        <h1>Smart Arca Music School</h1>
        <a href="../logout.php" class="bg-red-500 p-2 rounded-xl shadow-lg"><i class="fas fa-sign-out-alt"></i></a>
    </nav>

    <div class="max-w-5xl mx-auto px-4">
        <div class="bg-white p-6 rounded-[2rem] shadow-lg mb-8 border-l-8 border-indigo-600 flex justify-between items-center">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-2">Total Honor Terkumpul (50%)</p>
                <h2 class="text-3xl font-black text-slate-800 italic uppercase">Rp <?php echo number_format($total_hak, 0, ',', '.'); ?></h2>
            </div>
            <i class="fas fa-wallet text-slate-100 text-5xl"></i>
        </div>

        <h2 class="text-xl font-black text-indigo-900 uppercase italic mb-6"><i class="fas fa-calendar-day mr-2"></i> Jadwal & Pengisian Jurnal</h2>

        <div class="grid grid-cols-1 gap-8">
            <?php 
            $sql_j = "SELECT j.*, u.username as nama_murid FROM `jadwal` j JOIN `users` u ON j.`id_murid` = u.`id` WHERE j.`id_guru` = '$id_guru' ORDER BY FIELD(`hari`, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'), `jam` ASC";
            $res_j = mysqli_query($conn, $sql_j);
            
            while($r = mysqli_fetch_assoc($res_j)): 
                $id_jadwal = (int)$r['id'];
                
                // Cek apakah ada request edit
                $is_editing = false;
                $data_edit = null;
                if(isset($_GET['edit_id'])) {
                    $eid = (int)$_GET['edit_id'];
                    $q_edit = mysqli_query($conn, "SELECT * FROM `absensi` WHERE `id` = '$eid' AND `id_jadwal` = '$id_jadwal'");
                    $data_edit = mysqli_fetch_assoc($q_edit);
                    if($data_edit) $is_editing = true;
                }
            ?>
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-100 p-8 overflow-hidden">
                <div class="flex flex-col md:flex-row justify-between mb-6 gap-4">
                    <div>
                        <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest"><?php echo $r['hari']; ?></span>
                        <h3 class="text-3xl font-black text-slate-800 uppercase italic mt-2 leading-none"><?php echo $r['nama_murid']; ?></h3>
                        <p class="text-slate-400 font-bold text-xs mt-1 uppercase italic tracking-tighter"><?php echo $r['alat_musik']; ?> • <?php echo date('H:i', strtotime($r['jam'])); ?> WIB</p>
                    </div>
                </div>

                <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-6 rounded-[2rem] border-2 border-dashed border-slate-200 mb-8">
                    <input type="hidden" name="id_jadwal" value="<?php echo $id_jadwal; ?>">
                    <?php if($is_editing): ?><input type="hidden" name="id_edit" value="<?php echo $data_edit['id']; ?>"><?php endif; ?>

                    <div class="space-y-4">
                        <div class="space-y-1">
                            <label class="text-[9px] font-black text-slate-400 uppercase ml-2">Tanggal Pertemuan</label>
                            <input type="date" name="tanggal_absen" value="<?php echo $data_edit['tanggal'] ?? date('Y-m-d'); ?>" class="w-full p-3 rounded-xl border-2 border-white text-xs font-bold shadow-sm focus:border-indigo-600 outline-none" required>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label class="text-[9px] font-black text-slate-400 uppercase ml-2">Jam Mulai</label>
                                <input type="time" name="jam_mulai" value="<?php echo $data_edit['jam_mulai'] ?? ''; ?>" class="w-full p-3 rounded-xl border-2 border-white text-xs font-bold shadow-sm" required>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[9px] font-black text-slate-400 uppercase ml-2">Jam Selesai</label>
                                <input type="time" name="jam_selesai" value="<?php echo $data_edit['jam_selesai'] ?? ''; ?>" class="w-full p-3 rounded-xl border-2 border-white text-xs font-bold shadow-sm" required>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[9px] font-black text-slate-400 uppercase ml-2">Nominal Bayar (SPP)</label>
                            <input type="number" name="nominal_bayar" value="<?php echo $data_edit['nominal_bayar'] ?? ''; ?>" class="w-full p-3 rounded-xl border-2 border-white text-xs font-bold shadow-sm" placeholder="Contoh: 75000" required>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-1">
                            <label class="text-[9px] font-black text-slate-400 uppercase ml-2">Materi Ajar</label>
                            <textarea name="materi_les" rows="2" class="w-full p-3 rounded-xl border-2 border-white text-xs font-bold shadow-sm focus:border-indigo-600 outline-none" placeholder="Apa yang dipelajari hari ini?" required><?php echo $data_edit['materi_ajar'] ?? ''; ?></textarea>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[9px] font-black text-slate-400 uppercase ml-2">Perkembangan Murid</label>
                            <textarea name="refleksi_guru" rows="2" class="w-full p-3 rounded-xl border-2 border-white text-xs font-bold shadow-sm focus:border-indigo-600 outline-none" placeholder="Catatan untuk pertemuan ini..." required><?php echo $data_edit['perkembangan_murid'] ?? ''; ?></textarea>
                        </div>
                        <button type="submit" name="absen" class="w-full bg-indigo-600 text-white font-black py-4 rounded-xl uppercase text-[10px] shadow-lg hover:bg-indigo-700 transition">
                            <?php echo $is_editing ? 'Update Jurnal' : 'Simpan Jurnal Pertemuan'; ?>
                        </button>
                        <?php if($is_editing): ?>
                            <a href="index.php" class="block text-center text-[9px] font-bold text-red-500 uppercase">Batal Edit</a>
                        <?php endif; ?>
                    </div>
                </form>

                <div class="mt-4">
                    <h4 class="text-[10px] font-black text-indigo-900 uppercase tracking-widest mb-4 border-b pb-2"><i class="fas fa-history mr-1"></i> Riwayat Pertemuan - <?php echo $r['nama_murid']; ?></h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">
                                    <th class="p-2">Tanggal</th>
                                    <th class="p-2">Jam</th>
                                    <th class="p-2">Materi</th>
                                    <th class="p-2">Perkembangan</th>
                                    <th class="p-2 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-[11px] font-bold text-slate-600">
                                <?php 
                                $q_histori = mysqli_query($conn, "SELECT * FROM `absensi` WHERE `id_jadwal` = '$id_jadwal' ORDER BY `tanggal` DESC, `id` DESC LIMIT 5");
                                if(mysqli_num_rows($q_histori) == 0):
                                ?>
                                <tr><td colspan="5" class="p-4 text-center text-slate-300 italic">Belum ada catatan pertemuan.</td></tr>
                                <?php endif; while($h = mysqli_fetch_assoc($q_histori)): ?>
                                <tr class="border-b border-slate-50 hover:bg-slate-50 transition">
                                    <td class="p-2 text-indigo-600"><?php echo date('d/m/y', strtotime($h['tanggal'])); ?></td>
                                    <td class="p-2"><?php echo substr($h['jam_mulai'],0,5); ?>-<?php echo substr($h['jam_selesai'],0,5); ?></td>
                                    <td class="p-2 italic max-w-[150px] truncate"><?php echo $h['materi_ajar']; ?></td>
                                    <td class="p-2 text-slate-400 max-w-[150px] truncate"><?php echo $h['perkembangan_murid']; ?></td>
                                    <td class="p-2 text-right">
                                        <a href="index.php?edit_id=<?php echo $h['id']; ?>" class="text-yellow-500 hover:text-indigo-600"><i class="fas fa-edit"></i></a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
