<?php
session_start();
ob_start();
require_once(__DIR__ . '/../koneksi.php');

// Proteksi Admin
$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') || (isset($_COOKIE['user_role']) && $_COOKIE['user_role'] == 'admin');
if (!$is_admin) { header("Location: login.php"); exit(); }

// Ambil Statistik Singkat
$jml_murid = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM users WHERE role='murid'"));
$jml_guru  = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM users WHERE role='guru'"));
$jml_jadwal = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM jadwal"));

// Ambil Omzet Bulan Ini (Opsional)
$q_omzet = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal_bayar) as total FROM absensi"));
$total_omzet = $q_omzet['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Smart Arca</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .container { max-width: 1000px; margin: auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
        .grid-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 30px; }
        .card-stat { background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); text-align: center; }
        .card-stat h3 { margin: 10px 0 5px 0; font-size: 24px; color: #1a73e8; }
        .card-stat small { color: #888; text-transform: uppercase; font-weight: bold; font-size: 11px; }

        .menu-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .menu-item { 
            background: white; padding: 25px; border-radius: 15px; text-decoration: none; color: #333; 
            display: flex; align-items: center; gap: 15px; transition: 0.3s; box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            border: 1px solid transparent;
        }
        .menu-item:hover { transform: translateY(-5px); border-color: #1a73e8; }
        .icon-box { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
        
        .bg-money { background: #e8f5e9; color: #2e7d32; }
        .bg-users { background: #e3f2fd; color: #1565c0; }
        .bg-calendar { background: #fff3e0; color: #ef6c00; }
        
        .btn-logout { background: #fee2e2; color: #dc2626; padding: 10px 20px; border-radius: 10px; text-decoration: none; font-weight: bold; font-size: 14px; }
         <a href="../logout.php" class="btn-logout">Keluar</a>
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div>
            <h1 style="margin:0;">Dashboard Admin</h1>
            <p style="color:#666; margin:5px 0 0 0;">Smart Arca Music School</p>
        </div>
        <a href="../logout.php" class="btn-logout">Keluar</a>
    </div>

    <div class="grid-stats">
        <div class="card-stat">
            <small>Total Murid</small>
            <h3><?php echo $jml_murid; ?></h3>
        </div>
        <div class="card-stat">
            <small>Total Guru</small>
            <h3><?php echo $jml_guru; ?></h3>
        </div>
        <div class="card-stat">
            <small>Total Jadwal</small>
            <h3><?php echo $jml_jadwal; ?></h3>
        </div>
        <div class="card-stat" style="background: #1a73e8; color: white;">
            <small style="color: #e0e0e0;">Total Omzet</small>
            <h3 style="color: white;">Rp <?php echo number_format($total_omzet, 0, ',', '.'); ?></h3>
        </div>
    </div>

    <div class="menu-grid">
        <a href="honor.php" class="menu-item">
            <div class="icon-box bg-money">💰</div>
            <div>
                <strong style="display:block; font-size:18px;">Keuangan & Honor</strong>
                <small style="color:#888;">Kelola bagi hasil 50/50 & biaya</small>
            </div>
        </a>

        <a href="users.php" class="menu-item">
            <div class="icon-box bg-users">👥</div>
            <div>
                <strong style="display:block; font-size:18px;">Manajemen User</strong>
                <small style="color:#888;">Tambah/Edit Guru & Murid</small>
            </div>
        </a>

        <a href="jadwal.php" class="menu-item">
            <div class="icon-box bg-calendar">📅</div>
            <div>
                <strong style="display:block; font-size:18px;">Atur Jadwal</strong>
                <small style="color:#888;">Plotting waktu les & instrumen</small>
            </div>
        </a>
    </div>
</div>

</body>
</html>
