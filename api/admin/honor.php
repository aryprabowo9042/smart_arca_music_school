<?php
// 1. PROTEKSI HALAMAN (Hanya Admin)
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// 2. KONEKSI KE DATABASE
require_once(__DIR__ . '/../koneksi.php');

// ==========================================
// 3. LOGIKA PROSES (INPUT, EDIT, HAPUS, BAYAR)
// ==========================================

// A. SIMPAN ATAU UPDATE TRANSAKSI MANUAL
if (isset($_POST['simpan_transaksi'])) {
    $tgl = $_POST['tgl'];
    $jenis = $_POST['jenis']; 
    $kat = mysqli_real_escape_string($conn, $_POST['kategori']);
    $nom = (int)$_POST['nominal'];
    
    // Perbaikan Error: Gunakan ?? '' untuk mencegah error jika keterangan kosong
    $ket_input = $_POST['keterangan'] ?? ''; 
    $ket = mysqli_real_escape_string($conn, $ket_input);
    
    $id_edit = isset($_POST['id_edit']) ? (int)$_POST['id_edit'] : 0;

    if ($id_edit > 0) {
        $sql = "UPDATE transaksi_manual SET tanggal='$tgl', jenis='$jenis', kategori='$kat', nominal='$nom', keterangan='$ket' WHERE id='$id_edit'";
    } else {
        $sql = "INSERT INTO transaksi_manual (tanggal, jenis, kategori, nominal, keterangan) VALUES ('$tgl', '$jenis', '$kat', '$nom', '$ket')";
    }
    
    mysqli_query($conn, $sql);
    header("Location: honor.php?msg=sukses"); 
    exit();
}

// B. HAPUS TRANSAKSI MANUAL
if (isset($_GET['hapus_id'])) {
    $id_hapus = (int)$_GET['hapus_id'];
    mysqli_query($conn, "DELETE FROM transaksi_manual WHERE id = '$id_hapus'");
    header("Location: honor.php?msg=hapus_ok");
    exit();
}

// C. KONFIRMASI BAYAR HONOR GURU
if (isset($_POST['bayar_honor'])) {
    $id_guru_pilih = (int)$_POST['id_guru'];
    $sql_pay = "UPDATE absensi a JOIN jadwal j ON a.id_jadwal = j.id SET a.status_honor = 'selesai' WHERE j.id_guru = '$id_guru_pilih' AND a.status_honor = 'proses'";
    mysqli_query($conn, $sql_pay);
    header("Location: honor.php?msg=lunas"); 
    exit();
}

// D. AMBIL DATA UNTUK FORM EDIT
$data_edit = null;
if (isset($_GET['edit_id'])) {
    $id_edit_cari = (int)$_GET['edit_id'];
    $res_edit = mysqli_query($conn, "SELECT * FROM transaksi_manual WHERE id = '$id_edit_cari'");
    $data_edit = mysqli_fetch_assoc($res_edit);
}

// ==========================================
// 4. HITUNG RINGKASAN SALDO KAS
// ==========================================
$res_les = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal_bayar) as total FROM absensi"));
$total_les = $res_les['total'] ?? 0;

$res_man_in = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as total FROM transaksi_manual WHERE jenis = 'pemasukan'"));
$total_manual_masuk = $res_man_in['total'] ?? 0;

$res_honor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal_bayar) as total FROM absensi WHERE status_honor = 'selesai'"));
$total_honor_lunas = floor(($res_honor['total'] ?? 0) * 0.5);

$res_man_out = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as total FROM transaksi_manual WHERE jenis = 'pengeluaran'"));
$total_operasional = $res_man_out['total'] ?? 0;

$saldo_bersih = ($total_les + $total_manual_masuk) - ($total_honor_lunas + $total_operasional);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keuangan Pusat - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .table-container { max-height: 400px; overflow-y: auto; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen pb-20">

    <nav class="bg-red-800 text-white py-4 px-6 shadow-xl border-b-4 border-yellow-400 sticky top-0 z-50 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <a href="index.php" class="hover:text-yellow-400 transition"><i class="fas fa-arrow-left"></i></a>
            <h1 class="font-black italic uppercase tracking-tighter leading-none text-lg">Smart Arca Finance</h1>
        </div>
        <div class="flex items-center gap-4">
            <a href="keuangan.php" class="bg-yellow-400 hover:bg-yellow-300 text-red-800 px-4 py-2 rounded-xl text-[10px] font-black uppercase shadow-lg transition flex items-center gap-2">
                <i class="fas fa-file-invoice-dollar"></i> Laporan Bulanan
            </a>
            <div class="hidden md:block bg-red-900 px-4 py-2 rounded-full border border-red-700 text-[10px] font-black uppercase">
                Kas: Rp <?php echo number_format($saldo_bersih, 0, ',', '.'); ?>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto p-6 md:p-10">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border-b-8 border-green-500">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 leading-none">Total Pemasukan</p>
                <h3 class="text-3xl font-black text-green-600 italic">Rp <?php echo number_format($total_les + $total_manual_masuk, 0, ',', '.'); ?></h3>
            </div>
            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border-b-8 border-red-600">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 leading-none">Total Pengeluaran</p>
                <h3 class="text-3xl font-black text-red-700 italic">Rp <?php echo number_format($total_honor_lunas + $total_operasional, 0, ',', '.'); ?></h3>
            </div>
            <div class="bg-indigo-900 p-8 rounded-[2.5rem] shadow-xl text-white flex flex-col justify-center relative overflow-hidden">
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-indigo-300 uppercase tracking-widest mb-1">Saldo Kas Sekolah</p>
                    <h3 class="text-4xl font-black text-yellow-400 italic leading-none">Rp <?php echo number_format($saldo_bersih, 0, ',', '.'); ?></h3>
                    <a href="keuangan.php" class="inline-block mt-4 text-[9px] font-black uppercase tracking-widest text-white/50 hover:text-yellow-400 transition">
                        Lihat Detail Laporan <i class="fas fa-chevron-right ml-1"></i>
                    </a>
                </div>
                <i class="fas fa-vault text-7xl text-white/5 absolute right-4 -bottom-2"></i>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            
            <div class="lg:col-span-1">
                <div class="bg-white p-8 rounded-[3rem] shadow-2xl border-2 border-red-700 sticky top-28">
                    <h2 class="text-xl font-black text-slate-800 uppercase italic mb-6 border-b-2 border-yellow-400 pb-2">
                        <?php echo $data_edit ? 'Edit Transaksi' : 'Input Manual'; ?>
                    </h2>
                    
                    <form method="POST" class="space-y-4 text-[10px] font-black uppercase tracking-widest">
                        <?php if($data_edit): ?><input type="hidden" name="id_edit" value="<?php echo $data_edit['id']; ?>"><?php endif; ?>
                        
                        <div>
                            <label class="ml-2 text-slate-400 italic font-bold">Tanggal</label>
                            <input type="date" name="tgl" value="<?php echo $data_edit['tanggal'] ?? date('Y-m-d'); ?>" class="w-full p-4 rounded-2xl bg-slate-50 border-2 border-slate-50 focus:border-red-600 font-bold" required>
                        </div>
                        <div>
                            <label class="ml-2 text-slate-400 italic font-bold">Jenis</label>
                            <select name="jenis" class="w-full p-4 rounded-2xl bg-slate-50 border-2 border-slate-50 outline-none font-bold">
                                <option value="pemasukan" <?php echo ($data_edit && $data_edit['jenis'] == 'pemasukan') ? 'selected' : ''; ?>>Pemasukan (+)</option>
                                <option value="pengeluaran" <?php echo ($data_edit && $data_edit['jenis'] == 'pengeluaran') ? 'selected' : ''; ?>>Pengeluaran (-)</option>
                            </select>
                        </div>
                        <div>
                            <label class="ml-2 text-slate-400 italic font-bold">Kategori</label>
                            <input type="text" name="kategori" value="<?php echo $data_edit['kategori'] ?? ''; ?>" class="w-full p-4 rounded-2xl bg-slate-50 border-2 border-slate-50 font-bold" placeholder="Pendaftaran/Listrik" required>
                        </div>
                        <div>
                            <label class="ml-2 text-slate-400 italic font-bold">Nominal (Rp)</label>
                            <input type="number" name="nominal" value="<?php echo $data_edit['nominal'] ?? ''; ?>" class="w-full p-4 rounded-2xl bg-slate-50 border-2 border-slate-50 font-bold" required>
                        </div>
                        <div>
                            <label class="ml-2 text-slate-400 italic font-bold">Keterangan</label>
                            <textarea name="keterangan" class="w-full p-4 rounded-2xl bg-slate-50 border-2 border-slate-50 font-bold outline-none focus:border-red-600" placeholder="Catatan singkat"><?php echo $data_edit['keterangan'] ?? ''; ?></textarea>
                        </div>
                        
                        <button type="submit" name="simpan_transaksi" class="w-full bg-red-700 text-white py-4 rounded-2xl font-black uppercase shadow-xl hover:bg-red-800 transition transform active:scale-95 italic text-xs">
                            <i class="fas fa-save mr-1"></i> <?php echo $data_edit ? 'Update Data' : 'Simpan Transaksi'; ?>
                        </button>
                        
                        <?php if($data_edit): ?>
                            <a href="honor.php" class="block text-center text-slate-400 font-black text-[9px] uppercase mt-2">Batal Edit</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-12">
                <section>
                    <h2 class="text-xl font-black text-slate-800 uppercase italic mb-6 border-b-2 border-red-700 pb-2 inline-block"><i class="fas fa-hand-holding-usd mr-2 text-red-700"></i> Antrean Bayar Honor</h2>
                    <div class="grid grid-cols-1 gap-4">
                        <?php 
                        $q_req = mysqli_query($conn, "SELECT u.id as id_guru, u.username as nama_guru, SUM(a.nominal_bayar) as total FROM absensi a JOIN jadwal j ON a.id_jadwal = j.id JOIN users u ON j.id_guru = u.id WHERE a.status_honor = 'proses' GROUP BY u.id");
                        if(mysqli_num_rows($q_req) == 0) echo '<p class="text-[10px] italic text-slate-400 p-8 bg-white rounded-3xl border-2 border-dashed font-black uppercase tracking-widest text-center">Tidak ada permintaan tarik honor.</p>';
                        while($row = mysqli_fetch_assoc($q_req)): 
                        ?>
                        <div class="bg-white p-6 rounded-[2rem] shadow-lg flex justify-between items-center border-l-[10px] border-red-700">
                            <div>
                                <h4 class="font-black text-slate-800 uppercase italic text-lg leading-none"><?php echo $row['nama_guru']; ?></h4>
                                <p class="text-[10px] font-black text-red-600 mt-1 uppercase">Hak Honor: Rp <?php echo number_format($row['total']*0.5, 0, ',', '.'); ?></p>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="id_guru" value="<?php echo $row['id_guru']; ?>">
                                <button type="submit" name="bayar_honor" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-xl text-[10px] font-black uppercase shadow-lg">Konfirmasi Lunas</button>
                            </form>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </section>

                <section>
                    <h2 class="text-xl font-black text-slate-800 uppercase italic mb-6 border-b-2 border-green-500 pb-2 inline-block"><i class="fas fa-music mr-2 text-green-500"></i> Pemasukan Les (SPP Siswa)</h2>
                    <div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-slate-100">
                        <div class="table-container">
                            <table class="w-full text-[10px] text-left">
                                <thead class="bg-slate-50 text-slate-400 font-black uppercase italic tracking-widest sticky top-0">
                                    <tr><th class="p-5">Tanggal</th><th class="p-5">Nama Siswa</th><th class="p-5">Nominal</th></tr>
                                </thead>
                                <tbody class="font-bold text-slate-600 italic">
                                    <?php 
                                    $q_les = mysqli_query($conn, "SELECT a.*, u.username as nama_murid FROM absensi a JOIN jadwal j ON a.id_jadwal = j.id JOIN users u ON j.id_murid = u.id ORDER BY a.tanggal DESC");
                                    while($l = mysqli_fetch_assoc($q_les)):
                                    ?>
                                    <tr class="border-b border-slate-50">
                                        <td class="p-5 text-indigo-600"><?php echo date('d/m/y', strtotime($l['tanggal'])); ?></td>
                                        <td class="p-5 uppercase"><?php echo $l['nama_murid']; ?></td>
                                        <td class="p-5">Rp <?php echo number_format($l['nominal_bayar'], 0, ',', '.'); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <section>
                    <h2 class="text-xl font-black text-slate-800 uppercase italic mb-6 border-b-2 border-yellow-400 pb-2 inline-block"><i class="fas fa-history mr-2 text-slate-400"></i> Riwayat Transaksi Manual</h2>
                    <div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-slate-100">
                        <div class="table-container">
                            <table class="w-full text-[10px] text-left">
                                <thead class="bg-slate-50 text-slate-400 font-black uppercase italic tracking-widest sticky top-0">
                                    <tr><th class="p-5">Tanggal</th><th class="p-5">Kategori</th><th class="p-5">Jenis</th><th class="p-5">Nominal</th><th class="p-5 text-center">Aksi</th></tr>
                                </thead>
                                <tbody class="font-bold text-slate-600 italic">
                                    <?php 
                                    $q_man = mysqli_query($conn, "SELECT * FROM transaksi_manual ORDER BY tanggal DESC");
                                    while($m = mysqli_fetch_assoc($q_man)):
                                    ?>
                                    <tr class="border-b border-slate-50 hover:bg-slate-50">
                                        <td class="p-5"><?php echo date('d/m/y', strtotime($m['tanggal'])); ?></td>
                                        <td class="p-5 uppercase"><?php echo $m['kategori']; ?></td>
                                        <td class="p-5 uppercase <?php echo $m['jenis'] == 'pemasukan' ? 'text-green-500' : 'text-red-500'; ?>"><?php echo $m['jenis']; ?></td>
                                        <td class="p-5">Rp <?php echo number_format($m['nominal'], 0, ',', '.'); ?></td>
                                        <td class="p-5 text-center flex justify-center gap-3">
                                            <a href="honor.php?edit_id=<?php echo $m['id']; ?>" class="text-indigo-400 hover:text-indigo-700 transition"><i class="fas fa-edit"></i></a>
                                            <a href="honor.php?hapus_id=<?php echo $m['id']; ?>" class="text-red-300 hover:text-red-600 transition" onclick="return confirm('Hapus data ini?')"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </div>
</body>
</html>
