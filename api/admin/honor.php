<?php
session_start();
ob_start();

if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

require_once(__DIR__ . '/../koneksi.php');

// AUTO-REPAIR DB
mysqli_query($conn, "ALTER TABLE keuangan MODIFY COLUMN jumlah INT DEFAULT 1");
mysqli_query($conn, "ALTER TABLE keuangan MODIFY COLUMN jenis VARCHAR(50)");

// PROSES HAPUS / KONFIRMASI
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
    $tgl  = $_POST['tanggal'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama_pelaku']); 
    $ket  = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $tip  = $_POST['jenis']; 
    $nom  = (int)$_POST['nominal'];
    
    if (!empty($id_edit)) {
        $sql = "UPDATE keuangan SET tanggal='$tgl', nama_pelaku='$nama', keterangan='$ket', jenis='$tip', nominal='$nom' WHERE id='$id_edit'";
    } else {
        $sql = "INSERT INTO keuangan (tanggal, nama_pelaku, keterangan, jenis, nominal, status_konfirmasi) VALUES ('$tgl', '$nama', '$ket', '$tip', '$nom', 1)";
    }
    mysqli_query($conn, $sql);
    header("Location: honor.php"); exit();
}

// DATA EDIT (DIBUAT AMAN)
$edit_data = ['id' => '', 'tanggal' => date('Y-m-d'), 'jenis' => 'keluar', 'nama_pelaku' => '', 'keterangan' => '', 'nominal' => ''];
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $q_edit = mysqli_query($conn, "SELECT * FROM keuangan WHERE id = '$id'");
    if(mysqli_num_rows($q_edit) > 0) {
        $edit_data = mysqli_fetch_assoc($q_edit);
    }
}

// HITUNG SALDO
$q_spp = mysqli_query($conn, "SELECT SUM(nominal_bayar) as total FROM absensi");
$total_spp = mysqli_fetch_assoc($q_spp)['total'] ?? 0;
$q_masuk = mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan WHERE jenis='masuk' AND status_konfirmasi = 1");
$total_masuk_manual = mysqli_fetch_assoc($q_masuk)['total'] ?? 0;
$q_keluar = mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan WHERE jenis='keluar' AND status_konfirmasi = 1");
$total_keluar = mysqli_fetch_assoc($q_keluar)['total'] ?? 0;
$saldo_akhir = ($total_spp + $total_masuk_manual) - $total_keluar;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keuangan - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen pb-10">
    <nav class="bg-white shadow-sm px-6 py-4 flex justify-between items-center mb-6 sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <a href="index.php" class="text-gray-500 hover:text-blue-600"><i class="fas fa-arrow-left"></i></a>
            <h1 class="text-xl font-bold text-gray-800">Manajemen Keuangan</h1>
        </div>
        <div class="bg-blue-600 text-white px-6 py-2 rounded-full font-bold shadow-lg shadow-blue-200">
            Saldo Kas: Rp <?php echo number_format($saldo_akhir, 0, ',', '.'); ?>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 space-y-6">
        <?php 
        $q_pending = mysqli_query($conn, "SELECT * FROM keuangan WHERE status_konfirmasi = 0 ORDER BY tanggal ASC");
        if(mysqli_num_rows($q_pending) > 0): 
        ?>
        <div class="bg-orange-100 border-l-8 border-orange-500 p-6 rounded-2xl shadow-sm">
            <h3 class="font-bold text-orange-800 mb-4 flex items-center gap-2"><i class="fas fa-exclamation-circle text-xl"></i> PERMINTAAN PENCAIRAN HONOR</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php while($p = mysqli_fetch_assoc($q_pending)): ?>
                <div class="bg-white p-4 rounded-xl flex justify-between items-center shadow-sm">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase"><?php echo $p['nama_pelaku']; ?></p>
                        <p class="text-xl font-black text-gray-800">Rp <?php echo number_format($p['nominal']); ?></p>
                    </div>
                    <a href="honor.php?konfirmasi_cair=<?php echo $p['id']; ?>" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-xs font-bold transition">KONFIRMASI</a>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-700 border-b pb-3 mb-4 text-sm uppercase">Laporan Kas</h3>
                    <div class="space-y-4 text-sm">
                        <div class="flex justify-between"><span>Pemasukan Les</span><span class="font-bold text-green-600">+<?php echo number_format($total_spp); ?></span></div>
                        <div class="flex justify-between"><span>Pemasukan Lain</span><span class="font-bold text-green-600">+<?php echo number_format($total_masuk_manual); ?></span></div>
                        <div class="flex justify-between"><span>Pengeluaran</span><span class="font-bold text-red-500">-<?php echo number_format($total_keluar); ?></span></div>
                        <div class="border-t pt-3 flex justify-between"><span class="font-bold">Saldo</span><span class="font-bold text-blue-600">Rp <?php echo number_format($saldo_akhir); ?></span></div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100" id="formArea">
                    <h3 class="font-bold text-gray-700 mb-4"><?php echo !empty($edit_data['id']) ? '📝 Edit Transaksi' : '➕ Tambah Transaksi Manual'; ?></h3>
                    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="hidden" name="id_edit" value="<?php echo $edit_data['id']; ?>">
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase">Tanggal</label>
                            <input type="date" name="tanggal" value="<?php echo $edit_data['tanggal']; ?>" class="w-full p-2 border rounded-lg bg-gray-50">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase">Jenis</label>
                            <select name="jenis" class="w-full p-2 border rounded-lg bg-gray-50">
                                <option value="keluar" <?php echo ($edit_data['jenis'] == 'keluar') ? 'selected' : ''; ?>>🔴 Pengeluaran</option>
                                <option value="masuk" <?php echo ($edit_data['jenis'] == 'masuk') ? 'selected' : ''; ?>>🟢 Pemasukan</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-[10px] font-bold text-gray-400 uppercase">Nama Pelaku</label>
                            <input type="text" name="nama_pelaku" value="<?php echo htmlspecialchars($edit_data['nama_pelaku']); ?>" class="w-full p-2 border rounded-lg" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-[10px] font-bold text-gray-400 uppercase">Keterangan</label>
                            <input type="text" name="keterangan" value="<?php echo htmlspecialchars($edit_data['keterangan']); ?>" class="w-full p-2 border rounded-lg" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-[10px] font-bold text-gray-400 uppercase">Nominal</label>
                            <input type="number" name="nominal" value="<?php echo $edit_data['nominal']; ?>" class="w-full p-3 border rounded-lg font-bold text-xl text-gray-700" required>
                        </div>
                        <button type="submit" name="simpan_transaksi" class="md:col-span-2 bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 transition">
                            <?php echo !empty($edit_data['id']) ? 'UPDATE DATA' : 'SIMPAN TRANSAKSI'; ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
            <div class="p-4 bg-gray-50 border-b font-bold text-gray-700">Riwayat Transaksi</div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-white text-gray-400 text-[10px] uppercase border-b">
                        <tr><th class="p-4">Tgl</th><th class="p-4">Nama</th><th class="p-4">Ket</th><th class="p-4 text-right">Nominal</th><th class="p-4 text-center">Aksi</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php 
                        $riwayat = mysqli_query($conn, "SELECT * FROM keuangan WHERE status_konfirmasi = 1 ORDER BY tanggal DESC LIMIT 30");
                        while($r = mysqli_fetch_assoc($riwayat)): 
                            $warna = ($r['jenis'] == 'masuk') ? 'text-green-600' : 'text-red-500';
                        ?>
                        <tr class="hover:bg-blue-50 transition">
                            <td class="p-4 text-gray-500"><?php echo date('d/m', strtotime($r['tanggal'])); ?></td>
                            <td class="p-4 font-bold"><?php echo htmlspecialchars($r['nama_pelaku']); ?></td>
                            <td class="p-4 text-gray-500"><?php echo htmlspecialchars($r['keterangan']); ?></td>
                            <td class="p-4 text-right font-bold <?php echo $warna; ?>"><?php echo ($r['jenis'] == 'masuk' ? '+' : '-') . number_format($r['nominal']); ?></td>
                            <td class="p-4 text-center flex justify-center gap-2">
                                <a href="honor.php?edit=<?php echo $r['id']; ?>#formArea" class="text-yellow-500"><i class="fas fa-edit"></i></a>
                                <a href="honor.php?hapus=<?php echo $r['id']; ?>" class="text-red-300"><i class="fas fa-trash"></i></a>
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
