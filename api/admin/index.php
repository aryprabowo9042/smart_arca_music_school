<?php
session_start();
ob_start(); 
// Pastikan tidak ada spasi atau baris kosong sebelum tag <?php di atas

// 1. KONEKSI KE DATABASE
require_once(__DIR__ . '/../koneksi.php');

// 2. PROTEKSI HALAMAN (SATURAN LOGIN)
if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login' || $_SESSION['role'] != 'admin') {
    // Jika belum login atau bukan admin, lempar ke login
    echo '<meta http-equiv="refresh" content="0;url=login.php">';
    exit();
}

// 3. AMBIL DATA USER UNTUK TABEL AKUN
$query_users = mysqli_query($conn, "SELECT * FROM users ORDER BY role ASC, username ASC");

// 4. AMBIL DATA JADWAL DENGAN JOIN (GURU & MURID)
$sql_jadwal = "SELECT jadwal.*, g.username as nama_guru, m.username as nama_murid 
               FROM jadwal 
               JOIN users g ON jadwal.id_guru = g.id 
               JOIN users m ON jadwal.id_murid = m.id 
               ORDER BY FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'), jam ASC";
$query_jadwal = mysqli_query($conn, $sql_jadwal);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Smart Arca</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; padding: 20px; background-color: #f0f2f5; color: #333; }
        .container { max-width: 1100px; margin: auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        
        header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 30px; }
        h1 { color: #1a73e8; margin: 0; font-size: 24px; }
        .user-info { text-align: right; font-size: 14px; color: #666; }
        
        h2 { color: #444; margin-top: 40px; border-left: 5px solid #1a73e8; padding-left: 15px; font-size: 18px; }
        
        .action-buttons { margin-bottom: 25px; display: flex; gap: 10px; flex-wrap: wrap; }
        
        .btn { display: inline-block; padding: 10px 18px; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 13px; transition: 0.3s; border: none; cursor: pointer; }
        .btn-murid { background-color: #28a745; }
        .btn-guru { background-color: #007bff; }
        .btn-jadwal { background-color: #6f42c1; }
        .btn-keuangan { background-color: #ffc107; color: #212529; }
        .btn-logout { background-color: #dc3545; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; background: #fff; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #f8f9fa; color: #5f6368; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; }
        
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .badge-admin { background: #fff3cd; color: #856404; }
        .badge-guru { background: #cfe2ff; color: #084298; }
        .badge-murid { background: #d1e7dd; color: #0f5132; }
        
        .alat-musik { font-weight: bold; color: #1a73e8; }
        tr:hover { background-color: #fafafa; }
    </style>
</head>
<body>

<div class="container">
    <header>
        <div>
            <h1>Smart Arca Management</h1>
            <p style="margin: 5px 0 0 0; color: #888;">Administrator Control Panel</p>
        </div>
        <div class="user-info">
            Login sebagai: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong><br>
            Status: <span style="color: green;">● Online</span>
        </div>
    </header>

    <div class="action-buttons">
        <a href="tambah_murid.php" class="btn btn-murid">+ MURID</a>
        <a href="tambah_guru.php" class="btn btn-guru">+ GURU</a>
        <a href="tambah_jadwal.php" class="btn btn-jadwal">+ ATUR JADWAL</a>
        <a href="keuangan.php" class="btn btn-keuangan">💰 KEUANGAN</a>
        <a href="/api/logout.php" class="btn btn-logout" style="margin-left: auto;">LOGOUT</a>
    </div>

    <h2>Jadwal Les Musik Aktif</h2>
    <table>
        <thead>
            <tr>
                <th>Hari / Jam</th>
                <th>Guru Pengajar</th>
                <th>Nama Murid</th>
                <th>Alat Musik</th>
            </tr>
        </thead>
        <tbody>
            <?php if($query_jadwal && mysqli_num_rows($query_jadwal) > 0) {
                while($j = mysqli_fetch_assoc($query_jadwal)) { ?>
                <tr>
                    <td><strong><?php echo $j['hari']; ?></strong><br><small><?php echo date('H:i', strtotime($j['jam'])); ?> WIB</small></td>
                    <td><?php echo htmlspecialchars($j['nama_guru']); ?></td>
                    <td><?php echo htmlspecialchars($j['nama_murid']); ?></td>
                    <td><span class="alat-musik"><?php echo $j['alat_musik']; ?></span></td>
                </tr>
            <?php } } else { ?>
                <tr><td colspan="4" style="text-align:center; padding: 20px; color: #999;">Belum ada jadwal yang diatur.</td></tr>
            <?php } ?>
        </tbody>
    </table>

    <h2>Daftar Akun Pengguna</h2>
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Hak Akses</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while($user = mysqli_fetch_assoc($query_users)) { 
                $roleClass = 'badge-murid';
                if($user['role'] == 'admin') $roleClass = 'badge-admin';
                if($user['role'] == 'guru') $roleClass = 'badge-guru';
            ?>
            <tr>
                <td style="font-weight: 500;"><?php echo htmlspecialchars($user['username']); ?></td>
                <td><span class="badge <?php echo $roleClass; ?>"><?php echo strtoupper($user['role']); ?></span></td>
                <td>
                    <?php if($user['role'] != 'admin') { ?>
                        <a href="hapus_user.php?id=<?php echo $user['id']; ?>" style="color: #dc3545; font-size: 12px; font-weight: bold; text-decoration: none;" onclick="return confirm('Hapus akun ini?')">HAPUS</a>
                    <?php } else { echo "-"; } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
