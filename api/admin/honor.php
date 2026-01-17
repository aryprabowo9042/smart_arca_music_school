<?php
// 1. PROTEKSI HALAMAN (Hanya Admin)
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once(__DIR__ . '/../koneksi.php');

// ==========================================
// 2. LOGIKA PROSES SELESAI BAYAR
// ==========================================
if (isset($_POST['bayar_honor'])) {
    $id_guru_pilih = (int)$_POST['id_guru'];
    
    // Ubah semua status 'proses' menjadi 'selesai' untuk guru tsb
    $sql_selesai = "UPDATE absensi a 
                    JOIN jadwal j ON a.id_jadwal = j.id 
                    SET a.status_honor = 'selesai' 
                    WHERE j.id_guru = '$id_guru_pilih' AND a.status_honor = 'proses'";
    
    if(mysqli_query($conn, $sql_selesai)) {
        header("Location: honor.php?status=lunas");
        exit();
    }
}

// ==========================================
// 3. AMBIL DATA GURU YANG MENGAJUKAN (Status: proses)
// ==========================================
$sql_pengajuan = "SELECT u.id as id_guru, u.username as nama_guru, SUM(a.nominal_bayar) as total_bruto 
                  FROM absensi a 
                  JOIN jadwal j ON a.id_jadwal = j.id 
                  JOIN users u ON j.id_guru = u.id 
                  WHERE a.status_honor = 'proses' 
                  GROUP BY u.id";
$res_pengajuan = mysqli_query($conn, $sql_pengajuan);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Honor - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen pb-20">

    <nav class="bg-red-800 text-white py-4 px-6 shadow-xl border-b-4 border-yellow-400 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="index.php" class="hover:text-yellow-400 transition"><i class="fas fa-arrow-left"></i></a>
                <h1 class="font-black italic uppercase tracking-tighter">Manajemen Honor Guru</h1>
            </div>
            <div class="text-[10px] font-black uppercase bg-red-900 px-4 py-1 rounded-full border border-red-700">
                Admin Panel
            </div>
        </div>
    </nav>

    <div class="max-w-5xl mx-auto p-8">
        
        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl mb-10 border-l-[12px] border-yellow-400 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-black text-slate-800 uppercase italic leading-none mb-2">Daftar Pengajuan</h2>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Verifikasi dan selesaikan pembayaran honor pengajar</p>
            </div>
            <i class="fas fa-hand-holding-usd text-4xl text-slate-100"></i>
        </div>

        <?php if(isset($_GET['status']) && $_GET['status'] == 'lunas'): ?>
            <div class="bg-green-100 text-green-700 p-4 rounded-2xl mb-8 font-black text-xs uppercase italic border border-green-200 flex items-center gap-3">
                <i class="fas fa-check-circle text-lg"></i> Pembayaran Berhasil Dikonfirmasi!
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 gap-6">
            <?php 
            if(mysqli_num_rows($res_pengajuan) == 0): 
            ?>
                <div class="text-center py-20 bg-white rounded-[3rem] shadow-sm border-2 border-dashed border-slate-200">
                    <i class="fas fa-coffee text-5xl text-slate-200 mb-4"></i>
                    <p class="text-slate-400 font-black uppercase italic tracking-widest text-sm">Belum ada pengajuan honor saat ini.</p>
                </div>
            <?php 
            endif;
            while($row = mysqli_fetch_assoc($res_pengajuan)): 
                $hak_guru = floor($row['total_bruto'] * 0.5);
            ?>
            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-slate-100 flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-6 text-center md:text-left">
                    <div class="bg-red-700 text-white w-16 h-16 rounded-3xl flex items-center justify-center text-2xl shadow-lg">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-slate-800 uppercase italic leading-none mb-1"><?php echo $row['nama_guru']; ?></h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Honor Yang Harus Dibayar:</p>
                        <p class="text-xl font-black text-red-700 mt-1">Rp <?php echo number_format($hak_guru, 0, ',', '.'); ?></p>
                    </div>
                </div>

                <div class="flex flex-col gap-3 w-full md:w-auto">
                    <form method="POST" onsubmit="return confirm('Pastikan Anda sudah mentransfer honor sebesar Rp <?php echo number_format($hak_guru, 0, ',', '.'); ?> ke <?php echo $row['nama_guru']; ?>. Lanjutkan?')">
                        <input type="hidden" name="id_guru" value="<?php echo $row['id_guru']; ?>">
                        <button type="submit" name="bayar_honor" class="w-full bg-green-600 hover:bg-green-700 text-white font-black py-4 px-8 rounded-2xl uppercase italic text-xs shadow-xl transition transform active:scale-95 flex items-center justify-center gap-2">
                            <i class="fas fa-check-double"></i> Selesaikan Pembayaran
                        </button>
                    </form>
                    
                    <button class="w-full bg-slate-100 text-slate-400 font-black py-3 px-8 rounded-2xl uppercase italic text-[10px] cursor-not-allowed border border-slate-200">
                        Lihat Rincian Sesi
                    </button>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <div class="mt-16 bg-slate-900 rounded-[3rem] p-10 text-white flex flex-col md:flex-row justify-between items-center border-b-8 border-yellow-400">
            <div>
                <h4 class="text-yellow-400 font-black uppercase italic tracking-widest text-sm mb-2">Total Kas Smart Arca</h4>
                <p class="text-xs text-slate-400 font-bold uppercase mb-4 max-w-xs">Estimasi keuntungan sekolah (50% dari total pendaftaran yang lunas).</p>
            </div>
            <div class="text-right">
                <?php 
                $q_kas = mysqli_query($conn, "SELECT SUM(nominal_bayar) as total FROM absensi WHERE status_honor = 'selesai'");
                $res_kas = mysqli_fetch_assoc($q_kas);
                $total_kas = floor(($res_kas['total'] ?? 0) * 0.5);
                ?>
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Total Saldo Masuk:</p>
                <h3 class="text-4xl font-black italic uppercase text-white">Rp <?php echo number_format($total_kas, 0, ',', '.'); ?></h3>
            </div>
        </div>
    </div>

</body>
</html>
