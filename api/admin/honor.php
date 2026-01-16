<?php
// --- 1. MESIN LOGOUT INTERNAL ---
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    setcookie('user_role', '', time() - 3600, '/');
    setcookie('user_id', '', time() - 3600, '/');
    setcookie('user_username', '', time() - 3600, '/');
    header("Location: ../../index.php"); // Logout tetap lempar ke Landing Page paling luar
    exit();
}

session_start();
ob_start();

// 2. PROTEKSI HALAMAN
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'admin') {
    header("Location: login.php"); exit();
}

require_once(__DIR__ . '/../koneksi.php');

// ==========================================
// 3. PROSES DATA (SIMPAN / HAPUS / RESET)
// ==========================================

// Reset Data
if (isset($_POST['reset_total'])) {
    mysqli_query($conn, "DELETE FROM absensi");
    mysqli_query($conn, "DELETE FROM keuangan");
    header("Location: honor.php"); exit();
}

// Simpan Transaksi Manual
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

// Hapus Transaksi
if (isset($_GET['hapus'])) {
    $id_h = mysqli_real_escape_string($conn, $_GET['hapus']);
    mysqli_query($conn, "DELETE FROM keuangan WHERE id = '$id_h'");
    header("Location: honor.php"); exit();
}

// ==========================================
// 4. PERHITUNGAN SALDO (ANTI-GANJIL)
// ==========================================
$q_spp = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal_bayar) as t FROM absensi"));
$q_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as t FROM keuangan WHERE jenis='masuk' AND status_konfirmasi = 1"));
$q_keluar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as t FROM keuangan WHERE jenis='keluar' AND status_konfirmasi = 1"));

$saldo_akhir = ((int)($q_spp['t'] ?? 0) + (int)($q_masuk['t'] ?? 0)) - (int)($q_keluar['t'] ?? 0);

// Data Honor Guru (50%)
$list_guru = mysqli_query($conn, "SELECT u.id, u.username, FLOOR(IFNULL((SELECT SUM(a.nominal_bayar) FROM absensi a JOIN jadwal j ON a.id_jadwal = j.id WHERE j.id_guru = u.id), 0) * 0.5) as hak FROM users u WHERE u.role = 'guru'");

// Inisialisasi Data Edit
$edit_data = ['id' => '', 'tanggal' => date('Y-m-d'), 'jenis' => 'keluar', 'nama_pelaku' => '', 'keterangan' => '', 'nominal' => 0];
if (isset($_GET['edit'])) {
    $id_e = mysqli_real_escape_string($conn, $_GET['edit']);
    $res_e = mysqli_query($conn, "SELECT * FROM keuangan WHERE id = '$id_e'");
    if($res_e && mysqli_num_rows($res_e) > 0) $edit_data = mysqli_fetch_assoc($res_e);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Admin - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen pb-20">

    <nav class="bg-red-600 shadow-xl px-6 py-4 flex justify-between items-center mb-10 sticky top-0 z-50 border-b-4 border-yellow-400">
        <div class="flex items-center gap-3">
            <a href="index.php" class="text-white bg-red-700 w-10 h-10 rounded-xl flex items-center justify-center hover:bg-red-800 transition shadow-inner">
                <i class="fas fa-home"></i>
            </a>
            <div class="flex items-center gap-2">
                <img src="../logo.png" class="h-10 w-auto bg-white rounded-lg p-1 shadow-sm" onerror="this.src='https://ui-avatars.com/api/?name=SA&background=ffffff&color=dc2626'">
                <div>
                    <h1 class="text-white font-black text-lg italic tracking-tighter leading-none uppercase">Admin Finance</h1>
                    <p class="text-[8px] text-yellow-300 font-bold uppercase tracking-widest mt-1">Smart Arca System</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="bg-yellow-400 text-red-700 px-5 py-2 rounded-2xl font-black shadow-lg border-2 border-red-700">
                Rp <?php echo number_format($saldo_akhir, 0, ',', '.'); ?>
            </div>
            <a href="honor.php?action=logout" class="text-white hover:text-yellow-300 text-xl transition transform active:scale-90">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-[2.5rem] shadow-xl border-t-8 border-red-600">
                <h3 class="font-black text-slate-400 text-[10px] uppercase tracking-[0.2em] mb-6 border-b pb-2 italic text-center">Honor Guru (50%)</h3>
                <div class="space-y-4">
                    <?php while($g = mysqli_fetch_assoc($list_guru)): ?>
                    <div class="flex justify-between items-center border-b border-dashed border-slate-100 pb-3 last:border-0 last:pb-0">
                        <div>
                            <p class="font-black text-xs text-slate-700 uppercase leading-none mb-1"><?php echo htmlspecialchars($g['username']); ?></p>
                            <p class="text-[10px] text-green-600 font-bold">Rp <?php echo number_format($g['hak'], 0, ',', '.'); ?></p>
                        </div>
                        <button onclick="document.getElementById('nama_pelaku').value='<?php echo $g['username']; ?>'; document.getElementById('nominal').value='<?php echo $g['hak']; ?>'; document.getElementById('keterangan').value='Bayar Honor Guru'; document.getElementById('nominal').focus();" class="bg-red-50 text-red-600 px-3 py-1.5 rounded-xl text-[10px] font-black hover:bg-red-600 hover:text-white transition shadow-sm">BAYAR</button>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <form method="POST" onsubmit="return confirm('Hapus semua riwayat?')">
                <button type="submit" name="reset_total" class="w-full bg-slate-900 text-white py-4 rounded-[2rem] text-[10px] font-bold uppercase tracking-widest hover:bg-black shadow-xl">
                    <i class="fas fa-trash-alt mr-2"></i> Reset Keuangan
                </button>
            </form>
        </div>

        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border-t-8 border-yellow-400" id="formArea">
                <h3 class="font-black text-slate-800 text-sm uppercase italic tracking-widest mb-6 border-l-4 border-red-600 pl-3 leading-none">Input Transaksi</h3>
                <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="hidden" name="id_edit" value="<?php echo $edit_data['id']; ?>">
                    
                    <select name="jenis" class="w-full p-3 border-2 border-slate-50 rounded-xl bg-slate-50 font-bold text-xs focus:border-red-600 outline-none transition">
                        <option value="keluar" <?php echo ($edit_data['jenis'] == 'keluar') ? 'selected' : ''; ?>>🔴 PENGELUARAN</option>
                        <option value="masuk" <?php echo ($edit_data['jenis'] == 'masuk') ? 'selected' : ''; ?>>🟢 PEMASUKAN</option>
                    </select>

                    <input type="text" name="nama_pelaku" id="nama_pelaku" value="<?php echo htmlspecialchars($edit_data['nama_pelaku']); ?>" class="w-full p-3 border-2 border-slate-50 rounded-xl bg-slate-50 text-sm font-bold focus:border-red-600 outline-none" placeholder="Nama..." required>

                    <input type="text" name="keterangan" id="keterangan" value="<?php echo htmlspecialchars($edit_data['keterangan']); ?>" class="w-full p-3 border-2 border-slate-50 rounded-xl bg-slate-50 text-sm font-bold focus:border-red-600 outline-none md:col-span-2" placeholder="Keterangan..." required>

                    <input type="number" name="nominal" id="nominal" value="<?php echo (int)$edit_data['nominal']; ?>" class="w-full p-4 border-2 border-slate-50 rounded-2xl bg-slate-50 font-black text-3xl text-red-600 focus:border-red-600 outline-none md:col-span-2" required>

                    <button type="submit" name="simpan_transaksi" class="md:col-span-2 bg-red-600 hover:bg-red-700 text-yellow-400 font-black py-4 rounded-2xl shadow-xl transition uppercase text-xs tracking-widest">Simpan Transaksi</button>
                </form>
            </div>

            <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-slate-400 text-[9px] uppercase font-black border-b tracking-tighter">
                            <tr>
                                <th class="p-5">Tgl</th>
                                <th class="p-5">Pelaku & Ket</th>
                                <th class="p-5 text-right">Nominal</th>
                                <th class="p-5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php 
                            $sql_riwayat = "(SELECT id, tanggal, nama_pelaku, keterangan, jenis, nominal, 'manual' as tipe FROM keuangan WHERE status_konfirmasi = 1) UNION (SELECT a.id, a.tanggal, u.username, CONCAT('SPP: ', j.alat_musik), 'masuk', a.nominal_bayar, 'auto' FROM absensi a JOIN jadwal j ON a.id_jadwal = j.id JOIN users u ON j.id_murid = u.id) ORDER BY tanggal DESC LIMIT 50";
                            $res_riwayat = mysqli_query($conn, $sql_riwayat);
                            while($r = mysqli_fetch_assoc($res_riwayat)): 
                                $is_masuk = ($r['jenis'] == 'masuk'); ?>
                            <tr class="hover:bg-slate-50 transition group">
                                <td class="p-5 text-slate-400 font-bold text-[10px]"><?php echo date('d/m/y', strtotime($r['tanggal'])); ?></td>
                                <td class="p-5">
                                    <p class="font-black text-slate-800 uppercase text-xs leading-none mb-1"><?php echo htmlspecialchars($r['nama_pelaku']); ?></p>
                                    <p class="text-[9px] text-slate-400 font-bold uppercase italic tracking-tighter"><?php echo htmlspecialchars($r['keterangan']); ?></p>
                                </td>
                                <td class="p-5 text-right font-black <?php echo $is_masuk ? 'text-green-600' : 'text-red-600'; ?> text-base">
                                    <?php echo ($is_masuk ? '+' : '-') . number_format((int)$r['nominal'], 0, ',', '.'); ?>
                                </td>
                                <td class="p-5 text-center">
                                    <?php if($r['tipe'] == 'manual'): ?>
                                    <div class="flex justify-center gap-2">
                                        <a href="honor.php?edit=<?php echo $r['id']; ?>#formArea" class="text-yellow-500 hover:scale-110 transition"><i class="fas fa-edit"></i></a>
                                        <a href="honor.php?hapus=<?php echo $r['id']; ?>" onclick="return confirm('Hapus?')" class="text-red-300 hover:text-red-600 hover:scale-110 transition"><i class="fas fa-trash"></i></a>
                                    </div>
                                    <?php else: ?>
                                        <span class="text-[9px] bg-green-50 text-green-500 px-3 py-1 rounded-full font-black border border-green-100">SPP</span>
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
