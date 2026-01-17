<?php
// 1. PROTEKSI HALAMAN (Hanya Admin)
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once(__DIR__ . '/../koneksi.php');

// ==========================================
// 2. LOGIKA PROSES (INPUT & KONFIRMASI)
// ==========================================

// A. Input Transaksi Manual (Pendaftaran, Sewa, Listrik, dll)
if (isset($_POST['simpan_transaksi'])) {
    $tgl = $_POST['tgl'];
    $jenis = $_POST['jenis'];
    $kat = mysqli_real_escape_string($conn, $_POST['kategori']);
    $nom = (int)$_POST['nominal'];
    $ket = mysqli_real_escape_string($conn, $_POST['keterangan']);

    mysqli_query($conn, "INSERT INTO transaksi_manual (tanggal, jenis, kategori, nominal, keterangan) VALUES ('$tgl', '$jenis', '$kat', '$nom', '$ket')");
    header("Location: honor.php?msg=sukses"); exit();
}

// B. Konfirmasi Bayar Honor Guru
if (isset($_POST['bayar_honor'])) {
    $id_guru_pilih = (int)$_POST['id_guru'];
    mysqli_query($conn, "UPDATE absensi a JOIN jadwal j ON a.id_jadwal = j.id SET a.status_honor = 'selesai' WHERE j.id_guru = '$id_guru_pilih' AND a.status_honor = 'proses'");
    header("Location: honor.php?msg=lunas"); exit();
}

// ==========================================
// 3. KALKULASI TOTAL DATA
// ==========================================

// --- PEMASUKAN ---
$res_spp = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal_bayar) as total FROM absensi"));
$pemasukan_les = $res_spp['total'] ?? 0;

$res_man_in = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as total FROM transaksi_manual WHERE jenis = 'pemasukan'"));
$pemasukan_lain = $res_man_in['total'] ?? 0;

$total_pemasukan = $pemasukan_les + $pemasukan_lain;

// --- PENGELUARAN ---
$res_paid = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal_bayar) as total FROM absensi WHERE status_honor = 'selesai'"));
$honor_terbayar = floor(($res_paid['total'] ?? 0) * 0.5);

$res_man_out = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as total FROM transaksi_manual WHERE jenis = 'pengeluaran'"));
$pengeluaran_umum = $res_man_out['total'] ?? 0;

$total_pengeluaran = $honor_terbayar + $pengeluaran_umum;

// --- SALDO AKHIR ---
$saldo_kas = $total_pemasukan - $total_pengeluaran;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keuangan Smart Arca - Lengkap</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen pb-20">

    <nav class="bg-red-800 text-white py-4 px-6 shadow-xl border-b-4 border-yellow-400 sticky top-0 z-50 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <a href="index.php" class="hover:text-yellow-400"><i class="fas fa-arrow-left"></i></a>
            <h1 class="font-black italic uppercase tracking-tighter">Pusat Keuangan Smart Arca</h1>
        </div>
        <div class="bg-red-900 px-4 py-1 rounded-full border border-red-700 text-[10px] font-black uppercase">
            Admin: <?php echo $_COOKIE['user_username']; ?>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto p-6 md:p-10">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border-b-8 border-green-500">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Pemasukan (Les + Lain)</p>
                <h3 class="text-3xl font-black text-green-600 italic">Rp <?php echo number_format($total_pemasukan, 0, ',', '.'); ?></h3>
            </div>
            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border-b-8 border-red-600">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Pengeluaran (Honor + Ops)</p>
                <h3 class="text-3xl font-black text-red-700 italic">Rp <?php echo number_format($total_pengeluaran, 0, ',', '.'); ?></h3>
            </div>
            <div class="bg-indigo-900 p-8 rounded-[2.5rem] shadow-xl text-white flex justify-between items-center">
                <div>
                    <p class="text-[10px] font-black text-indigo-300 uppercase tracking-widest mb-1">Sisa Saldo Kas</p>
                    <h3 class="text-4xl font-black text-yellow-400 italic">Rp <?php echo number_format($saldo_kas, 0, ',', '.'); ?></h3>
                </div>
                <i class="fas fa-coins text-5xl text-white/10"></i>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            
            <div class="lg:col-span-1">
                <div class="bg-white p-8 rounded-[3rem] shadow-2xl border-2 border-red-700 sticky top-28">
                    <h2 class="text-xl font-black text-slate-800 uppercase italic mb-6 leading-none">Input Data <br><span class="text-red-600 text-[10px] tracking-widest uppercase italic">Pendaftaran / Sewa / Listrik</span></h2>
                    
                    <form method="POST" class="space-y-4 text-[10px] font-black uppercase tracking-widest">
                        <div>
                            <label class="ml-2 text-slate-400">Tanggal</label>
                            <input type="date" name="tgl" value="<?php echo date('Y-m-d'); ?>" class="w-full p-4 rounded-2xl bg-slate-50 border-2 border-slate-50 focus:border-red-600 outline-none" required>
                        </div>
                        <div>
                            <label class="ml-2 text-slate-400">Jenis</label>
                            <select name="jenis" class="w-full p-4 rounded-2xl bg-slate-50 border-2 border-slate-50 outline-none">
                                <option value="pemasukan">Pemasukan (+)</option>
                                <option value="pengeluaran">Pengeluaran (-)</option>
                            </select>
                        </div>
                        <div>
                            <label class="ml-2 text-slate-400">Kategori</label>
                            <input type="text" name="kategori" class="w-full p-4 rounded-2xl bg-slate-50 border-2 border-slate-50 outline-none" placeholder="Contoh: Sewa Gedung" required>
                        </div>
                        <div>
                            <label class="ml-2 text-slate-400">Nominal (Rp)</label>
                            <input type="number" name="nominal" class="w-full p-4 rounded-2xl bg-slate-50 border-2 border-slate-50 outline-none" placeholder="Rp" required>
                        </div>
                        <div>
                            <label class="ml-2 text-slate-400">Keterangan</label>
                            <textarea name="keterangan" class="w-full p-4 rounded-2xl bg-slate-50 border-2 border-slate-50 outline-none" placeholder="Catatan..."></textarea>
                        </div>
                        <button type="submit" name="simpan_transaksi" class="w-full bg-red-700 text-white py-4 rounded-2xl font-black uppercase shadow-xl hover:bg-red-800 transition transform active:scale-95 italic">Simpan Transaksi</button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-10">
                
                <section>
                    <h2 class="text-xl font-black text-slate-800 uppercase italic mb-6 border-b-2 border-yellow-400 pb-2 flex items-center gap-2">
                        <i class="fas fa-hand-holding-usd text-red-700"></i> Pengajuan Honor Guru
                    </h2>
                    <div class="grid grid-cols-1 gap-4">
                        <?php 
                        $q_req = mysqli_query($conn, "SELECT u.id as id_guru, u.username as nama_guru, SUM(a.nominal_bayar) as total FROM absensi a JOIN jadwal j ON a.id_jadwal = j.id JOIN users u ON j.id_guru = u.id WHERE a.status_honor = 'proses' GROUP BY u.id");
                        if(mysqli_num_rows($q_req) == 0) echo '<p class="text-xs italic text-slate-400 p-8 bg-white rounded-3xl border-2 border-dashed">Belum ada pengajuan pembayaran honor.</p>';
                        while($row = mysqli_fetch_assoc($q_req)): 
                        ?>
                        <div class="bg-white p-6 rounded-[2rem] shadow-lg flex justify-between items-center border-l-[10px] border-red-700">
                            <div>
                                <h4 class="font-black text-slate-800 uppercase italic text-lg"><?php echo $row['nama_guru']; ?></h4>
                                <p class="text-xs font-black text-red-600 italic tracking-widest">Hak Guru: Rp <?php echo number_format($row['total']*0.5, 0, ',', '.'); ?></p>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="id_guru" value="<?php echo $row['id_guru']; ?>">
                                <button type="submit" name="bayar_honor" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl text-[10px] font-black uppercase shadow-lg transition">Bayar Lunas</button>
                            </form>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </section>

                <section>
                    <h2 class="text-xl font-black text-slate-800 uppercase italic mb-6 border-b-2 border-red-700 pb-2 flex items-center gap-2">
                        <i class="fas fa-history text-slate-400"></i> Riwayat Kas Masuk & Keluar
                    </h2>
                    <div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border">
                        <table class="w-full text-[10px] text-left">
                            <thead class="bg-slate-50 text-slate-400 font-black uppercase italic">
                                <tr>
                                    <th class="p-5">Tanggal</th>
                                    <th class="p-5">Kategori/Sumber</th>
                                    <th class="p-5">Jenis</th>
                                    <th class="p-5">Nominal</th>
                                </tr>
                            </thead>
                            <tbody class="font-bold text-slate-600 italic">
                                <?php 
                                $q_man = mysqli_query($conn, "SELECT * FROM transaksi_manual ORDER BY tanggal DESC LIMIT 10");
                                while($m = mysqli_fetch_assoc($q_man)):
                                ?>
                                <tr class="border-b border-slate-50 hover:bg-slate-50 transition">
                                    <td class="p-5"><?php echo date('d/m/y', strtotime($m['tanggal'])); ?></td>
                                    <td class="p-5 uppercase"><?php echo $m['kategori']; ?></td>
                                    <td class="p-5 uppercase <?php echo $m['jenis'] == 'pemasukan' ? 'text-green-500' : 'text-red-500'; ?>"><?php echo $m['jenis']; ?></td>
                                    <td class="p-5">Rp <?php echo number_format($m['nominal'], 0, ',', '.'); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

            </div>
        </div>
    </div>
</body>
</html>
