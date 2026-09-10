<?php
// notifikasi.php
require 'config.php';
checkAuth();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi - SavingGoals</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-[#f8fafd] text-slate-800 antialiased min-h-screen">

    <div class="flex flex-col md:flex-row min-h-screen">
        
        <?php include 'sidebar.php'; ?>

        <main class="flex-1 p-4 md:p-8 overflow-y-auto max-w-xl mx-auto md:mx-0">
            <!-- Header dengan Tombol Kembali ke Beranda -->
            <div class="flex items-center space-x-3 mb-6">
                <a href="index.php" class="p-2.5 bg-white border border-slate-100 hover:bg-slate-50 rounded-2xl text-slate-600 transition shadow-sm flex items-center justify-center">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h1 class="text-xl md:text-2xl font-black text-slate-900 tracking-tight">Notifikasi</h1>
                    <p class="text-slate-400 text-xs md:text-sm font-medium mt-0.5">Pemberitahuan aktivitas tabunganmu.</p>
                </div>
            </div>

            <div class="bg-white border border-slate-100 rounded-3xl p-8 text-center shadow-sm">
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="bell-off" class="w-6 h-6"></i>
                </div>
                <p class="text-xs font-semibold text-slate-400">Belum ada notifikasi baru saat ini.</p>
            </div>
        </main>
    </div>

    <script> lucide.createIcons(); </script>
</body>
</html>