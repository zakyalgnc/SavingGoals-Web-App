<?php
// tabungan_edit.php
require 'config.php';
checkAuth();

$user_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: index.php");
    exit;
}

// Ambil data tabungan sesuai ID dan user
$stmt = $pdo->prepare("SELECT * FROM tabungan WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $user_id]);
$tabungan = $stmt->fetch();

if (!$tabungan) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $target_nominal = $_POST['target_nominal'] ?? 0;

    if (empty($judul) || $target_nominal <= 0) {
        $error = 'Judul dan target nominal harus diisi dengan benar!';
    } else {
        $stmt = $pdo->prepare("UPDATE tabungan SET judul = ?, target_nominal = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$judul, $target_nominal, $id, $user_id]);

        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Target Tabungan - SavingGoals</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-[#f8fafd] text-slate-800 p-6 flex justify-center items-center min-h-screen">
    <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Edit Target Tabungan</h2>

        <?php if ($error): ?>
            <div class="bg-rose-50 text-rose-600 p-3 rounded-xl text-xs mb-4"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label class="block text-xs font-bold mb-1">Judul Target</label>
                <input type="text" name="judul" value="<?= htmlspecialchars($tabungan['judul'] ?? $tabungan['nama_target'] ?? '') ?>" class="w-full p-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500" required>
            </div>
            <div class="mb-6">
                <label class="block text-xs font-bold mb-1">Target Nominal (Rp)</label>
                <input type="number" name="target_nominal" value="<?= $tabungan['target_nominal'] ?>" class="w-full p-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500" required>
            </div>
            <div class="flex gap-2">
                <a href="index.php" class="w-1/2 text-center py-3 bg-slate-100 font-bold rounded-xl text-xs text-slate-600 hover:bg-slate-200 transition">Batal</a>
                <button type="submit" class="w-1/2 py-3 bg-indigo-600 text-white font-bold rounded-xl text-xs hover:bg-indigo-700 transition">Simpan</button>
            </div>
        </form>
    </div>
</body>
</html>