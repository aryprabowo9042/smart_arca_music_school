<?php
session_start();
include '../includes/koneksi.php';

if ($_SESSION['status'] != "login" || $_SESSION['role'] != "admin") { header("Location: ../login.php"); exit(); }

$id_transaksi = $_GET['id'];

// 1. AMBIL DATA LAMA (Untuk ditampilkan di form & keperluan hitung saldo)
$query_old = "SELECT * FROM pembayaran WHERE id='$id_transaksi'";
$result_old = mysqli_query($koneksi, $query_old);
$data_lama = mysqli_fetch_assoc($result_old);

// PROSES UPDATE
if (isset($_POST['update'])) {
    $id_murid = $_POST['id_murid'];
    $id_guru_les = $_POST['id_guru_les'];
    $tanggal = $_POST['tanggal'];
    $jumlah_baru = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];

    // Hitung Komisi Baru (50%)
    $komisi_baru = $jumlah_baru * 0.5;

    // --- LOGIKA KOREKSI SALDO GURU ---
    // 1. Tarik kembali komisi LAMA dari saldo guru LAMA
    $guru_lama = $data_lama['id_guru_les'];
    $komisi_lama = $data_lama['komisi_guru'];
    mysqli_query($koneksi, "UPDATE users SET saldo = saldo - $komisi_lama WHERE id='$guru_lama'");

    // 2. Masukkan komisi BARU ke saldo guru BARU (biasanya gurunya sama, tapi jaga-jaga kalau admin ganti guru juga)
    mysqli_query($koneksi, "UPDATE users SET saldo = saldo + $komisi_baru WHERE id='$id_guru_les'");
    // ----------------------------------

    // 3. Update Data Transaksi di Database
    $query_update = "UPDATE pembayaran SET 
                     id_murid='$id_murid', 
                     id_guru_les='$id_guru_les', 
                     tanggal='$tanggal', 
                     jumlah='$jumlah_baru', 
                     komisi_guru='$komisi_baru', 
                     keterangan='$keterangan' 
                     WHERE id='$id_transaksi'";

    if (mysqli_query($koneksi, $query_update)) {
        echo "<script>alert('Data pembayaran berhasil diedit & saldo guru telah disesuaikan!'); window.location='pembayaran.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Pembayaran</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="sidebar">
        <h2>Smart Arca Admin</h2>
        <a href="index.php">Dashboard</a>
        <a href="pembayaran.php" style="background-color: #495057; color: white;">Keuangan</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Edit Transaksi Pembayaran</h1>
        <p>Mengubah data ini akan otomatis menghitung ulang komisi/saldo guru.</p>

        <div class="card" style="max-width: 600px;">
            <form action="" method="POST">
                
                <div class="form-group">
                    <label>Siswa</label>
                    <select name="id_murid" required style="width: 100%; padding: 10px;">
                        <?php
                        $qm = mysqli_query($koneksi, "SELECT * FROM users WHERE role='murid' ORDER BY nama_lengkap ASC");
                        while ($m = mysqli_fetch_assoc($qm)) {
                            $selected = ($m['id'] == $data_lama['id_murid']) ? 'selected' : '';
                            echo "<option value='".$m['id']."' $selected>".$m['nama_lengkap']."</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Guru Pengajar (Penerima Komisi)</label>
                    <select name="id_guru_les" required style="width: 100%; padding: 10px;">
                        <?php
                        $qg = mysqli_query($koneksi, "SELECT * FROM users WHERE role='guru' ORDER BY nama_lengkap ASC");
                        while ($g = mysqli_fetch_assoc($qg)) {
                            $selected = ($g['id'] == $data_lama['id_guru_les']) ? 'selected' : '';
                            echo "<option value='".$g['id']."' $selected>".$g['nama_lengkap']."</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" value="<?php echo $data_lama['tanggal']; ?>" required style="width: 100%; padding: 8px;">
                </div>

                <div class="form-group">
                    <label>Jumlah (Rp)</label>
                    <input type="number" name="jumlah" value="<?php echo $data_lama['jumlah']; ?>" required style="width: 100%; padding: 8px;">
                    <small style="color: red;">*Komisi guru akan dihitung ulang otomatis 50% dari nilai ini.</small>
                </div>

                <div class="form-group">
                    <label>Keterangan</label>
                    <input type="text" name="keterangan" value="<?php echo $data_lama['keterangan']; ?>" required style="width: 100%; padding: 8px;">
                </div>

                <button type="submit" name="update" class="btn btn-green">Simpan Perubahan</button>
                <a href="pembayaran.php" class="btn btn-red">Batal</a>
            </form>
        </div>
    </div>

</body>
</html>