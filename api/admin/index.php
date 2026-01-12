<?php
session_start();
ob_start();

// 1. KONEKSI KE DATABASE
require_once(__DIR__ . '/../koneksi.php');

// 2. SISTEM DOUBLE CHECK (Proteksi Halaman agar tidak mental ke login)
// Cek apakah ada Session login ATAU Cookie login yang tersimpan
$is_logged_in = (isset($_SESSION['status']) && $_SESSION['status'] == 'login') || isset($_COOKIE['user_login']);
$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'admin');

if (!$is_logged_in || !$is_admin) {
    // Jika tidak terdeteksi login sebagai admin, paksa balik ke login
    echo "<script>window.location.replace('login.php');</script>";
    exit();
}

// Gunakan nama dari session, jika kosong ambil dari cookie
$display_name = $_SESSION['username'] ?? $_COOKIE['user_login'];

// 3. AMBIL DATA DARI DATABASE
// Ambil daftar User
$query_users = mysqli_query($conn, "SELECT * FROM users ORDER BY role ASC, username ASC");

// Ambil Jadwal dengan JOIN Guru dan Murid
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
    <title>Dashboard Admin - Smart Arca</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; margin: 0; padding: 20px; background-color: #f4f7f9; color: #333; }
        .container { max-width: 1100px; margin: auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.1); }
        
        header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 30px; }
        h1 { color: #1a73e8; margin: 0; font-size: 24px; }
        .user-info { text-align: right; font-size: 14px; color: #666; }
        
        h2 { color: #444; margin: 35px 0 15px 0; border-left: 5px solid #1a73e8; padding-left: 15px; font-size: 18px; }
        
        .nav-buttons { margin-bottom: 25px; display: flex; gap: 10px; flex-wrap: wrap; }
        .btn { padding: 10px 20px; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 13px; transition: 0.3s; border: none; cursor: pointer; }
        .btn-murid { background-color: #28a745; }
        .btn-guru { background-color: #007bff; }
        .btn-jadwal { background-color: #6f42c1; }
        .btn-keuangan { background-color: #ffc107; color: #212529; }
        .btn-logout { background-color: #dc3545; }
        .btn:hover { opacity: 0.8; transform: translateY(-2px); }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #f8f9fa; color: #555; font-size: 12px; text-transform: uppercase; }
        
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .badge-admin { background: #fff3cd; color: #856404; }
        .badge-guru { background: #cfe2ff; color: #084298; }
        .badge-murid { background: #d1e7dd; color: #0f5132; }
        
        .text-blue { color: #1a73e8; font-weight: bold; }
        tr:hover { background-color: #f9f9f9; }
    </style>
</head>
<body>

<div class="container">
    <header>
        <div>
            <h1>Smart Arca Admin</h1>
            <p style="margin: 5px 0 0 0; color: #888;">Manajemen Sekolah Musik</p>
        </div>
        <div class="user-info">
            Selamat Datang, <strong><?php echo htmlspecialchars($display_name); ?></strong><br>
            <span style="color: #28a745; font-size: 12px;">● Sesi Aktif (Vercel Optimized)</span>
        </div>
    </header>

    <div class="nav-buttons">
        <a href="tambah_murid.php" class="btn btn-murid">+ DATA MURID</a>
        <a href="tambah_guru.php" class="btn btn-guru">+ DATA GURU</a>
        <a href="tambah_jadwal.php" class="btn btn-jadwal">+ ATUR JADWAL</a>
        <a href="keuangan.php" class="btn btn-keuangan">💰 KEUANGAN</a>
        <a href="/api/logout.php" class="btn btn-logout" style="margin-left: auto;">KELUAR</a>
    </div>

    <h2>Jadwal & Kelas Mengajar</h2>
    <table>
        <thead>
            <tr>
                <th>Hari & Jam</th>
                <th>Guru</th>
                <th>Murid</th>
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
                    <td class="text-blue"><?php echo $j['alat_musik']; ?></td>
                </tr>
            <?php } } else { ?>
                <tr><td colspan="4" style="text-align:center; padding: 30px; color: #999;">Belum ada jadwal yang diinput.</td></tr>
            <?php } ?>
        </tbody>
    </table>

    <h2>Daftar Akun Sistem</h2>
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Role / Hak Akses</th>
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
                        <a href="hapus_user.php?id=<?php echo $user['id']; ?>" style="color: #dc3545; font-size: 11px; text-decoration: none; font-weight: bold;" onclick="return confirm('Yakin ingin menghapus akun ini?')">HAPUS</a>
                    <?php } else { echo "-"; } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
