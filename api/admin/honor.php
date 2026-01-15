<?php
session_start();
ob_start();

// 1. KEAMANAN AKSES
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'admin') {
    header("Location: login.php"); exit();
}

require_once(__DIR__ . '/../koneksi.php');

// ==========================================
// 🛠️ AUTO-FIX & RESET LOGIC
// ==========================================

// Reset Total Data Keuangan
if (isset($_POST['reset_total'])) {
    mysqli_query($conn, "DELETE FROM absensi");
    mysqli_query($conn, "DELETE FROM keuangan");
    header("Location: honor.php"); exit();
}

// Konfirmasi Pencairan Guru
if (isset($_GET['konfirmasi_cair'])) {
    $id_c = mysqli_real_escape_string($conn, $_GET['konfirmasi_cair']);
    $tgl_skrg = date('Y-m-d');
    mysqli_query($conn, "UPDATE keuangan SET status_konfirmasi = 1, tanggal = '$tgl_skrg' WHERE id = '$id_c'");
    header("Location: honor.php"); exit();
}

// Hapus Transaksi Manual
if (isset($_GET['hapus'])) {
    $id_h = mysqli_real_escape_string($conn, $_GET['hapus']);
    mysqli_query($conn, "DELETE FROM keuangan WHERE id = '$id_h'");
    header("Location: honor.php"); exit();
}

// Simpan/Update Transaksi
if (isset($_POST['simpan_transaksi'])) {
    $id_edit = $_POST['id_edit'];
    $tgl = $_POST['tanggal'] ?: date('Y-m-d');
    $nama = mysqli_real_escape_string($conn, $_POST['nama_pelaku']); 
    $ket = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $tip = $_POST['jenis']; 
    $nom = (int)$_POST['nominal'];
    
    if (!empty($id_edit)) {
        $sql = "UPDATE keuangan SET tanggal='$tgl', nama_pelaku='$nama', keterangan='$ket', jenis='$tip', nominal='$nom' WHERE id='$id_edit'";
    } else {
        $sql = "INSERT INTO keuangan (tanggal, nama_pelaku, keterangan, jenis, nominal, status_konfirmasi) VALUES ('$tgl', '$nama', '$ket', '$tip', '$nom', 1)";
    }
    mysqli_query($conn, $sql);
    header("Location: honor.php"); exit();
}

// ==========================================
// 2. DATA BINDING & CALCULATIONS
// ==========================================

// Data untuk Form Edit (Anti-Warning)
$edit_data = ['id' => '', 'tanggal' => date('Y-m-d'), 'jenis' => 'keluar', 'nama_pelaku' => '', 'keterangan' => '', 'nominal' => 0];
if (isset($_GET['edit'])) {
    $id_e = mysqli_real_escape_string($conn, $_GET['edit']);
    $q_edit = mysqli_query($conn, "SELECT * FROM keuangan WHERE id = '$id_e'");
    if($q_edit && mysqli_num_rows($q_edit) > 0) { $edit_data = mysqli_fetch_assoc($q_edit); }
}

// Hitung Saldo Akhir (Force Integer to avoid 1-Rupiah error)
$total_spp = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal_bayar) as t FROM absensi"))['t'];
$total_masuk = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as t FROM keuangan WHERE jenis='masuk' AND status_konfirmasi = 1"))['t'];
$total_keluar = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as t FROM keuangan WHERE jenis='keluar' AND status_konfirmasi = 1"))['t'];
$saldo_akhir = ($total_spp + $total_masuk) - $total_keluar;

// List Hak Guru (50%)
$list_guru = mysqli_query($conn, "SELECT u.id, u.username, FLOOR(IFNULL((SELECT SUM(a.nominal_bayar) FROM absensi a JOIN jadwal j ON a.id_jadwal = j.id WHERE j.id_guru = u.id), 0) * 0.5) as hak FROM users u WHERE u.role = 'guru'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keuangan - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen pb-20">

    <nav class="bg-red-600 shadow-xl px-6 py-4 flex justify-between items-center mb-10 sticky top-0 z-50 border-b-4 border-yellow-400">
        <div class="flex items-center gap-3">
            <a href="index.php" class="text-white bg-red-700 w-10 h-10 rounded-xl flex items-center justify-center hover:bg-red-800 transition"><i class="fas fa-chevron-left"></i></a>
            <div>
                <h1 class="text-white font-black text-xl italic tracking-tighter leading-none">SMART ARCA</h1>
                <p class="text-[9px] text-yellow-300 font-bold uppercase tracking-widest mt-1">Finance System</p>
            </div>
        </div>
        <div class="bg-yellow-400 text-red-700 px-6 py-2.5 rounded-2xl font-black shadow-lg border-2 border-red-700 scale-105">
            Rp <?php echo number_format($saldo_akhir, 0, ',', '.'); ?>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="space-y-6">
            <?php 
            $q_pending = mysqli_query($conn, "SELECT * FROM keuangan WHERE status_konfirmasi = 0 ORDER BY tanggal ASC");
            if(mysqli_num_rows($q_pending) > 0): 
            ?>
            <div class="bg-white border-2 border-red-600 p-5 rounded-[2rem] shadow-xl animate-pulse">
                <h3 class="text-red-600 font-black text-xs uppercase mb-4 flex items-center gap-2"><i class="fas fa-clock"></i> Butuh Konfirmasi</h3>
                <?php while($p = mysqli_fetch_assoc($q_pending)): ?>
                <div class="bg-red-50 p-4 rounded-2xl flex justify-between items-center mb-2 last:mb-0">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase"><?php echo $p['nama_pelaku']; ?></p>
                        <p class="font-black text-slate-800">Rp <?php echo number_format($p['nominal']); ?></p>
                    </div>
                    <a href="honor.php?konfirmasi_cair=<?php echo $p['id']; ?>" class="bg-red-600 text-yellow-400 px-3 py-1.5 rounded-lg text-[10px] font-black shadow-md">CAIRKAN</a>
                </div>
                <?php endwhile; ?>
            </div>
            <?php endif; ?>

            <div class="bg-white p-6 rounded-[2.5rem] shadow-xl border-t-8 border-red-600">
                <h3 class="font-black text-slate-400 text-[10px] uppercase tracking-[0.2em] mb-6 border-b pb-2">Hak Honor Guru (50%)</h3>
                <div class="space-y-4">
                    <?php while($g = mysqli_fetch_assoc($list_guru)): ?>
                    <div class="flex justify-between items-center border-b border-dashed border-slate-100 pb-3 last:border-0">
                        <div>
                            <p class="font-black text-xs text-slate-700 uppercase"><?php echo htmlspecialchars($g['username']); ?></p>
                            <p class="text-[10px] text-green-600 font-bold tracking-tighter uppercase">Rp <?php echo number_format($g['hak']); ?></p>
                        </div>
                        <button onclick="document.getElementById('nama_pelaku').value='<?php echo $g['username']; ?>'; document.getElementById('nominal').value='<?php echo $g['hak']; ?>'; document.getElementById('keterangan').value='Pembayaran Honor'; document.getElementById('nominal').focus();" class="bg-red-50 text-red-600 px-3 py-1 rounded-lg text-[10px] font-black hover:bg-red-600 hover:text-white transition">BAYAR</button>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <form method="POST" onsubmit="return confirm('PERINGATAN! Ini akan menghapus SEMUA riwayat keuangan & absensi. Saldo akan kembali ke 0. Lanjutkan?')">
                <button type="submit" name="reset_total" class="w-full bg-slate-900 text-white py-4 rounded-2xl text-[10px] font-bold uppercase tracking-widest hover:bg-black transition shadow-lg">
                    <i class="fas fa-trash-alt mr-2"></i> Reset Keuangan ke 0
                </button>
            </form>
        </div>

        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border-t-8 border-yellow-400" id="formArea">
                <h3 class="font-black text-slate-800 text-sm uppercase italic tracking-widest mb-6 border-l-4 border-red-600 pl-3">Tambah Transaksi</h3>
                <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="hidden" name="id_edit" value="<?php echo $edit_data['id']; ?>">
                    <div class="md:col-span-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase mb-1 ml-1">Jenis Kas</label>
                        <select name="jenis" class="w-full p-3 border-2 border-slate-50 rounded-xl bg-slate-50 font-bold text-xs focus:border-red-600 outline-none transition">
                            <option value="keluar" <?php echo ($edit_data['jenis'] == 'keluar') ? 'selected' : ''; ?>>🔴 PENGELUARAN</option>
                            <option value="masuk" <?php echo ($edit_data['jenis'] == 'masuk') ? 'selected' : ''; ?>>🟢 PEMASUKAN</option>
                        </select>
                    </div>
                    <div class="md:col-span-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase mb-1 ml-1">Nama Pelaku</label>
                        <input type="text" name="nama_pelaku" id="nama_pelaku" value="<?php echo htmlspecialchars($edit_data['nama_pelaku']); ?>" class="w-full p-3 border-2 border-slate-50 rounded-xl bg-slate-50 text-sm focus:border-red-600 outline-none" placeholder="Contoh: Toko Musik / Guru" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase mb-1 ml-1">Keterangan Transaksi</label>
                        <input type="text" name="keterangan" id="keterangan" value="<?php echo htmlspecialchars($edit_data['keterangan']); ?>" class="w-full p-3 border-2 border-slate-50 rounded-xl bg-slate-50 text-sm focus:border-red-600 outline-none" placeholder="Tujuan pembayaran..." required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase mb-1 ml-1">Nominal (Rp)</label>
                        <input type="number" name="nominal" id="nominal" value="<?php echo (int)$edit_data['nominal']; ?>" class="w-full p-4 border-2 border-slate-50 rounded-2xl bg-slate-50 font-black text-3xl text-red-600 focus:border-red-600 outline-none" placeholder="0" required>
                    </div>
                    <input type="hidden" name="tanggal" value="<?php echo $edit_data['tanggal']; ?>">
                    <button type="submit" name="simpan_transaksi" class="md:col-span-2 bg-red-600 hover:bg-red-700 text-yellow-400 font-black py-4 rounded-2xl shadow-xl transition transform active:scale-95 uppercase tracking-[0.2em] text-xs">Simpan Transaksi</button>
                </form>
            </div>

            <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-100">
                <div class="p-6 bg-slate-50 border-b flex justify-between items-center">
                    <h3 class="font-black text-slate-800 uppercase text-xs italic tracking-widest">Arus Kas (Gabungan SPP & Manual)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-slate-400 text-[9px] uppercase font-black border-b">
                            <tr>
                                <th class="p-5">Tanggal</th>
                                <th class="p-5">Keterangan</th>
                                <th class="p-5 text-right">Nominal</th>
                                <th class="p-5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php 
                            $sql_union = "
                                (SELECT id, tanggal, nama_pelaku, keterangan, jenis, nominal, 'manual' as sumber FROM keuangan WHERE status_konfirmasi = 1)
                                UNION
                                (SELECT a.id, a.tanggal, u.username as nama_pelaku, CONCAT('Bayar SPP: ', j.alat_musik) as keterangan, 'masuk' as jenis, a.nominal_bayar as nominal, 'auto' as sumber 
                                 FROM absensi a 
                                 JOIN jadwal j ON a.id_jadwal = j.id 
                                 JOIN users u ON j.id_murid = u.id)
                                ORDER BY tanggal DESC, id DESC LIMIT 50";
                            $res_union = mysqli_query($conn, $sql_union);
                            if(mysqli_num_rows($res_union) == 0) {
                                echo '<tr><td colspan="4" class="p-10 text-center text-slate-300 italic text-xs font-bold uppercase tracking-widest">Belum ada aktivitas kas.</td></tr>';
                            }
                            while($r = mysqli_fetch_assoc($res_union)): 
                                $is_masuk = ($r['jenis'] == 'masuk');
                            ?>
                            <tr class="hover:bg-slate-50 transition group">
                                <td class="p-5 text-slate-400 font-bold text-[10px]"><?php echo date('d/m/Y', strtotime($r['tanggal'])); ?></td>
                                <td class="p-5">
                                    <p class="font-black text-slate-800 uppercase text-xs leading-none mb-1"><?php echo htmlspecialchars($r['nama_pelaku']); ?></p>
                                    <p class="text-[9px] text-slate-400 font-bold uppercase italic"><?php echo htmlspecialchars($r['keterangan']); ?></p>
                                </td>
                                <td class="p-5 text-right font-black <?php echo $is_masuk ? 'text-green-600' : 'text-red-600'; ?> text-base">
                                    <?php echo ($is_masuk ? '+' : '-') . ' ' . number_format((int)$r['nominal'], 0, ',', '.'); ?>
                                </td>
                                <td class="p-5 text-center">
                                    <?php if($r['sumber'] == 'manual'): ?>
                                    <div class="flex justify-center gap-2">
                                        <a href="honor.php?edit=<?php echo $r['id']; ?>#formArea" class="text-yellow-500 hover:text-yellow-600"><i class="fas fa-edit"></i></a>
                                        <a href="honor.php?hapus=<?php echo $r['id']; ?>" onclick="return confirm('Hapus data ini?')" class="text-red-300 hover:text-red-600"><i class="fas fa-trash"></i></a>
                                    </div>
                                    <?php else: ?>
                                        <span class="text-[8px] bg-green-50 text-green-500 px-2 py-0.5 rounded-full font-black tracking-tighter uppercase border border-green-100">SPP Murid</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-12 text-center">
        <p class="text-[10px] text-slate-300 font-black uppercase tracking-[0.4em]">Smart Arca Finance Dashboard</p>
    </div>

</body>
</html>
