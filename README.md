# SavingGoals - Responsive Web Application

Aplikasi manajemen target tabungan berbasis web yang responsif, dikembangkan untuk membantu pengguna mencatat dan memantau kemajuan tabungan secara terstruktur.

---

## 📹 Video Demonstrasi & Penjelasan
Saksikan video rekam layar penjelasan struktur database, demo fitur, pengujian responsif mobile, serta isolasi multi-user pada link berikut:

👉 [**Tonton Video Penjelasan Aplikasi di Sini**](https://drive.google.com/drive/folders/1U4Id0XDIICwhBUwjNjFg39_BcolFq2GW)

---

## 🛠️ Tech Stack
- **Backend:** PHP Native (PDO MySQL, Session Management)
- **Database:** MySQL / MariaDB
- **Frontend Framework:** Tailwind CSS (via CDN)
- **Icons:** Lucide Icons

---

## 🗄️ Skema Database
File skema database telah disertakan di dalam repository ini dengan nama **`db_tabungan.sql`**.

### Tabel Utama:
1. `users` — Menyimpan kredensial pengguna (nama, email, hashed password).
2. `tabungan` — Menyimpan data target tabungan, nominal target, dan gambar pendukung.
3. `transaksi_tabungan` — Menyimpan riwayat setoran uang ke tiap target tabungan.

---

## 🚀 Cara Menjalankan Project Secara Lokal
1. Import file `db_tabungan.sql` ke dalam **phpMyAdmin**.
2. Pastikan konfigurasi koneksi pada file `config.php` sudah sesuai dengan environment lokal Anda:
   ```php
   $host = 'localhost';$db   = 'db_tabungan';
   $user = 'root';$pass = '';
