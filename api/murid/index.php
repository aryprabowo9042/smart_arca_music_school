<?php
// 1. CEK LOGOUT
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    setcookie('user_role', '', time() - 3600, '/');
    setcookie('user_id', '', time() - 3600, '/');
    setcookie('user_username', '', time() - 3600, '/');
    header("Location: ../admin/login.php");
    exit();
}

// 2. CEK LOGIN
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'murid') {
    header("Location: ../admin/login.php");
    exit();
}

// --- PERBAIKAN PENTING DI SINI ---
// Cek apakah ID ada? Jika tidak, suruh login ulang biar dapat ID baru
if (!isset($_COOKIE['user_id'])) {
    header("Location: ../admin/login.php");
    exit();
}

require_once(__DIR__ . '/../koneksi.php');

// 3. AMBIL DATA
$id_murid = $_COOKIE['user_id']; // Baris ini sekarang aman karena sudah dicek di atas
$username = $_COOKIE['user_username'] ?? 'Siswa';

// 4. QUERY
$sql = "SELECT a.*, j.alat_musik, g.username as nama_guru 
        FROM absensi a 
        JOIN jadwal j ON a.id_jadwal = j.id 
        JOIN users g ON j.id_guru = g.id 
        WHERE j.id_murid = '$id_murid' 
        ORDER BY a.tanggal DESC";

$query = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-blue-50 min-h-screen pb-10">
    <div class="bg-blue-600 text-white p-6 rounded-b-[30px] shadow-lg mb-6 sticky top-0 z-50">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-blue-200 text-xs uppercase tracking-wider mb-1">Selamat Datang,</p>
                <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($username); ?> 👋</h1>
            </div>
            <a href="index.php?action=logout" class="bg-white/20 hover:bg-white/30 p-2 rounded-lg transition text-sm font-medium backdrop-blur-sm">
                <i class="fas fa-sign-out-alt"></i> Keluar
            </a>
        </div>
    </div>

    <div class="max-w-md mx-auto px-4">
        <h3 class="font-bold text-gray-700 mb-4 border-l-4 border-blue-600 pl-3">Riwayat Les</h3>
        
        <?php if(mysqli_num_rows($query) > 0): ?>
            <div class="space-y-4">
            <?php while($row = mysqli_fetch_assoc($query)) { ?>
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-blue-50">
                    <div class="flex justify-between items-center mb-2">
                        <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-1 rounded"><?php echo $row['alat_musik']; ?></span>
                        <span class="text-xs text-gray-400"><?php echo date('d M Y', strtotime($row['tanggal'])); ?></span>
                    </div>
                    <p class="font-bold text-gray-700"><?php echo $row['nama_guru']; ?></p>
                    <p class="text-sm text-gray-500 mb-3"><?php echo $row['materi_ajar']; ?></p>
                    <div class="flex justify-between items-center border-t pt-3">
                        <strong class="text-green-600">Rp <?php echo number_format($row['nominal_bayar']); ?></strong>
                        <a href="../cetak_kuitansi.php?id=<?php echo $row['id']; ?>" target="_blank" class="bg-blue-600 text-white px-3 py-1 rounded text-xs font-bold">KUITANSI</a>
                    </div>
                </div>
            <?php } ?>
            </div>
        <?php else: ?>
            <div class="text-center py-10 text-gray-400">
                <p>Belum ada data les.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
