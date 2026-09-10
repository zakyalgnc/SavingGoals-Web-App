<?php
// sidebar.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Header Topbar Khusus Mobile -->
<header class="md:hidden bg-white border-b border-slate-100 p-4 flex justify-between items-center sticky top-0 z-50">
    <a href="index.php" class="flex items-center space-x-2.5">
        <!-- GAMBAR LOGO MOBILE -->
        <img src="logo.png" alt="SavingGoals Logo" class="w-9 h-9 object-cover rounded-xl shadow-sm">
        <span class="text-lg font-black text-slate-900 tracking-tight">SavingGoals</span>
    </a>
    
    <!-- Tombol Hamburger Mobile -->
    <button id="hamburgerBtn" class="p-2 bg-slate-50 hover:bg-slate-100 rounded-xl text-slate-700 transition focus:outline-none">
        <i data-lucide="menu" class="w-6 h-6"></i>
    </button>
</header>

<!-- Overlay Gelap Pas Menu Mobile Terbuka -->
<div id="mobileOverlay" class="fixed inset-0 bg-slate-900/40 z-40 hidden md:hidden transition-opacity"></div>

<!-- Sidebar Utama (Desktop Fixed di Kiri, Mobile Slide Out) -->
<aside id="sidebarMenu" class="fixed md:static top-0 left-0 h-full w-64 bg-white border-r border-slate-100 p-6 flex flex-col justify-between z-50 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out md:flex-shrink-0">
    <div>
        <!-- Logo Header (Desktop) -->
        <div class="flex items-center justify-between mb-8">
            <a href="index.php" class="flex items-center space-x-3">
                <!-- GAMBAR LOGO DESKTOP -->
                <img src="logo.png" alt="SavingGoals Logo" class="w-10 h-10 object-cover rounded-2xl shadow-sm">
                <span class="text-xl font-black text-slate-900 tracking-tight">SavingGoals</span>
            </a>
            <!-- Tombol Close (Mobile) -->
            <button id="closeSidebarBtn" class="md:hidden p-1 text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <!-- Menu Navigasi Vertikal -->
        <nav class="space-y-1.5">
            <a href="index.php" class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-xs font-semibold transition <?= $currentPage == 'index.php' ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' ?>">
                <i data-lucide="home" class="w-4 h-4"></i>
                <span>Beranda</span>
            </a>
            <a href="target.php" class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-xs font-semibold transition <?= $currentPage == 'target.php' ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' ?>">
                <i data-lucide="target" class="w-4 h-4"></i>
                <span>Target Tabungan</span>
            </a>
            <a href="transaksi.php" class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-xs font-semibold transition <?= $currentPage == 'transaksi.php' ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' ?>">
                <i data-lucide="receipt" class="w-4 h-4"></i>
                <span>Transaksi</span>
            </a>
            <a href="profil.php" class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-xs font-semibold transition <?= $currentPage == 'profil.php' ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' ?>">
                <i data-lucide="user" class="w-4 h-4"></i>
                <span>Profil</span>
            </a>
            <a href="notifikasi.php" class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-xs font-semibold transition <?= $currentPage == 'notifikasi.php' ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' ?>">
                <i data-lucide="bell" class="w-4 h-4"></i>
                <span>Notifikasi</span>
            </a>
        </nav>
    </div>

    <!-- User Profile Footer -->
    <div class="pt-6 border-t border-slate-100 space-y-3">
        <div class="flex items-center space-x-3 p-2.5 bg-slate-50 rounded-2xl border border-slate-100">
            <div class="w-9 h-9 bg-indigo-600 text-white font-bold rounded-xl flex items-center justify-center text-sm shadow-sm">
                <?= strtoupper(substr($_SESSION['nama'] ?? 'U', 0, 1)) ?>
            </div>
            <div class="overflow-hidden">
                <p class="text-xs font-bold text-slate-800 truncate"><?= htmlspecialchars($_SESSION['nama'] ?? 'User') ?></p>
                <p class="text-[10px] text-slate-400 truncate"><?= htmlspecialchars($_SESSION['email'] ?? 'user@gmail.com') ?></p>
            </div>
        </div>

        <a href="logout.php" class="flex items-center space-x-3 px-4 py-2.5 rounded-2xl text-xs font-bold text-rose-500 hover:bg-rose-50 transition w-full">
            <i data-lucide="log-out" class="w-4 h-4"></i>
            <span>Keluar</span>
        </a>
    </div>
</aside>

<!-- Script Toggle Menu Mobile & Render Icon Lucide -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const closeSidebarBtn = document.getElementById('closeSidebarBtn');
        const sidebarMenu = document.getElementById('sidebarMenu');
        const mobileOverlay = document.getElementById('mobileOverlay');

        function toggleSidebar() {
            sidebarMenu.classList.toggle('-translate-x-full');
            mobileOverlay.classList.toggle('hidden');
        }

        if (hamburgerBtn) hamburgerBtn.addEventListener('click', toggleSidebar);
        if (closeSidebarBtn) closeSidebarBtn.addEventListener('click', toggleSidebar);
        if (mobileOverlay) mobileOverlay.addEventListener('click', toggleSidebar);

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>