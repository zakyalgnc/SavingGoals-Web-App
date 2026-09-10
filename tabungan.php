<?php
require 'config.php';
checkAuth();

$id = $_GET['edit'] ?? null;
$judul = ''; $target_nominal = ''; $target_tanggal = ''; $foto = '';

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM tabungan WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
    $data = $stmt->fetch();
    if ($data) {
        $judul = $data['judul'];
        $target_nominal = $data['target_nominal'];
        $target_tanggal = $data['target_tanggal'];
        $foto = $data['foto'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'];
    $target_nominal = $_POST['target_nominal'];
    $target_tanggal = $_POST['target_tanggal'];
    
    // Process Upload Foto
    $namaFoto = $foto ?: 'default.jpg';
    if (!empty($_FILES['foto']['name'])) {
        $namaFoto = time() . '_' . $_FILES['foto']['name'];
        move_uploaded_file($_FILES['foto']['tmp_name'], 'uploads/' . $namaFoto);
    }

    if ($id) {
        // Update
        $stmt = $pdo->prepare("UPDATE tabungan SET judul=?, target_nominal=?, target_tanggal=?, foto=? WHERE id=? AND user_id=?");
        $stmt->execute([$judul, $target_nominal, $target_tanggal, $namaFoto, $id, $_SESSION['user_id']]);
    } else {
        // Create
        $stmt = $pdo->prepare("INSERT INTO tabungan (user_id, judul, target_nominal, target_tanggal, foto) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $judul, $target_nominal, $target_tanggal, $namaFoto]);
    }

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $id ? 'Edit' : 'Tambah' ?> Tabungan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-lg">
        <h2 class="text-2xl font-bold text-slate-800 mb-6"><?= $id ? 'Edit' : 'Tambah' ?> Rencana Tabungan</h2>
        
        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Judul Tabungan</label>
                <input type="text" name="judul" value="<?= htmlspecialchars($judul) ?>" placeholder="Contoh: Liburan ke Bali, Beli HP" required class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Target Nominal (Rp)</label>
                <input type="number" name="target_nominal" value="<?= $target_nominal ?>" placeholder="5000000" required class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Target Tanggal Tercapai</label>
                <input type="date" name="target_tanggal" value="<?= $target_tanggal ?>" required class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Foto Sampul</label>
                <input type="file" name="foto" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100">
            </div>
            <div class="flex gap-3 pt-4">
                <a href="index.php" class="w-1/2 text-center py-2.5 bg-slate-200 text-slate-700 font-semibold rounded-xl hover:bg-slate-300">Batal</a>
                <button type="submit" class="w-1/2 py-2.5 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700">Simpan</button>
            </div>
        </form>
    </div>
</body>
</html>