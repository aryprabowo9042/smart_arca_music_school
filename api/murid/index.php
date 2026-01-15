<?php
// 1. PRIORITAS UTAMA: PROSES LOGOUT DULUAN
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    setcookie('user_role', '', time() - 3600, '/');
    setcookie('user_id', '', time() - 3600, '/');
    setcookie('user_username', '', time() - 3600, '/');
    header("Location: ../../index.php");
    exit();
}

// 2. CEK LOGIN (Tanpa Layar Merah, langsung lempar ke login jika tiket hilang)
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'murid') {
    header("Location: ../admin/login.php");
    exit();
}

require_once(__DIR__ . '/../koneksi.php');

$id_murid = $_COOKIE['user_id'];
$username = $_COOKIE['user_username'] ?? 'Siswa';

// QUERY RIWAYAT
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
    <title>Portal Siswa - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-blue-50 min-h-screen pb-10">
    <div class="bg-blue-600 text-white p-6 rounded-b-[30px] shadow-lg mb-6 flex justify-between items-center sticky top-0 z-50">
        <div>
            <p class="text-blue-200 text-xs uppercase tracking-wider mb-1">Halo,</p>
            <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($username); ?> 👋</h1>
        </div>
        <a href="index.php?action=logout" class="bg-white/20 p-2 rounded-lg text-sm hover:bg-white/30 transition">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </a>
    </div>

    <div class="max-w-md mx-auto px-4">
        <h3 class="font-bold text-gray-700 mb-4 border-l-4 border-blue-600 pl-3 text-lg">Riwayat & Pembayaran</h3>
        <?php if(mysqli_num_rows($query) > 0): ?>
            <div class="space-y-4">
                <?php while($row = mysqli_fetch_assoc($query)) { ?>
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-blue-50">
                    <div class="flex justify-between items-center mb-3">
                        <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-1 rounded"><?php echo $row['alat_musik']; ?></span>
                        <span class="text-xs text-gray-400"><?php echo date('d M Y', strtotime($row['tanggal'])); ?></span>
                    </div>
                    <div class="mb-3">
                        <p class="text-sm font-bold text-gray-700"><?php echo $row['nama_guru']; ?></p>
                        <p class="text-sm text-gray-500"><?php echo $row['materi_ajar']; ?></p>
                    </div>
                    <div class="flex justify-between items-center border-t pt-3">
                        <p class="text-green-600 font-bold text-lg">Rp <?php echo number_format($row['nominal_bayar']); ?></p>
                        <a href="../cetak_kuitansi.php?id=<?php echo $row['id']; ?>" target="_blank" class="bg-blue-600 text-white px-3 py-1 rounded-xl text-xs font-bold shadow">KUITANSI</a>
                    </div>
                </div>
                <?php } ?>
            </div>
        <?php else: ?>
            <div class="bg-white p-10 rounded-2xl text-center shadow-sm">
                <i class="fas fa-music text-4xl text-gray-200 mb-3"></i>
                <h4 class="font-bold text-gray-600">Belum Ada Data</h4>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
