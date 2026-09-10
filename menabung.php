<?php
session_start();
require_once 'config.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$id_tabungan = $_GET['id'] ?? null;
$message = '';
$error = '';

if (!$id_tabungan) {
    header("Location: index.php");
    exit();
}

// Fetch data tabungan milik user yang login (Isolasi Multi-user)
$stmt = $pdo->prepare("SELECT * FROM tabungan WHERE id = ? AND user_id = ?");
$stmt->execute([$id_tabungan, $user_id]);
$tabungan = $stmt->fetch();

if (!$tabungan) {
    header("Location: index.php");
    exit();
}

// Proses Form Menabung (Tambah Nominal)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nominal = $_POST['nominal'] ?? 0;

    if ($nominal <= 0) {
        $error = "Nominal tabungan harus lebih besar dari 0!";
    } else {
        try {
            $pdo->beginTransaction();

            // 1. Simpan riwayat transaksi
            $stmt_trans = $pdo->prepare("INSERT INTO transaksi_tabungan (tabungan_id, nominal, tanggal) VALUES (?, ?, NOW())");
            $stmt_trans->execute([$id_tabungan, $nominal]);

            // 2. Update saldo terkumpul pada tabel tabungan
            $stmt_update = $pdo->prepare("UPDATE tabungan SET terkumpul = terkumpul + ? WHERE id = ? AND user_id = ?");
            $stmt_update->execute([$nominal, $id_tabungan, $user_id]);

            $pdo->commit();

            // Refresh data tabungan setelah update
            $stmt_refresh = $pdo->prepare("SELECT * FROM tabungan WHERE id = ? AND user_id = ?");
            $stmt_refresh->execute([$id_tabungan, $user_id]);
            $tabungan = $stmt_refresh->fetch();

            $message = "Berhasil menambah tabungan sebesar Rp " . number_format($nominal, 0, ',', '.');
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Gagal memproses transaksi: " . $e->getMessage();
        }
    }
}

// Hitung persentase progress
$persen = ($tabungan['target'] > 0) ? min(100, round(($tabungan['terkumpul'] / $tabungan['target']) * 100)) : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menabung - <?= htmlspecialchars($tabungan['nama_target']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-md mx-auto p-4">
        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
            <a href="index.php" class="text-blue-600 hover:underline">&larr; Kembali</a>
            <h1 class="text-xl font-bold text-gray-800">Menabung</h1>
        </div>

        <!-- Alert Notifikasi -->
        <?php if ($message): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm font-semibold">
                <?= $message ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm font-semibold">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <!-- Info Target Tabungan -->
        <div class="bg-white p-5 rounded-lg shadow-sm border mb-4">
            <h2 class="font-bold text-lg text-gray-800 mb-1"><?= htmlspecialchars($tabungan['nama_target']) ?></h2>
            <p class="text-sm text-gray-500 mb-3">Target: Rp <?= number_format($tabungan['target'], 0, ',', '.') ?></p>
            
            <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                <div class="bg-green-500 h-3 rounded-full" style="width: <?= $persen ?>%"></div>
            </div>
            <p class="text-xs text-right text-gray-600 font-semibold"><?= $persen ?>% Terkumpul (Rp <?= number_format($tabungan['terkumpul'], 0, ',', '.') ?>)</p>
        </div>

        <!-- Form Tambah Tabungan -->
        <form action="menabung.php?id=<?= $id_tabungan ?>" method="POST" class="bg-white p-5 rounded-lg shadow-sm border space-y-4">
            <div>
                <label for="nominal" class="block text-sm font-medium text-gray-700 mb-1">Nominal Menabung (Rp)</label>
                <input type="number" id="nominal" name="nominal" required min="1" placeholder="Masukkan nominal..." class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded font-semibold transition">
                Simpan Setoran
            </button>
        </form>
    </div>
</body>
</html>