<?php
session_start(); ob_start();
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'admin') { header("Location: login.php"); exit(); }
require_once(__DIR__ . '/../koneksi.php');

// LOGIKA AKSI
if (isset($_GET['hapus'])) { mysqli_query($conn, "DELETE FROM keuangan WHERE id = '{$_GET['hapus']}'"); header("Location: honor.php"); exit(); }
if (isset($_GET['konfirmasi_cair'])) { mysqli_query($conn, "UPDATE keuangan SET status_konfirmasi = 1, tanggal = '".date('Y-m-d')."' WHERE id = '{$_GET['konfirmasi_cair']}'"); header("Location: honor.php"); exit(); }

if (isset($_POST['simpan_transaksi'])) {
    $id_edit = $_POST['id_edit']; $tgl = $_POST['tanggal']; $nama = mysqli_real_escape_string($conn, $_POST['nama_pelaku']); 
    $ket = mysqli_real_escape_string($conn, $_POST['keterangan']); $tip = $_POST['jenis']; $nom = (int)$_POST['nominal'];
    if (!empty($id_edit)) { $sql = "UPDATE keuangan SET tanggal='$tgl', nama_pelaku='$nama', keterangan='$ket', jenis='$tip', nominal='$nom' WHERE id='$id_edit'"; }
    else { $sql = "INSERT INTO keuangan (tanggal, nama_pelaku, keterangan, jenis, nominal, status_konfirmasi) VALUES ('$tgl', '$nama', '$ket', '$tip', '$nom', 1)"; }
    mysqli_query($conn, $sql); header("Location: honor.php"); exit();
}

// HITUNG SALDO
$q_spp = mysqli_query($conn, "SELECT SUM(nominal_bayar) as total FROM absensi"); $total_spp = mysqli_fetch_assoc($q_spp)['total'] ?? 0;
$q_masuk = mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan WHERE jenis='masuk' AND status_konfirmasi = 1"); $total_masuk = mysqli_fetch_assoc($q_masuk)['total'] ?? 0;
$q_keluar = mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan WHERE jenis='keluar' AND status_konfirmasi = 1"); $total_keluar = mysqli_fetch_assoc($q_keluar)['total'] ?? 0;
$saldo = ($total_spp + $total_masuk) - $total_keluar;

// DATA GURU UNTUK TOMBOL BAYAR
$list_guru = mysqli_query($conn, "SELECT u.id, u.username, (SELECT SUM(a.nominal_bayar) FROM absensi a JOIN jadwal j ON a.id_jadwal = j.id WHERE j.id_guru = u.id) * 0.5 as hak FROM users u WHERE u.role = 'guru'");
?>
<!DOCTYPE html>
<html lang="id">
<head><script src="https://cdn.tailwindcss.com"></script><link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"></head>
<body class="bg-gray-100 min-h-screen pb-10">
    <nav class="bg-white shadow-sm p-4 flex justify-between items-center sticky top-0 z-50">
        <h1 class="font-bold text-gray-800"><a href="index.php"><i class="fas fa-arrow-left mr-2"></i></a> Manajemen Keuangan</h1>
        <div class="bg-blue-600 text-white px-4 py-2 rounded-full font-bold">Saldo: Rp <?php echo number_format($saldo); ?></div>
    </nav>

    <div class="max-w-6xl mx-auto p-4 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="space-y-6">
            <?php $q_p = mysqli_query($conn, "SELECT * FROM keuangan WHERE status_konfirmasi = 0"); while($p = mysqli_fetch_assoc($q_p)): ?>
            <div class="bg-orange-100 p-4 rounded-xl border-l-4 border-orange-500">
                <p class="text-[10px] font-bold">PERMINTAAN GURU</p>
                <h4 class="font-bold"><?php echo $p['nama_pelaku']; ?></h4>
                <p class="text-lg font-black">Rp <?php echo number_format($p['nominal']); ?></p>
                <a href="honor.php?konfirmasi_cair=<?php echo $p['id']; ?>" class="block text-center bg-orange-500 text-white py-2 rounded-lg text-xs mt-2 font-bold">KONFIRMASI</a>
            </div>
            <?php endwhile; ?>

            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h3 class="font-bold text-gray-700 mb-4 border-b pb-2">Bayar Honor Guru</h3>
                <?php while($g = mysqli_fetch_assoc($list_guru)): ?>
                <div class="flex justify-between items-center mb-3">
                    <div><p class="font-bold text-sm"><?php echo $g['username']; ?></p><p class="text-xs text-green-600">Hak: Rp <?php echo number_format($g['hak']); ?></p></div>
                    <button onclick="document.getElementById('nama_pelaku').value='<?php echo $g['username']; ?>'; document.getElementById('nominal').value='<?php echo $g['hak']; ?>'; document.getElementById('keterangan').value='Honor Mengajar'; document.getElementById('jenis').value='keluar';" class="bg-purple-100 text-purple-700 px-3 py-1 rounded-lg text-xs font-bold">BAYAR</button>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border">
                <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="hidden" name="id_edit" id="id_edit"><input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" class="w-full p-2 border rounded-lg">
                    <select name="jenis" id="jenis" class="w-full p-2 border rounded-lg"><option value="keluar">🔴 Pengeluaran</option><option value="masuk">🟢 Pemasukan</option></select>
                    <input type="text" name="nama_pelaku" id="nama_pelaku" placeholder="Nama Pelaku" class="w-full p-2 border rounded-lg md:col-span-2">
                    <input type="text" name="keterangan" id="keterangan" placeholder="Keterangan" class="w-full p-2 border rounded-lg md:col-span-2">
                    <input type="number" name="nominal" id="nominal" placeholder="Nominal" class="w-full p-2 border rounded-lg md:col-span-2 font-bold text-lg">
                    <button type="submit" name="simpan_transaksi" class="md:col-span-2 bg-blue-600 text-white py-3 rounded-xl font-bold">SIMPAN TRANSAKSI</button>
                </form>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 uppercase text-[10px]"><tr class="border-b"><th class="p-4">Tgl</th><th class="p-4">Nama</th><th class="p-4">Ket</th><th class="p-4 text-right">Nominal</th><th class="p-4 text-center">Aksi</th></tr></thead>
                    <tbody>
                        <?php $riwayat = mysqli_query($conn, "SELECT * FROM keuangan WHERE status_konfirmasi = 1 ORDER BY tanggal DESC LIMIT 20"); while($r = mysqli_fetch_assoc($riwayat)): $c = $r['jenis']=='masuk'?'text-green-600':'text-red-500'; ?>
                        <tr class="border-b text-xs"><td class="p-4"><?php echo date('d/m', strtotime($r['tanggal'])); ?></td><td class="p-4 font-bold"><?php echo $r['nama_pelaku']; ?></td><td class="p-4"><?php echo $r['keterangan']; ?></td><td class="p-4 text-right font-bold <?php echo $c; ?>"><?php echo number_format($r['nominal']); ?></td><td class="p-4 text-center"><a href="honor.php?hapus=<?php echo $r['id']; ?>" class="text-red-300"><i class="fas fa-trash"></i></a></td></tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
