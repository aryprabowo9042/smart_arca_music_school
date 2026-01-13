<?php
session_start();
ob_start();
require_once(__DIR__ . '/../koneksi.php');

$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'admin');
if (!$is_admin) { header("Location: login.php"); exit(); }

// --- PROSES ACTION ---
if (isset($_POST['simpan_jadwal'])) {
    $id_guru = $_POST['id_guru'];
    $id_murid = $_POST['id_murid'];
    $hari = $_POST['hari'];
    $jam = mysqli_real_escape_string($conn, $_POST['jam']);
    $alat = mysqli_real_escape_string($conn, $_POST['alat_musik']);
    $id = $_POST['id_jadwal'] ?? '';

    if (!empty($id)) {
        mysqli_query($conn, "UPDATE jadwal SET id_guru='$id_guru', id_murid='$id_murid', hari='$hari', jam='$jam', alat_musik='$alat' WHERE id='$id'");
        header("Location: jadwal.php?msg=updated");
    } else {
        mysqli_query($conn, "INSERT INTO jadwal (id_guru, id_murid, hari, jam, alat_musik) VALUES ('$id_guru', '$id_murid', '$hari', '$jam', '$alat')");
        header("Location: jadwal.php?msg=added");
    }
    exit();
}

if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($conn, $_GET['hapus']);
    mysqli_query($conn, "DELETE FROM jadwal WHERE id='$id'");
    header("Location: jadwal.php?msg=deleted");
    exit();
}

// Data untuk Edit
$edit_j = null;
if (isset($_GET['edit'])) {
    $id_ed = mysqli_real_escape_string($conn, $_GET['edit']);
    $edit_j = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM jadwal WHERE id='$id_ed'"));
}

$gurus = mysqli_query($conn, "SELECT id, username FROM users WHERE role='guru'");
$murids = mysqli_query($conn, "SELECT id, username FROM users WHERE role='murid'");
$query_j = mysqli_query($conn, "SELECT j.*, g.username as n_guru, m.username as n_murid FROM jadwal j JOIN users g ON j.id_guru=g.id JOIN users m ON j.id_murid=m.id ORDER BY FIELD(hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')");
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Jadwal - Smart Arca</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 15px; }
        .container { max-width: 900px; margin: auto; background: white; padding: 20px; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-box { background: #f8f9fa; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #ddd; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
        input, select, button { padding: 8px; margin: 4px 0; border: 1px solid #ddd; border-radius: 6px; width: 100%; box-sizing: border-box; }
        button { background: #1a73e8; color: white; border: none; cursor: pointer; font-weight: bold; padding: 12px; }
        .btn-edit { color: #1a73e8; text-decoration: none; margin-right: 8px; font-weight: bold; }
        .btn-hapus { color: #dc3545; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <a href="index.php" style="text-decoration:none; color:#1a73e8; font-weight:bold;">← Dashboard</a>
    <h2>Jadwal Pelajaran</h2>

    <div class="form-box">
        <form method="POST">
            <input type="hidden" name="id_jadwal" value="<?php echo $edit_j['id'] ?? ''; ?>">
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                <select name="id_guru" required>
                    <option value="">-- Pilih Guru --</option>
                    <?php mysqli_data_seek($gurus, 0); while($g = mysqli_fetch_assoc($gurus)) { ?>
                        <option value="<?php echo $g['id']; ?>" <?php echo ($edit_j && $edit_j['id_guru']==$g['id'])?'selected':''; ?>><?php echo $g['username']; ?></option>
                    <?php } ?>
                </select>
                <select name="id_murid" required>
                    <option value="">-- Pilih Murid --</option>
                    <?php mysqli_data_seek($murids, 0); while($m = mysqli_fetch_assoc($murids)) { ?>
                        <option value="<?php echo $m['id']; ?>" <?php echo ($edit_j && $edit_j['id_murid']==$m['id'])?'selected':''; ?>><?php echo $m['username']; ?></option>
                    <?php } ?>
                </select>
                <select name="hari">
                    <?php $hr = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu']; foreach($hr as $h) { ?>
                        <option value="<?php echo $h; ?>" <?php echo ($edit_j && $edit_j['hari']==$h)?'selected':''; ?>><?php echo $h; ?></option>
                    <?php } ?>
                </select>
                <input type="text" name="jam" placeholder="Jam (00:00)" value="<?php echo $edit_j['jam'] ?? ''; ?>" required>
            </div>
            <input type="text" name="alat_musik" placeholder="Instrumen (Vokal/Gitar/dll)" value="<?php echo $edit_j['alat_musik'] ?? ''; ?>" required>
            <button type="submit" name="simpan_jadwal"><?php echo $edit_j ? 'UPDATE JADWAL' : 'TAMBAH JADWAL'; ?></button>
            <?php if($edit_j): ?> <a href="jadwal.php" style="display:block; text-align:center; font-size:12px; color:gray; margin-top:5px;">Batal Edit</a> <?php endif; ?>
        </form>
    </div>

    <table style="overflow-x:auto; display:block;">
        <thead><tr style="background:#f0f2f5;"><th>Guru</th><th>Murid</th><th>Jadwal</th><th>Alat</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php while($j = mysqli_fetch_assoc($query_j)) { ?>
            <tr>
                <td><strong><?php echo $j['n_guru']; ?></strong></td>
                <td><?php echo $j['n_murid']; ?></td>
                <td><?php echo $j['hari']; ?>, <?php echo $j['jam']; ?></td>
                <td><?php echo $j['alat_musik']; ?></td>
                <td>
                    <a href="jadwal.php?edit=<?php echo $j['id']; ?>" class="btn-edit">Edit</a>
                    <a href="jadwal.php?hapus=<?php echo $j['id']; ?>" class="btn-hapus" onclick="return confirm('Hapus?')">Hapus</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
