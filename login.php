<?php
// login.php
require 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['email'] = $user['email'];

                header("Location: index.php");
                exit;
            } else {
                $error = 'Email atau password salah!';
            }
        } catch (PDOException $e) {
            $error = 'Terjadi kesalahan sistem.';
        }
    } else {
        $error = 'Silakan isi email dan password!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SavingGoals</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-4xl bg-white rounded-3xl border border-slate-100 shadow-xl overflow-hidden grid grid-cols-1 md:grid-cols-2">
        
        <!-- Sisi Kiri: Banner Info -->
        <div class="bg-indigo-600 p-8 md:p-10 text-white flex flex-col justify-between relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex items-center space-x-3 mb-8">
                    <img src="logo.png" alt="Logo" class="w-10 h-10 object-cover rounded-2xl bg-white p-1">
                    <span class="text-xl font-black tracking-tight text-white">SavingGoals</span>
                </div>
                <h1 class="text-2xl md:text-3xl font-black tracking-tight leading-tight mb-3">
                    Mulai perjalanan menabungmu hari ini!
                </h1>
                <p class="text-indigo-100 text-xs md:text-sm leading-relaxed">
                    Raih mimpi besar dengan langkah kecil yang konsisten.
                </p>

                <ul class="mt-6 space-y-2.5 text-xs md:text-sm font-semibold">
                    <li class="flex items-center space-x-2">
                        <span class="p-1 bg-indigo-500 rounded-lg"><i data-lucide="check" class="w-3.5 h-3.5 text-white"></i></span>
                        <span>Target Laptop</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <span class="p-1 bg-indigo-500 rounded-lg"><i data-lucide="check" class="w-3.5 h-3.5 text-white"></i></span>
                        <span>Target Motor</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <span class="p-1 bg-indigo-500 rounded-lg"><i data-lucide="check" class="w-3.5 h-3.5 text-white"></i></span>
                        <span>Target Rumah</span>
                    </li>
                </ul>
            </div>
            
            <p class="text-[10px] text-indigo-200 mt-8 relative z-10">&copy; <?= date('Y') ?> SavingGoals. All rights reserved.</p>
        </div>

        <!-- Sisi Kanan: Form Login -->
        <div class="p-8 md:p-10 flex flex-col justify-center">
            <div class="mb-6">
                <h2 class="text-xl md:text-2xl font-black text-slate-900 tracking-tight">Masuk ke Akun</h2>
                <p class="text-xs text-slate-400 font-medium mt-1">Yuk, lanjutkan perjalanan menabungmu.</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-rose-50 border border-rose-100 text-rose-500 text-xs font-semibold p-3.5 rounded-2xl mb-5">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" required placeholder="contoh@email.com" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-medium focus:outline-none focus:border-indigo-600 transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Password</label>
                    <input type="password" name="password" required placeholder="Masukkan password" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-medium focus:outline-none focus:border-indigo-600 transition">
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 rounded-2xl text-xs shadow-lg shadow-indigo-100 transition mt-2">
                    Masuk
                </button>
            </form>

            <p class="text-xs text-center text-slate-500 mt-6 font-medium">
                Belum punya akun? <a href="register.php" class="text-indigo-600 font-bold hover:underline">Daftar sekarang</a>
            </p>
        </div>

    </div>

    <script> lucide.createIcons(); </script>
</body>
</html>