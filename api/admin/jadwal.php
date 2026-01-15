<?php
session_start();
ob_start();

// 1. Cek Login Admin
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

require_once(__DIR__ . '/../koneksi.php');

// --- 2. PROSES TAMBAH JADWAL ---
if (isset($_POST['tambah_jadwal'])) {
    $id_guru  = $_POST['id_guru'];
    $id_murid = $_POST['id_murid'];
    $hari     = $_POST['hari'];
    $jam      = $_POST['jam'];
    $alat     = mysqli_real_escape_string($conn, $_POST['alat_musik']);

    $sql = "INSERT INTO jadwal (id_guru, id_murid, hari, jam, alat_musik) 
            VALUES ('$id_guru', '$id_murid', '$hari', '$jam', '$alat')";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: jadwal.php");
        exit();
    }
}

// --- 3. PROSES HAPUS JADWAL ---
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM jadwal WHERE id = '$id'");
    header("Location: jadwal.php");
    exit();
}

// --- 4. AMBIL DATA GURU & MURID UNTUK DROPDOWN ---
// Ini yang membuat pilihan di dropdown selalu update otomatis
$list_guru  = mysqli_query($conn, "SELECT id, username FROM users WHERE role = 'guru' ORDER BY username ASC");
$list_murid = mysqli_query($conn, "SELECT id, username FROM users WHERE role = 'murid' ORDER BY username ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Jadwal - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen pb-10">

    <nav class="bg-white shadow-sm px-6 py-4 flex justify-between items-center mb-6">
        <div class="flex items-center gap-3">
            <a href="index.php" class="text-gray-500 hover:text-blue-600 transition"><i class="fas fa-arrow-left"></i></a>
            <h1 class="text-xl font-bold text-gray-800">Manajemen Jadwal Les</h1>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 sticky top-24">
                <h3 class="font-bold text-gray-700 mb-4 border-l-4 border-blue-600 pl-3">Tambah Jadwal Baru</h3>
                
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase">Pilih Guru</label>
                        <select name="id_guru" class="w-full p-2 border rounded-lg bg-gray-50 outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">-- Pilih Guru --</option>
                            <?php while($g = mysqli_fetch_assoc($list_guru)): ?>
                                <option value="<?php echo $g['id']; ?>"><?php echo htmlspecialchars($g['username']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase">Pilih Murid</label>
                        <select name="id_murid" class="w-full p-2 border rounded-lg bg-gray-50 outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">-- Pilih Murid --</option>
                            <?php while($m = mysqli_fetch_assoc($list_murid)): ?>
                                <option value="<?php echo $m['id']; ?>"><?php echo htmlspecialchars($m['username']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase">Hari</label>
                            <select name="hari" class="w-full p-2 border rounded-lg bg-gray-50 outline-none" required>
                                <option>Senin</option><option>Selasa</option><option>Rabu</option>
                                <option>Kamis</option><option>Jumat</option><option>Sabtu</option><option>Minggu</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase">Jam</label>
                            <input type="time" name="jam" class="w-full p-2 border rounded-lg bg-gray-50 outline-none" required>
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase">Alat Musik</label>
                        <input type="text" name="alat_musik" placeholder="Contoh: Piano, Gitar, Vokal" class="w-full p-2 border rounded-lg bg-gray-50 outline-none" required>
                    </div>

                    <button type="submit" name="tambah_jadwal" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-blue-100">
                        SIMPAN JADWAL
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-400 text-xs uppercase">
                        <tr>
                            <th class="p-4">Hari / Jam</th>
                            <th class="p-4">Guru</th>
                            <th class="p-4">Murid</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php 
                        $q_tabel = mysqli_query($conn, "
                            SELECT j.*, g.username as nama_guru, m.username as nama_murid 
                            FROM jadwal j
                            JOIN users g ON j.id_guru = g.id
                            JOIN users m ON j.id_murid = m.id
                            ORDER BY FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'), jam ASC
                        ");
                        while($row = mysqli_fetch_assoc($q_tabel)):
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="p-4">
                                <span class="font-bold text-blue-600"><?php echo $row['hari']; ?></span><br>
                                <span class="text-xs text-gray-400"><?php echo $row['jam']; ?></span>
                            </td>
                            <td class="p-4 font-medium"><?php echo $row['nama_guru']; ?></td>
                            <td class="p-4">
                                <span class="font-medium text-gray-800"><?php echo $row['nama_murid']; ?></span><br>
                                <span class="text-[10px] bg-gray-100 px-1 rounded"><?php echo $row['alat_musik']; ?></span>
                            </td>
                            <td class="p-4 text-center">
                                <a href="jadwal.php?hapus=<?php echo $row['id']; ?>" onclick="return confirm('Hapus jadwal ini?')" class="text-red-400 hover:text-red-600">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php if(mysqli_num_rows($q_tabel) == 0): ?>
                    <div class="p-10 text-center text-gray-400 text-xs italic">Belum ada jadwal yang diatur.</div>
                <?php endif; ?>
            </div>
        </div>

    </div>

</body>
</html>
