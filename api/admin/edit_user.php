<?php
session_start();
require_once(__DIR__ . '/../koneksi.php');

$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'admin');
if (!$is_admin) { header("Location: login.php"); exit(); }

$id = mysqli_real_escape_string($conn, $_GET['id'] ?? '');
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = '$id'"));

if (isset($_POST['update_user'])) {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $role = $_POST['role'];
    $pass = $_POST['password'];

    if (!empty($pass)) {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET username='$user', role='$role', password='$hash' WHERE id='$id'");
    } else {
        mysqli_query($conn, "UPDATE users SET username='$user', role='$role' WHERE id='$id'");
    }
    header("Location: users.php?pesan=updated");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 20px; }
        .box { background: white; padding: 25px; border-radius: 15px; max-width: 400px; margin: auto; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        input, select, button { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        button { background: #1a73e8; color: white; border: none; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
<div class="box">
    <h3>Edit User</h3>
    <form method="POST">
        <label style="font-size:12px;">Username</label>
        <input type="text" name="username" value="<?php echo $data['username']; ?>" required>
        <label style="font-size:12px;">Role</label>
        <select name="role">
            <option value="guru" <?php if($data['role']=='guru') echo 'selected'; ?>>Guru</option>
            <option value="murid" <?php if($data['role']=='murid') echo 'selected'; ?>>Murid</option>
            <option value="admin" <?php if($data['role']=='admin') echo 'selected'; ?>>Admin</option>
        </select>
        <label style="font-size:12px;">Password Baru (Kosongkan jika tidak ganti)</label>
        <input type="password" name="password" placeholder="Password Baru">
        <button type="submit" name="update_user">SIMPAN PERUBAHAN</button>
        <a href="users.php" style="display:block; text-align:center; margin-top:10px; color:#666; text-decoration:none; font-size:13px;">Batal</a>
    </form>
</div>
</body>
</html>
