<?php
// 1. PROTEKSI HALAMAN
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'guru') {
    header("Location: ../admin/login.php");
    exit();
}

require_once(__DIR__ . '/../koneksi.php');

$id_guru = $_COOKIE['user_id'];
$username = $_COOKIE['user_username'] ?? 'Guru';

// ==========================================
// 2. PROSES LOGIKA (ABSENSI & PENARIKAN)
// ==========================================

// A. Simpan / Edit Absensi
if (isset($_POST['absen'])) {
    $id_jadwal = $_POST['id_jadwal'];
    $tgl = date('Y-m-d');
    $nom = $_POST['nominal_bayar'];
    $id_edit = $_POST['id_edit'] ?? '';

    if (!empty($id_edit)) {
        // Mode Edit: Update data yang sudah ada
        mysqli_query($conn, "UPDATE absensi SET nominal_bayar='$nom' WHERE id='$id_edit'");
    } else {
        // Mode Baru: Masukkan data baru
        mysqli_query($conn, "INSERT INTO absensi (id_jadwal, tanggal, nominal_bayar) VALUES ('$id_jadwal', '$tgl', '$nom')");
    }
    header("Location: index.php"); exit();
}

// B. Ajukan Penarikan Saldo
if (isset($_POST['tarik_saldo'])) {
    $nominal_tarik = $_POST['total_hak'];
    $ket = "Penarikan Honor: " . $username;
    mysqli_query($conn, "INSERT INTO keuangan (tanggal, nama_pelaku, keterangan, jenis, nominal, status_konfirmasi) 
                         VALUES (CURDATE(), '$username', '$ket', 'keluar', '$nominal_tarik', 0)");
    header("Location: index.php?msg=pending"); exit();
}

// ==========================================
// 3. PERHITUNGAN SALDO
// ==========================================
$q_saldo = mysqli_query($conn, "SELECT SUM(a.nominal_bayar) as total FROM absensi a 
                                JOIN jadwal j ON a.id_jadwal = j.id 
                                WHERE j.id_guru = '$id_guru'");
$res_saldo = mysqli_fetch_assoc($q_saldo);
$total_hak = FLOOR(($res_saldo['total'] ?? 0) * 0.5);

$q_pending = mysqli_query($conn, "SELECT id FROM keuangan WHERE nama_pelaku = '$username' AND status_konfirmasi = 0");
$is_pending = mysqli_num_rows($q_pending) > 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen pb-20">

    <nav class="bg-indigo-900 shadow-xl px-6 py-4 flex justify-between items-center mb-6 border-b-4 border-yellow-400 sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <div class="bg-white/10 w-10 h-10 rounded-xl flex items-center justify-center text-white"><i class="fas fa-music"></i></div>
            <h1 class="text-white font-black text-lg italic uppercase tracking-tighter">Teacher Panel</h1>
        </div>
        <a href="../logout.php" class="bg-red-500 text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-lg"><i class="fas fa-sign-out-alt"></i></a>
    </nav>

    <div class="max-w-6xl mx-auto px-4">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="md:col-span-2 bg-white p-8 rounded-[2.5rem] shadow-xl border-l-[12px] border-indigo-600 flex justify-between items-center">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Honor Anda (50%)</p>
                    <h2 class="text-4xl font-black text-slate-800 italic">Rp <?php echo number_format($total_hak, 0, ',', '.'); ?></h2>
                </div>
                <?php if($total_hak > 0 && !$is_pending): ?>
                    <form method="POST">
                        <input type="hidden" name="total_hak" value="<?php echo $total_hak; ?>">
                        <button type="submit" name="tarik_saldo" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-2xl font-black text-xs uppercase shadow-lg transition">Tarik Saldo</button>
                    </form>
                <?php elseif($is_pending): ?>
                    <span class="bg-yellow-100 text-yellow-700 px-6 py-3 rounded-2xl font-black text-xs uppercase italic border-2 border-yellow-200">Menunggu Admin...</span>
                <?php endif; ?>
            </div>
            <div class="bg-indigo-600 p-8 rounded-[2.5rem] shadow-xl text-white flex flex-col justify-center">
                <p class="text-[10px] font-bold uppercase opacity-60">Guru Aktif</p>
                <h3 class="text-xl font-black uppercase italic"><?php echo $username; ?></h3>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-100">
            <div class="p-6 bg-slate-50 border-b">
                <h3 class="font-black text-slate-800 uppercase text-xs italic tracking-widest">Daftar Jadwal & Absensi Les</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-400 text-[9px] uppercase font-black border-b">
                        <tr>
                            <th class="p-6">Waktu</th>
                            <th class="p-6">Siswa / Alat</th>
                            <th class="p-6">Status Bayar</th>
                            <th class="p-6 text-center">Isi Absensi Les</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php 
                        $sql_j = "SELECT j.*, u.username as nama_murid 
                                  FROM jadwal j 
                                  JOIN users u ON j.id_murid = u.id 
                                  WHERE j.id_guru = '$id_guru'
                                  ORDER BY FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'), jam ASC";
                        $res_j = mysqli_query($conn, $sql_j);
                        
                        while($r = mysqli_fetch_assoc($res_j)): 
                            $id_jadwal = $r['id'];
                            $tgl_skrg = date('Y-m-d');
                            $cek_absen = mysqli_query($conn, "SELECT id, nominal_bayar FROM absensi WHERE id_jadwal = '$id_jadwal' AND tanggal = '$tgl_skrg'");
                            $data_absen = mysqli_fetch_assoc($cek_absen);
                            $is_done = ($data_absen != null);
                            $is_editing = (isset($_GET['edit_id']) && $_GET['edit_id'] == $data_absen['id']);
                        ?>
                        <tr class="hover:bg-indigo-50/30 transition">
                            <td class="p-6">
                                <p class="font-black text-slate-800 uppercase text-xs mb-1"><?php echo $r['hari']; ?></p>
                                <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md"><?php echo date('H:i', strtotime($r['jam'])); ?> WIB</span>
                            </td>
                            <td class="p-6">
                                <p class="font-black text-slate-800 uppercase text-sm leading-none"><?php echo $r['nama_murid']; ?></p>
                                <p class="text-[9px] text-slate-400 font-bold uppercase mt-1 italic"><?php echo $r['alat_musik']; ?></p>
                            </td>
                            <td class="p-6">
                                <?php if($is_done && !$is_editing): ?>
                                    <div class="flex flex-col">
                                        <span class="text-green-600 font-black text-[10px] uppercase italic">Terabsen</span>
                                        <span class="text-slate-400 font-bold text-xs">Rp <?php echo number_format($data_absen['nominal_bayar']); ?></span>
                                    </div>
                                <?php else: ?>
                                    <span class="text-slate-300 font-bold text-[10px] uppercase italic">Belum Diisi</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-6 text-center">
                                <?php if(!$is_done || $is_editing): ?>
                                    <form method="POST" class="flex gap-2 justify-center items-center">
                                        <input type="hidden" name="id_jadwal" value="<?php echo $r['id']; ?>">
                                        <?php if($is_editing): ?>
                                            <input type="hidden" name="id_edit" value="<?php echo $data_absen['id']; ?>">
                                        <?php endif; ?>
                                        
                                        <input type="number" name="nominal_bayar" 
                                               value="<?php echo $is_editing ? $data_absen['nominal_bayar'] : ''; ?>" 
                                               class="w-28 p-2 border-2 <?php echo $is_editing ? 'border-yellow-400' : 'border-slate-100'; ?> rounded-lg text-xs font-bold focus:border-indigo-600 outline-none" 
                                               placeholder="Nominal SPP" required>
                                        
                                        <button type="submit" name="absen" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-[10px] font-black uppercase shadow-md hover:bg-indigo-700">
                                            <?php echo $is_editing ? 'Update' : 'Kirim'; ?>
                                        </button>
                                        
                                        <?php if($is_editing): ?>
                                            <a href="index.php" class="text-slate-400"><i class="fas fa-times"></i></a>
                                        <?php endif; ?>
                                    </form>
                                <?php else: ?>
                                    <a href="index.php?edit_id=<?php echo $data_absen['id']; ?>" class="inline-block bg-yellow-400 text-yellow-900 px-4 py-2 rounded-lg text-[10px] font-black uppercase hover:bg-indigo-600 hover:text-white transition">
                                        <i class="fas fa-edit mr-1"></i> Edit Absensi
                                    </a>
                                <?php endif; ?>
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
