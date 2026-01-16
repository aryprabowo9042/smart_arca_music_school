<?php
// 1. PROTEKSI HALAMAN
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

require_once(__DIR__ . '/../koneksi.php');

// ==========================================
// 2. PROSES DATA (SIMPAN / EDIT / HAPUS)
// ==========================================

// Simpan & Update Jadwal
if (isset($_POST['simpan_jadwal'])) {
    $id_edit = $_POST['id_edit'];
    $id_guru = $_POST['id_guru'];
    $id_murid = $_POST['id_murid'];
    $hari = $_POST['hari'];
    $jam = $_POST['jam'];
    $alat = mysqli_real_escape_string($conn, $_POST['alat_musik']);

    if (!empty($id_edit)) {
        $sql = "UPDATE jadwal SET id_guru='$id_guru', id_murid='$id_murid', hari='$hari', jam='$jam', alat_musik='$alat' WHERE id='$id_edit'";
    } else {
        $sql = "INSERT INTO jadwal (id_guru, id_murid, hari, jam, alat_musik) VALUES ('$id_guru', '$id_murid', '$hari', '$jam', '$alat')";
    }
    mysqli_query($conn, $sql);
    header("Location: jadwal.php"); exit();
}

// Hapus Jadwal
if (isset($_GET['hapus'])) {
    $id_h = mysqli_real_escape_string($conn, $_GET['hapus']);
    mysqli_query($conn, "DELETE FROM jadwal WHERE id = '$id_h'");
    header("Location: jadwal.php"); exit();
}

// Data Edit (Untuk memicu Form Edit)
$edit_data = ['id' => '', 'id_guru' => '', 'id_murid' => '', 'hari' => '', 'jam' => '', 'alat_musik' => ''];
if (isset($_GET['edit'])) {
    $id_e = mysqli_real_escape_string($conn, $_GET['edit']);
    $res_e = mysqli_query($conn, "SELECT * FROM jadwal WHERE id = '$id_e'");
    if($res_e && mysqli_num_rows($res_e) > 0) $edit_data = mysqli_fetch_assoc($res_e);
}

// Ambil Data Relasi (Guru & Murid)
$gurus = mysqli_query($conn, "SELECT id, username FROM users WHERE role='guru'");
$murids = mysqli_query($conn, "SELECT id, username FROM users WHERE role='murid'");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Jadwal - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen pb-20">

    <nav class="bg-red-600 shadow-xl px-6 py-4 flex justify-between items-center mb-10 border-b-4 border-yellow-400 sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <a href="index.php" class="text-white bg-red-700 w-10 h-10 rounded-xl flex items-center justify-center hover:bg-red-800 transition shadow-inner">
                <i class="fas fa-home"></i>
            </a>
            <h1 class="text-white font-black text-xl italic uppercase tracking-tighter">Jadwal Kursus</h1>
        </div>
        <a href="../logout.php" class="text-white hover:text-yellow-300 text-2xl transition"><i class="fas fa-sign-out-alt"></i></a>
    </nav>

    <div class="max-w-6xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-1">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border-t-8 border-yellow-400 sticky top-28">
                <h3 class="font-black text-slate-800 text-sm uppercase italic tracking-widest mb-6 border-l-4 border-red-600 pl-3">
                    <?php echo $edit_data['id'] ? 'Edit Jadwal' : 'Tambah Jadwal'; ?>
                </h3>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="id_edit" value="<?php echo $edit_data['id']; ?>">
                    
                    <div>
                        <label class="text-[9px] font-black text-slate-400 uppercase ml-1">Pilih Guru</label>
                        <select name="id_guru" class="w-full p-3 border-2 border-slate-50 rounded-xl bg-slate-50 font-bold text-xs outline-none focus:border-red-600" required>
                            <?php mysqli_data_seek($gurus, 0); while($g = mysqli_fetch_assoc($gurus)): ?>
                            <option value="<?php echo $g['id']; ?>" <?php echo ($edit_data['id_guru'] == $g['id']) ? 'selected' : ''; ?>><?php echo $g['username']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div>
                        <label class="text-[9px] font-black text-slate-400 uppercase ml-1">Pilih Murid</label>
                        <select name="id_murid" class="w-full p-3 border-2 border-slate-50 rounded-xl bg-slate-50 font-bold text-xs outline-none focus:border-red-600" required>
                            <?php mysqli_data_seek($murids, 0); while($m = mysqli_fetch_assoc($murids)): ?>
                            <option value="<?php echo $m['id']; ?>" <?php echo ($edit_data['id_murid'] == $m['id']) ? 'selected' : ''; ?>><?php echo $m['username']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase ml-1">Hari</label>
                            <select name="hari" class="w-full p-3 border-2 border-slate-50 rounded-xl bg-slate-50 font-bold text-xs outline-none focus:border-red-600">
                                <?php $days = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
                                foreach($days as $day): ?>
                                <option value="<?php echo $day; ?>" <?php echo ($edit_data['hari'] == $day) ? 'selected' : ''; ?>><?php echo $day; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase ml-1">Jam</label>
                            <input type="time" name="jam" value="<?php echo $edit_data['jam']; ?>" class="w-full p-3 border-2 border-slate-50 rounded-xl bg-slate-50 font-bold text-xs outline-none focus:border-red-600" required>
                        </div>
                    </div>

                    <div>
                        <label class="text-[9px] font-black text-slate-400 uppercase ml-1">Alat Musik</label>
                        <input type="text" name="alat_musik" value="<?php echo $edit_data['alat_musik']; ?>" class="w-full p-3 border-2 border-slate-50 rounded-xl bg-slate-50 font-bold text-xs outline-none focus:border-red-600" placeholder="Contoh: Piano, Gitar..." required>
                    </div>

                    <button type="submit" name="simpan_jadwal" class="w-full bg-red-600 hover:bg-red-700 text-yellow-400 font-black py-4 rounded-2xl shadow-xl transition active:scale-95 uppercase text-xs tracking-widest mt-4">
                        <?php echo $edit_data['id'] ? 'Update Jadwal' : 'Simpan Jadwal'; ?>
                    </button>
                    <?php if($edit_data['id']): ?>
                        <a href="jadwal.php" class="block text-center text-[10px] font-black text-slate-400 uppercase mt-2">Batal Edit</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-100">
                <div class="p-6 bg-slate-50 border-b flex justify-between items-center">
                    <h3 class="font-black text-slate-800 uppercase text-xs italic tracking-widest">Daftar Pertemuan Aktif</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-slate-400 text-[9px] uppercase font-black border-b">
                            <tr>
                                <th class="p-5">Hari & Jam</th>
                                <th class="p-5">Murid</th>
                                <th class="p-5">Guru / Alat</th>
                                <th class="p-5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php 
                            $sql_j = "SELECT j.*, g.username as nama_guru, m.username as nama_murid 
                                      FROM jadwal j 
                                      JOIN users g ON j.id_guru = g.id 
                                      JOIN users m ON j.id_murid = m.id 
                                      ORDER BY FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'), jam ASC";
                            $res_j = mysqli_query($conn, $sql_j);
                            while($r = mysqli_fetch_assoc($res_j)): ?>
                            <tr class="hover:bg-red-50/50 transition group">
                                <td class="p-5">
                                    <p class="font-black text-slate-800 uppercase text-xs mb-1"><?php echo $r['hari']; ?></p>
                                    <p class="text-[10px] font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-md inline-block tracking-tighter"><?php echo date('H:i', strtotime($r['jam'])); ?> WIB</p>
                                </td>
                                <td class="p-5">
                                    <p class="font-black text-slate-800 uppercase text-xs leading-none"><?php echo $r['nama_murid']; ?></p>
                                    <p class="text-[9px] text-slate-400 font-bold uppercase mt-1 tracking-tighter italic">Siswa Aktif</p>
                                </td>
                                <td class="p-5">
                                    <p class="font-black text-slate-800 uppercase text-xs leading-none mb-1"><?php echo $r['alat_musik']; ?></p>
                                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter">Guru: <?php echo $r['nama_guru']; ?></p>
                                </td>
                                <td class="p-5 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="jadwal.php?edit=<?php echo $r['id']; ?>" class="w-8 h-8 bg-yellow-400 text-red-700 rounded-lg flex items-center justify-center hover:bg-red-600 hover:text-white transition shadow-sm">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        <a href="jadwal.php?hapus=<?php echo $r['id']; ?>" onclick="return confirm('Hapus jadwal ini?')" class="w-8 h-8 bg-slate-100 text-slate-400 rounded-lg flex items-center justify-center hover:bg-red-600 hover:text-white transition shadow-sm">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </a>
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
