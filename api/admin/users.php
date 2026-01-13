<?php
session_start();
ob_start();
require_once(__DIR__ . '/../koneksi.php');

// Proteksi Admin
$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'admin');
if (!$is_admin) { header("Location: login.php"); exit(); }

// 1. PROSES TAMBAH USER
if (isset($_POST['tambah_user'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = $_POST['role'];

    $cek = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Username sudah digunakan!');</script>";
    } else {
        mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')");
        header("Location: users.php?pesan=berhasil");
    }
}

// 2. PROSES HAPUS USER
if (isset($_GET['hapus'])) {
    $id_hapus = mysqli_real_escape_string($conn, $_GET['hapus']);
    mysqli_query($conn, "DELETE FROM users WHERE id = '$id_hapus'");
    header("Location: users.php?pesan=terhapus");
}

// 3. AMBIL DATA USER
$query_users = mysqli_query($conn, "SELECT * FROM users ORDER BY role, username ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - Smart Arca</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .form-tambah { background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 25px; border: 1px solid #eee; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #1a73e8; color: white; font-size: 14px; }
        .badge { padding: 4px 8px; border-radius: 5px; font-size: 11px; font-weight: bold; color: white; text-transform: uppercase; }
        .bg-guru { background: #198754; }
        .bg-murid { background: #0dcaf0; color: #333; }
        .bg-admin { background: #6c757d; }
        input, select, button { padding: 10px; margin-top: 5px; border-radius: 6px; border: 1px solid #ddd; width: 100%; box-sizing: border-box; }
        button { background: #1a73e8; color: white; border: none; font-weight: bold; cursor: pointer; }
        .btn-hapus { color: #dc3545; text-decoration: none; font-size: 12px; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php" style="text-decoration:none; color:#1a73e8;">← Kembali ke Dashboard</a>
    <h2>Manajemen User</h2>

    <div class="form-tambah">
        <h4 style="margin:0 0 10px 0;">+ Tambah User (Guru/Murid)</h4>
        <form method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px;">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role">
                <option value="guru">Guru</option>
                <option value="murid">Murid</option>
                <option value="admin">Admin</option>
            </select>
            <button type="submit" name="tambah_user">Simpan User</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while($u = mysqli_fetch_assoc($query_users)) { ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($u['username']); ?></strong></td>
                <td>
                    <span class="badge <?php echo 'bg-'.$u['role']; ?>">
                        <?php echo $u['role']; ?>
                    </span>
                </td>
                <td>
                    <?php 
                    // Kita gunakan pengecekan yang lebih fleksibel
                    $session_user = $_SESSION['username'] ?? $_SESSION['user_login'] ?? '';
                    if($u['username'] != $session_user) { 
                     ?>
                        <a href="users.php?hapus=<?php echo $u['id']; ?>" class="btn-hapus" onclick="return confirm('Hapus user ini?')">Hapus</a>
                    <?php } else { echo "-"; } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
