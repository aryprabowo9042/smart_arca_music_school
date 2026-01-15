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
// 🛠️ AUTO-REPAIR DATABASE (ANTI-ERROR)
// ==========================================
$cek_nama = mysqli_query($conn, "SHOW COLUMNS FROM keuangan LIKE 'nama_pelaku'");
if (mysqli_num_rows($cek_nama) == 0) {
    mysqli_query($conn, "ALTER TABLE keuangan ADD COLUMN nama_pelaku VARCHAR(100) AFTER tanggal");
}
$cek_konf = mysqli_query($conn, "SHOW COLUMNS FROM keuangan LIKE 'status_konfirmasi'");
if (mysqli_num_rows($cek_konf) == 0) {
    mysqli_query($conn, "ALTER TABLE keuangan ADD COLUMN status_konfirmasi INT DEFAULT 1");
}
$cek_uid = mysqli_query($conn, "SHOW COLUMNS FROM keuangan LIKE 'id_user'");
if (mysqli_num_rows($cek_uid) == 0) {
    mysqli_query($conn, "ALTER TABLE keuangan ADD COLUMN id_user INT AFTER nama_pelaku");
}
mysqli_query($conn, "ALTER TABLE keuangan MODIFY COLUMN jumlah INT DEFAULT 1");
mysqli_query($conn, "ALTER TABLE keuangan MODIFY COLUMN jenis VARCHAR(50)");


// ==========================================
// 2. PROSES AKSI (HAPUS / KONFIRMASI)
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
    // Set status jadi 1 (Selesai/Cair)
    mysqli_query($conn, "UPDATE keuangan SET status_konfirmasi = 1, tanggal = '$tgl_skrg' WHERE id = '$id_trans'");
    header("Location: honor.php"); exit();
}

// ==========================================
// 3. PROSES SIMPAN (BARU / UPDATE)
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
    
    if (mysqli_query($conn, $sql)) {
        header("Location: honor.php"); exit();
    }
}

// AMBIL DATA UNTUK EDIT
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $q_edit = mysqli_query($conn, "SELECT * FROM keuangan WHERE id = '$id'");
    $edit_data = mysqli_fetch_assoc($q_edit);
}

// ==========================================
// 4. PERHITUNGAN KEUANGAN
// ==========================================
// Omzet dari Absensi (Uang Les)
$q_spp = mysqli_query($conn, "SELECT SUM(nominal_bayar) as total FROM absensi");
$total_spp = mysqli_fetch_assoc($q_spp)['total'] ?? 0;

// Pemasukan Manual (Lain-lain)
$q_masuk = mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan WHERE jenis='masuk' AND status_konfirmasi = 1");
$total_masuk_manual = mysqli_fetch_assoc($q_masuk)['total'] ?? 0;

// Pengeluaran (Hanya yang sudah cair/konfirmasi)
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
            <a href="index.php" class="text-gray-500 hover:text-blue-600 transition"><i class="fas fa-arrow-left"></i></a>
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
            <h3 class="font-bold text-orange-800 mb-4 flex items-center gap-2">
                <i class="fas fa-exclamation-circle text-xl"></i> PERMINTAAN PENCAIRAN HONOR
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php while($p = mysqli_fetch_assoc($q_pending)): ?>
                <div class="bg-white p-4 rounded-xl flex justify-between items-center shadow-sm">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase"><?php echo $p['nama_pelaku']; ?></p>
                        <p class="text-xl font-black text-gray-800">Rp <?php echo number_format($p['nominal']); ?></p>
                        <p class="text-[10px] text-orange-600 font-bold italic">Menunggu Pembayaran</p>
                    </div>
                    <a href="honor.php?konfirmasi_cair=<?php echo $p['id']; ?>" onclick="return confirm('Konfirmasi pencairan untuk <?php echo $p['nama_pelaku']; ?>?')" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-xs font-bold transition">
                        KONFIRMASI
                    </a>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-700 border-b pb-3 mb-4 text-sm uppercase tracking-wider">Laporan Arus Kas</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 text-sm">Pemasukan Les</span>
                            <span class="font-bold text-green-600">+ <?php echo number_format($total_spp); ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 text-sm">Pemasukan Lain</span>
                            <span class="font-bold text-green-600">+ <?php echo number_format($total_masuk_manual); ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 text-sm">Total Pengeluaran</span>
                            <span class="font-bold text-red-500">- <?php echo number_format($total_keluar); ?></span>
                        </div>
                        <div class="border-t pt-3 flex justify-between items-center">
                            <span class="font-bold text-gray-800">Sisa Saldo</span>
                            <span class="font-bold text-blue-600 text-lg">Rp <?php echo number_format($saldo_akhir); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100" id="formArea">
                    <h3 class="font-bold text-gray-700 mb-4"><?php echo $edit_data ? '📝 Edit Transaksi' : '➕ Tambah Transaksi Manual'; ?></h3>
                    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="hidden" name="id_edit" value="<?php echo $edit_data['id'] ?? ''; ?>">
                        
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase">Tanggal</label>
                            <input type="date" name="tanggal" value="<?php echo $edit_data['tanggal'] ?? date('Y-m-d'); ?>" class="w-full p-2 border rounded-lg bg-gray-50">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase">Jenis</label>
                            <select name="jenis" class="w-full p-2 border rounded-lg bg-gray-50">
                                <option value="keluar" <?php echo ($edit_data['jenis'] == 'keluar') ? 'selected' : ''; ?>>🔴 Pengeluaran</option>
                                <option value="masuk" <?php echo ($edit_data['jenis'] == 'masuk') ? 'selected' : ''; ?>>🟢 Pemasukan</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-[10px] font-bold text-gray-400 uppercase">Nama Pelaku (Pemberi/Penerima)</label>
                            <input type="text" name="nama_pelaku" value="<?php echo htmlspecialchars($edit_data['nama_pelaku'] ?? ''); ?>" placeholder="Contoh: Toko Musik Arca / Orang Tua Murid" class="w-full p-2 border rounded-lg" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-[10px] font-bold text-gray-400 uppercase">Keterangan</label>
                            <input type="text" name="keterangan" value="<?php echo htmlspecialchars($edit_data['keterangan'] ?? ''); ?>" placeholder="Tujuan transaksi..." class="w-full p-2 border rounded-lg" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-[10px] font-bold text-gray-400 uppercase">Nominal</label>
                            <input type="number" name="nominal" value="<?php echo $edit_data['nominal'] ?? ''; ?>" class="w-full p-3 border rounded-lg font-bold text-xl text-gray-700" required>
                        </div>
                        
                        <button type="submit" name="simpan_transaksi" class="md:col-span-2 bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 transition">
                            <?php echo $edit_data ? 'UPDATE DATA' : 'SIMPAN TRANSAKSI'; ?>
                        </button>
                        <?php if($edit_data): ?>
                            <a href="honor.php" class="md:col-span-2 text-center text-gray-400 text-xs">Batal Edit</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 bg-gray-50 border-b font-bold text-gray-700">Riwayat Transaksi Manual</div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-white text-gray-400 text-[10px] uppercase border-b">
                        <tr>
                            <th class="p-4">Tanggal</th>
                            <th class="p-4">Nama Pelaku</th>
                            <th class="p-4">Keterangan</th>
                            <th class="p-4 text-right">Nominal</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php 
                        $riwayat = mysqli_query($conn, "SELECT * FROM keuangan WHERE status_konfirmasi = 1 ORDER BY tanggal DESC LIMIT 30");
                        while($r = mysqli_fetch_assoc($riwayat)): 
                            $warna = ($r['jenis'] == 'masuk') ? 'text-green-600' : 'text-red-500';
                        ?>
                        <tr class="hover:bg-blue-50 transition">
                            <td class="p-4 text-gray-500"><?php echo date('d/m/y', strtotime($r['tanggal'])); ?></td>
                            <td class="p-4 font-bold text-gray-800"><?php echo htmlspecialchars($r['nama_pelaku']); ?></td>
                            <td class="p-4 text-gray-500"><?php echo htmlspecialchars($r['keterangan']); ?></td>
                            <td class="p-4 text-right font-bold <?php echo $warna; ?>">
                                <?php echo ($r['jenis'] == 'masuk' ? '+' : '-') . ' ' . number_format($r['nominal']); ?>
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="honor.php?edit=<?php echo $r['id']; ?>#formArea" class="text-yellow-500 hover:text-yellow-700"><i class="fas fa-edit"></i></a>
                                    <a href="honor.php?hapus=<?php echo $r['id']; ?>" onclick="return confirm('Hapus transaksi ini?')" class="text-red-300 hover:text-red-600"><i class="fas fa-trash"></i></a>
                                </div>
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
