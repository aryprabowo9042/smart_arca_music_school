<?php
require_once('koneksi.php');

// 1. CEK STATUS LOGIN
$role = $_COOKIE['user_role'] ?? null;
$username_login = $_COOKIE['user_username'] ?? '';
$is_login = ($role !== null);

// 2. LOGIKA PROSES LOGIN
$error = '';
if (isset($_POST['login'])) {
    $user_input = mysqli_real_escape_string($conn, $_POST['username']);
    $pass_input = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = '$user_input' AND password = '$pass_input' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Set Cookie (berlaku 30 hari)
        setcookie('user_id', $user['id'], time() + (86400 * 30), "/");
        setcookie('user_role', $user['role'], time() + (86400 * 30), "/");
        setcookie('user_username', $user['username'], time() + (86400 * 30), "/");

        // Pengalihan berdasarkan role (semua folder ada di dalam /api/)
        header("Location: " . $user['role'] . "/index.php");
        exit();
    } else {
        $error = "Username atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Arca Music School</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-white">

    <nav class="bg-red-700 text-white py-4 px-6 shadow-xl border-b-4 border-yellow-400 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="bg-yellow-400 text-red-700 w-10 h-10 rounded-lg flex items-center justify-center text-xl shadow-md">
                    <i class="fas fa-music"></i>
                </div>
                <div>
                    <h1 class="font-black italic uppercase text-lg leading-none tracking-tighter">Smart Arca</h1>
                    <p class="text-[10px] font-bold text-red-200 uppercase tracking-widest">Music School</p>
                </div>
            </div>

            <div class="flex gap-4">
                <?php if($is_login): ?>
                    <a href="<?php echo $role; ?>/index.php" class="bg-yellow-400 hover:bg-yellow-300 text-red-800 px-6 py-2 rounded-full font-black text-xs uppercase shadow-lg transition flex items-center gap-2">
                        <i class="fas fa-tachometer-alt"></i> Dashboard <?php echo ucfirst($role); ?>
                    </a>
                    <a href="logout.php" class="bg-white hover:bg-slate-100 text-red-600 px-4 py-2 rounded-full font-black text-xs uppercase shadow-lg transition">Keluar</a>
                <?php else: ?>
                    <a href="#login-section" class="bg-yellow-400 hover:bg-yellow-300 text-red-800 px-8 py-3 rounded-full font-black text-xs uppercase shadow-lg tracking-widest">Login Portal <i class="fas fa-arrow-right ml-2"></i></a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <header class="py-20 px-6 bg-gradient-to-b from-red-50 to-white text-center">
        <div class="max-w-4xl mx-auto">
            <span class="inline-block bg-red-100 text-red-600 px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-[0.2em] mb-6">Professional Music Education</span>
            <h1 class="text-5xl md:text-7xl font-black text-slate-900 italic uppercase tracking-tighter mb-6 leading-tight">Wujudkan <span class="text-red-600">Mimpimu</span><br>Lewat <span class="text-yellow-500">Nada & Irama</span></h1>
            <p class="text-slate-500 font-bold text-sm max-w-xl mx-auto mb-10 leading-relaxed uppercase">Belajar musik lebih menyenangkan dengan kurikulum modern dan pengajar profesional.</p>
        </div>
    </header>

    <?php if(!$is_login): ?>
    <section id="login-section" class="py-16 bg-slate-50">
        <div class="max-w-md mx-auto px-6">
            <div class="bg-white p-10 rounded-[2.5rem] shadow-2xl border-b-8 border-yellow-400">
                <h2 class="text-2xl font-black text-slate-800 uppercase italic text-center mb-8">Masuk ke Sistem</h2>
                
                <?php if($error): ?>
                    <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-xs font-bold border border-red-100 italic flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2 mb-2 block">Username</label>
                        <input type="text" name="username" class="w-full p-4 rounded-2xl bg-slate-50 border-2 border-slate-50 focus:border-red-600 outline-none font-bold italic" placeholder="Username" required>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2 mb-2 block">Password</label>
                        <input type="password" name="password" class="w-full p-4 rounded-2xl bg-slate-50 border-2 border-slate-50 focus:border-red-600 outline-none font-bold" placeholder="••••••••" required>
                    </div>
                    <button type="submit" name="login" class="w-full bg-red-600 hover:bg-red-700 text-white font-black py-4 rounded-2xl uppercase italic shadow-xl transition active:scale-95">Masuk <i class="fas fa-sign-in-alt ml-2"></i></button>
                </form>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <footer class="bg-slate-900 text-white py-10 text-center border-t-8 border-yellow-400">
        <p class="font-black italic uppercase tracking-widest text-lg">Smart Arca Music School</p>
        <p class="text-slate-500 text-[10px] font-bold mt-2 uppercase">Copyright &copy; 2026 - All Rights Reserved</p>
    </footer>

</body>
</html>
