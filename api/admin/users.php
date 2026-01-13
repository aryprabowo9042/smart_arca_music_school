<?php
session_start();
ob_start();
require_once(__DIR__ . '/../koneksi.php');

$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'admin');
if (!$is_admin) { header("Location: login.php"); exit(); }

// --- PROSES ACTION ---
if (isset($_POST['simpan_user'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $role = $_POST['role'];
    $id = $_POST['id_user'] ?? '';

    if (!empty($id)) {
        // UPDATE USER
        if (!empty($_POST['password'])) {
            $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE users SET username='$username', role='$role', password='$pass' WHERE id='$id'");
        } else {
            mysqli_query($conn, "UPDATE users SET username='$username', role='$role' WHERE id='$id'");
        }
        header("Location: users.php?msg=updated");
    } else {
        // TAMBAH USER BARU
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('$username', '$pass', '$role')");
        header("Location: users.php?msg=added");
    }
    exit();
}

if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($conn, $_GET['hapus']);
    mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
    header("Location: users.php?msg=deleted");
    exit();
}

// Data untuk Edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = mysqli_real_escape_string($conn, $_GET['edit']);
    $edit_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$id_edit'"));
}

$query_users = mysqli_query($conn, "SELECT * FROM users ORDER BY role, username ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - Smart Arca</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 15px; }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-box { background: #f8f9fa; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #ddd; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 14px; }
        th, td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
        .badge { padding: 3px 7px; border-radius: 4px; color: white; font-size: 11px; font-weight: bold; }
        input, select, button { padding: 10px; margin: 5px 0; border: 1px solid #ddd; border-radius: 6px; width: 100%; box-sizing: border-box; }
        button { background: #1a73e8; color: white; border: none; cursor: pointer; font-weight: bold; }
        .btn-edit { color: #1a73e8; text-decoration: none; margin-right: 10px; }
        .btn-hapus { color: #dc3545; text-decoration: none; }
    </style>
</head>
<body>
<div class="container">
    <a href="index.php" style="text-decoration:none; color:#1a73e8; font-weight:bold;">← Dashboard</a>
    <h2>Manajemen User</h2>

    <div class="form-box">
        <h4><?php echo $edit_data ? 'Edit User' : 'Tambah User Baru'; ?></h4>
        <form method="POST">
            <input type="hidden" name="id_user" value="<?php echo $edit_data['id'] ?? ''; ?>">
            <input type="text" name="username" placeholder="Username" value="<?php echo $edit_data['username'] ?? ''; ?>" required>
            <input type="password" name="password" placeholder="<?php echo $edit_data ? 'Password Baru (Kosongkan jika tidak ganti)' : 'Password'; ?>" <?php echo $edit_data ? '' : 'required'; ?>>
            <select name="role">
                <option value="guru" <?php echo ($edit_data && $edit_data['role']=='guru') ? 'selected':''; ?>>Guru</option>
                <option value="murid" <?php echo ($edit_data && $edit_data['role']=='murid') ? 'selected':''; ?>>Murid</option>
                <option value="admin" <?php echo ($edit_data && $edit_data['role']=='admin') ? 'selected':''; ?>>Admin</option>
            </select>
            <button type="submit" name="simpan_user"><?php echo $edit_data ? 'UPDATE USER' : 'SIMPAN USER'; ?></button>
            <?php if($edit_data): ?> <a href="users.php" style="display:block; text-align:center; font-size:12px; color:gray; margin-top:5px;">Batal Edit</a> <?php endif; ?>
        </form>
    </div>

    <table>
        <thead><tr style="background:#f0f2f5;"><th>User</th><th>Role</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php while($u = mysqli_fetch_assoc($query_users)) { ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($u['username']); ?></strong></td>
                <td><span class="badge" style="background:<?php echo $u['role']=='admin'?'#666':($u['role']=='guru'?'#198754':'#0dcaf0'); ?>"><?php echo $u['role']; ?></span></td>
                <td>
                    <a href="users.php?edit=<?php echo $u['id']; ?>" class="btn-edit">Edit</a>
                    <a href="users.php?hapus=<?php echo $u['id']; ?>" class="btn-hapus" onclick="return confirm('Hapus user?')">Hapus</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
