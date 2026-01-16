<?php
// 1. PROTEKSI HALAMAN
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'guru') {
    header("Location: ../admin/login.php");
    exit();
}

require_once(__DIR__ . '/../koneksi.php');

$id_guru = $_COOKIE['user_id'];
$username = $_COOKIE['user_username'] ?? 'Guru';

// ==========================================
// 2. PROSES LOGIKA (ABSENSI & PENARIKAN)
// ==========================================

// A. Simpan / Edit Absensi & Jurnal
if (isset($_POST['absen'])) {
    $id_jadwal = $_POST['id_jadwal'];
    $tgl = date('Y-m-d');
    $nom = $_POST['nominal_bayar'];
    $materi = mysqli_real_escape_string($conn, $_POST['materi_les']);
    $refleksi = mysqli_real_escape_string($conn, $_POST['refleksi_guru']);
    $mulai = $_POST['jam_mulai'];
    $selesai = $_POST['jam_selesai'];
    $id_edit = $_POST['id_edit'] ?? '';

    if (!empty($id_edit)) {
        $sql = "UPDATE absensi SET nominal_bayar='$nom', materi_les='$materi', refleksi_guru='$refleksi', jam_mulai='$mulai', jam_selesai='$selesai' WHERE id='$id_edit'";
    } else {
        $sql = "INSERT INTO absensi (id_jadwal, tanggal, nominal_bayar, materi_les, refleksi_guru, jam_mulai, jam_selesai) 
                VALUES ('$id_jadwal', '$tgl', '$nom', '$materi', '$refleksi', '$mulai', '$selesai')";
    }
    mysqli_query($conn, $sql);
    header("Location: index.php"); exit();
}

// B. Ajukan Penarikan Saldo
if (isset($_POST['tarik_saldo'])) {
    $nominal_tarik = $_POST['total_hak'];
    $ket = "Penarikan Honor: " . $username;
    mysqli_query($conn, "INSERT INTO keuangan (tanggal, nama_pelaku, keterangan, jenis, nominal, status_konfirmasi) 
                         VALUES (CURDATE(), '$username', '$ket', 'keluar', '$nominal_tarik', 0)");
    header("Location: index.php?msg=pending"); exit();
}

// ==========================================
// 3. PERHITUNGAN SALDO (ANTI-ERROR)
// ==========================================
$q_saldo = mysqli_query($conn, "SELECT SUM(nominal_bayar) as total FROM absensi a JOIN jadwal j ON a.id_jadwal = j.id WHERE j.id_guru = '$id_guru'");
$res_saldo = mysqli_fetch_assoc($q_saldo);
$total_hak = FLOOR(($res_saldo['total'] ?? 0) * 0.5);

$q_pending = mysqli_query($conn, "SELECT id FROM keuangan WHERE nama_pelaku = '$username' AND status_konfirmasi = 0");
$is_pending = mysqli_num_rows($q_pending) > 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jurnal Mengajar - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen pb-20">

    <nav class="bg-indigo-900 shadow-xl px-6 py-4 flex justify-between items-center mb-6 border-b-4 border-yellow-400 sticky top-0 z-50 text-white">
        <div class="flex items-center gap-3">
            <div class="bg-white/10 w-10 h-10 rounded-xl flex items-center justify-center text-white"><i class="fas fa-book-open"></i></div>
            <h1 class="font-black text-lg italic uppercase tracking-tighter">Teacher Journal</h1>
        </div>
        <a href="../logout.php" class="bg-red-500 hover:bg-red-600 text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-lg transition transform active:scale-90">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </nav>

    <div class="max-w-6xl mx-auto px-4">
        
        <div class="bg-white p-6 rounded-[2rem] shadow-lg mb-8 flex justify-between items-center border-l-8 border-indigo-600">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Estimasi Honor (50%)</p>
                <h2 class="text-3xl font-black text-slate-800 italic">Rp <?php echo number_format($total_hak, 0, ',', '.'); ?></h2>
            </div>
            <?php if($total_hak > 0 && !$is_pending): ?>
                <form method="POST">
                    <input type="hidden" name="total_hak" value="<?php echo $total_hak; ?>">
                    <button type="submit" name="tarik_saldo" class="bg-green-500 hover:bg-green-600 text-white px-5 py-2.5 rounded-2xl font-black text-[10px] uppercase shadow-lg transition">Tarik Saldo</button>
                </form>
            <?php elseif($is_pending): ?>
                <span class="bg-yellow-100 text-yellow-700 px-5 py-2.5 rounded-2xl font-black text-[10px] uppercase italic border-2 border-yellow-200 tracking-tighter">Menunggu Persetujuan Admin</span>
            <?php endif; ?>
        </div>

        <div class="space-y-6">
            <?php 
            $sql_j = "SELECT j.*, u.username as nama_murid FROM jadwal j JOIN users u ON j.id_murid = u.id WHERE j.id_guru = '$id_guru' ORDER BY FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'), jam ASC";
            $res_j = mysqli_query($conn, $sql_j);
            
            while($r = mysqli_fetch_assoc($res_j)): 
                $id_jadwal = $r['id'];
                $tgl_skrg = date('Y-m-d');
                $cek_absen = mysqli_query($conn, "SELECT * FROM absensi WHERE id_jadwal = '$id_jadwal' AND tanggal = '$tgl_skrg'");
                $data_a = mysqli_fetch_assoc($cek_absen);
                
                $is_done = ($data_a !== null);
                // Gunakan Null Coalescing (??) untuk mencegah Warning Undefined Key
                $absen_id = $data_a['id'] ?? '';
                $is_editing = (isset($_GET['edit_id']) && $_GET['edit_id'] == $absen_id && $is_done);
            ?>
            
            <div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-slate-100 p-6 md:p-8">
                <div class="flex flex-wrap justify-between items-start gap-4 mb-6">
                    <div>
                        <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-[9px] font-black uppercase"><?php echo $r['hari']; ?></span>
                        <h3 class="text-2xl font-black text-slate-800 uppercase italic mt-2 leading-none"><?php echo $r['nama_murid']; ?></h3>
                        <p class="text-slate-400 font-bold text-[10px] uppercase tracking-[0.2em] mt-2 italic"><?php echo $r['alat_musik']; ?> • <?php echo date('H:i', strtotime($r['jam'])); ?> WIB</p>
                    </div>
                    <?php if($is_done && !$is_editing): ?>
                        <div class="text-right">
                            <span class="bg-green-100 text-green-600 px-4 py-2 rounded-xl text-[9px] font-black uppercase italic tracking-widest">Sudah Terabsen</span>
                            <a href="index.php?edit_id=<?php echo $absen_id; ?>" class="block mt-3 text-indigo-500 font-black text-[9px] uppercase hover:underline tracking-tighter">Edit Jurnal <i class="fas fa-edit ml-1"></i></a>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if(!$is_done || $is_editing): ?>
                    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-6 rounded-[2rem]">
                        <input type="hidden" name="id_jadwal" value="<?php echo $r['id']; ?>">
                        <?php if($is_editing): ?><input type="hidden" name="id_edit" value="<?php echo $absen_id; ?>"><?php endif; ?>

                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-[9px] font-black text-slate-400 uppercase ml-1">Jam Mulai</label>
                                    <input type="time" name="jam_mulai" value="<?php echo $data_a['jam_mulai'] ?? ''; ?>" class="w-full p-3 rounded-xl border-2 border-white focus:border-indigo-600 outline-none text-xs font-bold shadow-sm" required>
                                </div>
                                <div>
                                    <label class="text-[9px] font-black text-slate-400 uppercase ml-1">Jam Selesai</label>
                                    <input type="time" name="jam_selesai" value="<?php echo $data_a['jam_selesai'] ?? ''; ?>" class="w-full p-3 rounded-xl border-2 border-white focus:border-indigo-600 outline-none text-xs font-bold shadow-sm" required>
                                </div>
                            </div>
                            <div>
                                <label class="text-[9px] font-black text-slate-400 uppercase ml-1">Nominal SPP Diterima (Rp)</label>
                                <input type="number" name="nominal_bayar" value="<?php echo $data_a['nominal_bayar'] ?? ''; ?>" class="w-full p-3 rounded-xl border-2 border-white focus:border-indigo-600 outline-none text-xs font-bold shadow-sm" placeholder="Contoh: 75000" required>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="text-[9px] font-black text-slate-400 uppercase ml-1">Materi Les</label>
                                <textarea name="materi_les" rows="2" class="w-full p-3 rounded-xl border-2 border-white focus:border-indigo-600 outline-none text-xs font-bold shadow-sm" placeholder="Latihan lagu..." required><?php echo $data_a['materi_les'] ?? ''; ?></textarea>
                            </div>
                            <div>
                                <label class="text-[9px] font-black text-slate-400 uppercase ml-1">Refleksi Guru</label>
                                <textarea name="refleksi_guru" rows="2" class="w-full p-3 rounded-xl border-2 border-white focus:border-indigo-600 outline-none text-xs font-bold shadow-sm" placeholder="Perkembangan murid..." required><?php echo $data_a['refleksi_guru'] ?? ''; ?></textarea>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" name="absen" class="flex-1 bg-indigo-600 text-white font-black py-3 rounded-xl uppercase text-[10px] shadow-lg hover:bg-indigo-700 transition">
                                    <i class="fas fa-save mr-2"></i> <?php echo $is_editing ? 'Update Jurnal' : 'Kirim Absen'; ?>
                                </button>
                                <?php if($is_editing): ?>
                                    <a href="index.php" class="bg-slate-200 text-slate-600 px-5 py-3 rounded-xl flex items-center shadow-md"><i class="fas fa-times"></i></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4 border-t pt-6 border-dashed border-slate-200">
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Durasi Belajar</p>
                            <p class="text-xs font-bold text-indigo-700 mt-1 uppercase italic">
                                <?php 
                                    $m = $data_a['jam_mulai'] ?? '';
                                    $s = $data_a['jam_selesai'] ?? '';
                                    echo (!empty($m) && !empty($s)) ? date('H:i', strtotime($m))." - ".date('H:i', strtotime($s))." WIB" : "Waktu belum diisi";
                                ?>
                            </p>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Materi</p>
                            <p class="text-xs font-bold text-slate-700 leading-relaxed mt-1 italic uppercase">
                                <?php echo htmlspecialchars($data_a['materi_les'] ?? 'Belum ada materi'); ?>
                            </p>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Refleksi Guru</p>
                            <p class="text-xs italic text-slate-500 leading-relaxed mt-1">
                                "<?php echo htmlspecialchars($data_a['refleksi_guru'] ?? 'Belum ada catatan refleksi'); ?>"
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
