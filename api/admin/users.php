<?php
// Matikan error reporting yang mengganggu, tapi tetap log error fatal
error_reporting(E_ALL & ~E_NOTICE);

// Cek Login Admin (Cookie Mode)
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

require_once(__DIR__ . '/../koneksi.php');

// --- 1. PROSES TAMBAH USER ---
if (isset($_POST['tambah_user'])) {
    $u = trim($_POST['username']);
    $p = $_POST['password']; // Simpan teks biasa agar pasti bisa login
    $r = $_POST['role'];

    // Cek dulu: Apakah username sudah ada?
    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username = '$u'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('GAGAL: Username \'$u\' sudah dipakai! Gunakan nama lain.');</script>";
    } else {
        // Jika belum ada, baru simpan
        $simpan = mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('$u', '$p', '$r')");
        
        if ($simpan) {
            echo "<script>alert('BERHASIL! User \'$u\' telah ditambahkan.'); window.location='users.php';</script>";
        } else {
            echo "<script>alert('Database Error: " . mysqli_error($conn) . "');</script>";
        }
    }
}

// --- 2. PROSES HAPUS USER ---
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    // Jangan biarkan admin menghapus dirinya sendiri
    if ($id == $_COOKIE['user_id']) {
        echo "<script>alert('Anda tidak bisa menghapus akun sendiri!'); window.location='users.php';</script>";
    } else {
        mysqli_query($conn, "DELETE FROM users WHERE id = '$id'");
        header("Location: users.php");
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen pb-10">

    <nav class="bg-white shadow-sm px-6 py-4 flex justify-between items-center mb-6">
        <div class="flex items-center gap-3">
            <a href="index.php" class="text-gray-500 hover:text-blue-600 transition">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <h1 class="text-xl font-bold text-gray-800">Manajemen Pengguna</h1>
        </div>
        <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded text-xs font-bold">
            Admin Area
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="md:col-span-1">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 sticky top-24">
                <h3 class="font-bold text-gray-700 mb-4 border-l-4 border-blue-600 pl-3">Tambah User Baru</h3>
                
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="text-[10px] uppercase font-bold text-gray-400">Username</label>
                        <input type="text" name="username" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-gray-50" placeholder="Contoh: pakbudi" required>
                    </div>

                    <div>
                        <label class="text-[10px] uppercase font-bold text-gray-400">Password</label>
                        <input type="text" name="password" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-gray-50" placeholder="Password..." required>
                    </div>

                    <div>
                        <label class="text-[10px] uppercase font-bold text-gray-400">Role / Jabatan</label>
                        <select name="role" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                            <option value="guru">Guru</option>
                            <option value="murid">Murid</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <button type="submit" name="tambah_user" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-lg shadow-blue-200 transition">
                        <i class="fas fa-plus mr-1"></i> SIMPAN USER
                    </button>
                </form>
            </div>
        </div>

        <div class="md:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-700">Daftar Pengguna Aktif</h3>
                    <span class="text-xs bg-white border px-2 py-1 rounded text-gray-500">
                        Total: <?php echo mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users")); ?>
                    </span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-white text-gray-400 text-xs uppercase border-b">
                            <tr>
                                <th class="p-4">Username</th>
                                <th class="p-4">Role</th>
                                <th class="p-4 text-center">Password</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php 
                            $users = mysqli_query($conn, "SELECT * FROM users ORDER BY role ASC, username ASC");
                            while($u = mysqli_fetch_assoc($users)): 
                                // Warna badge role
                                $bg_role = 'bg-gray-100 text-gray-600';
                                if($u['role'] == 'admin') $bg_role = 'bg-red-100 text-red-600';
                                if($u['role'] == 'guru') $bg_role = 'bg-purple-100 text-purple-600';
                                if($u['role'] == 'murid') $bg_role = 'bg-green-100 text-green-600';
                            ?>
                            <tr class="hover:bg-blue-50 transition">
                                <td class="p-4 font-bold text-gray-700">
                                    <?php echo htmlspecialchars($u['username']); ?>
                                </td>
                                <td class="p-4">
                                    <span class="<?php echo $bg_role; ?> px-2 py-1 rounded text-[10px] uppercase font-bold">
                                        <?php echo $u['role']; ?>
                                    </span>
                                </td>
                                <td class="p-4 text-center font-mono text-gray-400 text-xs">
                                    <?php echo htmlspecialchars($u['password']); ?>
                                </td>
                                <td class="p-4 text-center">
                                    <?php if($u['username'] !== 'admin' && $u['id'] !== $_COOKIE['user_id']): ?>
                                        <a href="users.php?hapus=<?php echo $u['id']; ?>" onclick="return confirm('Yakin hapus user <?php echo $u['username']; ?>? Data jadwal & absen terkait mungkin akan error.')" class="text-red-400 hover:text-red-600 transition">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-gray-300"><i class="fas fa-lock"></i></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
