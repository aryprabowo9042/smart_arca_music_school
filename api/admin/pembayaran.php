<?php
session_start();
include '../includes/koneksi.php';

if ($_SESSION['status'] != "login" || $_SESSION['role'] != "admin") { 
    header("Location: ../login.php"); 
    exit(); 
}

function buatRupiah($angka){ 
    return "Rp " . number_format($angka,0,',','.'); 
}

// --- PROSES SIMPAN TRANSAKSI ---
if (isset($_POST['simpan_admin'])) {
    $kategori = $_POST['kategori'];
    $tanggal = $_POST['tanggal'];
    $jumlah = $_POST['jumlah'];
    
    $id_murid = !empty($_POST['id_murid']) ? $_POST['id_murid'] : "NULL";
    $nama_luar = mysqli_real_escape_string($koneksi, $_POST['nama_luar']);
    $keterangan_input = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
    
    // Gabungkan nama orang luar ke keterangan jika id_murid kosong
    $keterangan_final = ($id_murid == "NULL") ? "[UMUM: $nama_luar] " . $keterangan_input : $keterangan_input;

    $id_penerima = $_SESSION['id_user'];
    $komisi = 0;
    $id_guru_les = "NULL";

    if ($kategori == 'Les') {
        $id_guru_les = $_POST['id_guru_les'];
        if(empty($id_guru_les) || $id_murid == "NULL") {
            echo "<script>alert('Untuk kategori LES, Siswa dan Guru wajib dipilih!'); window.history.back();</script>";
            exit();
        }
        $komisi = $jumlah * 0.5;
    }

    $query = "INSERT INTO pembayaran (id_murid, id_guru_les, id_penerima, tanggal, jumlah, komisi_guru, keterangan, kategori) 
              VALUES ($id_murid, $id_guru_les, '$id_penerima', '$tanggal', '$jumlah', '$komisi', '$keterangan_final', '$kategori')";

    if (mysqli_query($koneksi, $query)) {
        if ($komisi > 0) {
            mysqli_query($koneksi, "UPDATE users SET saldo = saldo + $komisi WHERE id = $id_guru_les");
        }
        echo "<script>alert('Transaksi berhasil disimpan!'); window.location='pembayaran.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Manajemen Kas - Smart Arca</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function sesuaikanForm() {
            var kategori = document.getElementById("kategori").value;
            var selSiswa = document.getElementById("id_murid").value;
            var divGuru = document.getElementById("div_guru");
            var divNamaLuar = document.getElementById("div_nama_luar");

            divGuru.style.display = (kategori === "Les") ? "block" : "none";
            divNamaLuar.style.display = (selSiswa === "") ? "block" : "none";
        }
    </script>
</head>
<body>

    <div class="sidebar">
        <h2>Smart Arca Admin</h2>
        <a href="index.php">Dashboard</a>
        <a href="pembayaran.php" style="background-color: #495057; color: white;">Pemasukan</a>
        <a href="pengeluaran.php">Pengeluaran</a>
        <a href="laporan_keuangan.php">Laporan Kas</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Manajemen Pemasukan Kas</h1>
        
        <div style="margin-bottom: 20px;">
            <a href="pembayaran.php" class="btn btn-green">Input Pemasukan</a>
            <a href="pengeluaran.php" class="btn btn-blue">Input Pengeluaran</a>
            <a href="laporan_keuangan.php" class="btn btn-blue">Laporan Keuangan</a>
        </div>

        <div class="card" style="background-color: #f0fdf4; border: 1px solid #bbf7d0;">
            <h3>+ Input Transaksi Baru</h3>
            <form action="" method="POST">
                <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                    <div style="flex: 1;">
                        <label>Kategori:</label>
                        <select name="kategori" id="kategori" required onchange="sesuaikanForm()" style="width: 100%; padding: 8px;">
                            <option value="Les">Pembayaran SPP / Les</option>
                            <option value="Pendaftaran">Uang Pendaftaran</option>
                            <option value="Buku">Penjualan Buku</option>
                            <option value="Alat Musik">Penjualan Alat</option>
                            <option value="Lainnya">Lain-lain</option>
                        </select>
                    </div>
                    <div style="flex: 1;">
                        <label>Siswa (Kosongkan jika Umum):</label>
                        <select name="id_murid" id="id_murid" onchange="sesuaikanForm()" style="width: 100%; padding: 8px;">
                            <option value="">-- Pihak Luar / Umum --</option>
                            <?php
                            $q = mysqli_query($koneksi, "SELECT * FROM users WHERE role='murid' ORDER BY nama_lengkap ASC");
                            while ($m = mysqli_fetch_assoc($q)) { echo "<option value='".$m['id']."'>".$m['nama_lengkap']."</option>"; }
                            ?>
                        </select>
                    </div>
                </div>

                <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                    <div style="flex: 1; display: block;" id="div_nama_luar">
                        <label>Nama Pihak Luar (Manual):</label>
                        <input type="text" name="nama_luar" placeholder="Contoh: Budi (Umum)" style="width: 100%; padding: 8px;">
                    </div>
                    <div style="flex: 1; display: block;" id="div_guru">
                        <label>Guru Pengajar (50%):</label>
                        <select name="id_guru_les" style="width: 100%; padding: 8px;">
                            <option value="">-- Pilih Guru --</option>
                            <?php
                            $qg = mysqli_query($koneksi, "SELECT * FROM users WHERE role='guru' ORDER BY nama_lengkap ASC");
                            while ($g = mysqli_fetch_assoc($qg)) { echo "<option value='".$g['id']."'>".$g['nama_lengkap']."</option>"; }
                            ?>
                        </select>
                    </div>
                </div>

                <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                    <div style="flex: 1;">
                        <label>Tanggal:</label>
                        <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required style="width: 100%; padding: 8px;">
                    </div>
                    <div style="flex: 1;">
                        <label>Nominal (Rp):</label>
                        <input type="number" name="jumlah" required style="width: 100%; padding: 8px;">
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label>Keterangan:</label>
                    <input type="text" name="keterangan" required style="width: 100%; padding: 8px;">
                </div>

                <button type="submit" name="simpan_admin" class="btn btn-green" style="width:100%;">SIMPAN TRANSAKSI</button>
            </form>
        </div>

        <br>

        <h3>Riwayat Transaksi Masuk</h3>
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Siswa / Umum</th>
                        <th>Total</th>
                        <th>Bagi Guru</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res = mysqli_query($koneksi, "SELECT pembayaran.*, users.nama_lengkap FROM pembayaran LEFT JOIN users ON pembayaran.id_murid = users.id ORDER BY pembayaran.id DESC");
                    while ($d = mysqli_fetch_assoc($res)) {
                    ?>
                    <tr>
                        <td><?php echo date('d/m/y', strtotime($d['tanggal'])); ?></td>
                        <td><small><?php echo strtoupper($d['kategori']); ?></small></td>
                        <td><?php echo ($d['nama_lengkap']) ? "<b>".$d['nama_lengkap']."</b>" : $d['keterangan']; ?></td>
                        <td><?php echo buatRupiah($d['jumlah']); ?></td>
                        <td style="color: green;"><?php echo ($d['komisi_guru'] > 0) ? buatRupiah($d['komisi_guru']) : "-"; ?></td>
                        <td>
                            <a href="edit_pembayaran.php?id=<?php echo $d['id']; ?>" class="btn btn-blue" style="padding: 4px 8px; font-size: 11px;">Edit</a>
                            <a href="hapus_pembayaran.php?id=<?php echo $d['id']; ?>" class="btn btn-red" style="padding: 4px 8px; font-size: 11px;" onclick="return confirm('Hapus transaksi?')">Hapus</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>