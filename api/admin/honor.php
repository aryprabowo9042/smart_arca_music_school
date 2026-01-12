<?php
session_start();
require_once(__DIR__ . '/../koneksi.php');
// (Tambahkan proteksi admin di sini)

$sql = "SELECT u.username as nama_guru, 
               SUM(a.nominal_bayar) as total_bruto,
               SUM(a.nominal_bayar) * 0.6 as hak_guru,
               SUM(a.nominal_bayar) * 0.4 as hak_admin
        FROM absensi a
        JOIN jadwal j ON a.id_jadwal = j.id
        JOIN users u ON j.id_guru = u.id
        GROUP BY u.id";
$result = mysqli_query($conn, $sql);
?>
<div class="container">
    <h2>Manajemen Honor Guru</h2>
    <table border="1" cellpadding="10" style="width:100%; border-collapse:collapse;">
        <tr style="background:#eee;">
            <th>Nama Guru</th>
            <th>Total Setoran Murid</th>
            <th>Honor Guru (60%)</th>
            <th>Profit Sekolah (40%)</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['nama_guru']; ?></td>
            <td>Rp <?php echo number_format($row['total_bruto']); ?></td>
            <td style="color:green; font-weight:bold;">Rp <?php echo number_format($row['hak_guru']); ?></td>
            <td style="color:blue;">Rp <?php echo number_format($row['hak_admin']); ?></td>
        </tr>
        <?php } ?>
    </table>
</div>
