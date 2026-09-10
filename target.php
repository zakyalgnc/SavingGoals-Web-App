<?php
// target.php
require 'config.php';
checkAuth();

$user_id = $_SESSION['user_id'];

$sql = "SELECT t.*, COALESCE(SUM(m.nominal), 0) AS total_terkumpul 
        FROM tabungan t 
        LEFT JOIN transaksi_tabungan m ON t.id = m.tabungan_id 
        WHERE t.user_id = ? AND t.id > 0 AND t.judul != '0'
        GROUP BY t.id 
        ORDER BY t.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$tabungan = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Target Tabungan - SavingGoals</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-[#f8fafd] text-slate-800 antialiased min-h-screen">

    <div class="flex flex-col md:flex-row min-h-screen">
        
        <?php include 'sidebar.php'; ?>

        <!-- Content Area -->
        <main class="flex-1 p-4 md:p-8 overflow-y-auto max-w-7xl">
            <!-- Header dengan Tombol Kembali ke Beranda -->
            <div class="flex items-center space-x-3 mb-6">
                <a href="index.php" class="p-2.5 bg-white border border-slate-100 hover:bg-slate-50 rounded-2xl text-slate-600 transition shadow-sm flex items-center justify-center">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h1 class="text-xl md:text-2xl font-black text-slate-900 tracking-tight">Target Tabungan</h1>
                    <p class="text-slate-400 text-xs md:text-sm font-medium mt-0.5">Daftar kemajuan seluruh target pencapaianmu.</p>
                </div>
            </div>

            <?php if (empty($tabungan)): ?>
                <div class="bg-white border border-slate-100 rounded-3xl p-8 text-center shadow-sm">
                    <p class="text-slate-400 text-xs font-semibold">Belum ada target tabungan yang dibuat.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                    <?php foreach ($tabungan as $item): 
                        $target = $item['target_nominal'] > 0 ? $item['target_nominal'] : 1;
                        $persen = min(100, round(($item['total_terkumpul'] / $target) * 100));
                    ?>
                    <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm flex flex-col justify-between">
                        <div>
                            <?php if (!empty($item['foto']) && file_exists('uploads/' . $item['foto'])): ?>
                                <img src="uploads/<?= htmlspecialchars($item['foto']) ?>" alt="Foto Tabungan" class="w-full h-40 object-cover">
                            <?php else: ?>
                                <div class="w-full h-28 bg-gradient-to-r from-indigo-500 to-blue-500 flex items-center justify-center text-white font-extrabold text-2xl">
                                    🎯
                                </div>
                            <?php endif; ?>

                            <div class="p-5 space-y-4">
                                <div class="flex justify-between items-start">
                                    <h3 class="font-bold text-slate-900 text-base break-words max-w-[70%]"><?= htmlspecialchars($item['judul']) ?></h3>
                                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full <?= $item['status'] == 'Tercapai' ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600' ?>">
                                        <?= $item['status'] ?>
                                    </span>
                                </div>

                                <div class="space-y-1.5">
                                    <div class="flex justify-between text-xs font-semibold">
                                        <span class="text-slate-400">Kemajuan</span>
                                        <span class="text-indigo-600"><?= $persen ?>%</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                        <div class="bg-indigo-600 h-2 rounded-full" style="width: <?= $persen ?>%"></div>
                                    </div>
                                </div>

                                <div class="bg-slate-50 rounded-2xl p-3 text-xs space-y-1">
                                    <div class="flex justify-between">
                                        <span class="text-slate-400">Terkumpul:</span>
                                        <span class="font-bold text-emerald-600">Rp <?= number_format($item['total_terkumpul'], 0, ',', '.') ?></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-400">Target:</span>
                                        <span class="font-semibold text-slate-700">Rp <?= number_format($item['target_nominal'], 0, ',', '.') ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script> lucide.createIcons(); </script>
</body>
</html>