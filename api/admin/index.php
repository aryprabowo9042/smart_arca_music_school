<?php
require_once(__DIR__ . '/../koneksi.php');
// Satpam dimatikan sementara agar Bapak bisa masuk
?>
// Jika tidak ada session login, kembalikan ke login
if (!isset($_SESSION['status'])) {
    header("Location: login.php");
    exit();
}

// JIKA GURU NYASAR KE SINI, LEMPAR KE HALAMAN GURU
if ($_SESSION['role'] == 'guru') {
    header("Location: ../guru/index.php");
    exit();
}

// JIKA MURID NYASAR KE SINI, LEMPAR KE HALAMAN MURID
if ($_SESSION['role'] == 'murid') {
    header("Location: ../murid/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Smart Arca Music School</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; padding: 20px; background-color: #f0f2f5; color: #333; }
        .container { max-width: 1100px; margin: auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        
        header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 30px; }
        h1 { color: #1a73e8; margin: 0; font-size: 24px; }
        .user-info { text-align: right; font-size: 14px; color: #666; }
        
        h2 { color: #444; margin-top: 40px; border-left: 5px solid #1a73e8; padding-left: 15px; font-size: 20px; }
        
        .action-buttons { margin-bottom: 25px; display: flex; gap: 10px; flex-wrap: wrap; }
        
        .btn { display: inline-block; padding: 10px 18px; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 13px; transition: 0.3s; border: none; cursor: pointer; }
        .btn-murid { background-color: #28a745; }
        .btn-guru { background-color: #007bff; }
        .btn-jadwal { background-color: #6f42c1; }
        .btn-keuangan { background-color: #ffc107; color: #212529; }
        .btn-logout { background-color: #dc3545; }
        .btn-hapus { background-color: #dc3545; padding: 5px 10px; font-size: 11px; }
        .btn:hover { opacity: 0.85; transform: translateY(-1px); }

        table { width: 100%; border-collapse: collapse; margin-top: 15px; background: #fff; border-radius: 10px; overflow: hidden; }
        th, td { padding: 14px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #f8f9fa; color: #5f6368; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; }
        
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .badge-admin { background: #fff3cd; color: #856404; }
        .badge-guru { background: #cfe2ff; color: #084298; }
        .badge-murid { background: #d1e7dd; color: #0f5132; }
        
        .instrument-tag { font-weight: bold; color: #1a73e8; background: #e8f0fe; padding: 3px 8px; border-radius: 5px; }
        
        tr:hover { background-color: #fafafa; }
    </style>
</head>
<body>

<div class="container">
    <header>
        <div>
            <h1>Smart Arca Management</h1>
            <p style="margin: 5px 0 0 0; color: #888;">Sistem Informasi Sekolah Musik</p>
        </div>
        <div class="user-info">
    Login sebagai: <strong><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Administrator'; ?></strong> (Admin)<br>
    <span style="color: green;">● Server Online</span>
</div>
    </header>

    <div class="action-buttons">
        <a href="tambah_murid.php" class="btn btn-murid">+ TAMBAH MURID</a>
        <a href="tambah_guru.php" class="btn btn-guru">+ TAMBAH GURU</a>
        <a href="tambah_jadwal.php" class="btn btn-jadwal">+ ATUR JADWAL LES</a>
        <a href="keuangan.php" class="btn btn-keuangan">💰 KEUANGAN & HONOR</a>
        <a href="../logout.php" class="btn btn-logout" style="margin-left: auto;">KELUAR</a>
    </div>

    <h2>Jadwal & Kelas Mengajar</h2>
    <table>
        <thead>
            <tr>
                <th>Hari / Jam</th>
                <th>Guru Pengajar</th>
                <th>Nama Murid</th>
                <th>Alat Musik / Kelas</th>
            </tr>
        </thead>
        <tbody>
            <?php if(mysqli_num_rows($query_jadwal) > 0) {
                while($j = mysqli_fetch_assoc($query_jadwal)) { ?>
                <tr>
                    <td><strong><?php echo $j['hari']; ?></strong><br><small><?php echo date('H:i', strtotime($j['jam'])); ?> WIB</small></td>
                    <td><?php echo htmlspecialchars($j['nama_guru']); ?></td>
                    <td><?php echo htmlspecialchars($j['nama_murid']); ?></td>
                    <td><span class="instrument-tag"><?php echo $j['alat_musik']; ?></span></td>
                </tr>
            <?php } } else { ?>
                <tr><td colspan="4" style="text-align:center; padding: 30px; color: #999;">Belum ada jadwal les yang diatur. Silakan klik tombol 'Atur Jadwal'.</td></tr>
            <?php } ?>
        </tbody>
    </table>

    <h2>Manajemen Akun Pengguna</h2>
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
                <td><span class="badge <?php echo $roleClass; ?>"><?php echo $user['role']; ?></span></td>
                <td>
                    <?php if($user['role'] != 'admin') { ?>
                        <a href="hapus_user.php?id=<?php echo $user['id']; ?>" class="btn btn-hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">HAPUS</a>
                    <?php } else { echo "<small style='color:#ccc'>Utama</small>"; } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
