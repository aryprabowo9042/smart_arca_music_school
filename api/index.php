<?php
require_once('koneksi.php');
$role = $_COOKIE['user_role'] ?? null;
$is_login = ($role !== null);

// Logika Login Sederhana
if (isset($_POST['login'])) {
    $u = mysqli_real_escape_string($conn, $_POST['username']);
    $p = $_POST['password'];
    $q = mysqli_query($conn, "SELECT * FROM users WHERE username = '$u' AND password = '$p' LIMIT 1");
    if (mysqli_num_rows($q) > 0) {
        $user = mysqli_fetch_assoc($q);
        setcookie('user_role', $user['role'], time() + (86400 * 30), "/");
        header("Location: " . $user['role'] . "/index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Arca Music School - Weleri</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700;800&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root { --arca-red: #d31f26; --arca-yellow: #fdb813; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f9f9f9; }
        .bg-arca-red { background-color: var(--arca-red) !important; }
        .text-arca-red { color: var(--arca-red) !important; }
        .bg-arca-yellow { background-color: var(--arca-yellow) !important; }
        .button.is-arca { background-color: var(--arca-red); color: white; border-radius: 12px; font-weight: 800; border: none; }
        .button.is-arca:hover { background-color: #b11a20; color: white; }
        .card-kursus { border-radius: 20px; border: 2px solid #eee; transition: all 0.3s ease; cursor: pointer; height: 100%; }
        .card-kursus:hover { border-color: var(--arca-red); transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.05); }
        .hero.is-arca { background: linear-gradient(135deg, var(--arca-red) 0%, #a0171c 100%); }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body x-data="{ 
    openModal: null, 
    silabus: {
        'Drum': 'Fokus: Pengenalan drum set, matched grip, koordinasi tangan-kaki, membaca notasi ritme, hingga basic rock beat.',
        'Keyboard': 'Fokus: Penjarian (fingering), notasi balok (Kunci G & F), akord mayor dasar (C, G, F), dan lagu anak-anak.',
        'Gitar Akustik': 'Fokus: Akor dasar (G, C, D, Em, Am, E), pola strumming 4/4, perpindahan akor, dan lagu pop.',
        'Gitar Elektrik': 'Fokus: Pengenalan Amp & Jack, teknik power chords, palm muting, cara membaca TAB, dan down-up stroke.',
        'Bass Elektrik': 'Fokus: Postur & plucking jari, pengenalan fretboard senar E & A, skala mayor, dan menjaga tempo (groove).',
        'Vokal': 'Fokus: Pernapasan diafragma, postur bernyanyi, pitch control (intonasi), resonansi dasar, dan ekspresi lagu.'
    }
}">

    <nav class="navbar is-white is-spaced shadow-sm border-b-4 border-arca-yellow" role="navigation" aria-label="main navigation">
        <div class="container">
            <div class="navbar-brand">
                <a class="navbar-item" href="#">
                    <strong class="is-size-4 has-text-weight-black is-italic uppercase tracking-tighter text-arca-red">SMART ARCA</strong>
                </a>
            </div>
            <div class="navbar-end">
                <div class="navbar-item">
                    <div class="buttons">
                        <?php if($is_login): ?>
                            <a href="<?php echo $role; ?>/index.php" class="button is-arca">DASHBOARD</a>
                        <?php else: ?>
                            <a href="#login-section" class="button is-arca">LOGIN PORTAL</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <section class="hero is-arca is-medium has-text-white">
        <div class="hero-body">
            <div class="container has-text-centered">
                <p class="subtitle is-size-7 has-text-weight-bold is-uppercase has-text-warning mb-2 tracking-widest">Weleri Music Education</p>
                <h1 class="title is-size-1 is-size-3-mobile has-text-weight-black is-italic uppercase mb-6">Wujudkan <span class="has-text-warning">Mimpimu</span> Lewat Musik</h1>
                <div class="columns is-centered mt-5">
                    <div class="column is-4">
                        <div class="notification is-white has-text-dark py-4 px-6 shadow" style="border-radius: 15px;">
                            <p class="is-size-7 has-text-weight-bold text-arca-red italic">HUBUNGI MBAK FIA:</p>
                            <a href="https://wa.me/62895360796038" target="_blank" class="is-size-6 has-text-weight-bold"><i class="fab fa-whatsapp has-text-success mr-2"></i> 0895-3607-96038</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <h2 class="title is-3 has-text-centered has-text-weight-black is-italic is-uppercase mb-6">Program Unggulan</h2>
            <div class="columns is-multiline">
                <template x-for="(desc, title) in silabus">
                    <div class="column is-4">
                        <div class="card card-kursus" @click="openModal = title">
                            <div class="card-content has-text-centered py-6">
                                <div class="icon is-large text-arca-red mb-4">
                                    <i class="fas fa-play-circle fa-3x"></i>
                                </div>
                                <h3 class="title is-5 has-text-weight-black is-uppercase italic" x-text="'Kelas ' + title"></h3>
                                <p class="is-size-7 has-text-grey-light has-text-weight-bold uppercase tracking-widest">Klik Untuk Silabus</p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </section>

    <div class="modal" :class="openModal ? 'is-active' : ''" x-cloak>
        <div class="modal-background" @click="openModal = null"></div>
        <div class="modal-content px-4">
            <div class="box p-0 overflow-hidden" style="border-radius: 20px;">
                <div class="bg-arca-red p-5 has-text-white border-b-4 border-arca-yellow">
                    <h3 class="title is-5 has-text-white has-text-weight-black italic uppercase mb-0" x-text="'Silabus ' + openModal"></h3>
                </div>
                <div class="p-6">
                    <p class="is-size-6 has-text-grey-darker has-text-weight-semibold italic mb-6" x-text="silabus[openModal]"></p>
                    <a href="https://wa.me/62895360796038" target="_blank" class="button is-arca is-fullwidth is-medium">DAFTAR KELAS SEKARANG</a>
                </div>
            </div>
        </div>
        <button class="modal-close is-large" aria-label="close" @click="openModal = null"></button>
    </div>

    <?php if(!$is_login): ?>
    <section id="login-section" class="section bg-light">
        <div class="container">
            <div class="columns is-centered">
                <div class="column is-4">
                    <div class="box p-6 shadow-lg border-b-8 border-arca-yellow" style="border-radius: 30px;">
                        <h2 class="title is-4 has-text-centered has-text-weight-black italic uppercase mb-6">Login Portal</h2>
                        <form method="POST">
                            <div class="field mb-4">
                                <div class="control has-icons-left">
                                    <input class="input is-medium" type="text" name="username" placeholder="USERNAME" style="border-radius: 12px;" required>
                                    <span class="icon is-small is-left"><i class="fas fa-user"></i></span>
                                </div>
                            </div>
                            <div class="field mb-6">
                                <div class="control has-icons-left">
                                    <input class="input is-medium" type="password" name="password" placeholder="PASSWORD" style="border-radius: 12px;" required>
                                    <span class="icon is-small is-left"><i class="fas fa-lock"></i></span>
                                </div>
                            </div>
                            <button type="submit" name="login" class="button is-arca is-fullwidth is-medium">MASUK DASHBOARD</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <footer class="footer bg-arca-red has-text-white border-t-8 border-arca-yellow pt-6 pb-6">
        <div class="content has-text-centered">
            <p class="is-size-7 has-text-weight-black is-uppercase tracking-widest mb-0 opacity-50">&copy; 2026 Smart Arca Music School. Professional Music Education.</p>
        </div>
    </footer>

    <script>
    (function(){if(!window.chatbase||window.chatbase("getState")!=="initialized"){window.chatbase=(...arguments)=>{if(!window.chatbase.q){window.chatbase.q=[]}window.chatbase.q.push(arguments)};window.chatbase=new Proxy(window.chatbase,{get(target,prop){if(prop==="q"){return target.q}return(...args)=>target(prop,...args)}})}const onLoad=function(){const script=document.createElement("script");script.src="https://www.chatbase.co/embed.min.js";script.id="N1ganJY1PR_sq1a-xetvM";script.domain="www.chatbase.co";document.body.appendChild(script)};if(document.readyState==="complete"){onLoad()}else{window.addEventListener("load",onLoad)}})();
    </script>

</body>
</html>
