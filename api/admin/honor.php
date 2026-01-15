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
// 🛠️ AUTO-REPAIR DATABASE & FORMATTING
// ==========================================
mysqli_query($conn, "ALTER TABLE keuangan MODIFY COLUMN jumlah INT DEFAULT 1");
mysqli_query($conn, "ALTER TABLE keuangan MODIFY COLUMN jenis VARCHAR(50)");

// ==========================================
// 2. PROSES AKSI (HAPUS / KONFIRMASI)
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

// PROSES SIMPAN (BARU / UPDATE)
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

// DATA EDIT
$edit_data = ['id' => '', 'tanggal' => date('Y-m-d'), 'jenis' => 'keluar', 'nama_pelaku' => '', 'keterangan' => '', 'nominal' => ''];
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $q_edit = mysqli_query($conn, "SELECT * FROM keuangan WHERE id = '$id'");
    if(mysqli_num_rows($q_edit) > 0) { $edit_data = mysqli_fetch_assoc($q_edit); }
}

// ==========================================
// 3. PERHITUNGAN SALDO (ANTI-NULL)
// ==========================================
$q_spp = mysqli_query($conn, "SELECT SUM(nominal_bayar) as total FROM absensi");
$total_spp = (float)(mysqli_fetch_assoc($q_spp)['total'] ?? 0);

$q_masuk = mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan WHERE jenis='masuk' AND status_konfirmasi = 1");
$total_masuk_manual = (float)(mysqli_fetch_assoc($q_masuk)['total'] ?? 0);

$q_keluar = mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan WHERE jenis='keluar' AND status_konfirmasi = 1");
$total_keluar = (float)(mysqli_fetch_assoc($q_keluar)['total'] ?? 0);

$saldo_akhir = ($total_spp + $total_masuk_manual) - $total_keluar;

// DATA GURU UNTUK DROPDOWN & HAK HONOR
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
    <title>Keuangan - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen pb-10">
    <nav class="bg-white shadow-sm px-6 py-4 flex justify-between items-center mb-6 sticky top-0 z-50">
        <h1 class="text-xl font-bold text-gray-800"><a href="index.php"><i class="fas fa-arrow-left mr-2"></i></a> Keuangan</h1>
        <div class="bg-blue-600 text-white px-5 py-2 rounded-full font-bold">
            Saldo: Rp <?php echo number_format($saldo_akhir, 0, ',', '.'); ?>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-700 mb-4 flex items-center gap-2"><i class="fas fa-money-check-alt text-purple-500"></i> Hak Honor Guru</h3>
                <div class="space-y-3">
                    <?php while($g = mysqli_fetch_assoc($list_guru)): 
                        // PERBAIKAN: Pastikan nilai honor tidak NULL
                        $honor = (float)($g['hak_honor'] ?? 0); 
                    ?>
                    <div class="flex justify-between items-center border-b border-dashed pb-2">
                        <div>
                            <p class="font-bold text-sm"><?php echo htmlspecialchars($g['username']); ?></p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase">Hak: Rp <?php echo number_format($honor, 0, ',', '.'); ?></p>
                        </div>
                        <button onclick="document.getElementById('nama_pelaku').value='<?php echo $g['username']; ?>'; document.getElementById('nominal').value='<?php echo $honor; ?>'; document.getElementById('keterangan').value='Pembayaran Honor'; document.getElementById('jenis_transaksi').value='keluar'; document.getElementById('nominal').focus();" class="bg-purple-100 text-purple-700 px-3 py-1.5 rounded-lg text-xs font-bold transition hover:bg-purple-600 hover:text-white">BAYAR</button>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100" id="formArea">
                <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="hidden" name="id_edit" value="<?php echo $edit_data['id']; ?>">
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase">Tanggal</label>
                        <input type="date" name="tanggal" value="<?php echo $edit_data['tanggal']; ?>" class="w-full p-2 border rounded-lg bg-gray-50">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase">Jenis</label>
                        <select name="jenis" id="jenis_transaksi" class="w-full p-2 border rounded-lg bg-gray-50">
                            <option value="keluar" <?php echo ($edit_data['jenis'] == 'keluar') ? 'selected' : ''; ?>>🔴 Pengeluaran</option>
                            <option value="masuk" <?php echo ($edit_data['jenis'] == 'masuk') ? 'selected' : ''; ?>>🟢 Pemasukan</option>
                        </select>
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
                        <input type="number" name="nominal" id="nominal" value="<?php echo (float)($edit_data['nominal'] ?? 0); ?>" class="w-full p-3 border rounded-lg font-bold text-xl" required>
                    </div>
                    <button type="submit" name="simpan_transaksi" class="md:col-span-2 bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 transition shadow-lg">SIMPAN TRANSAKSI</button>
                </form>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-white text-gray-400 text-[10px] uppercase border-b">
                            <tr><th class="p-4">Tgl</th><th class="p-4">Nama Pelaku</th><th class="p-4">Ket</th><th class="p-4 text-right">Nominal</th><th class="p-4 text-center">Aksi</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php 
                            $riwayat = mysqli_query($conn, "SELECT * FROM keuangan WHERE status_konfirmasi = 1 ORDER BY tanggal DESC LIMIT 20");
                            while($r = mysqli_fetch_assoc($riwayat)): 
                                $warna = ($r['jenis'] == 'masuk') ? 'text-green-600' : 'text-red-500';
                                $nom = (float)($r['nominal'] ?? 0);
                            ?>
                            <tr class="hover:bg-blue-50 transition">
                                <td class="p-4 text-gray-500"><?php echo date('d/m', strtotime($r['tanggal'])); ?></td>
                                <td class="p-4 font-bold"><?php echo htmlspecialchars($r['nama_pelaku']); ?></td>
                                <td class="p-4 text-gray-500"><?php echo htmlspecialchars($r['keterangan']); ?></td>
                                <td class="p-4 text-right font-bold <?php echo $warna; ?>"><?php echo number_format($nom, 0, ',', '.'); ?></td>
                                <td class="p-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="honor.php?edit=<?php echo $r['id']; ?>#formArea" class="text-yellow-500"><i class="fas fa-edit"></i></a>
                                        <a href="honor.php?hapus=<?php echo $r['id']; ?>" class="text-red-300"><i class="fas fa-trash"></i></a>
                                    </div>
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
