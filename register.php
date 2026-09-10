<?php
// register.php
require 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';

    if (!empty($nama) && !empty($email) && !empty($password) && !empty($konfirmasi_password)) {
        if ($password !== $konfirmasi_password) {
            $error = 'Konfirmasi password tidak cocok!';
        } elseif (strlen($password) < 8) {
            $error = 'Password minimal 8 karakter!';
        } else {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $error = 'Email sudah digunakan!';
            } else {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $insert = $pdo->prepare("INSERT INTO users (nama, email, password) VALUES (?, ?, ?)");
                
                if ($insert->execute([$nama, $email, $hashed_password])) {
                    header("Location: login.php?registered=1");
                    exit;
                } else {
                    $error = 'Gagal mendaftar, coba lagi!';
                }
            }
        }
    } else {
        $error = 'Harap isi semua kolom!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Daftar Akun - SavingGoals</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-[#f0f4fd] min-h-screen flex items-center justify-center p-3 sm:p-6">

    <!-- Card Container Responsif -->
    <div class="bg-white rounded-3xl md:rounded-[2.5rem] shadow-xl overflow-hidden w-full max-w-4xl flex flex-col md:flex-row-reverse border border-slate-100">
        
        <!-- Banner Kanan (Atas di HP) -->
        <div class="md:w-1/2 bg-gradient-to-br from-indigo-500 via-indigo-600 to-blue-600 p-6 sm:p-8 md:p-10 text-white flex flex-col justify-between relative overflow-hidden shrink-0">
            <div class="z-10">
                <div class="flex items-center space-x-2.5 mb-4 md:mb-8">
                    <div class="w-8 h-8 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center text-base">🐷</div>
                    <span class="font-extrabold text-base md:text-lg tracking-tight">SavingGoals</span>
                </div>
                <h2 class="text-xl sm:text-2xl md:text-3xl font-black leading-tight tracking-tight mb-2">
                    Wujudkan mimpi<br class="hidden sm:inline"> finansialmu sekarang!
                </h2>
                <p class="text-indigo-100 text-xs md:text-sm font-medium leading-relaxed max-w-xs">
                    Menabung hari ini, untuk masa depan yang lebih baik.
                </p>
            </div>

            <div class="my-4 md:my-6 z-10 flex justify-center items-center">
                <div class="text-5xl sm:text-7xl md:text-8xl select-none filter drop-shadow-md">🐖💰</div>
            </div>

            <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-3.5 md:p-4 z-10 max-w-xs">
                <p class="text-xs font-semibold text-white/90">
                    Ayo mulai menabung dan capai tujuan finansialmu bersama kami!
                </p>
            </div>
        </div>

        <!-- Form Kiri (Bawah di HP) -->
        <div class="md:w-1/2 p-6 sm:p-8 md:p-10 flex flex-col justify-center">
            <a href="login.php" class="inline-flex items-center space-x-1.5 text-slate-400 hover:text-slate-600 text-xs font-bold mb-4 transition">
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
                <span>Kembali</span>
            </a>

            <div class="mb-4">
                <h2 class="text-xl md:text-2xl font-black text-slate-900 tracking-tight">Buat Akun Baru</h2>
                <p class="text-slate-400 text-xs font-medium mt-0.5">Isi data di bawah ini untuk mulai menabung.</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-rose-50 border border-rose-100 text-rose-500 text-xs font-semibold p-3 rounded-2xl mb-4">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-3">
                <!-- Nama -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap</label>
                    <div class="relative">
                        <i data-lucide="user" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5"></i>
                        <input type="text" name="nama" required placeholder="Masukkan nama lengkap" class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:border-indigo-600 focus:bg-white transition">
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Email</label>
                    <div class="relative">
                        <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5"></i>
                        <input type="email" name="email" required placeholder="contoh@email.com" class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:border-indigo-600 focus:bg-white transition">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Password</label>
                    <div class="relative">
                        <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5"></i>
                        <input type="password" name="password" required placeholder="Minimal 8 karakter" class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:border-indigo-600 focus:bg-white transition">
                    </div>
                </div>

                <!-- Konfirmasi Password -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Konfirmasi Password</label>
                    <div class="relative">
                        <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5"></i>
                        <input type="password" name="konfirmasi_password" required placeholder="Ulangi password" class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:border-indigo-600 focus:bg-white transition">
                    </div>
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl text-xs shadow-lg shadow-indigo-200 transition flex items-center justify-center space-x-2 mt-2">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    <span>Daftar</span>
                </button>
            </form>

            <p class="text-center text-xs text-slate-500 font-medium mt-4">
                Sudah punya akun? <a href="login.php" class="text-indigo-600 font-bold hover:underline">Login →</a>
            </p>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>