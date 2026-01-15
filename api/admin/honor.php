<?php
session_start();
ob_start();

// 1. KEAMANAN LOGIN
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'admin') {
    header("Location: login.php"); exit();
}

require_once(__DIR__ . '/../koneksi.php');

// ==========================================
// 🛠️ AUTO-FIX: MEMBERSIHKAN SALDO GANJIL (1 PERAK)
// Menghapus nilai desimal agar 49.999 kembali jadi 50.000
// ==========================================
mysqli_query($conn, "UPDATE absensi SET nominal_bayar = ROUND(nominal_bayar)");
mysqli_query($conn, "UPDATE keuangan SET nominal = ROUND(nominal)");

// ==========================================
// 2. PROSES RESET DATA (JIKA INGIN SALDO 0)
// ==========================================
if (isset($_POST['reset_total'])) {
    mysqli_query($conn, "DELETE FROM absensi");
    mysqli_query($conn, "DELETE FROM keuangan");
    header("Location: honor.php"); exit();
}

// ==========================================
// 3. PROSES AKSI (HAPUS / KONFIRMASI)
// ==========================================
if (isset($_GET['hapus'])) {
    $id_h = mysqli_real_escape_string($conn, $_GET['hapus']);
    mysqli_query($conn, "DELETE FROM keuangan WHERE id = '$id_h'");
    header("Location: honor.php"); exit();
}

if (isset($_GET['konfirmasi_cair'])) {
    $id_c = mysqli_real_escape_string($conn, $_GET['konfirmasi_cair']);
    mysqli_query($conn, "UPDATE keuangan SET status_konfirmasi = 1, tanggal = '".date('Y-m-d')."' WHERE id = '$id_c'");
    header("Location: honor.php"); exit();
}

// SIMPAN TRANSAKSI MANUAL
if (isset($_POST['simpan_transaksi'])) {
    $id_edit = $_POST['id_edit'];
    $tgl = $_POST['tanggal'];
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
// 4. INISIALISASI DATA EDIT (ANTI-WARNING)
// ==========================================
$edit_data = [
    'id' => '', 
    'tanggal' => date('Y-m-d'), 
    'jenis' => 'keluar', 
    'nama_pelaku' => '', 
    'keterangan' => '', 
    'nominal' => 0
];

if (isset($_GET['edit'])) {
    $id_e = mysqli_real_escape_string($conn, $_GET['edit']);
    $q_edit = mysqli_query($conn, "SELECT * FROM keuangan WHERE id = '$id_e'");
    if($q_edit && mysqli_num_rows($q_edit) > 0) {
        $edit_data = mysqli_fetch_assoc($q_edit);
    }
}

// ==========================================
// 5. PERHITUNGAN SALDO (FORCE INTEGER)
// ==========================================
$total_spp = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal_bayar) as t FROM absensi"))['t'];
$total_masuk = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as t FROM keuangan WHERE jenis='masuk' AND status_konfirmasi = 1"))['t'];
$total_keluar = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as t FROM keuangan WHERE jenis='keluar' AND status_konfirmasi = 1"))['t'];
$saldo_akhir = ($total_spp + $total_masuk) - $total_keluar;

// DATA GURU
$list_guru = mysqli_query($conn, "SELECT u.id, u.username, FLOOR(IFNULL((SELECT SUM(a.nominal_bayar) FROM absensi a JOIN jadwal j ON a.id_jadwal = j.id WHERE j.id_guru = u.id), 0) * 0.5) as hak FROM users u WHERE u.role = 'guru'");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Smart Arca Finance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-50 min-h-screen pb-10">
    <nav class="bg-red-600 shadow-lg px-6 py-4 flex justify-between items-center mb-8 border-b-4 border-yellow-400">
        <div class="flex items-center gap-3">
            <a href="index.php" class="text-white text-xl"><i class="fas fa-chevron-left"></i></a>
            <h1 class="text-white font-black text-xl italic tracking-tighter uppercase">Smart Arca Finance</h1>
        </div>
        <div class="bg-yellow-400 text-red-700 px-5 py-2 rounded-2xl font-black border-2 border-red-700 shadow-xl scale-110">
            Rp <?php echo number_format($saldo_akhir, 0, ',', '.'); ?>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-[2.5rem] shadow-xl border-t-8 border-red-600">
                <h3 class="font-black text-xs uppercase tracking-widest border-b-2 border-yellow-400 pb-2 mb-4 text-slate-400">Hak Honor Guru</h3>
                <?php while($g = mysqli_fetch_assoc($list_guru)): ?>
                <div class="flex justify-between items-center py-3 border-b border-dashed last:border-0">
                    <div>
                        <p class="font-black text-sm uppercase text-slate-700"><?php echo $g['username']; ?></p>
                        <p class="text-[10px] text-green-600 font-bold uppercase">Saldo: Rp <?php echo number_format($g['hak'], 0, ',', '.'); ?></p>
                    </div>
                    <button onclick="document.getElementById('nama_pelaku').value='<?php echo $g['username']; ?>'; document.getElementById('nominal').value='<?php echo $g['hak']; ?>'; document.getElementById('keterangan').value='Bayar Honor'; document.getElementById('nominal').focus();" class="bg-red-600 text-yellow-400 px-3 py-1 rounded-lg text-[10px] font-black shadow-md hover:scale-105 transition">BAYAR</button>
                </div>
                <?php endwhile; ?>
            </div>

            <form method="POST" onsubmit="return confirm('Hapus semua data keuangan untuk reset ke Rp 0?')">
                <button type="submit" name="reset_total" class="w-full bg-slate-800 text-white py-4 rounded-2xl text-[10px] font-bold uppercase tracking-widest hover:bg-black transition shadow-lg">
                    <i class="fas fa-trash-alt mr-2"></i> Reset Semua Data Keuangan
                </button>
            </form>
        </div>

        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border-t-8 border-yellow-400" id="formArea">
                <h3 class="font-black text-xs uppercase tracking-widest mb-6 text-slate-400">Tambah Transaksi Manual</h3>
                <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="hidden" name="id_edit" value="<?php echo $edit_data['id']; ?>">
                    
                    <div class="md:col-span-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">Jenis</label>
                        <select name="jenis" class="w-full p-3 border-2 rounded-xl bg-slate-50 font-black text-xs outline-none focus:border-red-600 transition">
                            <option value="keluar" <?php echo ($edit_data['jenis'] == 'keluar') ? 'selected' : ''; ?>>🔴 PENGELUARAN</option>
                            <option value="masuk" <?php echo ($edit_data['jenis'] == 'masuk') ? 'selected' : ''; ?>>🟢 PEMASUKAN</option>
                        </select>
                    </div>

                    <div class="md:col-span-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">Nama Pelaku</label>
                        <input type="text" name="nama_pelaku" id="nama_pelaku" value="<?php echo htmlspecialchars($edit_data['nama_pelaku']); ?>" placeholder="Nama Pelaku" class="w-full p-3 border-2 rounded-xl text-sm outline-none focus:border-red-600 transition" required>
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">Keterangan</label>
                        <input type="text" name="keterangan" id="keterangan" value="<?php echo htmlspecialchars($edit_data['keterangan']); ?>" placeholder="Keterangan" class="w-full p-3 border-2 rounded-xl text-sm outline-none focus:border-red-600 transition" required>
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">Nominal (Rp)</label>
                        <input type="number" name="nominal" id="nominal" value="<?php echo (int)$edit_data['nominal']; ?>" class="w-full p-4 border-2 rounded-xl font-black text-3xl text-red-600 outline-none focus:border-red-600 transition" required>
                    </div>

                    <input type="hidden" name="tanggal" value="<?php echo $edit_data['tanggal']; ?>">
                    <button type="submit" name="simpan_transaksi" class="md:col-span-2 bg-red-600 hover:bg-red-700 text-yellow-400 font-black py-4 rounded-2xl shadow-xl uppercase transition active:scale-95">Simpan Transaksi</button>
                </form>
            </div>

            <div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border">
                <div class="p-6 bg-slate-50 border-b font-black text-sm uppercase italic tracking-widest text-slate-400">Arus Kas Terakhir</div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-[10px] uppercase font-black border-b text-slate-400">
                            <tr><th class="p-5">Tgl</th><th class="p-5">Keterangan</th><th class="p-5 text-right">Nominal</th><th class="p-5 text-center">Aksi</th></tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php 
                            $sql_riwayat = "
                                (SELECT id, tanggal, nama_pelaku, keterangan, jenis, nominal, 'manual' as tipe FROM keuangan WHERE status_konfirmasi = 1)
                                UNION
                                (SELECT a.id, a.tanggal, u.username, CONCAT('SPP: ', j.alat_musik), 'masuk', a.nominal_bayar, 'auto' FROM absensi a JOIN jadwal j ON a.id_jadwal = j.id JOIN users u ON j.id_murid = u.id)
                                ORDER BY tanggal DESC, id DESC LIMIT 50";
                            $res_riwayat = mysqli_query($conn, $sql_riwayat);
                            while($r = mysqli_fetch_assoc($res_riwayat)): 
                                $is_masuk = ($r['jenis'] == 'masuk'); ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-5 text-xs text-slate-400 font-bold"><?php echo date('d/m', strtotime($r['tanggal'])); ?></td>
                                <td class="p-5">
                                    <p class="font-black text-xs uppercase text-slate-700"><?php echo htmlspecialchars($r['nama_pelaku']); ?></p>
                                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter"><?php echo htmlspecialchars($r['keterangan']); ?> <?php if($r['tipe']=='auto') echo '<span class="bg-green-100 text-green-600 px-1 rounded text-[8px]">SPP</span>'; ?></p>
                                </td>
                                <td class="p-5 text-right font-black <?php echo $is_masuk ? 'text-green-600' : 'text-red-600'; ?>">
                                    <?php echo ($is_masuk ? '+' : '-') . number_format((int)$r['nominal'], 0, ',', '.'); ?>
                                </td>
                                <td class="p-5 text-center">
                                    <?php if($r['tipe'] == 'manual'): ?>
                                        <div class="flex justify-center gap-2">
                                            <a href="honor.php?edit=<?php echo $r['id']; ?>#formArea" class="text-yellow-500"><i class="fas fa-edit"></i></a>
                                            <a href="honor.php?hapus=<?php echo $r['id']; ?>" class="text-red-300 hover:text-red-600"><i class="fas fa-trash"></i></a>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-[8px] text-slate-300 font-black uppercase tracking-widest">System</span>
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
</body>
</html>
