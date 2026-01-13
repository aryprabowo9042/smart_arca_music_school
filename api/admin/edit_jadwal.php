<?php
session_start();
require_once(__DIR__ . '/../koneksi.php');

$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'admin');
if (!$is_admin) { header("Location: login.php"); exit(); }

$id = mysqli_real_escape_string($conn, $_GET['id'] ?? '');
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM jadwal WHERE id = '$id'"));

if (isset($_POST['update_jadwal'])) {
    $g = $_POST['id_guru'];
    $m = $_POST['id_murid'];
    $h = $_POST['hari'];
    $j = $_POST['jam'];
    $a = mysqli_real_escape_string($conn, $_POST['alat_musik']);

    mysqli_query($conn, "UPDATE jadwal SET id_guru='$g', id_murid='$m', hari='$h', jam='$j', alat_musik='$a' WHERE id='$id'");
    header("Location: jadwal.php?pesan=updated");
}

$gurus  = mysqli_query($conn, "SELECT id, username FROM users WHERE role = 'guru'");
$murids = mysqli_query($conn, "SELECT id, username FROM users WHERE role = 'murid'");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Jadwal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 20px; }
        .box { background: white; padding: 25px; border-radius: 15px; max-width: 500px; margin: auto; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        input, select, button { width: 100%; padding: 12px; margin: 8px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        button { background: #1a73e8; color: white; border: none; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
<div class="box">
    <h3>Edit Jadwal Les</h3>
    <form method="POST">
        <label style="font-size:11px;">Guru</label>
        <select name="id_guru">
            <?php while($gr = mysqli_fetch_assoc($gurus)) { ?>
                <option value="<?php echo $gr['id']; ?>" <?php if($gr['id']==$data['id_guru']) echo 'selected'; ?>><?php echo $gr['username']; ?></option>
            <?php } ?>
        </select>
        <label style="font-size:11px;">Murid</label>
        <select name="id_murid">
            <?php while($mr = mysqli_fetch_assoc($murids)) { ?>
                <option value="<?php echo $mr['id']; ?>" <?php if($mr['id']==$data['id_murid']) echo 'selected'; ?>><?php echo $mr['username']; ?></option>
            <?php } ?>
        </select>
        <input type="text" name="hari" value="<?php echo $data['hari']; ?>" placeholder="Hari">
        <input type="text" name="jam" value="<?php echo $data['jam']; ?>" placeholder="Jam (Contoh 16:00)">
        <input type="text" name="alat_musik" value="<?php echo $data['alat_musik']; ?>" placeholder="Instrumen">
        <button type="submit" name="update_jadwal">UPDATE JADWAL</button>
        <a href="jadwal.php" style="display:block; text-align:center; margin-top:10px; color:#666; text-decoration:none; font-size:13px;">Batal</a>
    </form>
</div>
</body>
</html>
