<?php
session_start();
ob_start();
require_once(__DIR__ . '/../koneksi.php');

$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'admin');
if (!$is_admin) { echo "<script>window.location.replace('login.php');</script>"; exit(); }

if (isset($_POST['save_j'])) {
    $g = $_POST['id_g']; $m = $_POST['id_m']; $h = $_POST['hari'];
    $j = mysqli_real_escape_string($conn, $_POST['jam']);
    $a = mysqli_real_escape_string($conn, $_POST['alat']);
    $id = $_POST['id_j'] ?? '';
    if (!empty($id)) {
        mysqli_query($conn, "UPDATE jadwal SET id_guru='$g', id_murid='$m', hari='$h', jam='$j', alat_musik='$a' WHERE id='$id'");
    } else {
        mysqli_query($conn, "INSERT INTO jadwal (id_guru, id_murid, hari, jam, alat_musik) VALUES ('$g', '$m', '$h', '$j', '$a')");
    }
    header("Location: jadwal.php"); exit();
}

if (isset($_GET['del'])) {
    $id = mysqli_real_escape_string($conn, $_GET['del']);
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
        body { font-family: sans-serif; background: #f0f2f5; padding: 15px; }
        .box { background: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        select, input, button { width: 100%; padding: 10px; margin: 4px 0; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        button { background: #1a73e8; color: white; border: none; font-weight: bold; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; background: white; font-size: 13px; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>
    <a href="index.php" style="text-decoration:none; color:#1a73e8;">← Dashboard</a>
    <h3>Atur Jadwal</h3>
    <div class="box">
        <form method="POST">
            <input type="hidden" name="id_j" value="<?php echo $edit['id'] ?? ''; ?>">
            <select name="id_g" required>
                <option value="">-- Guru --</option>
                <?php mysqli_data_seek($gurus, 0); while($g = mysqli_fetch_assoc($gurus)) { ?>
                    <option value="<?php echo $g['id']; ?>" <?php echo ($edit && $edit['id_guru']==$g['id'])?'selected':''; ?>><?php echo $g['username']; ?></option>
                <?php } ?>
            </select>
            <select name="id_m" required>
                <option value="">-- Murid --</option>
                <?php mysqli_data_seek($murids, 0); while($m = mysqli_fetch_assoc($murids)) { ?>
                    <option value="<?php echo $m['id']; ?>" <?php echo ($edit && $edit['id_murid']==$m['id'])?'selected':''; ?>><?php echo $m['username']; ?></option>
                <?php } ?>
            </select>
            <select name="hari">
                <?php $hr=['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu']; foreach($hr as $h){ ?>
                    <option value="<?php echo $h; ?>" <?php echo ($edit && $edit['hari']==$h)?'selected':''; ?>><?php echo $h; ?></option>
                <?php } ?>
            </select>
            <input type="text" name="jam" placeholder="Jam (16:00)" value="<?php echo $edit['jam'] ?? ''; ?>" required>
            <input type="text" name="alat" placeholder="Instrumen (Piano/Gitar)" value="<?php echo $edit['alat_musik'] ?? ''; ?>" required>
            <button type="submit" name="save_j">SIMPAN JADWAL</button>
            <?php if($edit): ?> <a href="jadwal.php" style="display:block; text-align:center; font-size:12px; margin-top:5px;">Batal</a> <?php endif; ?>
        </form>
    </div>
    <table>
        <?php while($j = mysqli_fetch_assoc($query_j)) { ?>
        <tr>
            <td><strong><?php echo $j['n_guru']; ?></strong> → <?php echo $j['n_murid']; ?><br><small><?php echo $j['hari']; ?>, <?php echo $j['jam']; ?> (<?php echo $j['alat_musik']; ?>)</small></td>
            <td style="text-align:right;">
                <a href="jadwal.php?edit=<?php echo $j['id']; ?>" style="text-decoration:none;">Edit</a> | 
                <a href="jadwal.php?del=<?php echo $j['id']; ?>" style="color:red; text-decoration:none;" onclick="return confirm('Hapus?')">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
