<?php
session_start();
// Mengaktifkan laporan error
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Panggil koneksi (Naik satu tingkat ke folder api)
require_once(__DIR__ . '/../koneksi.php');

// 1. Ambil data semua user
$query_users = mysqli_query($conn, "SELECT * FROM users ORDER BY role ASC, username ASC");

// 2. Ambil data Jadwal dengan JOIN untuk menampilkan nama Guru dan Murid
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
        
        h1 { color: #1a73e8; margin-bottom: 5px; }
        h2 { color: #444; margin-top: 40px; border-left: 5px solid #1a73e8; padding-left: 15px; }
        
        .action-buttons { margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap; }
        
        .btn { display: inline-block; padding: 10px 18px; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 13px; transition: 0.3s; }
        .btn-murid { background-color: #28a745; }
        .btn-guru { background-color: #007bff; }
        .btn-jadwal { background-color: #6f42c1; }
        .btn-logout { background-color: #dc3545; }
        .btn-hapus { background-color: #dc3545; padding: 5px 10px; font-size: 11px; }

        table { width: 100%; border-collapse: collapse; margin-top: 15px; background: #fff; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #f8f9fa; color: #5f6368; font-size: 12px; text-transform: uppercase; }
        
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .badge-admin { background: #fff3cd; color: #856404; }
        .badge-guru { background: #cfe2ff; color: #084298; }
        .badge-murid { background: #d1e7dd; color: #0f5132; }
        
        .alat-musik { font-weight: bold; color: #1a73e8; }
    </style>
</head>
<body>

<div class="container">
    <h1>Smart Arca Management</h1>
    <p style="color: #666;">Selamat Datang, Administrator</p>

    <div class="action-buttons">
        <a href="tambah_murid.php" class="btn btn-murid">+ MURID</a>
        <a href="tambah_guru.php" class="btn btn-guru">+ GURU</a>
        <a href="tambah_jadwal.php" class="btn btn-jadwal">+ ATUR JADWAL LES</a>
        <a href="../logout.php" class="btn btn-logout" style="margin-left: auto;">KELUAR</a>
    </div>

    <h2>Jadwal & Kelas Mengajar</h2>
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
            <?php if(mysqli_num_rows($query_jadwal) > 0) {
                while($j = mysqli_fetch_assoc($query_jadwal)) { ?>
                <tr>
                    <td><strong><?php echo $j['hari']; ?></strong>, <?php echo date('H:i', strtotime($j['jam'])); ?></td>
                    <td><?php echo htmlspecialchars($j['nama_guru']); ?></td>
                    <td><?php echo htmlspecialchars($j['nama_murid']); ?></td>
                    <td><span class="alat-musik"><?php echo $j['alat_musik']; ?></span></td>
                </tr>
            <?php } } else { ?>
                <tr><td colspan="4" style="text-align:center;">Belum ada jadwal yang diatur.</td></tr>
            <?php } ?>
        </tbody>
    </table>

    <h2>Daftar Akun Pengguna</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Username</th>
                <th>Hak Akses</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while($user = mysqli_fetch_assoc($query_users)) { 
                $roleClass = 'badge-murid';
                if($user['role'] == 'admin') $roleClass = 'badge-admin';
                if($user['role'] == 'guru') $roleClass = 'badge-guru';
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td style="font-weight: 500;"><?php echo htmlspecialchars($user['username']); ?></td>
                <td><span class="badge <?php echo $roleClass; ?>"><?php echo strtoupper($user['role']); ?></span></td>
                <td>
                    <?php if($user['role'] != 'admin') { ?>
                        <a href="hapus_user.php?id=<?php echo $user['id']; ?>" class="btn btn-hapus" onclick="return confirm('Hapus user ini?')">HAPUS</a>
                    <?php } else { echo "-"; } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
