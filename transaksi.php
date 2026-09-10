<?php
// transaksi.php
require 'config.php';
checkAuth();

$user_id = $_SESSION['user_id'];

$sql = "SELECT m.*, t.judul 
        FROM transaksi_tabungan m 
        JOIN tabungan t ON m.tabungan_id = t.id 
        WHERE t.user_id = ? 
        ORDER BY m.tanggal DESC, m.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$transaksi = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - SavingGoals</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-[#f8fafd] text-slate-800 antialiased min-h-screen">

    <div class="flex flex-col md:flex-row min-h-screen">
        
        <?php include 'sidebar.php'; ?>

        <main class="flex-1 p-4 md:p-8 overflow-y-auto max-w-7xl">
            <!-- Header dengan Tombol Kembali ke Beranda -->
            <div class="flex items-center space-x-3 mb-6">
                <a href="index.php" class="p-2.5 bg-white border border-slate-100 hover:bg-slate-50 rounded-2xl text-slate-600 transition shadow-sm flex items-center justify-center">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h1 class="text-xl md:text-2xl font-black text-slate-900 tracking-tight">Riwayat Transaksi</h1>
                    <p class="text-slate-400 text-xs md:text-sm font-medium mt-0.5">Catatan seluruh setoran tabungan yang pernah kamu lakukan.</p>
                </div>
            </div>

            <div class="bg-white border border-slate-100 rounded-3xl p-4 md:p-6 shadow-sm overflow-x-auto">
                <?php if (empty($transaksi)): ?>
                    <p class="text-slate-400 text-xs font-semibold text-center py-6">Belum ada riwayat transaksi.</p>
                <?php else: ?>
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-slate-400 border-b border-slate-100">
                                <th class="pb-3 font-semibold">Tanggal</th>
                                <th class="pb-3 font-semibold">Target Tabungan</th>
                                <th class="pb-3 font-semibold">Keterangan</th>
                                <th class="pb-3 font-semibold text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php foreach ($transaksi as $row): ?>
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 text-slate-500 font-medium whitespace-nowrap"><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                                <td class="py-3.5 font-bold text-slate-800 whitespace-nowrap"><?= htmlspecialchars($row['judul']) ?></td>
                                <td class="py-3.5 text-slate-500"><?= htmlspecialchars($row['keterangan'] ?: '-') ?></td>
                                <td class="py-3.5 font-extrabold text-emerald-600 text-right whitespace-nowrap">+ Rp <?= number_format($row['nominal'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script> lucide.createIcons(); </script>
</body>
</html>