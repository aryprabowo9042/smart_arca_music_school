<?php
// 1. PROTEKSI HALAMAN
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

require_once(__DIR__ . '/../koneksi.php');

// ==========================================
// 2. PROSES DATA (TAMBAH / EDIT / HAPUS)
// ==========================================

// Simpan & Update User
if (isset($_POST['simpan_user'])) {
    $id_edit = $_POST['id_edit'];
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; // Simpan plain text sesuai sistem Bapak sebelumnya
    $role = $_POST['role'];

    if (!empty($id_edit)) {
        // Jika password diisi, update password juga. Jika kosong, biarkan yang lama.
        if (!empty($password)) {
            $sql = "UPDATE users SET username='$username', password='$password', role='$role' WHERE id='$id_edit'";
        } else {
            $sql = "UPDATE users SET username='$username', role='$role' WHERE id='$id_edit'";
        }
    } else {
        $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
    }
    mysqli_query($conn, $sql);
    header("Location: users.php"); exit();
}

// Hapus User
if (isset($_GET['hapus'])) {
    $id_h = mysqli_real_escape_string($conn, $_GET['hapus']);
    mysqli_query($conn, "DELETE FROM users WHERE id = '$id_h'");
    header("Location: users.php"); exit();
}

// Data Edit
$edit_data = ['id' => '', 'username' => '', 'password' => '', 'role' => 'murid'];
if (isset($_GET['edit'])) {
    $id_e = mysqli_real_escape_string($conn, $_GET['edit']);
    $res_e = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id_e'");
    if($res_e && mysqli_num_rows($res_e) > 0) $edit_data = mysqli_fetch_assoc($res_e);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Pengguna - Smart Arca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen pb-20">

    <nav class="bg-red-600 shadow-xl px-6 py-4 flex justify-between items-center mb-10 border-b-4 border-yellow-400 sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <a href="index.php" class="text-white bg-red-700 w-10 h-10 rounded-xl flex items-center justify-center hover:bg-red-800 transition shadow-inner">
                <i class="fas fa-home"></i>
            </a>
            <h1 class="text-white font-black text-xl italic uppercase tracking-tighter">Manajemen Pengguna</h1>
        </div>
        <a href="../logout.php" class="text-white hover:text-yellow-300 text-2xl transition"><i class="fas fa-sign-out-alt"></i></a>
    </nav>

    <div class="max-w-6xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-1">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border-t-8 border-red-600 sticky top-28">
                <h3 class="font-black text-slate-800 text-sm uppercase italic tracking-widest mb-6 border-l-4 border-yellow-400 pl-3 leading-none">
                    <?php echo $edit_data['id'] ? 'Edit Akun' : 'Tambah Akun'; ?>
                </h3>
                <form method="POST" class="space-y-5">
                    <input type="hidden" name="id_edit" value="<?php echo $edit_data['id']; ?>">
                    
                    <div>
                        <label class="text-[9px] font-black text-slate-400 uppercase ml-1">Username / Nama Lengkap</label>
                        <input type="text" name="username" value="<?php echo htmlspecialchars($edit_data['username']); ?>" class="w-full p-3 border-2 border-slate-50 rounded-xl bg-slate-50 text-sm font-bold focus:border-red-600 outline-none" placeholder="Masukkan nama..." required>
                    </div>

                    <div>
                        <label class="text-[9px] font-black text-slate-400 uppercase ml-1">Password</label>
                        <input type="text" name="password" class="w-full p-3 border-2 border-slate-50 rounded-xl bg-slate-50 text-sm font-bold focus:border-red-600 outline-none" placeholder="<?php echo $edit_data['id'] ? 'Kosongkan jika tak diubah' : 'Masukkan password...'; ?>" <?php echo $edit_data['id'] ? '' : 'required'; ?>>
                    </div>

                    <div>
                        <label class="text-[9px] font-black text-slate-400 uppercase ml-1">Role / Hak Akses</label>
                        <select name="role" class="w-full p-3 border-2 border-slate-50 rounded-xl bg-slate-50 font-bold text-xs focus:border-red-600 outline-none transition">
                            <option value="murid" <?php echo ($edit_data['role'] == 'murid') ? 'selected' : ''; ?>>🎓 MURID (SISWA)</option>
                            <option value="guru" <?php echo ($edit_data['role'] == 'guru') ? 'selected' : ''; ?>>🎻 GURU (TEACHER)</option>
                            <option value="admin" <?php echo ($edit_data['role'] == 'admin') ? 'selected' : ''; ?>>⚡ ADMIN (PENGELOLA)</option>
                        </select>
                    </div>

                    <button type="submit" name="simpan_user" class="w-full bg-red-600 hover:bg-red-700 text-yellow-400 font-black py-4 rounded-2xl shadow-xl transition active:scale-95 uppercase text-xs tracking-widest mt-4">
                        <?php echo $edit_data['id'] ? 'Update Akun' : 'Daftarkan Akun'; ?>
                    </button>
                    <?php if($edit_data['id']): ?>
                        <a href="users.php" class="block text-center text-[10px] font-black text-slate-400 uppercase mt-2">Batalkan Perubahan</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-100">
                <div class="p-6 bg-slate-50 border-b flex justify-between items-center">
                    <h3 class="font-black text-slate-800 uppercase text-xs italic tracking-widest">Daftar Pengguna Aktif</h3>
                    <span class="text-[9px] bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full font-black uppercase">Total: <?php echo mysqli_num_rows(mysqli_query($conn, "SELECT id FROM users")); ?> Akun</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-slate-400 text-[9px] uppercase font-black border-b tracking-tighter">
                            <tr>
                                <th class="p-5">User & Password</th>
                                <th class="p-5">Hak Akses</th>
                                <th class="p-5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php 
                            $sql_u = "SELECT * FROM users ORDER BY role ASC, username ASC";
                            $res_u = mysqli_query($conn, $sql_u);
                            while($r = mysqli_fetch_assoc($res_u)): 
                                // Warna label berdasarkan role
                                $label_color = "bg-slate-100 text-slate-500";
                                if($r['role'] == 'admin') $label_color = "bg-red-100 text-red-600";
                                if($r['role'] == 'guru') $label_color = "bg-yellow-100 text-yellow-700";
                                if($r['role'] == 'murid') $label_color = "bg-green-100 text-green-600";
                            ?>
                            <tr class="hover:bg-slate-50 transition group">
                                <td class="p-5">
                                    <p class="font-black text-slate-800 uppercase text-xs leading-none mb-1"><?php echo htmlspecialchars($r['username']); ?></p>
                                    <p class="text-[10px] font-bold text-slate-400 font-mono tracking-tighter">Pass: <?php echo htmlspecialchars($r['password']); ?></p>
                                </td>
                                <td class="p-5">
                                    <span class="<?php echo $label_color; ?> px-3 py-1 rounded-lg text-[9px] font-black uppercase italic">
                                        <?php echo $r['role']; ?>
                                    </span>
                                </td>
                                <td class="p-5 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="users.php?edit=<?php echo $r['id']; ?>" class="w-8 h-8 bg-yellow-400 text-red-700 rounded-lg flex items-center justify-center hover:bg-red-600 hover:text-white transition shadow-sm">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        <a href="users.php?hapus=<?php echo $r['id']; ?>" onclick="return confirm('Hapus akun <?php echo $r['username']; ?>?')" class="w-8 h-8 bg-slate-100 text-slate-400 rounded-lg flex items-center justify-center hover:bg-red-600 hover:text-white transition shadow-sm">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </a>
                                    </div>
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
