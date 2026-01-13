<?php
session_start();
ob_start();

// 1. KONEKSI KE DATABASE
require_once(__DIR__ . '/../koneksi.php');

// 2. PROTEKSI ADMIN
$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'admin');
if (!$is_admin) { 
    header("Location: login.php"); 
    exit(); 
}

// 3. PROSES SIMPAN JADWAL (TAMBAH)
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
        exit();
    } else {
        $error_msg = mysqli_error($conn);
    }
}

// 4. PROSES HAPUS JADWAL
if (isset($_GET['hapus'])) {
    $id_hapus = mysqli_real_escape_string($conn, $_GET['hapus']);
    mysqli_query($conn, "DELETE FROM jadwal WHERE id = '$id_hapus'");
    header("Location: jadwal.php?pesan=terhapus");
    exit();
}

// 5. AMBIL DATA UNTUK FORM & TABEL
$gurus  = mysqli_query($conn, "SELECT id, username FROM users WHERE role = 'guru' ORDER BY username ASC");
$murids = mysqli_query($conn, "SELECT id, username FROM users WHERE role = 'murid' ORDER BY username ASC");
$query_jadwal = mysqli_query($conn, "SELECT j.*, g.username as nama_guru, m.username as nama_murid 
                                     FROM jadwal j
                                     JOIN users g ON j.id_guru = g.id
                                     JOIN users m ON j.id_murid = m.id
                                     ORDER BY FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'), jam ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Jadwal - Smart Arca</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; padding: 20px; margin: 0; }
        .container { max-width: 1100px; margin: auto; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 2px solid #f0f2f5; padding-bottom: 15px; }
        
        /* Notifikasi */
        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; font-weight: bold; }
        .alert-success { background: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
        .alert-error { background: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }

        /* Form Styling */
        .form-box { background: #f8f9fa; padding: 25px; border-radius: 15px; margin-bottom: 30px; border: 1px solid #e9ecef; }
        .grid-form { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
        label { display: block; font-size: 12px; font-weight: bold; color: #666; margin-bottom: 5px; text-transform: uppercase; }
        select, input { width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd; box-sizing: border-box; font-size: 14px; }
        
        .btn-simpan { background: #1a73e8; color: white; border: none; padding: 15px; border-radius: 10px; font-weight: bold; cursor: pointer; transition: 0.3s; margin-top: 15px; width: 100%; }
        .btn-simpan:hover { background: #1557b0; }

        /* Table Styling */
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f8f9fa; color: #666; font-size: 12px; text-transform: uppercase; padding: 15px; border-bottom: 2px solid #eee; text-align: left; }
        td { padding: 15px; border-bottom: 1px solid #f0f2f5; font-size: 14px; }
        .instrumen { background: #e8f0fe; color: #1a73e8; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .btn-hapus { color: #dc3545; text-decoration: none; font-weight: bold; font-size: 12px; padding: 5px 10px; border: 1px solid #dc3545; border-radius: 5px; }
        .btn-hapus:hover { background: #dc3545; color: white; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2 style="margin:0; color: #333;">📅 Manajemen Jadwal</h2>
        <a href="index.php" style="text-decoration:none; color:#1a73e8; font-weight:bold;">← Kembali</a>
    </div>

    <?php if(isset($_GET['pesan']) && $_GET['pesan'] == 'berhasil'): ?>
        <div class="alert alert-success">✅ Jadwal berhasil ditambahkan!</div>
    <?php elseif(isset($_GET['pesan']) && $_GET['pesan'] == 'terhapus'): ?>
        <div class="alert alert-success">🗑️ Jadwal berhasil dihapus.</div>
    <?php elseif(isset($error_msg)): ?>
        <div class="alert alert-error">❌ Gagal: <?php echo $error_msg; ?></div>
    <?php endif; ?>

    <div class="form-box">
        <form method="POST">
            <div class="grid-form">
                <div>
                    <label>Pilih Guru</label>
                    <select name="id_guru" required>
                        <option value="">-- Pilih Guru --</option>
                        <?php while($g = mysqli_fetch_assoc($gurus)): ?>
                            <option value="<?php echo $g['id']; ?>"><?php echo htmlspecialchars($g['username']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div>
                    <label>Pilih Murid</label>
                    <select name="id_murid" required>
                        <option value="">-- Pilih Murid --</option>
                        <?php while($m = mysqli_fetch_assoc($murids)): ?>
                            <option value="<?php echo $m['id']; ?>"><?php echo htmlspecialchars($m['username']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div>
                    <label>Hari Les</label>
                    <select name="hari" required>
                        <option value="Senin">Senin</option>
                        <option value="Selasa">Selasa</option>
                        <option value="Rabu">Rabu</option>
                        <option value="Kamis">Kamis</option>
                        <option value="Jumat">Jumat</option>
                        <option value="Sabtu">Sabtu</option>
                        <option value="Minggu">Minggu</option>
                    </select>
                </div>
                <div>
                    <label>Jam Les</label>
                    <input type="text" name="jam" placeholder="Contoh: 14:00 - 15:00" required>
                </div>
                <div>
                    <label>Alat Musik / Kelas</label>
                    <input type="text" name="alat_musik" placeholder="Vokal, Piano, dll." required>
                </div>
            </div>
            <button type="submit" name="tambah_jadwal" class="btn-simpan">SIMPAN JADWAL BARU</button>
        </form>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Guru Pengajar</th>
                    <th>Nama Murid</th>
                    <th>Hari & Jam</th>
                    <th>Instrumen</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($query_jadwal) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($query_jadwal)): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($row['nama_guru']); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['nama_murid']); ?></td>
                        <td><?php echo $row['hari']; ?>, <?php echo $row['jam']; ?></td>
                        <td><span class="instrumen"><?php echo htmlspecialchars($row['alat_musik']); ?></span></td>
                        <td>
                            <a href="jadwal.php?hapus=<?php echo $row['id']; ?>" class="btn-hapus" onclick="return confirm('Hapus jadwal antara <?php echo $row['nama_guru']; ?> dan <?php echo $row['nama_murid']; ?>?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center; padding:30px; color:#999;">Belum ada jadwal yang diatur.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
