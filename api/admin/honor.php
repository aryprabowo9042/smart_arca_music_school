<?php
// --- 1. LOGIKA LOGOUT INTERNAL ---
// Jika ada aksi logout, kita hapus cookie dan lempar ke halaman depan luar
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    setcookie('user_role', '', time() - 3600, '/');
    setcookie('user_id', '', time() - 3600, '/');
    setcookie('user_username', '', time() - 3600, '/');
    header("Location: ../../index.php"); // Keluar dari api/admin/ ke root
    exit();
}

session_start();
ob_start();

// 2. PROTEKSI HALAMAN
// Tetap di folder yang sama (admin), jika tidak ada session/cookie lempar ke login.php
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'admin') {
    header("Location: login.php"); 
    exit();
}

// 3. KONEKSI (Mundur satu langkah ke folder api)
require_once(__DIR__ . '/../koneksi.php');

// ==========================================
// 4. PROSES DATA (SIMPAN / HAPUS)
// ==========================================

// Simpan Transaksi
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
// 5. PERHITUNGAN SALDO (ANTI-GANJIL)
// ==========================================
$q_spp = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal_bayar) as t FROM absensi"));
$q_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as t FROM keuangan WHERE jenis='masuk' AND status_konfirmasi = 1"));
$q_keluar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as t FROM keuangan WHERE jenis='keluar' AND status_konfirmasi = 1"));

$saldo_akhir = ((int)$q_spp['t'] + (int)$q_masuk['t']) - (int)$q_keluar['t'];

// List Honor Guru
$list_guru = mysqli_query($conn, "SELECT u.id, u.username, FLOOR(IFNULL((SELECT SUM(a.nominal_bayar) FROM absensi a JOIN jadwal j ON a.id_jadwal = j.id WHERE j.id_guru = u.id), 0) * 0.5) as hak FROM users u WHERE u.role = 'guru'");

// Data Edit
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
<body class="bg-slate-50 min-h-screen pb-10">

    <nav class="bg-red-600 shadow-xl px-6 py-4 flex justify-between items-center mb-10 border-b-4 border-yellow-400">
        <div class="flex items-center gap-3">
            <a href="../../index.php" class="text-white bg-red-700 w-10 h-10 rounded-xl flex items-center justify-center hover:bg-red-800 transition">
                <i class="fas fa-home"></i>
            </a>
            <img src="../logo.png" class="h-10 w-auto bg-white rounded-lg p-1" onerror="this.src='https://ui-avatars.com/api/?name=SA&background=ffffff&color=dc2626'">
            <h1 class="text-white font-black text-xl italic uppercase tracking-tighter">Finance Admin</h1>
        </div>

        <div class="flex items-center gap-4">
            <div class="bg-yellow-400 text-red-700 px-5 py-2 rounded-2xl font-black shadow-md border-2 border-red-700">
                Rp <?php echo number_format($saldo_akhir, 0, ',', '.'); ?>
            </div>
            <a href="honor.php?action=logout" class="text-white hover:text-yellow-300 text-2xl transition">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-[2rem] shadow-xl border-t-8 border-red-600">
                <h3 class="font-black text-slate-400 text-[10px] uppercase mb-6 border-b pb-2 italic text-center">Estimasi Honor Guru</h3>
                <div class="space-y-4">
                    <?php while($g = mysqli_fetch_assoc($list_guru)): ?>
                    <div class="flex justify-between items-center border-b border-dashed border-slate-100 pb-3 last:border-0">
                        <div>
                            <p class="font-black text-xs uppercase"><?php echo $g['username']; ?></p>
                            <p class="text-[10px] text-green-600 font-bold">Rp <?php echo number_format($g['hak'], 0, ',', '.'); ?></p>
                        </div>
                        <button onclick="document.getElementById('nama_pelaku').value='<?php echo $g['username']; ?>'; document.getElementById('nominal').value='<?php echo $g['hak']; ?>'; document.getElementById('keterangan').value='Bayar Honor';" class="bg-red-50 text-red-600 px-3 py-1 rounded-lg text-[10px] font-black hover:bg-red-600 hover:text-white transition">BAYAR</button>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white p-8 rounded-[2rem] shadow-xl border-t-8 border-yellow-400">
                <form method="POST" class="grid grid-cols-1 gap-4">
                    <input type="hidden" name="id_edit" value="<?php echo $edit_data['id']; ?>">
                    <div class="grid grid-cols-2 gap-4">
                        <select name="jenis" class="p-3 border-2 border-slate-50 rounded-xl bg-slate-50 font-bold text-xs focus:border-red-600 outline-none">
                            <option value="keluar" <?php echo ($edit_data['jenis'] == 'keluar') ? 'selected' : ''; ?>>🔴 PENGELUARAN</option>
                            <option value="masuk" <?php echo ($edit_data['jenis'] == 'masuk') ? 'selected' : ''; ?>>🟢 PEMASUKAN</option>
                        </select>
                        <input type="text" name="nama_pelaku" id="nama_pelaku" value="<?php echo $edit_data['nama_pelaku']; ?>" class="p-3 border-2 border-slate-50 rounded-xl bg-slate-50 text-sm font-bold focus:border-red-600 outline-none" placeholder="Nama..." required>
                    </div>
                    <input type="text" name="keterangan" id="keterangan" value="<?php echo $edit_data['keterangan']; ?>" class="p-3 border-2 border-slate-50 rounded-xl bg-slate-50 text-sm font-bold focus:border-red-600 outline-none" placeholder="Keterangan transaksi..." required>
                    <input type="number" name="nominal" id="nominal" value="<?php echo (int)$edit_data['nominal']; ?>" class="p-4 border-2 border-slate-50 rounded-2xl bg-slate-50 font-black text-3xl text-red-600 focus:border-red-600 outline-none" required>
                    <button type="submit" name="simpan_transaksi" class="bg-red-600 text-yellow-400 font-black py-4 rounded-2xl shadow-xl uppercase transition active:scale-95">Simpan Transaksi</button>
                </form>
            </div>

            <div class="bg-white rounded-[2rem] shadow-xl overflow-hidden border">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-[10px] uppercase font-black border-b text-slate-400">
                        <tr><th class="p-5">Tgl</th><th class="p-5">Ket</th><th class="p-5 text-right">Nominal</th><th class="p-5 text-center">Aksi</th></tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php 
                        $sql_r = "(SELECT id, tanggal, nama_pelaku, keterangan, jenis, nominal, 'manual' as tipe FROM keuangan WHERE status_konfirmasi = 1) UNION (SELECT a.id, a.tanggal, u.username, CONCAT('SPP: ', j.alat_musik), 'masuk', a.nominal_bayar, 'auto' FROM absensi a JOIN jadwal j ON a.id_jadwal = j.id JOIN users u ON j.id_murid = u.id) ORDER BY tanggal DESC LIMIT 20";
                        $res_r = mysqli_query($conn, $sql_r);
                        while($r = mysqli_fetch_assoc($res_r)): $is_masuk = ($r['jenis'] == 'masuk'); ?>
                        <tr class="hover:bg-slate-50">
                            <td class="p-5 text-[10px] font-bold text-slate-400"><?php echo date('d/m', strtotime($r['tanggal'])); ?></td>
                            <td class="p-5">
                                <p class="font-black text-xs uppercase text-slate-800"><?php echo htmlspecialchars($r['nama_pelaku']); ?></p>
                                <p class="text-[9px] text-slate-400 italic font-medium"><?php echo htmlspecialchars($r['keterangan']); ?></p>
                            </td>
                            <td class="p-5 text-right font-black <?php echo $is_masuk ? 'text-green-600' : 'text-red-600'; ?>">
                                <?php echo number_format($r['nominal'], 0, ',', '.'); ?>
                            </td>
                            <td class="p-5 text-center">
                                <?php if($r['tipe'] == 'manual'): ?>
                                <a href="honor.php?hapus=<?php echo $r['id']; ?>" onclick="return confirm('Hapus?')" class="text-red-300 hover:text-red-600 transition"><i class="fas fa-trash"></i></a>
                                <?php else: ?>
                                <span class="text-[8px] bg-green-50 text-green-500 px-2 py-1 rounded-full font-black border border-green-100 uppercase">SPP</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
