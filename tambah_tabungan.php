<?php
// tambah_tabungan.php
require 'config.php';
checkAuth();

$user_id = $_SESSION['user_id'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $target_nominal = $_POST['target_nominal'] ?? 0;
    $target_tanggal = $_POST['target_tanggal'] ?? null;
    $foto_name = null;

    if (!empty($judul) && $target_nominal > 0) {
        
        // 1. Cek & Buat Folder uploads jika belum ada
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // 2. Validasi File Foto jika diunggah
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $file_tmp  = $_FILES['foto']['tmp_name'];
            $file_name = $_FILES['foto']['name'];
            $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            // Ekstensi yang diizinkan
            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($ext, $allowed_ext)) {
                $error = 'Format foto tidak valid! Gunakan format JPG, JPEG, PNG, atau WEBP.';
            } else {
                // Generate nama file unik
                $foto_name = time() . '_' . uniqid() . '.' . $ext;
                
                if (!move_uploaded_file($file_tmp, $upload_dir . $foto_name)) {
                    $error = 'Gagal mengunggah foto ke direktori server.';
                }
            }
        }

        // Jika tidak ada error pada proses upload, simpan ke database
        if (empty($error)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO tabungan (user_id, judul, target_nominal, target_tanggal, foto, status) VALUES (?, ?, ?, ?, ?, 'Belum Tercapai')");
                $stmt->execute([$user_id, $judul, $target_nominal, $target_tanggal ?: null, $foto_name]);

                header("Location: target.php");
                exit;
            } catch (PDOException $e) {
                $error = 'Gagal menyimpan target tabungan: ' . $e->getMessage();
            }
        }

    } else {
        $error = 'Judul dan target nominal wajib diisi!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Tabungan - SavingGoals</title>
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
                    <h1 class="text-xl md:text-2xl font-black text-slate-900 tracking-tight">Tambah Target Tabungan</h1>
                    <p class="text-slate-400 text-xs md:text-sm font-medium mt-0.5">Buat impian tabungan barumu di sini.</p>
                </div>
            </div>

            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                <?php if ($error): ?>
                    <div class="bg-rose-50 border border-rose-100 text-rose-500 text-xs font-semibold p-3.5 rounded-2xl mb-5">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Impian / Target</label>
                        <input type="text" name="judul" required placeholder="Contoh: Beli Laptop Baru" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-medium focus:outline-none focus:border-indigo-600 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Target Nominal (Rp)</label>
                        <input type="number" name="target_nominal" required placeholder="10000000" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-medium focus:outline-none focus:border-indigo-600 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Target Tanggal Selesai (Opsional)</label>
                        <input type="date" name="target_tanggal" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-medium focus:outline-none focus:border-indigo-600 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Foto Sampul (Opsional: JPG, PNG, WEBP)</label>
                        <input type="file" name="foto" accept="image/png, image/jpeg, image/webp" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-medium text-slate-500 focus:outline-none">
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 rounded-2xl text-xs shadow-lg shadow-indigo-100 transition mt-2">
                        Simpan Target Tabungan
                    </button>
                </form>
            </div>
        </main>
    </div>

    <script> lucide.createIcons(); </script>
</body>
</html>