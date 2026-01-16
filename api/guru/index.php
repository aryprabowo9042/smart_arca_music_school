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
// 2. PROSES ABSENSI (SIMPAN & UPDATE)
// ==========================================
if (isset($_POST['absen'])) {
    $id_jadwal = $_POST['id_jadwal'];
    $tgl = date('Y-m-d');
    $nom = $_POST['nominal_bayar'];
    $id_edit = $_POST['id_edit'] ?? '';

    if (!empty($id_edit)) {
        // Update absensi yang sudah ada
        $sql = "UPDATE absensi SET nominal_bayar='$nom' WHERE id='$id_edit'";
    } else {
        // Simpan absensi baru
        $sql = "INSERT INTO absensi (id_jadwal, tanggal, nominal_bayar) VALUES ('$id_jadwal', '$tgl', '$nom')";
    }
    
    mysqli_query($conn, $sql);
    header("Location: index.php"); exit();
}

// Data Edit Absensi (Jika tombol edit diklik)
$edit_absensi = ['id' => '', 'id_jadwal' => '', 'nominal' => ''];
if (isset($_GET['edit_absen'])) {
    $id_a = mysqli_real_escape_string($conn, $_GET['edit_absen']);
    $res_a = mysqli_query($conn, "SELECT * FROM absensi WHERE id = '$id_a'");
    if($res_a && mysqli_num_rows($res_a) > 0) {
        $data_a = mysqli_fetch_assoc($res_a);
        $edit_absensi = ['id' => $data_a['id'], 'id_jadwal' => $data_a['id_jadwal'], 'nominal' => $data_a['nominal_bayar']];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guru Dashboard - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen pb-20">

    <nav class="bg-indigo-900 shadow-xl px-6 py-4 flex justify-between items-center mb-10 border-b-4 border-yellow-400 sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <div class="bg-white/10 w-10 h-10 rounded-xl flex items-center justify-center text-white">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div>
                <h1 class="text-white font-black text-lg italic tracking-tighter leading-none uppercase">Teacher Room</h1>
                <p class="text-[8px] text-indigo-300 font-bold uppercase tracking-widest mt-1">Smart Arca Music School</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="hidden md:block text-right">
                <p class="text-[10px] text-indigo-300 font-bold uppercase leading-none">Selamat Mengajar,</p>
                <p class="text-white font-black text-sm italic uppercase"><?php echo $username; ?></p>
            </div>
            <a href="../logout.php" class="bg-red-500 hover:bg-red-600 text-white w-10 h-10 rounded-xl flex items-center justify-center transition shadow-lg transform active:scale-90">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4">
        
        <div class="bg-indigo-600 rounded-[2.5rem] p-8 mb-10 text-white shadow-2xl relative overflow-hidden">
            <div class="relative z-10">
                <h2 class="text-2xl font-black italic uppercase leading-none">Jadwal Mengajar Anda</h2>
                <p class="text-indigo-100 text-xs mt-2 font-medium opacity-80 uppercase tracking-widest">Daftar seluruh jadwal pertemuan kursus</p>
            </div>
            <i class="fas fa-music absolute -right-4 -bottom-4 text-white/10 text-9xl"></i>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-slate-100">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-400 text-[9px] uppercase font-black border-b tracking-widest">
                        <tr>
                            <th class="p-6">Waktu</th>
                            <th class="p-6">Murid & Instrumen</th>
                            <th class="p-6">Status Absensi</th>
                            <th class="p-6 text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php 
                        // Munculkan semua jadwal guru ini tanpa filter hari
                        $sql_j = "SELECT j.*, u.username as nama_murid 
                                  FROM jadwal j 
                                  JOIN users u ON j.id_murid = u.id 
                                  WHERE j.id_guru = '$id_guru'
                                  ORDER BY FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'), jam ASC";
                        $res_j = mysqli_query($conn, $sql_j);
                        
                        while($r = mysqli_fetch_assoc($res_j)): 
                            $id_jadwal = $r['id'];
                            $tgl_skrg = date('Y-m-d');
                            
                            // Cek apakah hari ini sudah absen
                            $cek_absen = mysqli_query($conn, "SELECT id, nominal_bayar FROM absensi WHERE id_jadwal = '$id_jadwal' AND tanggal = '$tgl_skrg'");
                            $is_done = mysqli_num_rows($cek_absen) > 0;
                            $data_absen = mysqli_fetch_assoc($cek_absen);
                        ?>
                        <tr class="hover:bg-indigo-50/30 transition group">
                            <td class="p-6">
                                <p class="font-black text-slate-800 uppercase text-xs mb-1"><?php echo $r['hari']; ?></p>
                                <p class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md inline-block"><?php echo date('H:i', strtotime($r['jam'])); ?> WIB</p>
                            </td>
                            <td class="p-6">
                                <p class="font-black text-slate-800 uppercase text-sm leading-none"><?php echo $r['nama_murid']; ?></p>
                                <p class="text-[9px] text-slate-400 font-bold uppercase mt-1 italic tracking-widest"><?php echo $r['alat_musik']; ?></p>
                            </td>
                            <td class="p-6">
                                <?php if($is_done): ?>
                                    <div class="flex items-center gap-2">
                                        <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-tighter italic">Selesai Absen</span>
                                        <span class="text-[10px] font-bold text-slate-400">Rp <?php echo number_format($data_absen['nominal_bayar']); ?></span>
                                    </div>
                                <?php else: ?>
                                    <span class="bg-slate-100 text-slate-400 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-tighter italic">Belum Ada Sesi</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-6 text-center">
                                <?php if(!$is_done): ?>
                                    <form method="POST" class="flex items-center justify-center gap-2">
                                        <input type="hidden" name="id_jadwal" value="<?php echo $r['id']; ?>">
                                        <input type="number" name="nominal_bayar" class="w-24 p-2 border-2 border-slate-100 rounded-lg text-xs font-bold focus:border-indigo-600 outline-none" placeholder="Nominal SPP" required>
                                        <button type="submit" name="absen" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-[10px] font-black uppercase hover:bg-indigo-700 transition shadow-md">Kirim</button>
                                    </form>
                                <?php else: ?>
                                    <?php if(isset($_GET['edit_absen']) && $_GET['edit_absen'] == $data_absen['id']): ?>
                                        <form method="POST" class="flex items-center justify-center gap-2">
                                            <input type="hidden" name="id_edit" value="<?php echo $data_absen['id']; ?>">
                                            <input type="number" name="nominal_bayar" value="<?php echo $data_absen['nominal_bayar']; ?>" class="w-24 p-2 border-2 border-indigo-600 rounded-lg text-xs font-bold outline-none" required>
                                            <button type="submit" name="absen" class="bg-green-600 text-white px-4 py-2 rounded-lg text-[10px] font-black uppercase">Simpan</button>
                                            <a href="index.php" class="text-slate-400 text-xs"><i class="fas fa-times"></i></a>
                                        </form>
                                    <?php else: ?>
                                        <a href="index.php?edit_absen=<?php echo $data_absen['id']; ?>" class="inline-flex items-center gap-2 text-indigo-500 hover:text-indigo-700 font-black text-[10px] uppercase tracking-widest transition">
                                            <i class="fas fa-edit"></i> Edit Absensi
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-12 text-center">
            <p class="text-slate-300 text-[9px] font-black uppercase tracking-[0.5em] italic">&copy; Teacher Dashboard - Smart Arca System</p>
        </div>
    </div>

</body>
</html>
