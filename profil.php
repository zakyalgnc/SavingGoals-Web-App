<?php
// profil.php
require 'config.php';
checkAuth();

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT nama, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - SavingGoals</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-[#f8fafd] text-slate-800 antialiased min-h-screen">

    <!-- Container Flexbox Pembungkus Layout -->
    <div class="flex flex-col md:flex-row min-h-screen">
        
        <?php include 'sidebar.php'; ?>

        <!-- Content Area -->
        <main class="flex-1 p-4 md:p-8 overflow-y-auto max-w-4xl">
            <!-- Header dengan Tombol Kembali ke Beranda -->
            <div class="flex items-center space-x-3 mb-6">
                <a href="index.php" class="p-2.5 bg-white border border-slate-100 hover:bg-slate-50 rounded-2xl text-slate-600 transition shadow-sm flex items-center justify-center">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h1 class="text-xl md:text-2xl font-black text-slate-900 tracking-tight">Pengaturan Profil</h1>
                    <p class="text-slate-400 text-xs md:text-sm font-medium mt-0.5">Kelola akun dan data diri kamu.</p>
                </div>
            </div>

            <!-- Card Profil Utama -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 md:p-8 shadow-sm space-y-6">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-indigo-600 text-white font-black text-2xl rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-100">
                        <?= strtoupper(substr($user['nama'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900"><?= htmlspecialchars($user['nama'] ?? 'User') ?></h2>
                        <p class="text-xs text-slate-400 font-medium"><?= htmlspecialchars($user['email'] ?? '-') ?></p>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-4 space-y-3 text-xs md:text-sm">
                    <div class="flex justify-between items-center py-3 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">Nama Lengkap</span>
                        <span class="font-bold text-slate-800"><?= htmlspecialchars($user['nama'] ?? '-') ?></span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">Alamat Email</span>
                        <span class="font-bold text-slate-800"><?= htmlspecialchars($user['email'] ?? '-') ?></span>
                    </div>
                </div>

                <div class="pt-2">
                    <a href="logout.php" class="flex items-center justify-center space-x-2 w-full bg-rose-50 hover:bg-rose-100 text-rose-500 font-bold py-3.5 rounded-2xl text-xs md:text-sm transition">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        <span>Keluar Akun</span>
                    </a>
                </div>
            </div>
        </main>
    </div>

    <script> lucide.createIcons(); </script>
</body>
</html>