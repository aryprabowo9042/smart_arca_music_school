<?php
session_start();
ob_start();
require_once(__DIR__ . '/../koneksi.php');

$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'admin');
if (!$is_admin) { echo "<script>window.location.replace('login.php');</script>"; exit(); }

if (isset($_POST['save'])) {
    $u = mysqli_real_escape_string($conn, $_POST['username']);
    $r = $_POST['role'];
    $id = $_POST['id'] ?? '';
    if (!empty($id)) {
        if (!empty($_POST['pass'])) {
            $p = password_hash($_POST['pass'], PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE users SET username='$u', role='$r', password='$p' WHERE id='$id'");
        } else {
            mysqli_query($conn, "UPDATE users SET username='$u', role='$r' WHERE id='$id'");
        }
    } else {
        $p = password_hash($_POST['pass'], PASSWORD_DEFAULT);
        mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('$u', '$p', '$r')");
    }
    header("Location: users.php"); exit();
}

if (isset($_GET['del'])) {
    $id = mysqli_real_escape_string($conn, $_GET['del']);
    mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
    header("Location: users.php"); exit();
}

$edit = null;
if (isset($_GET['edit'])) {
    $id_ed = mysqli_real_escape_string($conn, $_GET['edit']);
    $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$id_ed'"));
}
$users = mysqli_query($conn, "SELECT * FROM users ORDER BY role ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 15px; }
        .box { background: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        input, select, button { width: 100%; padding: 10px; margin: 5px 0; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        button { background: #1a73e8; color: white; border: none; font-weight: bold; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; }
        td { padding: 12px; border-bottom: 1px solid #eee; font-size: 14px; }
    </style>
</head>
<body>
    <a href="index.php" style="text-decoration:none; color:#1a73e8;">← Dashboard</a>
    <h3>Manajemen User</h3>
    <div class="box">
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $edit['id'] ?? ''; ?>">
            <input type="text" name="username" placeholder="Username" value="<?php echo $edit['username'] ?? ''; ?>" required>
            <input type="password" name="pass" placeholder="Password (Kosongkan jika tidak ganti)">
            <select name="role">
                <option value="guru" <?php echo ($edit && $edit['role']=='guru')?'selected':''; ?>>Guru</option>
                <option value="murid" <?php echo ($edit && $edit['role']=='murid')?'selected':''; ?>>Murid</option>
                <option value="admin" <?php echo ($edit && $edit['role']=='admin')?'selected':''; ?>>Admin</option>
            </select>
            <button type="submit" name="save">SIMPAN USER</button>
            <?php if($edit): ?> <a href="users.php" style="display:block; text-align:center; font-size:12px; margin-top:5px;">Batal</a> <?php endif; ?>
        </form>
    </div>
    <table>
        <?php while($u = mysqli_fetch_assoc($users)) { ?>
        <tr>
            <td><strong><?php echo $u['username']; ?></strong><br><small style="color:blue;"><?php echo $u['role']; ?></small></td>
            <td style="text-align:right;">
                <a href="users.php?edit=<?php echo $u['id']; ?>" style="text-decoration:none;">Edit</a> | 
                <a href="users.php?del=<?php echo $u['id']; ?>" style="color:red; text-decoration:none;" onclick="return confirm('Hapus?')">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
