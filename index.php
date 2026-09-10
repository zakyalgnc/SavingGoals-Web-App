<?php
// index.php
require 'config.php';
checkAuth();

$user_id = $_SESSION['user_id'];

// 1. Ambil data statistik
$stmt = $pdo->prepare("SELECT COUNT(*) as total_aktif FROM tabungan WHERE user_id = ? AND status = 'Belum Tercapai'");
$stmt->execute([$user_id]);
$target_aktif = $stmt->fetch()['total_aktif'] ?? 0;

$stmt = $pdo->prepare("SELECT COUNT(*) as total_tercapai FROM tabungan WHERE user_id = ? AND status = 'Tercapai'");
$stmt->execute([$user_id]);
$target_tercapai = $stmt->fetch()['total_tercapai'] ?? 0;

$stmt = $pdo->prepare("SELECT SUM(target_nominal) as total_target FROM tabungan WHERE user_id = ?");
$stmt->execute([$user_id]);
$total_target = $stmt->fetch()['total_target'] ?? 0;

$stmt = $pdo->prepare("
    SELECT SUM(t.nominal) as total_terkumpul 
    FROM transaksi_tabungan t 
    JOIN tabungan tb ON t.tabungan_id = tb.id 
    WHERE tb.user_id = ?
");
$stmt->execute([$user_id]);
$total_terkumpul = $stmt->fetch()['total_terkumpul'] ?? 0;

// 2. Ambil daftar target tabungan untuk ditampilkan di beranda
$stmt = $pdo->prepare("
    SELECT tb.*, COALESCE(SUM(t.nominal), 0) as terkumpul 
    FROM tabungan tb 
    LEFT JOIN transaksi_tabungan t ON tb.id = t.tabungan_id 
    WHERE tb.user_id = ? 
    GROUP BY tb.id 
    ORDER BY tb.created_at DESC 
    LIMIT 4
");
$stmt->execute([$user_id]);
$daftar_tabungan = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - SavingGoals</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-[#f8fafd] text-slate-800 antialiased min-h-screen">

    <div class="flex flex-col md:flex-row min-h-screen">

        <?php include 'sidebar.php'; ?>

        <!-- Main Content Area -->
        <main class="flex-1 p-4 md:p-8 overflow-y-auto max-w-7xl">
            
            <!-- Banner Welcome Card -->
            <div class="bg-indigo-600 rounded-3xl p-6 md:p-8 text-white mb-6 shadow-xl shadow-indigo-100 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <span class="inline-block px-3 py-1 bg-white/10 rounded-full text-[10px] font-bold tracking-wider uppercase mb-3 backdrop-blur-sm">
                        Selamat Datang Kembali 👋
                    </span>
                    <h1 class="text-2xl md:text-3xl font-black tracking-tight mb-1">
                        Halo, <?= htmlspecialchars($_SESSION['nama'] ?? 'User') ?>!
                    </h1>
                    <p class="text-indigo-100 text-xs md:text-sm font-medium">
                        Pantau kemajuan tabungan dan capai impianmu tepat waktu.
                    </p>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="tambah_tabungan.php" class="flex-1 md:flex-none px-4 py-3 bg-white text-indigo-600 font-bold rounded-2xl text-xs hover:bg-indigo-50 transition shadow-sm text-center flex items-center justify-center space-x-2">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Target Baru</span>
                    </a>
                    <a href="transaksi.php" class="flex-1 md:flex-none px-4 py-3 bg-emerald-500 text-white font-bold rounded-2xl text-xs hover:bg-emerald-600 transition shadow-sm text-center flex items-center justify-center space-x-2">
                        <i data-lucide="wallet" class="w-4 h-4"></i>
                        <span>+ Menabung</span>
                    </a>
                </div>
            </div>

            <!-- Ringkasan Statistik (2 Kolom di Mobile, 4 Kolom di Desktop) -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-8">
                
                <!-- Total Terkumpul -->
                <div class="bg-white border border-slate-100 rounded-2xl md:rounded-3xl p-3.5 md:p-5 shadow-sm flex items-center space-x-3">
                    <div class="p-2.5 md:p-3 bg-indigo-50 text-indigo-600 rounded-xl md:rounded-2xl flex-shrink-0">
                        <i data-lucide="wallet" class="w-5 h-5 md:w-6 md:h-6"></i>
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-[10px] md:text-xs font-semibold text-slate-400 truncate">Total Terkumpul</p>
                        <h3 class="text-xs md:text-base font-extrabold text-slate-900 truncate">Rp <?= number_format($total_terkumpul, 0, ',', '.') ?></h3>
                    </div>
                </div>

                <!-- Total Target -->
                <div class="bg-white border border-slate-100 rounded-2xl md:rounded-3xl p-3.5 md:p-5 shadow-sm flex items-center space-x-3">
                    <div class="p-2.5 md:p-3 bg-blue-50 text-blue-600 rounded-xl md:rounded-2xl flex-shrink-0">
                        <i data-lucide="target" class="w-5 h-5 md:w-6 md:h-6"></i>
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-[10px] md:text-xs font-semibold text-slate-400 truncate">Total Target</p>
                        <h3 class="text-xs md:text-base font-extrabold text-slate-900 truncate">Rp <?= number_format($total_target, 0, ',', '.') ?></h3>
                    </div>
                </div>

                <!-- Target Aktif -->
                <div class="bg-white border border-slate-100 rounded-2xl md:rounded-3xl p-3.5 md:p-5 shadow-sm flex items-center space-x-3">
                    <div class="p-2.5 md:p-3 bg-amber-50 text-amber-600 rounded-xl md:rounded-2xl flex-shrink-0">
                        <i data-lucide="clock" class="w-5 h-5 md:w-6 md:h-6"></i>
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-[10px] md:text-xs font-semibold text-slate-400 truncate">Target Aktif</p>
                        <h3 class="text-xs md:text-base font-extrabold text-slate-900 truncate"><?= $target_aktif ?> Rencana</h3>
                    </div>
                </div>

                <!-- Tercapai -->
                <div class="bg-white border border-slate-100 rounded-2xl md:rounded-3xl p-3.5 md:p-5 shadow-sm flex items-center space-x-3">
                    <div class="p-2.5 md:p-3 bg-emerald-50 text-emerald-600 rounded-xl md:rounded-2xl flex-shrink-0">
                        <i data-lucide="check-circle-2" class="w-5 h-5 md:w-6 md:h-6"></i>
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-[10px] md:text-xs font-semibold text-slate-400 truncate">Tercapai</p>
                        <h3 class="text-xs md:text-base font-extrabold text-slate-900 truncate"><?= $target_tercapai ?> Impian</h3>
                    </div>
                </div>

            </div>

            <!-- Header Daftar Target Tabungan -->
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-black text-slate-900 tracking-tight">Target Impian Saya</h2>
                    <p class="text-xs text-slate-400 font-medium">Perkembangan tabungan yang sedang berjalan.</p>
                </div>
                <a href="target.php" class="text-xs font-bold text-indigo-600 hover:underline">Lihat Semua →</a>
            </div>

            <!-- Grid Daftar Target Tabungan -->
            <?php if (empty($daftar_tabungan)): ?>
                <div class="bg-white border border-slate-100 rounded-3xl p-8 text-center text-slate-400">
                    <i data-lucide="folder-open" class="w-10 h-10 mx-auto mb-2 text-slate-300"></i>
                    <p class="text-xs font-semibold">Belum ada target tabungan yang dibuat.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">
                    <?php foreach ($daftar_tabungan as $item): 
                        $persen = $item['target_nominal'] > 0 ? min(100, round(($item['terkumpul'] / $item['target_nominal']) * 100)) : 0;
                    ?>
                        <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm flex flex-col justify-between">
                            <div class="flex items-start space-x-3 mb-4">
                                <?php if (!empty($item['foto']) && file_exists('uploads/' . $item['foto'])): ?>
                                    <img src="uploads/<?= htmlspecialchars($item['foto']) ?>" class="w-12 h-12 rounded-2xl object-cover border border-slate-100">
                                <?php else: ?>
                                    <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center font-bold">
                                        <i data-lucide="piggy-bank" class="w-6 h-6"></i>
                                    </div>
                                <?php endif; ?>

                                <div class="flex-1 overflow-hidden">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-bold text-slate-800 truncate"><?= htmlspecialchars($item['judul']) ?></h3>
                                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full <?= $item['status'] == 'Tercapai' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' ?>">
                                            <?= $item['status'] ?>
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-0.5">Target: Rp <?= number_format($item['target_nominal'], 0, ',', '.') ?></p>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between items-center text-xs font-bold mb-1.5">
                                    <span class="text-slate-500">Rp <?= number_format($item['terkumpul'], 0, ',', '.') ?></span>
                                    <span class="text-indigo-600"><?= $persen ?>%</span>
                                </div>
                                <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                                    <div class="bg-indigo-600 h-full rounded-full transition-all duration-500" style="width: <?= $persen ?>%"></div>
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