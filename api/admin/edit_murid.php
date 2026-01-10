<?php
session_start();
include '../includes/koneksi.php';

if ($_SESSION['status'] != "login" || $_SESSION['role'] != "admin") {
    header("Location: ../login.php");
    exit();
}

// Ambil ID dari URL
$id = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$id'");
$data = mysqli_fetch_assoc($query);

// Proses Update
if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $hp = $_POST['hp'];
    $kelas = $_POST['kelas']; // Ambil data kelas baru
    $level = $_POST['level']; // Ambil data level baru
    $password_baru = $_POST['password'];

    // Cek ganti password atau tidak
    if ($password_baru != "") {
        $pass_hash = md5($password_baru);
        $sql = "UPDATE users SET nama_lengkap='$nama', username='$username', password='$pass_hash', no_hp='$hp', kelas_musik='$kelas', level_musik='$level' WHERE id='$id'";
    } else {
        $sql = "UPDATE users SET nama_lengkap='$nama', username='$username', no_hp='$hp', kelas_musik='$kelas', level_musik='$level' WHERE id='$id'";
    }
    
    if (mysqli_query($koneksi, $sql)) {
        echo "<script>alert('Data murid berhasil diupdate!'); window.location='data_murid.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Murid</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="sidebar">
        <h2>Smart Arca Admin</h2>
        <a href="index.php">Dashboard</a>
        <a href="data_guru.php">Data Guru</a>
        <a href="data_murid.php" style="background-color: #495057; color: white;">Data Murid</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Edit Data Murid</h1>
        <div class="card" style="max-width: 600px;">
            <form action="" method="POST">
                
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" value="<?php echo $data['nama_lengkap']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" value="<?php echo $data['username']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Password Baru (Kosongkan jika tidak diganti)</label>
                    <input type="password" name="password" placeholder="***">
                </div>

                <div class="form-group">
                    <label>Kelas Musik</label>
                    <select name="kelas" style="width: 100%; padding: 8px;">
                        <?php
                        $list_kelas = ["Piano/Keyboard", "Gitar Klasik", "Gitar Elektrik", "Drum", "Vokal", "Theory"];
                        foreach ($list_kelas as $kls) {
                            // Jika data di database sama dengan opsi ini, tambahkan atribut 'selected'
                            $selected = ($data['kelas_musik'] == $kls) ? 'selected' : '';
                            echo "<option value='$kls' $selected>$kls</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Level Musik</label>
                    <select name="level" style="width: 100%; padding: 8px;">
                        <option value="Basic" <?php if($data['level_musik'] == 'Basic') echo 'selected'; ?>>Basic</option>
                        <option value="Intermediate" <?php if($data['level_musik'] == 'Intermediate') echo 'selected'; ?>>Intermediate</option>
                        <option value="Advance" <?php if($data['level_musik'] == 'Advance') echo 'selected'; ?>>Advance</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>No. HP</label>
                    <input type="text" name="hp" value="<?php echo $data['no_hp']; ?>">
                </div>

                <button type="submit" name="update" class="btn btn-blue">Update Data</button>
                <a href="data_murid.php" class="btn btn-red">Batal</a>
            </form>
        </div>
    </div>
</body>
</html>