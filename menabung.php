<?php
// menabung.php
require 'config.php';
checkAuth();

$user_id = $_SESSION['user_id'];
$id_tabungan = $_GET['id'] ?? $_POST['tabungan_id'] ?? null;
$error = '';
$success = '';

// Ambil semua daftar target tabungan user yang Belum Tercapai untuk pilihan dropdown
$stmt = $pdo->prepare("SELECT * FROM tabungan WHERE user_id = ? AND status != 'Tercapai' ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$daftar_target = $stmt->fetchAll();

// Proses simpan setoran tabungan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nominal = filter_var($_POST['nominal'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
    $target_id = $_POST['tabungan_id'] ?? $id_tabungan;

    if (!$target_id) {
        $error = "Pilih target tabungan terlebih dahulu!";
    } elseif ($nominal <= 0) {
        $error = "Nominal tabungan harus lebih besar dari 0!";
    } else {
        try {
            $pdo->beginTransaction();

            // 1. Catat transaksi baru
            $stmt_trans = $pdo->prepare("INSERT INTO transaksi_tabungan (tabungan_id, nominal, created_at) VALUES (?, ?, NOW())");
            $stmt_trans->execute([$target_id, $nominal]);

            // 2. Hitung total akumulasi tabungan
            $stmt_sum = $pdo->prepare("SELECT SUM(nominal) as total FROM transaksi_tabungan WHERE tabungan_id = ?");
            $stmt_sum->execute([$target_id]);
            $total_terkumpul = $stmt_sum->fetch()['total'] ?? 0;

            // 3. Cek target nominal
            $stmt_target = $pdo->prepare("SELECT target_nominal FROM tabungan WHERE id = ? AND user_id = ?");
            $stmt_target->execute([$target_id, $user_id]);
            $target_data = $stmt_target->fetch();

            $status = ($target_data && $total_terkumpul >= $target_data['target_nominal']) ? 'Tercapai' : 'Belum Tercapai';

            // 4. Update status jika target sudah tercapai
            $stmt_update = $pdo->prepare("UPDATE tabungan SET status = ? WHERE id = ? AND user_id = ?");
            $stmt_update->execute([$status, $target_id, $user_id]);

            $pdo->commit();
            $success = "Berhasil menambah tabungan sebesar Rp " . number_format($nominal, 0, ',', '.');
            $id_tabungan = $target_id;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Gagal menyimpan tabungan: " . $e->getMessage();
        }
    }
}

// Ambil detail tabungan jika ID dikirimkan via URL
$tabungan_detail = null;
if ($id_tabungan) {
    $stmt = $pdo->prepare("SELECT tb.*, COALESCE(SUM(t.nominal), 0) as terkumpul FROM tabungan tb LEFT JOIN transaksi_tabungan t ON tb.id = t.tabungan_id WHERE tb.id = ? AND tb.user_id = ? GROUP BY tb.id");
    $stmt->execute([$id_tabungan, $user_id]);
    $tabungan_detail = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setor Tabungan - SavingGoals</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-[#f8fafd] text-slate-800 antialiased min-h-screen">

    <div class="flex flex-col md:flex-row min-h-screen">

        <?php include 'sidebar.php'; ?>

        <!-- Main Content Area -->
        <main class="flex-1 p-4 md:p-8 overflow-y-auto max-w-2xl mx-auto">
            
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Form Menabung</h1>
                    <p class="text-xs text-slate-400 font-medium">Tambah nominal tabungan kamu hari ini.</p>
                </div>
                <a href="index.php" class="text-xs font-bold text-indigo-600 hover:underline flex items-center gap-1">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
                </a>
            </div>

            <?php if ($success): ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl mb-4 text-xs font-semibold flex items-center gap-2">
                    <i data-lucide="check-circle-2" class="w-4 h-4 flex-shrink-0"></i>
                    <span><?= $success ?></span>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-2xl mb-4 text-xs font-semibold flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0"></i>
                    <span><?= $error ?></span>
                </div>
            <?php endif; ?>

            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                <form action="menabung.php<?= $id_tabungan ? '?id='.$id_tabungan : '' ?>" method="POST" class="space-y-4">
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Pilih Target Tabungan</label>
                        <?php if ($tabungan_detail): ?>
                            <!-- Jika ID Spesifik Ada -->
                            <input type="hidden" name="tabungan_id" value="<?= $tabungan_detail['id'] ?>">
                            <div class="p-3.5 bg-indigo-50/50 border border-indigo-100 rounded-2xl text-xs flex items-center justify-between">
                                <div>
                                    <span class="font-bold text-slate-800 block"><?= htmlspecialchars($tabungan_detail['judul'] ?? $tabungan_detail['nama_target'] ?? 'Target Tabungan') ?></span>
                                    <span class="text-slate-400 text-[11px]">Terkumpul: Rp <?= number_format($tabungan_detail['terkumpul'], 0, ',', '.') ?> / Rp <?= number_format($tabungan_detail['target_nominal'], 0, ',', '.') ?></span>
                                </div>
                                <a href="menabung.php" class="text-[10px] font-bold text-indigo-600 hover:underline">Ganti Target</a>
                            </div>
                        <?php else: ?>
                            <!-- Jika Masuk Tanpa ID (Buka via tombol Banner) -->
                            <select name="tabungan_id" required class="w-full border border-slate-200 rounded-2xl p-3.5 text-xs focus:ring-2 focus:ring-indigo-500 outline-none">
                                <option value="">-- Pilih Target Tabungan Kamu --</option>
                                <?php foreach ($daftar_target as $t): ?>
                                    <option value="<?= $t['id'] ?>" <?= ($id_tabungan == $t['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($t['judul'] ?? $t['nama_target']) ?> (Target: Rp <?= number_format($t['target_nominal'], 0, ',', '.') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (empty($daftar_target)): ?>
                                <p class="text-[11px] text-rose-500 mt-1 font-medium">* Belum ada target aktif. Silakan buat target dulu.</p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="nominal" class="block text-xs font-bold text-slate-700 mb-2">Nominal Setoran (Rp)</label>
                        <input type="number" id="nominal" name="nominal" required min="1" placeholder="Contoh: 50000" class="w-full border border-slate-200 rounded-2xl p-3.5 text-xs focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>

                    <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3.5 rounded-2xl text-xs transition shadow-sm flex items-center justify-center gap-2">
                        <i data-lucide="wallet" class="w-4 h-4"></i>
                        <span>Simpan Tabungan Sekarang</span>
                    </button>
                </form>
            </div>

        </main>
    </div>

    <script> lucide.createIcons(); </script>
</body>
</html>