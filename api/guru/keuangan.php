<?php
session_start();
include '../includes/koneksi.php';

if ($_SESSION['status'] != "login" || $_SESSION['role'] != "guru") { header("Location: ../login.php"); exit(); }

$id_guru = $_SESSION['id_user'];
function buatRupiah($angka){ return "Rp " . number_format($angka,0,',','.'); }

// --- 1. PROSES INPUT PEMBAYARAN DARI MURID ---
if (isset($_POST['simpan_guru'])) {
    $id_murid = $_POST['id_murid'];
    $tanggal = $_POST['tanggal'];
    $jumlah = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];

    // Hitung Komisi (Hanya untuk pencatatan, karena guru pegang cash langsung)
    $komisi_guru = $jumlah * 0.5; 
    
    // Status setoran 'pending' karena uang admin masih dibawa guru
    $query_simpan = "INSERT INTO pembayaran (id_murid, id_penerima, id_guru_les, tanggal, jumlah, komisi_guru, keterangan, status_setoran) 
                     VALUES ('$id_murid', '$id_guru', '$id_guru', '$tanggal', '$jumlah', '$komisi_guru', '$keterangan', 'pending')";

    if (mysqli_query($koneksi, $query_simpan)) {
        echo "<script>alert('Pembayaran diterima! Anda memegang uang tunai. Jangan lupa setor 50% ke Admin.'); window.location='keuangan.php';</script>";
    }
}

// --- 2. PROSES SETOR KE ADMIN (TOMBOL DIKLIK) ---
if (isset($_POST['setor_ke_admin'])) {
    $id_transaksi = $_POST['id_transaksi'];
    
    // Update status menjadi sudah disetor
    mysqli_query($koneksi, "UPDATE pembayaran SET status_setoran='sudah_disetor' WHERE id='$id_transaksi'");
    
    echo "<script>alert('Terima kasih! Status transaksi diperbarui menjadi Sudah Disetor ke Admin.'); window.location='keuangan.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Keuangan Guru</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="sidebar">
        <h2>Panel Guru</h2>
        <a href="index.php">Dashboard</a>
        <a href="jadwal_saya.php">Jadwal Mengajar</a>
        <a href="keuangan.php" style="background-color: #495057; color: white;">Input Pembayaran</a>
        <a href="dompet.php">Dompet Saya</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Input Pembayaran (Tunai)</h1>
        <p>Gunakan halaman ini jika Murid membayar tunai (Cash) kepada Anda.</p>
        
        <div class="card" style="background-color: #e2e3e5; border: 2px solid #d3d6d8; margin-bottom: 30px;">
            <h2 style="margin-top: 0; color: #383d41;">+ Terima Uang Tunai</h2>
            <form action="" method="POST">
                <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                    <div style="flex: 1;">
                        <label><b>Nama Siswa:</b></label>
                        <select name="id_murid" required style="width: 100%; padding: 10px; margin-top: 5px;">
                            <option value="">-- Pilih Siswa --</option>
                            <?php
                            $q_murid = mysqli_query($koneksi, "SELECT * FROM users WHERE role='murid' ORDER BY nama_lengkap ASC");
                            while ($m = mysqli_fetch_assoc($q_murid)) {
                                echo "<option value='".$m['id']."'>".$m['nama_lengkap']."</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div style="flex: 1;">
                        <label><b>Tanggal:</b></label>
                        <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required style="width: 100%; padding: 8px; margin-top: 5px;">
                    </div>
                    <div style="flex: 1;">
                        <label><b>Jumlah Diterima (Rp):</b></label>
                        <input type="number" name="jumlah" placeholder="Contoh: 100000" required style="width: 100%; padding: 8px; margin-top: 5px;">
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label><b>Keterangan:</b></label>
                    <input type="text" name="keterangan" placeholder="Contoh: SPP, Buku, dll" required style="width: 100%; padding: 8px; margin-top: 5px;">
                </div>

                <button type="submit" name="simpan_guru" style="background-color: #0d6efd; color: white; padding: 15px 30px; border: none; font-size: 16px; font-weight: bold; cursor: pointer; width: 100%; border-radius: 5px;">
                    TERIMA UANG (CASH)
                </button>
            </form>
        </div>

        <hr>

        <h3>Riwayat Uang Tunai yang Anda Terima</h3>
        <p>Anda wajib menyetorkan <b>50%</b> dari total uang yang diterima kepada Admin.</p>
        
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Siswa</th>
                        <th>Total Terima</th>
                        <th>Bagian Anda (50%)</th>
                        <th>Jatah Admin (50%)</th>
                        <th>Status Setoran</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Tampilkan hanya transaksi yang diterima oleh Guru ini
                    $query = "SELECT pembayaran.*, users.nama_lengkap 
                              FROM pembayaran 
                              JOIN users ON pembayaran.id_murid = users.id
                              WHERE pembayaran.id_penerima = '$id_guru'
                              ORDER BY pembayaran.tanggal DESC";
                    
                    $result = mysqli_query($koneksi, $query);
                    while ($data = mysqli_fetch_assoc($result)) {
                        
                        // Hitung Jatah Admin (Total - Komisi Guru)
                        $jatah_admin = $data['jumlah'] - $data['komisi_guru'];
                    ?>
                    <tr>
                        <td><?php echo date('d-m-Y', strtotime($data['tanggal'])); ?></td>
                        <td><?php echo $data['nama_lengkap']; ?></td>
                        <td style="font-weight: bold;"><?php echo buatRupiah($data['jumlah']); ?></td>
                        
                        <td style="color: green;">
                            <?php echo buatRupiah($data['komisi_guru']); ?> <br>
                            <small>(Ambil Langsung)</small>
                        </td>
                        
                        <td style="color: red; font-weight: bold;">
                            <?php echo buatRupiah($jatah_admin); ?>
                        </td>

                        <td>
                            <?php if($data['status_setoran'] == 'pending'): ?>
                                
                                <form action="" method="POST" onsubmit="return confirm('Apakah Anda sudah menyerahkan uang Rp <?php echo number_format($jatah_admin); ?> ke Admin?')">
                                    <input type="hidden" name="id_transaksi" value="<?php echo $data['id']; ?>">
                                    <button type="submit" name="setor_ke_admin" style="background-color: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-weight: bold;">
                                        Setor ke Admin ➜
                                    </button>
                                </form>

                            <?php else: ?>
                                <span style="background-color: #198754; color: white; padding: 5px 10px; border-radius: 4px; font-size: 12px;">✅ Sudah Disetor</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>