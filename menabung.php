<?php
// menabung.php
require 'config.php';
checkAuth();

$user_id = $_SESSION['user_id'];
$error = '';

$stmt = $pdo->prepare("SELECT id, judul FROM tabungan WHERE user_id = ? AND judul != '0' AND judul != '' AND id > 0");
$stmt->execute([$user_id]);
$daftar_tabungan = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tabungan_id = $_POST['tabungan_id'] ?? '';
    $nominal = $_POST['nominal'] ?? 0;
    $keterangan = trim($_POST['keterangan'] ?? '');
    $tanggal = date('Y-m-d');

    if (!empty($tabungan_id) && $nominal > 0) {
        try {
            $stmtInsert = $pdo->prepare("INSERT INTO transaksi_tabungan (tabungan_id, nominal, keterangan, tanggal) VALUES (?, ?, ?, ?)");
            $stmtInsert->execute([$tabungan_id, $nominal, $keterangan, $tanggal]);

            $stmtCek = $pdo->prepare("SELECT t.target_nominal, COALESCE(SUM(m.nominal), 0) AS total 
                                      FROM tabungan t 
                                      LEFT JOIN transaksi_tabungan m ON t.id = m.tabungan_id 
                                      WHERE t.id = ? 
                                      GROUP BY t.id");
            $stmtCek->execute([$tabungan_id]);
            $dataTabungan = $stmtCek->fetch();

            if ($dataTabungan && $dataTabungan['total'] >= $dataTabungan['target_nominal']) {
                $stmtUpdate = $pdo->prepare("UPDATE tabungan SET status = 'Tercapai' WHERE id = ?");
                $stmtUpdate->execute([$tabungan_id]);
            }

            header("Location: transaksi.php");
            exit;
        } catch (PDOException $e) {
            $error = 'Gagal menyimpan transaksi: ' . $e->getMessage();
        }
    } else {
        $error = 'Pilih tabungan dan masukkan nominal yang valid!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Setoran - SavingGoals</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-[#f8fafd] text-slate-800 antialiased min-h-screen flex flex-col md:flex-row">

    <?php include 'sidebar.php'; ?>

    <main class="flex-1 p-4 md:p-8 w-full max-w-xl mx-auto overflow-x-hidden">
        <div class="bg-white border border-slate-100 rounded-3xl p-5 md:p-8 shadow-sm">
            <div class="flex items-center space-x-3 mb-6">
                <a href="index.php" class="p-2 bg-slate-50 hover:bg-slate-100 rounded-xl text-slate-500 transition">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h1 class="text-xl font-bold text-slate-900">Form Menabung</h1>
                    <p class="text-xs text-slate-400">Catat setoran tabunganmu di sini.</p>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="bg-rose-50 border border-rose-100 text-rose-500 text-xs font-semibold p-3.5 rounded-2xl mb-5">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Target Tabungan</label>
                    <select name="tabungan_id" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-medium focus:outline-none focus:border-indigo-600 transition">
                        <option value="">-- Pilih Tabungan --</option>
                        <?php foreach ($daftar_tabungan as $item): ?>
                            <option value="<?= $item['id'] ?>"><?= htmlspecialchars($item['judul']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Nominal Setoran (Rp)</label>
                    <input type="number" name="nominal" required placeholder="50000" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-medium focus:outline-none focus:border-indigo-600 transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Keterangan (Opsional)</label>
                    <input type="text" name="keterangan" placeholder="Contoh: Sisihan uang jajan" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-medium focus:outline-none focus:border-indigo-600 transition">
                </div>

                <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3.5 rounded-2xl text-xs shadow-lg shadow-emerald-100 transition mt-2">
                    Simpan Setoran
                </button>
            </form>
        </div>
    </main>

    <script> lucide.createIcons(); </script>
</body>
</html>