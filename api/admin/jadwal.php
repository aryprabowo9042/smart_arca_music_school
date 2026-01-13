<?php
session_start();
ob_start();
require_once(__DIR__ . '/../koneksi.php');

$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'admin');
if (!$is_admin) { header("Location: login.php"); exit(); }

if (isset($_POST['simpan_j'])) {
    $g = $_POST['id_guru']; $m = $_POST['id_murid']; $h = $_POST['hari'];
    $j = mysqli_real_escape_string($conn, $_POST['jam']);
    $a = mysqli_real_escape_string($conn, $_POST['alat_musik']);
    $id = $_POST['id_jadwal'] ?? '';

    if (!empty($id)) {
        mysqli_query($conn, "UPDATE jadwal SET id_guru='$g', id_murid='$m', hari='$h', jam='$j', alat_musik='$a' WHERE id='$id'");
    } else {
        mysqli_query($conn, "INSERT INTO jadwal (id_guru, id_murid, hari, jam, alat_musik) VALUES ('$g', '$m', '$h', '$j', '$a')");
    }
    header("Location: jadwal.php"); exit();
}

if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($conn, $_GET['hapus']);
    mysqli_query($conn, "DELETE FROM jadwal WHERE id='$id'");
    header("Location: jadwal.php"); exit();
}

$edit = null;
if (isset($_GET['edit'])) {
    $id_ed = mysqli_real_escape_string($conn, $_GET['edit']);
    $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM jadwal WHERE id='$id_ed'"));
}

$gurus = mysqli_query($conn, "SELECT id, username FROM users WHERE role='guru'");
$murids = mysqli_query($conn, "SELECT id, username FROM users WHERE role='murid'");
$query_j = mysqli_query($conn, "SELECT j.*, g.username as n_guru, m.username as n_murid FROM jadwal j JOIN users g ON j.id_guru=g.id JOIN users m ON j.id_murid=m.id ORDER BY FIELD(hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')");
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal</title>
    <style>
        body { font-family: sans-serif; padding: 15px; background: #f0f2f5; }
        .box { background: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
        input, select, button { width: 100%; padding: 8px; margin: 4px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { background: #1a73e8; color: white; border: none; font-weight: bold; }
    </style>
</head>
<body>
    <a href="index.php">← Kembali</a>
    <h3>Atur Jadwal</h3>
    <div class="box">
        <form method="POST">
            <input type="hidden" name="id_jadwal" value="<?php echo $edit['id'] ?? ''; ?>">
            <select name="id_guru" required>
                <?php mysqli_data_seek($gurus, 0); while($g = mysqli_fetch_assoc($gurus)) { ?>
                    <option value="<?php echo $g['id']; ?>" <?php echo ($edit && $edit['id_guru']==$g['id'])?'selected':''; ?>><?php echo $g['username']; ?></option>
                <?php } ?>
            </select>
            <select name="id_murid" required>
                <?php mysqli_data_seek($murids, 0); while($m = mysqli_fetch_assoc($murids)) { ?>
                    <option value="<?php echo $m['id']; ?>" <?php echo ($edit && $edit['id_murid']==$m['id'])?'selected':''; ?>><?php echo $m['username']; ?></option>
                <?php } ?>
            </select>
            <select name="hari">
                <?php $hr = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu']; foreach($hr as $h) { ?>
                    <option value="<?php echo $h; ?>" <?php echo ($edit && $edit['hari']==$h)?'selected':''; ?>><?php echo $h; ?></option>
                <?php } ?>
            </select>
            <input type="text" name="jam" placeholder="Jam" value="<?php echo $edit['jam'] ?? ''; ?>" required>
            <input type="text" name="alat_musik" placeholder="Instrumen" value="<?php echo $edit['alat_musik'] ?? ''; ?>" required>
            <button type="submit" name="simpan_j">SIMPAN JADWAL</button>
        </form>
    </div>
    <table>
        <?php while($j = mysqli_fetch_assoc($query_j)) { ?>
        <tr>
            <td><strong><?php echo $j['n_guru']; ?></strong> → <?php echo $j['n_murid']; ?><br><small><?php echo $j['hari']; ?>, <?php echo $j['jam']; ?> (<?php echo $j['alat_musik']; ?>)</small></td>
            <td><a href="jadwal.php?edit=<?php echo $j['id']; ?>">Edit</a> | <a href="jadwal.php?hapus=<?php echo $j['id']; ?>" style="color:red" onclick="return confirm('Hapus?')">Hapus</a></td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
