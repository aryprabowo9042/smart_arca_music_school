<?php
session_start();
ob_start();

// 1. PERBAIKAN PATH KONEKSI (Disesuaikan dengan folder api Bapak)
require_once(__DIR__ . '/../koneksi.php');

// 2. PROTEKSI ADMIN (Menggunakan logika yang sama dengan honor.php & users.php)
$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'admin');
if (!$is_admin) { 
    header("Location: login.php"); 
    exit(); 
}

// 3. PROSES TAMBAH JADWAL
if (isset($_POST['tambah_jadwal'])) {
    $id_guru    = mysqli_real_escape_string($conn, $_POST['id_guru']);
    $id_murid   = mysqli_real_escape_string($conn, $_POST['id_murid']);
    $hari       = mysqli_real_escape_string($conn, $_POST['hari']);
    $jam        = mysqli_real_escape_string($conn, $_POST['jam']);
    $alat_musik = mysqli_real_escape_string($conn, $_POST['alat_musik']);

    $insert = mysqli_query($conn, "INSERT INTO jadwal (id_guru, id_murid, hari, jam, alat_musik) 
                                   VALUES ('$id_guru', '$id_murid', '$hari', '$jam', '$alat_musik')");
    if ($insert) {
        header("Location: jadwal.php?pesan=berhasil");
    } else {
        echo "<script>alert('Gagal menambah jadwal: " . mysqli_error($conn) . "');</script>";
    }
}

// 4. PROSES HAPUS JADWAL
if (isset($_GET['hapus'])) {
    $id_hapus = mysqli_real_escape_string($conn, $_GET['hapus']);
    mysqli_query($conn, "DELETE FROM jadwal WHERE id = '$id_hapus'");
    header("Location: jadwal.php?pesan=terhapus");
}

// AMBIL DATA GURU, MURID, DAN DAFTAR JADWAL
$gurus  = mysqli_query($conn, "SELECT id, username FROM users WHERE role = 'guru'");
$murids = mysqli_query($conn, "SELECT id, username FROM users WHERE role = 'murid'");
$query_jadwal = mysqli_query($conn, "SELECT j.*, g.username as nama_guru, m.username as nama_murid 
                                     FROM jadwal j
                                     JOIN users g ON j.id_guru = g.id
                                     JOIN users m ON j.id_murid = m.id
                                     ORDER BY j.id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Jadwal - Smart Arca</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .form-box { background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 25px; border: 1px solid #eee; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 14px; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #1a73e8; color: white; }
        input, select, button { padding: 10px; margin-top: 5px; border-radius: 8px; border: 1px solid #ddd; width: 100%; box-sizing: border-box; }
        button { background: #1a73e8; color: white; border: none; font-weight: bold; cursor: pointer; margin-top: 10px; }
        .btn-hapus { color: #dc3545; text-decoration: none; font-weight: bold; font-size: 12px; }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php" style="text-decoration:none; color:#1a73e8; font-weight:bold;">← Kembali</a>
    <h2>Manajemen Jadwal Les</h2>

    <div class="form-box">
        <h4 style="margin:0 0 10px 0;">+ Plotting Jadwal Baru</h4>
        <form method="POST">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 10px;">
                <div>
                    <label style="font-size:12px;">Pilih Guru:</label>
                    <select name="id_guru" required>
                        <?php while($g = mysqli_fetch_assoc($gurus)) echo "<option value='{$g['id']}'>{$g['username']}</option>"; ?>
                    </select>
                </div>
                <div>
                    <label style="font-size:12px;">Pilih Murid:</label>
                    <select name="id_murid" required>
                        <?php while($m = mysqli_fetch_assoc($murids)) echo "<option value='{$m['id']}'>{$m['username']}</option>"; ?>
                    </select>
                </div>
                <div>
                    <label style="font-size:12px;">Hari:</label>
                    <select name="hari" required>
                        <option>Senin</option><option>Selasa</option><option>Rabu</option>
                        <option>Kamis</option><option>Jumat</option><option>Sabtu</option><option>Minggu</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:12px;">Jam:</label>
                    <input type="text" name="jam" placeholder="Contoh: 16:00" required>
                </div>
                <div>
                    <label style="font-size:12px;">Alat Musik:</label>
                    <input type="text" name="alat_musik" placeholder="Vokal/Gitar/Piano" required>
                </div>
            </div>
            <button type="submit" name="tambah_jadwal">Simpan Jadwal</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Guru</th>
                <th>Murid</th>
                <th>Jadwal</th>
                <th>Instrumen</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($query_jadwal)) { ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($row['nama_guru']); ?></strong></td>
                <td><?php echo htmlspecialchars($row['nama_murid']); ?></td>
                <td><?php echo $row['hari']; ?>, <?php echo $row['jam']; ?></td>
                <td><span style="background:#e8f0fe; padding:3px 8px; border-radius:5px; font-size:12px;"><?php echo $row['alat_musik']; ?></span></td>
                <td>
                    <a href="jadwal.php?hapus=<?php echo $row['id']; ?>" class="btn-hapus" onclick="return confirm('Hapus jadwal ini?')">Hapus</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
