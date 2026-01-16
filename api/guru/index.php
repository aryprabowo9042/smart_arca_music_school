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
// 2. LOGIKA SIMPAN JURNAL (SETIAP PERTEMUAN)
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
        $sql = "UPDATE `absensi` SET 
                `tanggal` = '$tgl',
                `nominal_bayar` = '$nom', 
                `materi_ajar` = '$materi', 
                `perkembangan_murid` = '$perkembangan', 
                `jam_mulai` = '$mulai', 
                `jam_selesai` = '$selesai' 
                WHERE `id` = '$id_edit'";
    } else {
        $sql = "INSERT INTO `absensi` (`id_jadwal`, `tanggal`, `nominal_bayar`, `materi_ajar`, `perkembangan_murid`, `jam_mulai`, `jam_selesai`) 
                VALUES ('$id_jadwal', '$tgl', '$nom', '$materi', '$perkembangan', '$mulai', '$selesai')";
    }
    
    if(mysqli_query($conn, $sql)) {
        header("Location: index.php?status=sukses"); 
        exit();
    }
}

// 3. HITUNG TOTAL HONOR GURU (50% DARI TOTAL BAYAR)
$q_saldo = mysqli_query($conn, "SELECT SUM(`nominal_bayar`) as total FROM `absensi` a JOIN `jadwal` j ON a.`id_jadwal` = j.`id` WHERE j.`id_guru` = '$id_guru'");
$res_saldo = mysqli_fetch_assoc($q_saldo);
$total_hak = floor(($res_saldo['total'] ?? 0) * 0.5);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guru Dashboard - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen pb-20">

    <nav class="bg-indigo-900 shadow-xl px-6 py-4 flex justify-between items-center mb-6 border-b-4 border-yellow-400 sticky top-0 z-50 text-white font-black italic uppercase">
        <div class="flex items-center gap-3">
            <i class="fas fa-chalkboard-teacher text-yellow-400"></i>
            <h1 class="tracking-tighter uppercase">Teacher Room</h1>
        </div>
        <a href="../logout.php" class="bg-red-500 hover:bg-red-600 p-2 rounded-xl shadow-lg transition">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </nav>

    <div class="max-w-5xl mx-auto px-4">
        
        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl mb-8 border-l-[12px] border-indigo-600 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-6">
                <div class="bg-indigo-50 w-16 h-16 rounded-3xl flex items-center justify-center text-indigo-600 text-3xl shadow-inner">
                    <i class="fas fa-wallet"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-2 italic">Honor Terkumpul (50%)</p>
                    <h2 class="text-4xl font-black text-slate-800 italic uppercase tracking-tighter">
                        Rp <?php echo number_format($total_hak, 0, ',', '.'); ?>
                    </h2>
                </div>
            </div>
            
            <div class="w-full md:w-auto">
                <?php if($total_hak > 0): ?>
                    <button onclick="alert('Permintaan tarik honor Rp <?php echo number_format($total_hak, 0, ',', '.'); ?> telah dikirim ke Admin.')" class="w-full bg-yellow-400 hover:bg-yellow-300 text-indigo-900 font-black py-4 px-8 rounded-2xl uppercase italic text-xs shadow-xl transition transform active:scale-95 flex items-center justify-center gap-3 border-b-4 border-yellow-600">
                        <i class="fas fa-hand-holding-usd text-lg"></i> Tarik Honor Sekarang
                    </button>
                <?php else: ?>
                    <button class="w-full bg-slate-100 text-slate-400 font-black py-4 px-8 rounded-2xl uppercase italic text-xs cursor-not-allowed border-b-4 border-slate-200" disabled>
                        Honor Belum Tersedia
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <h2 class="text-xl font-black text-indigo-900 uppercase italic mb-6"><i class="fas fa-calendar-alt mr-2 text-yellow-500"></i> Jurnal Mengajar</h2>

        <div class="grid grid-cols-1 gap-10">
            <?php 
            $sql_j = "SELECT j.*, u.username as nama_murid FROM `jadwal` j JOIN `users` u ON j.`id_murid` = u.`id` WHERE j.`id_guru` = '$id_guru' ORDER BY FIELD(`hari`, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'), `jam` ASC";
            $res_j = mysqli_query($conn, $sql_j);
            
            while($r = mysqli_fetch_assoc($res_j)): 
                $id_jadwal = (int)$r['id'];
                
                // Logika Edit
                $is_editing = false;
                $data_edit = null;
                if(isset($_GET['edit_id'])) {
                    $eid = (int)$_GET['edit_id'];
                    $q_edit = mysqli_query($conn, "SELECT * FROM `absensi` WHERE `id` = '$eid' AND `id_jadwal` = '$id_jadwal'");
                    $data_edit = mysqli_fetch_assoc($q_edit);
                    if($data_edit) $is_editing = true;
                }
            ?>
            <div class="bg-white rounded-[3rem] shadow-2xl border border-slate-100 p-8 relative overflow-hidden">
                <div class="flex flex-col md:flex-row justify-between mb-8 gap-4 border-b pb-6 border-slate-50">
                    <div>
                        <span class="bg-indigo-100 text-indigo-700 px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest italic"><?php echo $r['hari']; ?></span>
                        <h3 class="text-4xl font-black text-slate-800 uppercase italic mt-2 tracking-tighter leading-none"><?php echo $r['nama_murid']; ?></h3>
                        <p class="text-slate-400 font-bold text-xs mt-2 uppercase italic"><i class="fas fa-music mr-1"></i> <?php echo $r['alat_musik']; ?> • <?php echo date('H:i', strtotime($r['jam'])); ?> WIB</p>
                    </div>
                </div>

                <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-8 bg-slate-50 p-8 rounded-[2.5rem] border-2 border-dashed border-slate-200 mb-10">
                    <input type="hidden" name="id_jadwal" value="<?php echo $id_jadwal; ?>">
                    <?php if($is_editing): ?><input type="hidden" name="id_edit" value="<?php echo $data_edit['id']; ?>"><?php endif; ?>

                    <div class="space-y-5">
                        <div class="space-y-1">
                            <label class="text-[9px] font-black text-indigo-400 uppercase ml-3">Tanggal Les</label>
                            <input type="date" name="tanggal_absen" value="<?php echo $data_edit['tanggal'] ?? date('Y-m-d'); ?>" class="w-full p-4 rounded-2xl border-2 border-white text-xs font-bold shadow-sm focus:border-indigo-600 outline-none transition" required>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[9px] font-black text-indigo-400 uppercase ml-3">Mulai</label>
                                <input type="time" name="jam_mulai" value="<?php echo $data_edit['jam_mulai'] ?? ''; ?>" class="w-full p-4 rounded-2xl border-2 border-white text-xs font-bold shadow-sm" required>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[9px] font-black text-indigo-400 uppercase ml-3">Selesai</label>
                                <input type="time" name="jam_selesai" value="<?php echo $data_edit['jam_selesai'] ?? ''; ?>" class="w-full p-4 rounded-2xl border-2 border-white text-xs font-bold shadow-sm" required>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[9px] font-black text-indigo-400 uppercase ml-3">Nominal Bayar (SPP)</label>
                            <input type="number" name="nominal_bayar" value="<?php echo $data_edit['nominal_bayar'] ?? ''; ?>" class="w-full p-4 rounded-2xl border-2 border-white text-xs font-bold shadow-sm" placeholder="Misal: 75000" required>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div class="space-y-1">
                            <label class="text-[9px] font-black text-indigo-400 uppercase ml-3">Materi Belajar</label>
                            <textarea name="materi_les" rows="2" class="w-full p-4 rounded-2xl border-2 border-white text-xs font-bold shadow-sm outline-none focus:border-indigo-600" placeholder="Lagu / Teknik yang dipelajari" required><?php echo $data_edit['materi_ajar'] ?? ''; ?></textarea>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[9px] font-black text-indigo-400 uppercase ml-3">Catatan Perkembangan</label>
                            <textarea name="refleksi_guru" rows="2" class="w-full p-4 rounded-2xl border-2 border-white text-xs font-bold shadow-sm outline-none focus:border-indigo-600" placeholder="Pesan untuk murid/orang tua" required><?php echo $data_edit['perkembangan_murid'] ?? ''; ?></textarea>
                        </div>
                        <button type="submit" name="absen" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 rounded-2xl uppercase italic text-[11px] shadow-xl transition transform active:scale-95">
                            <i class="fas fa-save mr-2"></i> <?php echo $is_editing ? 'Update Jurnal' : 'Simpan Pertemuan'; ?>
                        </button>
                    </div>
                </form>

                <div class="px-2">
                    <h4 class="text-[10px] font-black text-indigo-900 uppercase tracking-[0.2em] mb-4 border-b pb-2 italic"><i class="fas fa-history mr-1 text-yellow-500"></i> Riwayat Pertemuan</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-[9px] font-black text-slate-400 uppercase">
                                    <th class="p-3">Tanggal</th>
                                    <th class="p-3">Jam</th>
                                    <th class="p-3">Materi</th>
                                    <th class="p-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-[11px] font-bold text-slate-600">
                                <?php 
                                $q_histori = mysqli_query($conn, "SELECT * FROM `absensi` WHERE `id_jadwal` = '$id_jadwal' ORDER BY `tanggal` DESC, `id` DESC LIMIT 5");
                                if(mysqli_num_rows($q_histori) == 0):
                                ?>
                                    <tr><td colspan="4" class="p-4 text-center text-slate-300 italic">Belum ada data.</td></tr>
                                <?php endif; while($h = mysqli_fetch_assoc($q_histori)): 
                                    $jm = !empty($h['jam_mulai']) ? substr($h['jam_mulai'], 0, 5) : "--:--";
                                    $js = !empty($h['jam_selesai']) ? substr($h['jam_selesai'], 0, 5) : "--:--";
                                ?>
                                <tr class="border-b border-slate-50 hover:bg-slate-50 transition">
                                    <td class="p-3 text-indigo-600"><?php echo date('d/m/y', strtotime($h['tanggal'])); ?></td>
                                    <td class="p-3 text-slate-400"><?php echo $jm; ?>-<?php echo $js; ?></td>
                                    <td class="p-3 italic max-w-[180px] truncate"><?php echo htmlspecialchars($h['materi_ajar'] ?? '-'); ?></td>
                                    <td class="p-3 text-right">
                                        <a href="index.php?edit_id=<?php echo $h['id']; ?>" class="bg-yellow-100 text-yellow-600 w-8 h-8 rounded-lg inline-flex items-center justify-center hover:bg-yellow-400 hover:text-white transition shadow-sm"><i class="fas fa-edit"></i></a>
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
