<?php
session_start();
ob_start();

if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

require_once(__DIR__ . '/../koneksi.php');

// ==========================================
// 1. PROSES AKSI (HAPUS / KONFIRMASI)
// ==========================================
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM keuangan WHERE id = '$id'");
    header("Location: honor.php"); exit();
}

if (isset($_GET['konfirmasi_cair'])) {
    $id_trans = $_GET['konfirmasi_cair'];
    $tgl_skrg = date('Y-m-d');
    mysqli_query($conn, "UPDATE keuangan SET status_konfirmasi = 1, tanggal = '$tgl_skrg' WHERE id = '$id_trans'");
    header("Location: honor.php"); exit();
}

// PROSES SIMPAN
if (isset($_POST['simpan_transaksi'])) {
    $id_edit = $_POST['id_edit'];
    $tgl = $_POST['tanggal'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama_pelaku']); 
    $ket = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $tip = $_POST['jenis']; 
    $nom = (int)$_POST['nominal']; // Pastikan jadi Integer
    
    if (!empty($id_edit)) {
        $sql = "UPDATE keuangan SET tanggal='$tgl', nama_pelaku='$nama', keterangan='$ket', jenis='$tip', nominal='$nom' WHERE id='$id_edit'";
    } else {
        $sql = "INSERT INTO keuangan (tanggal, nama_pelaku, keterangan, jenis, nominal, status_konfirmasi) VALUES ('$tgl', '$nama', '$ket', '$tip', '$nom', 1)";
    }
    mysqli_query($conn, $sql);
    header("Location: honor.php"); exit();
}

// DATA EDIT
$edit_data = ['id' => '', 'tanggal' => date('Y-m-d'), 'jenis' => 'keluar', 'nama_pelaku' => '', 'keterangan' => '', 'nominal' => 0];
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $q_edit = mysqli_query($conn, "SELECT * FROM keuangan WHERE id = '$id'");
    if(mysqli_num_rows($q_edit) > 0) { $edit_data = mysqli_fetch_assoc($q_edit); }
}

// ==========================================
// 2. PERHITUNGAN KAS (DIPAKSA BULAT / ROUND)
// ==========================================
// Kita gunakan ROUND() di SQL agar database memberikan angka bulat murni
$q_spp = mysqli_query($conn, "SELECT ROUND(SUM(nominal_bayar)) as total FROM absensi");
$total_spp = (float)(mysqli_fetch_assoc($q_spp)['total'] ?? 0);

$q_masuk = mysqli_query($conn, "SELECT ROUND(SUM(nominal)) as total FROM keuangan WHERE jenis='masuk' AND status_konfirmasi = 1");
$total_masuk = (float)(mysqli_fetch_assoc($q_masuk)['total'] ?? 0);

$q_keluar = mysqli_query($conn, "SELECT ROUND(SUM(nominal)) as total FROM keuangan WHERE jenis='keluar' AND status_konfirmasi = 1");
$total_keluar = (float)(mysqli_fetch_assoc($q_keluar)['total'] ?? 0);

$saldo_akhir = ($total_spp + $total_masuk) - $total_keluar;

// DATA GURU - Perhitungan Honor dibulatkan ke bawah (FLOOR) agar tidak ada ,5
$list_guru = mysqli_query($conn, "
    SELECT u.username, 
    FLOOR(SUM(a.nominal_bayar) * 0.5) as hak_honor
    FROM users u 
    LEFT JOIN jadwal j ON u.id = j.id_guru
    LEFT JOIN absensi a ON j.id = a.id_jadwal
    WHERE u.role = 'guru'
    GROUP BY u.id, u.username");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keuangan - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-50 min-h-screen pb-10">

    <nav class="bg-red-600 shadow-lg px-6 py-4 flex justify-between items-center mb-8 sticky top-0 z-50 border-b-4 border-yellow-400">
        <div class="flex items-center gap-3">
            <a href="index.php" class="text-white hover:text-yellow-400 transition text-xl"><i class="fas fa-chevron-left"></i></a>
            <div>
                <h1 class="text-white font-black text-xl leading-none tracking-tighter uppercase">Smart Arca</h1>
                <p class="text-[9px] text-yellow-300 font-bold uppercase tracking-widest mt-1">Finance System</p>
            </div>
        </div>
        <div class="bg-yellow-400 text-red-700 px-5 py-2 rounded-2xl font-black shadow-xl border-2 border-red-700">
            Rp <?php echo number_format($saldo_akhir, 0, ',', '.'); ?>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 space-y-8">
        <?php 
        $q_pending = mysqli_query($conn, "SELECT * FROM keuangan WHERE status_konfirmasi = 0 ORDER BY tanggal ASC");
        while($p = mysqli_fetch_assoc($q_pending)): 
        ?>
        <div class="bg-white border-2 border-red-600 p-6 rounded-[2rem] shadow-2xl flex justify-between items-center mb-4">
            <div>
                <p class="text-xs font-black text-gray-400 uppercase">Tarik Honor: <?php echo $p['nama_pelaku']; ?></p>
                <p class="text-xl font-black text-red-700">Rp <?php echo number_format($p['nominal']); ?></p>
            </div>
            <a href="honor.php?konfirmasi_cair=<?php echo $p['id']; ?>" class="bg-red-600 text-yellow-400 px-6 py-3 rounded-2xl text-xs font-black shadow-lg">KONFIRMASI</a>
        </div>
        <?php endwhile; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-[2rem] shadow-xl border-t-8 border-red-600">
                    <h3 class="font-black text-slate-800 mb-6 uppercase text-xs tracking-widest border-b-2 border-yellow-400 pb-2">Hak Honor Guru</h3>
                    <?php while($g = mysqli_fetch_assoc($list_guru)): ?>
                    <div class="flex justify-between items-center border-b border-dashed py-3">
                        <div>
                            <p class="font-black text-sm text-slate-700"><?php echo $g['username']; ?></p>
                            <p class="text-[10px] text-green-600 font-bold">Saldo: Rp <?php echo number_format((float)($g['hak_honor'] ?? 0)); ?></p>
                        </div>
                        <button onclick="document.getElementById('nama_pelaku').value='<?php echo $g['username']; ?>'; document.getElementById('nominal').value='<?php echo (float)$g['hak_honor']; ?>'; document.getElementById('keterangan').value='Pembayaran Honor'; document.getElementById('jenis_transaksi').value='keluar'; document.getElementById('nominal').focus();" class="bg-red-50 text-red-600 px-3 py-1 rounded-lg text-[10px] font-black hover:bg-red-600 hover:text-white transition">BAYAR</button>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-8 rounded-[2rem] shadow-xl border-t-8 border-yellow-400" id="formArea">
                    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="hidden" name="id_edit" value="<?php echo $edit_data['id']; ?>">
                        <select name="jenis" id="jenis_transaksi" class="p-3 border-2 border-slate-100 rounded-xl bg-slate-50 font-bold focus:border-red-500 outline-none">
                            <option value="keluar" <?php echo ($edit_data['jenis'] == 'keluar') ? 'selected' : ''; ?>>🔴 PENGELUARAN</option>
                            <option value="masuk" <?php echo ($edit_data['jenis'] == 'masuk') ? 'selected' : ''; ?>>🟢 PEMASUKAN</option>
                        </select>
                        <input type="text" name="nama_pelaku" id="nama_pelaku" value="<?php echo htmlspecialchars($edit_data['nama_pelaku']); ?>" placeholder="Nama Pelaku" class="p-3 border-2 border-slate-100 rounded-xl focus:border-red-500 outline-none" required>
                        <input type="text" name="keterangan" id="keterangan" value="<?php echo htmlspecialchars($edit_data['keterangan']); ?>" placeholder="Keterangan" class="p-3 border-2 border-slate-100 rounded-xl md:col-span-2 focus:border-red-500 outline-none" required>
                        <input type="number" name="nominal" id="nominal" value="<?php echo (float)$edit_data['nominal']; ?>" placeholder="Nominal" class="p-4 border-2 border-slate-100 rounded-xl md:col-span-2 font-black text-2xl text-red-600 focus:border-red-500 outline-none" required>
                        <input type="hidden" name="tanggal" value="<?php echo $edit_data['tanggal']; ?>">
                        <button type="submit" name="simpan_transaksi" class="md:col-span-2 bg-red-600 text-yellow-400 font-black py-4 rounded-2xl shadow-xl transition transform active:scale-95 uppercase tracking-widest">Simpan Transaksi</button>
                    </form>
                </div>

                <div class="bg-white rounded-[2rem] shadow-xl overflow-hidden">
                    <div class="p-6 bg-slate-50 border-b font-black text-slate-800 text-sm italic uppercase">Riwayat Kas</div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-slate-50 text-slate-400 text-[10px] uppercase font-black border-b">
                                <tr><th class="p-5">Tgl</th><th class="p-5">Nama & Ket</th><th class="p-5 text-right">Nominal</th><th class="p-5 text-center">Aksi</th></tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <?php 
                                $riwayat = mysqli_query($conn, "SELECT * FROM keuangan WHERE status_konfirmasi = 1 ORDER BY tanggal DESC, id DESC LIMIT 20");
                                while($r = mysqli_fetch_assoc($riwayat)): 
                                    $is_masuk = ($r['jenis'] == 'masuk');
                                ?>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="p-5 text-slate-400 font-bold text-xs"><?php echo date('d/m', strtotime($r['tanggal'])); ?></td>
                                    <td class="p-5">
                                        <p class="font-black text-slate-800 uppercase text-xs"><?php echo htmlspecialchars($r['nama_pelaku']); ?></p>
                                        <p class="text-[10px] text-slate-400"><?php echo htmlspecialchars($r['keterangan']); ?></p>
                                    </td>
                                    <td class="p-5 text-right font-black <?php echo $is_masuk ? 'text-green-600' : 'text-red-600'; ?>">
                                        <?php echo ($is_masuk ? '+' : '-') . number_format((float)$r['nominal'], 0, ',', '.'); ?>
                                    </td>
                                    <td class="p-5 text-center flex justify-center gap-2">
                                        <a href="honor.php?edit=<?php echo $r['id']; ?>#formArea" class="text-yellow-500 hover:text-yellow-600"><i class="fas fa-edit"></i></a>
                                        <a href="honor.php?hapus=<?php echo $r['id']; ?>" class="text-red-300 hover:text-red-600"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
