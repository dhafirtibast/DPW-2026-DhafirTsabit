# Jobsheet 10 — Autentikasi & Manajemen Sesi

Sub-CPMK: Menerapkan autentikasi & manajemen sesi pengguna pada aplikasi.

## Struktur Folder

```
jobsheet-10/
├── anggota/
│   ├── edit.php
│   ├── hapus.php
│   ├── list.php
│   ├── proses_edit.php
│   ├── proses_tambah.php
│   └── tambah.php
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── app.js
├── auth/
│   ├── login.php
│   ├── logout.php
│   ├── proses_login.php
│   ├── proses_register.php
│   └── register.php
├── buku/
│   ├── edit.php
│   ├── hapus.php
│   ├── list.php
│   ├── proses_edit.php
│   ├── proses_tambah.php
│   └── tambah.php
├── docs/
│   └── wireframe.md
├── includes/
│   ├── auth.php
│   ├── footer.php
│   ├── header.php
│   └── koneksi.php
├── sql/
│   ├── 01_buku_anggota.sql
│   └── 02_users.sql
├── index.php
└── README.md
```

## Perubahan dari Jobsheet 9

- **Tabel `users`** (`sql/02_users.sql`) — menampung petugas: `id`, `nama`, `username` (UNIQUE), `password`, `role` (DEFAULT `'petugas'`).
- **Registrasi** — `auth/register.php` (form) + `auth/proses_register.php`. Password disimpan sebagai **hash** lewat `password_hash($password, PASSWORD_DEFAULT)`; ada pengecekan **username duplikat** agar pesan error ramah dan tidak bergantung pada error `UNIQUE` mentah.
- **Login** — `auth/login.php` (form) + `auth/proses_login.php`. Memverifikasi lewat `password_verify()`; bila cocok, menyimpan identitas ke session (`$_SESSION['user_id']`, `['nama']`, `['role']`). Pesan gagal sengaja **umum** ("Username atau password salah.") agar tidak membocorkan username mana yang valid.
- **Logout** — `auth/logout.php` memanggil `session_destroy()` lalu mengalihkan ke `login.php`.
- **Guard halaman** — `includes/auth.php`, *guard clause* yang mengalihkan pengunjung belum login ke `../auth/login.php` bila `$_SESSION['user_id']` belum ada. **Wajib di-`require` sebagai baris paling pertama** (sebelum `includes/header.php`) agar `header('Location: ...')` masih bisa dipanggil sebelum ada output HTML.
  - Halaman **terkunci**: `buku/tambah|edit|hapus|proses_tambah|proses_edit.php` dan **seluruh** `anggota/*.php`.
  - Halaman **tetap publik**: `index.php` (Beranda) dan `buku/list.php` (katalog buku) — sesuai pembagian aktor Tamu/Petugas yang dirancang sejak Jobsheet 4.
- **`includes/header.php`** — `session_start()` dibungkus `if (session_status() === PHP_SESSION_NONE)` agar tidak bentrok dengan `auth.php`; navbar kini **dinamis** memakai `$sudahLogin`: menu Tambah Buku/Daftar Anggota/Tambah Anggota hanya tampil saat login, plus blok `.auth-status` (nama petugas + Logout, atau tautan Login).
- **`assets/css/style.css`** — tambah style `.auth-status` (Flexbox) agar status akun sejajar rapi di header.

## Persiapan Database

1. Pastikan PostgreSQL berjalan dan ekstensi PHP `pdo_pgsql` aktif (`php -m | findstr pgsql`; bila belum, aktifkan `extension=pdo_pgsql` dan `extension=pgsql` di `php.ini` lalu restart server).
2. Buat database dan jalankan skema:
   ```bash
   createdb -p 5433 simpus_mini
   psql -p 5433 -d simpus_mini -f sql/01_buku_anggota.sql
   psql -p 5433 -d simpus_mini -f sql/02_users.sql
   ```
3. Sesuaikan kredensial di `includes/koneksi.php` (`$user`, `$pass`, `$port`) dengan environment lokal.

> **Catatan port:** environment lokal ini memakai `5433` (bukan `5432`) karena port default sudah terpakai instalasi lain. Karena itu semua perintah `createdb`/`psql` disertai `-p 5433`. Bila instalasi yang dipakai menggunakan port `5432`, hilangkan `-p 5433` dan sesuaikan `$port` di `koneksi.php`.

## Cara Menjalankan

**Opsi 1 — PHP built-in server**, dari dalam folder `jobsheet-10/`:
```bash
php -S localhost:8000
```
Buka `http://localhost:8000/index.php`.

**Opsi 2 — Laragon (Apache)**: lewat virtual host langsung ke folder `jobsheet-10/` (mis. `http://jobsheet10.test/`), atau bersarang di bawah domain proyek (mis. `http://dp2026.test/kode-praktikum/jobsheet-10/`) — path CSS/JS/link/redirect sudah relatif otomatis (lihat `includes/header.php` & `includes/auth.php`), jadi keduanya jalan.

**Uji cepat:** akses `buku/tambah.php` langsung tanpa login → harus dialihkan ke halaman Login. Setelah Register & Login, halaman yang sama berhasil dibuka; klik Logout → kembali ke kondisi semula.

## Catatan

- **Password tidak pernah disimpan apa adanya** — hanya hash dari `password_hash()` yang masuk database; verifikasi memakai `password_verify()`.
- **Menyembunyikan menu di navbar bukan keamanan.** Tautan yang tidak ditampilkan hanyalah kenyamanan tampilan; proteksi sesungguhnya adalah guard `includes/auth.php` yang tetap memblokir akses URL langsung.
- **Urutan `require`/`include` kritis.** `auth.php` harus dipanggil sebelum ada output HTML, karena `header('Location: ...')` gagal bila headers sudah terkirim.
- **Guard tidak bergantung database.** `auth.php` hanya memeriksa `$_SESSION`, sehingga tetap mengalihkan ke Login meski PostgreSQL sedang mati.
- **Kontrol akses berbasis `role` belum diterapkan** — kolom `role` baru disiapkan (tugas mandiri).
- **Session berbasis file** (default PHP) — identitas login disimpan di server selama sesi browser berlangsung.

## Refleksi

Jobsheet ini mewujudkan sesuatu yang sudah dirancang **jauh sebelumnya**: halaman Login dan pembagian aktor Tamu/Petugas yang sejak Jobsheet 4 masih berupa wireframe, kini benar-benar berjalan sebagai kode PHP. Beberapa hal yang dipelajari:

- **Autentikasi vs otorisasi adalah dua lapis berbeda.** Autentikasi menjawab "siapa kamu" (Login), otorisasi menjawab "boleh melakukan apa" (guard halaman). Keduanya baru bisa lengkap setelah `$_SESSION` dikuasai di Jobsheet 7.
- **Hashing satu arah melindungi password.** `password_hash()` + `password_verify()` membuat password asli tidak pernah tersimpan, sehingga kebocoran database tidak langsung membahayakan akun.
- **Session adalah jembatan antar-request.** Identitas pengguna disimpan sekali saat Login, lalu "diingat" di setiap halaman berikutnya tanpa login ulang.
- **Keamanan nyata ada di server.** Menyembunyikan menu dan memvalidasi di klien tidak cukup; guard `auth.php` dan validasi server-side tetap penentu.
- **Identitas sesi harus dipercaya dari server.** `$_SESSION` hanya boleh diisi setelah kredensial terverifikasi, sehingga pengguna tidak bisa "mengaku" sudah login dari sisi klien.

Setelah Jobsheet 10, SIMPUS-Mini tidak hanya dapat mengelola data, tetapi juga **mengontrol siapa yang boleh mengaksesnya** — fondasi yang tepat menuju modul transaksi **Peminjaman/Pengembalian**.
