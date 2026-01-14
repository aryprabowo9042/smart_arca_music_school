<?php
session_start();
ob_start();

// Cek Login Admin
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

require_once(__DIR__ . '/../koneksi.php');

// ==========================================
// 1. PROSES HAPUS DATA
// ==========================================
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $del = mysqli_query($conn, "DELETE FROM keuangan WHERE id = '$id'");
    if ($del) {
        header("Location: honor.php"); // Refresh bersih
        exit();
    }
}

// ==========================================
// 2. PROSES AMBIL DATA EDIT
// ==========================================
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $q_edit = mysqli_query($conn, "SELECT * FROM keuangan WHERE id = '$id'");
    if (mysqli_num_rows($q_edit) > 0) {
        $edit_data = mysqli_fetch_assoc($q_edit);
    }
}

// ==========================================
// 3. PROSES SIMPAN (BARU / UPDATE)
// ==========================================
if (isset($_POST['simpan_transaksi'])) {
    $id_edit = $_POST['id_edit']; // ID Transaksi (Kosong = Baru, Ada Isi = Update)
    
    $tgl  = $_POST['tanggal'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama_pelaku']); 
    $ket  = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $tip  = $_POST['jenis']; 
    $nom  = (int)$_POST['nominal'];
    
    if (!empty($id_edit)) {
        // --- LOGIKA UPDATE ---
        $sql = "UPDATE keuangan SET 
                tanggal='$tgl', nama_pelaku='$nama', keterangan='$ket', jenis='$tip', nominal='$nom' 
                WHERE id='$id_edit'";
    } else {
        // --- LOGIKA INSERT BARU ---
        $sql = "INSERT INTO keuangan (tanggal, nama_pelaku, keterangan, jenis, nominal, jumlah) 
                VALUES ('$tgl', '$nama', '$ket', '$tip', '$nom', 1)";
    }
    
    if (mysqli_query($conn, $sql)) {
        header("Location: honor.php"); 
        exit();
    } else {
        echo "<script>alert('Gagal menyimpan: " . mysqli_error($conn) . "');</script>";
    }
}

// --- HITUNG KEUANGAN ---
$q_spp = mysqli_query($conn, "SELECT SUM(nominal_bayar) as total FROM absensi");
$total_spp = mysqli_fetch_assoc($q_spp)['total'] ?? 0;

$q_masuk = mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan WHERE jenis='masuk'");
$total_masuk_manual = mysqli_fetch_assoc($q_masuk)['total'] ?? 0;

$q_keluar = mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan WHERE jenis='keluar'");
$total_keluar = mysqli_fetch_assoc($q_keluar)['total'] ?? 0;

$total_pendapatan = $total_spp + $total_masuk_manual;
$saldo_akhir = $total_pendapatan - $total_keluar;

// --- DATA HONOR GURU ---
$list_guru = mysqli_query($conn, "
    SELECT u.username, 
    (SELECT SUM(a.nominal_bayar) FROM absensi a 
     JOIN jadwal j ON a.id_jadwal = j.id 
     WHERE j.id_guru = u.id) * 0.5 as hak_honor
    FROM users u 
    WHERE u.role = 'guru'
");
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
            <h1 class="text-xl font-bold text-gray-800">Keuangan & Honor</h1>
        </div>
        <div class="bg-blue-600 text-white px-5 py-2 rounded-full font-bold shadow-lg shadow-blue-200">
            Saldo: Rp <?php echo number_format($saldo_akhir, 0, ',', '.'); ?>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-700 border-b pb-3 mb-3">Ringkasan Kas</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Pemasukan Les</span>
                        <span class="font-bold text-green-600">+ <?php echo number_format($total_spp); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Pemasukan Lain</span>
                        <span class="font-bold text-green-600">+ <?php echo number_format($total_masuk_manual); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Pengeluaran</span>
                        <span class="font-bold text-red-500">- <?php echo number_format($total_keluar); ?></span>
                    </div>
                    <div class="border-t pt-2 flex justify-between text-base">
                        <span class="font-bold text-gray-800">Sisa Saldo</span>
                        <span class="font-bold text-blue-600"><?php echo number_format($saldo_akhir); ?></span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-700 mb-2 flex items-center gap-2">
                    <i class="fas fa-money-check-alt text-purple-500"></i> Bayar Honor Guru
                </h3>
                <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                    <?php 
                    mysqli_data_seek($list_guru, 0); 
                    while($g = mysqli_fetch_assoc($list_guru)): 
                        $honor = $g['hak_honor'] ?? 0;
                    ?>
                    <div class="flex justify-between items-center border-b border-dashed border-gray-200 pb-2">
                        <div>
                            <p class="font-bold text-sm text-gray-800"><?php echo htmlspecialchars($g['username']); ?></p>
                            <p class="text-[10px] text-gray-500">Estimasi: Rp <?php echo number_format($honor); ?></p>
                        </div>
                        <button onclick="bayarHonor('<?php echo $g['username']; ?>', '<?php echo $honor; ?>')" 
                                class="bg-purple-50 text-purple-600 hover:bg-purple-600 hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition">
                            Bayar
                        </button>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <div class="md:col-span-2 space-y-6">

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden" id="formArea">
                <div class="absolute top-0 right-0 bg-<?php echo $edit_data ? 'orange' : 'blue'; ?>-50 px-4 py-2 rounded-bl-2xl text-xs font-bold text-<?php echo $edit_data ? 'orange' : 'blue'; ?>-600">
                    <?php echo $edit_data ? 'MODE EDIT DATA' : 'INPUT TRANSAKSI BARU'; ?>
                </div>
                
                <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                    <input type="hidden" name="id_edit" value="<?php echo $edit_data['id'] ?? ''; ?>">

                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase">Tanggal</label>
                        <input type="date" name="tanggal" value="<?php echo $edit_data['tanggal'] ?? date('Y-m-d'); ?>" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-gray-50" required>
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase">Jenis</label>
                        <select name="jenis" id="jenis_transaksi" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-gray-50">
                            <option value="keluar" <?php echo ($edit_data && $edit_data['jenis'] == 'keluar') ? 'selected' : ''; ?>>🔴 Pengeluaran</option>
                            <option value="masuk" <?php echo ($edit_data && $edit_data['jenis'] == 'masuk') ? 'selected' : ''; ?>>🟢 Pemasukan</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase">Nama Pelaku</label>
                        <input type="text" name="nama_pelaku" id="nama_pelaku" value="<?php echo htmlspecialchars($edit_data['nama_pelaku'] ?? ''); ?>" placeholder="Contoh: Pak Budi / Toko Alat Tulis" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase">Keterangan</label>
                        <input type="text" name="keterangan" id="keterangan" value="<?php echo htmlspecialchars($edit_data['keterangan'] ?? ''); ?>" placeholder="Keterangan transaksi..." class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase">Nominal (Rp)</label>
                        <input type="number" name="nominal" id="nominal" value="<?php echo $edit_data['nominal'] ?? ''; ?>" placeholder="0" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-bold text-xl text-gray-700" required>
                    </div>

                    <?php if($edit_data): ?>
                         <div class="md:col-span-2 flex gap-2">
                            <button type="submit" name="simpan_transaksi" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 rounded-xl transition">
                                <i class="fas fa-save mr-1"></i> UPDATE DATA
                            </button>
                            <a href="honor.php" class="bg-gray-200 text-gray-600 px-4 py-3 rounded-xl font-bold hover:bg-gray-300">Batal</a>
                        </div>
                    <?php else: ?>
                        <button type="submit" name="simpan_transaksi" class="md:col-span-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-blue-100">
                            <i class="fas fa-plus-circle mr-1"></i> SIMPAN TRANSAKSI
                        </button>
                    <?php endif; ?>
                </form>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-bold text-gray-700 text-sm">Riwayat Transaksi</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-white text-gray-400 text-xs uppercase border-b">
                            <tr>
                                <th class="p-3 font-semibold">Tgl</th>
                                <th class="p-3 font-semibold">Ket</th>
                                <th class="p-3 text-right font-semibold">Nominal</th>
                                <th class="p-3 text-center font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php 
                            $riwayat = mysqli_query($conn, "SELECT * FROM keuangan ORDER BY tanggal DESC LIMIT 20");
                            while($r = mysqli_fetch_assoc($riwayat)): 
                                $is_masuk = ($r['jenis'] == 'masuk');
                                $warna = $is_masuk ? 'text-green-600' : 'text-red-500';
                                $tanda = $is_masuk ? '+' : '-';
                            ?>
                            <tr class="hover:bg-blue-50 transition-colors">
                                <td class="p-3 whitespace-nowrap text-gray-500">
                                    <?php echo date('d/m', strtotime($r['tanggal'])); ?><br>
                                    <span class="text-[10px] text-gray-400"><?php echo htmlspecialchars($r['nama_pelaku']); ?></span>
                                </td>
                                <td class="p-3 text-gray-600"><?php echo htmlspecialchars($r['keterangan']); ?></td>
                                <td class="p-3 text-right font-bold <?php echo $warna; ?>">
                                    <?php echo $tanda . ' ' . number_format($r['nominal']); ?>
                                </td>
                                <td class="p-3 text-center flex justify-center gap-2">
                                    <a href="honor.php?edit=<?php echo $r['id']; ?>#formArea" class="bg-yellow-100 text-yellow-600 p-2 rounded hover:bg-yellow-200" title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <a href="honor.php?hapus=<?php echo $r['id']; ?>" onclick="return confirm('Yakin hapus transaksi ini? Saldo akan berubah.')" class="bg-red-100 text-red-600 p-2 rounded hover:bg-red-200" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script>
        function bayarHonor(nama, nominal) {
            document.getElementById('nama_pelaku').value = nama;
            document.getElementById('keterangan').value = 'Honor Mengajar ' + nama;
            document.getElementById('nominal').value = nominal;
            document.getElementById('jenis_transaksi').value = 'keluar';
            document.getElementById('nominal').focus();
            document.querySelector('form').scrollIntoView({ behavior: 'smooth' });
        }
    </script>

</body>
</html>
