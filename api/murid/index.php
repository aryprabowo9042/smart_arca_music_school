<?php
// api/murid/index.php

// 1. CEK COOKIE (DEBUG MODE)
// Kita matikan redirect header("Location: ...") agar tidak mental-mental.
$role = isset($_COOKIE['user_role']) ? $_COOKIE['user_role'] : '';
$id_murid = isset($_COOKIE['user_id']) ? $_COOKIE['user_id'] : '';

// Jika Cookie Kosong atau Salah Role
if ($role != 'murid') {
    ?>
    <!DOCTYPE html>
    <html>
    <head><script src="https://cdn.tailwindcss.com"></script></head>
    <body class="bg-red-50 p-10 text-center flex flex-col items-center justify-center min-h-screen">
        <div class="bg-white p-8 rounded-2xl shadow-xl max-w-lg">
            <h1 class="text-3xl font-bold text-red-600 mb-2">AKSES DITOLAK (DEBUG)</h1>
            <p class="text-gray-600 mb-6">Dashboard Murid tidak bisa membaca data login Anda.</p>
            
            <div class="bg-gray-100 p-4 rounded text-left text-sm font-mono mb-6 w-full">
                <p><strong>Status Cookie:</strong> <?php echo $role ? $role : "KOSONG (Tidak terbaca)"; ?></p>
                <p><strong>ID Murid:</strong> <?php echo $id_murid ? $id_murid : "KOSONG"; ?></p>
                <p><strong>Yang Diharapkan:</strong> murid</p>
            </div>

            <a href="../admin/login.php" class="bg-red-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-red-700 transition">
                ULANGI LOGIN
            </a>
        </div>
    </body>
    </html>
    <?php
    exit(); // Stop script disini
}

// --- JIKA LOLOS, LANJUT KE DASHBOARD ---
require_once(__DIR__ . '/../koneksi.php');

$username = $_COOKIE['user_username'] ?? 'Siswa';

// Query Data
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

    <div class="bg-blue-600 text-white p-6 rounded-b-[30px] shadow-lg mb-6 flex justify-between items-start">
        <div>
            <p class="text-blue-200 text-xs uppercase tracking-wider mb-1">Halo Siswa,</p>
            <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($username); ?> 👋</h1>
        </div>
        <a href="index.php?action=logout" onclick="document.cookie='user_role=; path=/;'; window.location='../admin/login.php';" class="bg-white/20 p-2 rounded-lg text-sm hover:bg-white/30 transition">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </a>
    </div>

    <div class="max-w-md mx-auto px-4">
        <h3 class="font-bold text-gray-700 mb-4 border-l-4 border-blue-600 pl-3">Riwayat Les</h3>

        <?php if(mysqli_num_rows($query) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($query)) { ?>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-blue-50 mb-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-1 rounded"><?php echo $row['alat_musik']; ?></span>
                    <span class="text-xs text-gray-400"><?php echo date('d M Y', strtotime($row['tanggal'])); ?></span>
                </div>
                <p class="text-sm font-bold text-gray-700 mb-1"><?php echo $row['nama_guru']; ?></p>
                <p class="text-sm text-gray-500 mb-3"><?php echo $row['materi_ajar']; ?></p>
                
                <div class="flex justify-between items-center border-t pt-3">
                    <span class="font-bold text-green-600">Rp <?php echo number_format($row['nominal_bayar']); ?></span>
                    <a href="../cetak_kuitansi.php?id=<?php echo $row['id']; ?>" class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold">BUKTI</a>
                </div>
            </div>
            <?php } ?>
        <?php else: ?>
            <div class="text-center py-10 text-gray-400">
                <i class="fas fa-box-open text-4xl mb-2"></i>
                <p>Belum ada data absensi.</p>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
