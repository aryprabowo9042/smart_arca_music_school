<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__ . '/../koneksi.php');

$query_users = mysqli_query($conn, "SELECT * FROM users ORDER BY role ASC, username ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin - Smart Arca</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; padding: 20px; background-color: #f0f2f5; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        h1 { color: #1a73e8; }
        .action-buttons { margin-bottom: 25px; display: flex; gap: 10px; }
        .btn { padding: 10px 15px; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 13px; }
        .btn-murid { background: #28a745; }
        .btn-guru { background: #1a73e8; }
        .btn-logout { background: #6c757d; }
        .btn-hapus { background: #dc3545; padding: 5px 10px; font-size: 11px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; color: #555; }
        
        .badge { padding: 4px 8px; border-radius: 10px; font-size: 11px; font-weight: bold; }
        .badge-admin { background: #fff3cd; color: #856404; }
        .badge-guru { background: #cfe2ff; color: #084298; }
        .badge-murid { background: #d1e7dd; color: #0f5132; }
    </style>
</head>
<body>

<div class="container">
    <h1>Panel Administrasi</h1>
    
    <div class="action-buttons">
        <a href="tambah_murid.php" class="btn btn-murid">+ TAMBAH MURID</a>
        <a href="tambah_guru.php" class="btn btn-guru">+ TAMBAH GURU</a>
        <a href="../logout.php" class="btn btn-logout" style="margin-left:auto;">KELUAR</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Username</th>
                <th>Role</th>
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
                <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                <td><span class="badge <?php echo $roleClass; ?>"><?php echo $user['role']; ?></span></td>
                <td>
                    <?php if($user['role'] != 'admin') { ?>
                        <a href="hapus_user.php?id=<?php echo $user['id']; ?>" 
                           class="btn btn-hapus" 
                           onclick="return confirm('Yakin ingin menghapus user ini?')">HAPUS</a>
                    <?php } else { echo "-"; } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
