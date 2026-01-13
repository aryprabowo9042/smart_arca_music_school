<?php
session_start();
ob_start();
require_once(__DIR__ . '/../koneksi.php');

$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'admin');
if (!$is_admin) { header("Location: login.php"); exit(); }

if (isset($_POST['simpan_user'])) {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $role = $_POST['role'];
    $id = $_POST['id_user'] ?? '';
    if (!empty($id)) {
        if (!empty($_POST['password'])) {
            $p = password_hash($_POST['password'], PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE users SET username='$user', role='$role', password='$p' WHERE id='$id'");
        } else {
            mysqli_query($conn, "UPDATE users SET username='$user', role='$role' WHERE id='$id'");
        }
    } else {
        $p = password_hash($_POST['password'], PASSWORD_DEFAULT);
        mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('$user', '$p', '$role')");
    }
    header("Location: users.php"); exit();
}

if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($conn, $_GET['hapus']);
    mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
    header("Location: users.php"); exit();
}

$edit = null;
if (isset($_GET['edit'])) {
    $id_ed = mysqli_real_escape_string($conn, $_GET['edit']);
    $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$id_ed'"));
}
$query_users = mysqli_query($conn, "SELECT * FROM users ORDER BY role, username ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <style>
        body { font-family: sans-serif; padding: 15px; background: #f0f2f5; }
        .box { background: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
        input, select, button { width: 100%; padding: 10px; margin: 5px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { background: #1a73e8; color: white; border: none; font-weight: bold; }
    </style>
</head>
<body>
    <a href="index.php">← Kembali</a>
    <h3>Manajemen User</h3>
    <div class="box">
        <form method="POST">
            <input type="hidden" name="id_user" value="<?php echo $edit['id'] ?? ''; ?>">
            <input type="text" name="username" placeholder="Username" value="<?php echo $edit['username'] ?? ''; ?>" required>
            <input type="password" name="password" placeholder="Password (Kosongkan jika tidak ganti)">
            <select name="role">
                <option value="guru" <?php echo ($edit && $edit['role']=='guru')?'selected':''; ?>>Guru</option>
                <option value="murid" <?php echo ($edit && $edit['role']=='murid')?'selected':''; ?>>Murid</option>
                <option value="admin" <?php echo ($edit && $edit['role']=='admin')?'selected':''; ?>>Admin</option>
            </select>
            <button type="submit" name="simpan_user">SIMPAN</button>
        </form>
    </div>
    <table>
        <?php while($u = mysqli_fetch_assoc($query_users)) { ?>
        <tr>
            <td><strong><?php echo $u['username']; ?></strong><br><small><?php echo $u['role']; ?></small></td>
            <td>
                <a href="users.php?edit=<?php echo $u['id']; ?>">Edit</a> | 
                <a href="users.php?hapus=<?php echo $u['id']; ?>" style="color:red" onclick="return confirm('Hapus?')">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
