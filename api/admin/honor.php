<?php
// 1. LOGOUT INTERNAL
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    setcookie('user_role', '', time() - 3600, '/');
    setcookie('user_id', '', time() - 3600, '/');
    header("Location: /index.php");
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
    $nom = (int)$_POST['nominal']; // Pastikan bulat
    
    if (!empty($id_edit)) {
        $sql = "UPDATE keuangan SET tanggal='$tgl', nama_pelaku='$nama', keterangan='$ket', jenis='$tip', nominal='$nom' WHERE id='$id_edit'";
    } else {
        $sql = "INSERT INTO keuangan (tanggal, nama_pelaku, keterangan, jenis, nominal, status_konfirmasi) VALUES ('$tgl', '$nama', '$ket', '$tip', '$nom', 1)";
    }
    mysqli_query($conn, $sql);
    header("Location: honor.php"); exit();
}

// Hapus
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

$saldo_akhir = ((int)$q_spp['t'] + (int)$q_masuk['t']) - (int)$q_keluar['t'];

// Data Honor Guru
$list_guru = mysqli_query($conn, "SELECT u.id, u.username, FLOOR(IFNULL((SELECT SUM(a.nominal_bayar) FROM absensi a JOIN jadwal j ON a.id_jadwal = j.id WHERE j.id_guru = u.id), 0) * 0.5) as hak FROM users u WHERE u.role = 'guru'");

// Inisialisasi Edit
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
    <title>Finance Admin - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-50 min-h-screen pb-20">

    <nav class="bg-red-600 shadow-xl px-6 py-4 flex justify-between items-center mb-10 border-b-4 border-yellow-400">
        <div class="flex items-center gap-3">
            <a href="/index.php" class="text-white text-xl"><i class="fas fa-home"></i></a>
            <h1 class="text-white font-black text-xl italic uppercase">Finance Admin</h1>
        </div>
        <div class="flex items-center gap-4">
            <div class="bg-yellow-400 text-red-700 px-5 py-2 rounded-2xl font-black shadow-md">
                Rp <?php echo number_format($saldo_akhir, 0, ',', '.'); ?>
            </div>
            <a href="honor.php?action=logout" class="text-white hover:text-yellow-300 text-xl"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-[2rem] shadow-xl border-t-8 border-red-600">
                <h3 class="font-black text-slate-400 text-[10px] uppercase mb-6 border-b pb-2">Honor Guru (50%)</h3>
                <?php while($g = mysqli_fetch_assoc($list_guru)): ?>
                <div class="flex justify-between items-center mb-4 pb-2 border-b border-dashed border-slate-100 last:border-0">
                    <div>
                        <p class="font-black text-xs uppercase"><?php echo $g['username']; ?></p>
                        <p class="text-[10px] text-green-600 font-bold">Rp <?php echo number_format($g['hak']); ?></p>
                    </div>
                    <button onclick="document.getElementById('nama_pelaku').value='<?php echo $g['username']; ?>'; document.getElementById('nominal').value='<?php echo $g['hak']; ?>'; document.getElementById('keterangan').value='Bayar Honor';" class="bg-red-50 text-red-600 px-3 py-1 rounded-lg text-[10px] font-black">BAYAR</button>
                </div>
                <?php endwhile; ?>
            </div>

            <form method="POST" onsubmit="return confirm('Reset semua data?')">
                <button type="submit" name="reset_total" class="w-full bg-slate-800 text-white py-4 rounded-2xl text-[10px] font-bold uppercase"><i class="fas fa-trash mr-2"></i> Reset Keuangan</button>
            </form>
        </div>

        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white p-8 rounded-[2rem] shadow-xl border-t-8 border-yellow-400">
                <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="hidden" name="id_edit" value="<?php echo $edit_data['id']; ?>">
                    <select name="jenis" class="p-3 border-2 rounded-xl bg-slate-50 font-bold text-xs">
                        <option value="keluar" <?php echo ($edit_data['jenis'] == 'keluar') ? 'selected' : ''; ?>>🔴 PENGELUARAN</option>
                        <option value="masuk" <?php echo ($edit_data['jenis'] == 'masuk') ? 'selected' : ''; ?>>🟢 PEMASUKAN</option>
                    </select>
                    <input type="text" name="nama_pelaku" id="nama_pelaku" value="<?php echo $edit_data['nama_pelaku']; ?>" class="p-3 border-2 rounded-xl text-sm" placeholder="Nama" required>
                    <input type="text" name="keterangan" id="keterangan" value="<?php echo $edit_data['keterangan']; ?>" class="p-3 border-2 rounded-xl text-sm md:col-span-2" placeholder="Keterangan" required>
                    <input type="number" name="nominal" id="nominal" value="<?php echo (int)$edit_data['nominal']; ?>" class="p-4 border-2 rounded-2xl font-black text-3xl text-red-600 md:col-span-2" required>
                    <button type="submit" name="simpan_transaksi" class="md:col-span-2 bg-red-600 text-yellow-400 font-black py-4 rounded-2xl shadow-xl uppercase">Simpan Transaksi</button>
                </form>
            </div>

            <div class="bg-white rounded-[2rem] shadow-xl overflow-hidden border">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-[10px] uppercase font-black border-b">
                            <tr><th class="p-5">Tanggal</th><th class="p-5">Pelaku & Ket</th><th class="p-5 text-right">Nominal</th><th class="p-5 text-center">Aksi</th></tr>
                        </thead>
                        <tbody class="divide-y">
                            <?php 
                            $sql = "(SELECT id, tanggal, nama_pelaku, keterangan, jenis, nominal, 'manual' as tipe FROM keuangan WHERE status_konfirmasi = 1) UNION (SELECT a.id, a.tanggal, u.username, CONCAT('SPP: ', j.alat_musik), 'masuk', a.nominal_bayar, 'auto' FROM absensi a JOIN jadwal j ON a.id_jadwal = j.id JOIN users u ON j.id_murid = u.id) ORDER BY tanggal DESC LIMIT 50";
                            $res = mysqli_query($conn, $sql);
                            while($r = mysqli_fetch_assoc($res)): $is_masuk = ($r['jenis'] == 'masuk'); ?>
                            <tr>
                                <td class="p-5 text-[10px] font-bold text-slate-400"><?php echo date('d/m/y', strtotime($r['tanggal'])); ?></td>
                                <td class="p-5">
                                    <p class="font-black text-xs uppercase"><?php echo $r['nama_pelaku']; ?></p>
                                    <p class="text-[9px] text-slate-400 italic"><?php echo $r['keterangan']; ?></p>
                                </td>
                                <td class="p-5 text-right font-black <?php echo $is_masuk ? 'text-green-600' : 'text-red-600'; ?>"><?php echo ($is_masuk ? '+' : '-') . number_format((int)$r['nominal'], 0, ',', '.'); ?></td>
                                <td class="p-5 text-center">
                                    <?php if($r['tipe'] == 'manual'): ?>
                                    <a href="honor.php?hapus=<?php echo $r['id']; ?>" class="text-red-300 hover:text-red-600"><i class="fas fa-trash"></i></a>
                                    <?php else: ?><span class="text-[8px] bg-green-50 text-green-500 px-2 rounded-full font-bold">SPP</span><?php endif; ?>
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
