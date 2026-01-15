<?php
session_start();
ob_start();

// 1. CEK LOGIN ADMIN
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

require_once(__DIR__ . '/../koneksi.php');

// ==========================================
// 🛠️ PROSES AKSI (HAPUS / KONFIRMASI)
// ==========================================

// Hapus Transaksi
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM keuangan WHERE id = '$id'");
    header("Location: honor.php"); exit();
}

// Konfirmasi Pencairan Guru
if (isset($_GET['konfirmasi_cair'])) {
    $id_trans = $_GET['konfirmasi_cair'];
    $tgl_skrg = date('Y-m-d');
    mysqli_query($conn, "UPDATE keuangan SET status_konfirmasi = 1, tanggal = '$tgl_skrg' WHERE id = '$id_trans'");
    header("Location: honor.php"); exit();
}

// ==========================================
// 2. PROSES SIMPAN (BARU / UPDATE)
// ==========================================
if (isset($_POST['simpan_transaksi'])) {
    $id_edit = $_POST['id_edit'];
    $tgl  = $_POST['tanggal'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama_pelaku']); 
    $ket  = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $tip  = $_POST['jenis']; 
    $nom  = (int)$_POST['nominal'];
    
    if (!empty($id_edit)) {
        $sql = "UPDATE keuangan SET tanggal='$tgl', nama_pelaku='$nama', keterangan='$ket', jenis='$tip', nominal='$nom' WHERE id='$id_edit'";
    } else {
        $sql = "INSERT INTO keuangan (tanggal, nama_pelaku, keterangan, jenis, nominal, status_konfirmasi) 
                VALUES ('$tgl', '$nama', '$ket', '$tip', '$nom', 1)";
    }
    mysqli_query($conn, $sql);
    header("Location: honor.php"); exit();
}

// AMBIL DATA UNTUK EDIT
$edit_data = ['id' => '', 'tanggal' => date('Y-m-d'), 'jenis' => 'keluar', 'nama_pelaku' => '', 'keterangan' => '', 'nominal' => 0];
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $q_edit = mysqli_query($conn, "SELECT * FROM keuangan WHERE id = '$id'");
    if(mysqli_num_rows($q_edit) > 0) { $edit_data = mysqli_fetch_assoc($q_edit); }
}

// ==========================================
// 3. PERHITUNGAN KAS
// ==========================================
$q_spp = mysqli_query($conn, "SELECT SUM(nominal_bayar) as total FROM absensi");
$total_spp = (float)(mysqli_fetch_assoc($q_spp)['total'] ?? 0);

$q_masuk = mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan WHERE jenis='masuk' AND status_konfirmasi = 1");
$total_masuk_manual = (float)(mysqli_fetch_assoc($q_masuk)['total'] ?? 0);

$q_keluar = mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan WHERE jenis='keluar' AND status_konfirmasi = 1");
$total_keluar = (float)(mysqli_fetch_assoc($q_keluar)['total'] ?? 0);

$saldo_akhir = ($total_spp + $total_masuk_manual) - $total_keluar;

// DATA GURU
$list_guru = mysqli_query($conn, "
    SELECT u.username, 
    (SELECT SUM(a.nominal_bayar) FROM absensi a 
     JOIN jadwal j ON a.id_jadwal = j.id 
     WHERE j.id_guru = u.id) * 0.5 as hak_honor
    FROM users u WHERE u.role = 'guru'");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keuangan - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .bg-smart-red { background-color: #dc2626; }
        .text-smart-yellow { color: #facc15; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen pb-10">

    <nav class="bg-smart-red shadow-lg px-6 py-4 flex justify-between items-center mb-8 sticky top-0 z-50 border-b-4 border-yellow-400">
        <div class="flex items-center gap-3">
            <a href="index.php" class="text-white hover:text-yellow-400 transition text-xl"><i class="fas fa-chevron-left"></i></a>
            <div>
                <h1 class="text-white font-black text-xl leading-none tracking-tighter">SMART ARCA</h1>
                <p class="text-[9px] text-yellow-300 font-bold uppercase tracking-widest mt-1">Management Finance</p>
            </div>
        </div>
        <div class="bg-yellow-400 text-red-700 px-5 py-2 rounded-2xl font-black shadow-xl transform scale-105 border-2 border-red-700">
            Rp <?php echo number_format($saldo_akhir, 0, ',', '.'); ?>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 space-y-8">

        <?php 
        $q_pending = mysqli_query($conn, "SELECT * FROM keuangan WHERE status_konfirmasi = 0 ORDER BY tanggal ASC");
        if(mysqli_num_rows($q_pending) > 0): 
        ?>
        <div class="bg-white border-2 border-red-600 p-6 rounded-[2.5rem] shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 bg-red-600 text-yellow-400 px-4 py-1 text-[10px] font-black rounded-bl-xl">PENDING REQUEST</div>
            <h3 class="font-bold text-red-600 mb-4 flex items-center gap-2">
                <i class="fas fa-bell animate-bounce"></i> Permintaan Tarik Honor
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php while($p = mysqli_fetch_assoc($q_pending)): ?>
                <div class="bg-red-50 p-4 rounded-2xl flex justify-between items-center border border-red-100">
                    <div>
                        <p class="text-xs font-black text-gray-400 uppercase"><?php echo $p['nama_pelaku']; ?></p>
                        <p class="text-xl font-black text-red-700">Rp <?php echo number_format($p['nominal']); ?></p>
                    </div>
                    <a href="honor.php?konfirmasi_cair=<?php echo $p['id']; ?>" class="bg-red-600 text-yellow-400 px-4 py-2 rounded-xl text-[10px] font-black hover:bg-red-700 transition shadow-lg">KONFIRMASI</a>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-[2rem] shadow-xl border-t-8 border-red-600" id="formArea">
                    <h3 class="font-black text-slate-800 mb-6 uppercase text-sm italic tracking-widest border-b-2 border-yellow-400 pb-2">
                        <?php echo $edit_data['id'] ? 'Edit Transaksi' : 'Transaksi Baru'; ?>
                    </h3>
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="id_edit" value="<?php echo $edit_data['id']; ?>">
                        
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase ml-1">Jenis</label>
                            <select name="jenis" id="jenis_transaksi" class="w-full p-3 border-2 border-slate-100 rounded-xl bg-slate-50 font-bold focus:border-red-500 outline-none transition">
                                <option value="keluar" <?php echo ($edit_data['jenis'] == 'keluar') ? 'selected' : ''; ?>>🔴 PENGELUARAN</option>
                                <option value="masuk" <?php echo ($edit_data['jenis'] == 'masuk') ? 'selected' : ''; ?>>🟢 PEMASUKAN</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase ml-1">Nama Pelaku</label>
                            <input type="text" name="nama_pelaku" id="nama_pelaku" value="<?php echo htmlspecialchars($edit_data['nama_pelaku']); ?>" class="w-full p-3 border-2 border-slate-100 rounded-xl focus:border-red-500 outline-none" required>
                        </div>

                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase ml-1">Keterangan</label>
                            <input type="text" name="keterangan" id="keterangan" value="<?php echo htmlspecialchars($edit_data['keterangan']); ?>" class="w-full p-3 border-2 border-slate-100 rounded-xl focus:border-red-500 outline-none" required>
                        </div>

                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase ml-1">Nominal (Rp)</label>
                            <input type="number" name="nominal" id="nominal" value="<?php echo (float)$edit_data['nominal']; ?>" class="w-full p-4 border-2 border-slate-100 rounded-xl font-black text-2xl text-red-600 focus:border-red-500 outline-none" required>
                        </div>
                        
                        <input type="hidden" name="tanggal" value="<?php echo $edit_data['tanggal']; ?>">

                        <button type="submit" name="simpan_transaksi" class="w-full bg-red-600 hover:bg-red-700 text-yellow-400 font-black py-4 rounded-2xl shadow-xl shadow-red-100 transition transform active:scale-95">
                            SIMPAN DATA
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-[2rem] shadow-xl border-t-8 border-yellow-400 overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                        <h3 class="font-black text-slate-800 uppercase text-sm italic tracking-widest">Riwayat Kas Terkini</h3>
                        <span class="bg-white px-3 py-1 rounded-full text-[10px] font-bold text-slate-400 border">Confirmed Only</span>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-slate-50 text-slate-400 text-[10px] uppercase font-black tracking-widest border-b">
                                <tr>
                                    <th class="p-5">Tanggal</th>
                                    <th class="p-5">Pelaku & Keterangan</th>
                                    <th class="p-5 text-right">Nominal</th>
                                    <th class="p-5 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <?php 
                                $riwayat = mysqli_query($conn, "SELECT * FROM keuangan WHERE status_konfirmasi = 1 ORDER BY tanggal DESC, id DESC LIMIT 50");
                                while($r = mysqli_fetch_assoc($riwayat)): 
                                    $is_masuk = ($r['jenis'] == 'masuk');
                                    $warna = $is_masuk ? 'text-green-600' : 'text-red-600';
                                    $bg_icon = $is_masuk ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600';
                                ?>
                                <tr class="hover:bg-slate-50/80 transition-all group">
                                    <td class="p-5 text-slate-400 font-bold text-xs">
                                        <?php echo date('d M Y', strtotime($r['tanggal'])); ?>
                                    </td>
                                    <td class="p-5">
                                        <p class="font-black text-slate-800 uppercase text-xs tracking-tighter">
                                            <?php echo htmlspecialchars($r['nama_pelaku']); ?>
                                        </p>
                                        <p class="text-[10px] text-slate-400 italic">
                                            <?php echo htmlspecialchars($r['keterangan']); ?>
                                        </p>
                                    </td>
                                    <td class="p-5 text-right font-black <?php echo $warna; ?> text-base">
                                        <?php echo ($is_masuk ? '+' : '-') . ' ' . number_format((float)$r['nominal'], 0, ',', '.'); ?>
                                    </td>
                                    <td class="p-5 text-center">
                                        <div class="flex justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <a href="honor.php?edit=<?php echo $r['id']; ?>#formArea" class="bg-yellow-100 text-yellow-700 p-2 rounded-lg hover:bg-yellow-400 transition">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="honor.php?hapus=<?php echo $r['id']; ?>" onclick="return confirm('Hapus transaksi ini?')" class="bg-red-50 text-red-400 p-2 rounded-lg hover:bg-red-600 hover:text-white transition">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <?php if(mysqli_num_rows($riwayat) == 0): ?>
                                <tr>
                                    <td colspan="4" class="p-10 text-center text-slate-400 italic text-xs">Belum ada transaksi tercatat.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
