<?php
session_start();
ob_start();

if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'admin') {
    header("Location: login.php"); exit();
}

require_once(__DIR__ . '/../koneksi.php');

// LOGIKA AKSI
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

// FIX WARNING: Inisialisasi variabel agar tidak NULL
$edit_data = ['id' => '', 'tanggal' => date('Y-m-d'), 'jenis' => 'keluar', 'nama_pelaku' => '', 'keterangan' => '', 'nominal' => 0];
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $q_edit = mysqli_query($conn, "SELECT * FROM keuangan WHERE id = '$id'");
    if(mysqli_num_rows($q_edit) > 0) { $edit_data = mysqli_fetch_assoc($q_edit); }
}

// HITUNG SALDO
$q_spp = mysqli_query($conn, "SELECT SUM(nominal_bayar) as total FROM absensi");
$total_spp = (float)(mysqli_fetch_assoc($q_spp)['total'] ?? 0);
$q_masuk = mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan WHERE jenis='masuk' AND status_konfirmasi = 1");
$total_masuk = (float)(mysqli_fetch_assoc($q_masuk)['total'] ?? 0);
$q_keluar = mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan WHERE jenis='keluar' AND status_konfirmasi = 1");
$total_keluar = (float)(mysqli_fetch_assoc($q_keluar)['total'] ?? 0);
$saldo_akhir = ($total_spp + $total_masuk) - $total_keluar;

$list_guru = mysqli_query($conn, "SELECT u.username, (SELECT SUM(a.nominal_bayar) FROM absensi a JOIN jadwal j ON a.id_jadwal = j.id WHERE j.id_guru = u.id) * 0.5 as hak_honor FROM users u WHERE u.role = 'guru'");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keuangan - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen pb-10">
    <nav class="bg-red-600 shadow-md px-6 py-4 flex justify-between items-center mb-6 sticky top-0 z-50 border-b-4 border-yellow-400">
        <div class="flex items-center gap-3">
            <a href="index.php" class="text-yellow-400 hover:text-white transition"><i class="fas fa-arrow-left"></i></a>
            <h1 class="text-xl font-bold text-white">Manajemen Kas</h1>
        </div>
        <div class="bg-yellow-400 text-red-700 px-5 py-2 rounded-full font-bold shadow-lg">
            Saldo: Rp <?php echo number_format($saldo_akhir, 0, ',', '.'); ?>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-3">
            <?php 
            $q_pending = mysqli_query($conn, "SELECT * FROM keuangan WHERE status_konfirmasi = 0");
            while($p = mysqli_fetch_assoc($q_pending)): ?>
            <div class="bg-yellow-100 border-l-8 border-red-600 p-4 rounded-xl flex justify-between items-center shadow-sm mb-4">
                <div>
                    <p class="text-xs font-bold text-red-600 uppercase">Permintaan Pencairan: <?php echo $p['nama_pelaku']; ?></p>
                    <p class="text-xl font-black text-gray-800">Rp <?php echo number_format($p['nominal']); ?></p>
                </div>
                <a href="honor.php?konfirmasi_cair=<?php echo $p['id']; ?>" class="bg-red-600 text-yellow-400 px-4 py-2 rounded-xl text-xs font-bold hover:bg-red-700 shadow-md transition">KONFIRMASI</a>
            </div>
            <?php endwhile; ?>
        </div>

        <div class="space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-red-600 mb-4 border-b-2 border-yellow-400 pb-2 uppercase text-xs tracking-wider">Hak Guru</h3>
                <?php while($g = mysqli_fetch_assoc($list_guru)): ?>
                <div class="flex justify-between items-center border-b border-dashed py-3">
                    <div><p class="font-bold text-sm text-gray-800"><?php echo $g['username']; ?></p><p class="text-[10px] text-gray-400 font-bold uppercase">Rp <?php echo number_format((float)$g['hak_honor']); ?></p></div>
                    <button onclick="document.getElementById('nama_pelaku').value='<?php echo $g['username']; ?>'; document.getElementById('nominal').value='<?php echo (float)$g['hak_honor']; ?>'; document.getElementById('keterangan').value='Pembayaran Honor'; document.getElementById('jenis_transaksi').value='keluar';" class="bg-red-50 text-red-600 hover:bg-red-600 hover:text-yellow-400 px-3 py-1 rounded-lg text-[10px] font-bold transition">BAYAR</button>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100" id="formArea">
                <h3 class="font-bold text-gray-700 mb-4"><?php echo $edit_data['id'] ? '📝 Edit Data' : '➕ Transaksi Manual'; ?></h3>
                <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="hidden" name="id_edit" value="<?php echo $edit_data['id']; ?>">
                    <div class="md:col-span-1">
                        <label class="text-[10px] font-bold text-gray-400 uppercase">Jenis</label>
                        <select name="jenis" id="jenis_transaksi" class="w-full p-2 border rounded-lg bg-gray-50 focus:ring-2 focus:ring-red-500 outline-none">
                            <option value="keluar" <?php echo ($edit_data['jenis'] == 'keluar') ? 'selected' : ''; ?>>🔴 Pengeluaran</option>
                            <option value="masuk" <?php echo ($edit_data['jenis'] == 'masuk') ? 'selected' : ''; ?>>🟢 Pemasukan</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase">Tanggal</label>
                        <input type="date" name="tanggal" value="<?php echo $edit_data['tanggal']; ?>" class="w-full p-2 border rounded-lg bg-gray-50">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase">Nama Pelaku</label>
                        <input type="text" name="nama_pelaku" id="nama_pelaku" value="<?php echo htmlspecialchars($edit_data['nama_pelaku']); ?>" class="w-full p-2 border rounded-lg" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase">Keterangan</label>
                        <input type="text" name="keterangan" id="keterangan" value="<?php echo htmlspecialchars($edit_data['keterangan']); ?>" class="w-full p-2 border rounded-lg" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase">Nominal</label>
                        <input type="number" name="nominal" id="nominal" value="<?php echo (float)$edit_data['nominal']; ?>" class="w-full p-3 border rounded-lg font-bold text-2xl text-red-600" required>
                    </div>
                    <button type="submit" name="simpan_transaksi" class="md:col-span-2 bg-red-600 hover:bg-red-700 text-yellow-400 font-bold py-4 rounded-xl shadow-lg transition transform active:scale-95">SIMPAN TRANSAKSI</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
