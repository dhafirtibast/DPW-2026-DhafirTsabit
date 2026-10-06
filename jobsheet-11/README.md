# Jobsheet 11 — Keamanan Web Dasar

Sub-CPMK: Menerapkan prinsip keamanan web dasar.

## Struktur Folder

```
jobsheet-11/
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
│   ├── security-checklist.md
│   └── wireframe.md
├── includes/
│   ├── auth.php
│   ├── csrf.php
│   ├── footer.php
│   ├── header.php
│   ├── helpers.php
│   └── koneksi.php
├── sql/
│   ├── 01_buku_anggota.sql
│   └── 02_users.sql
├── index.php
└── README.md
```

## Perubahan dari Jobsheet 10

Jobsheet ini adalah **audit keamanan menyeluruh** terhadap kode Jobsheet 7-10, mencakup 5 kerentanan. Hasilnya: 2 kerentanan ternyata sudah aman (dikonfirmasi), 3 benar-benar diperbaiki.

- **File baru `includes/helpers.php`** — fungsi `e()` yang membungkus `htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8')` untuk mencegah XSS.
- **File baru `includes/csrf.php`** — `csrf_token()` (token acak per-sesi via `random_bytes()`), `csrf_field()` (input tersembunyi), dan `csrf_verify()` (verifikasi dengan `hash_equals()`, HTTP 403 bila gagal). Kedua file ini di-`require_once` dari `includes/header.php` sehingga tersedia di semua halaman.
- **XSS** — seluruh output data dari database/`$_GET` dibungkus `e()`: `judul`, `pengarang`, `nama`, `alamat`, `no_hp`, nilai pencarian (`q`), dan nama petugas di navbar (`$_SESSION['nama']`). Termasuk atribut `value="..."` pada form Edit yang rawan "memutus" atribut lewat tanda kutip.
- **CSRF** — token tersembunyi (`csrf_field()`) ditambahkan ke **semua** form `POST`: Tambah/Edit/Hapus Buku & Anggota, Login, dan Register. Setiap `proses_*.php` dan `hapus.php` memanggil `csrf_verify()` **sebelum** menyentuh database.
- **Session fixation** — `session_regenerate_id(true)` dipanggil di `auth/proses_login.php` tepat setelah `password_verify()` berhasil, sebelum mengisi `$_SESSION`.
- **Validasi & sanitasi input** — diaudit ulang (sudah ada `is_numeric()` dan cek wajib-isi sejak Jobsheet 7-9); ditambah cast eksplisit `(int)` pada `id` di form Edit Buku & Anggota.
- **SQL Injection** — diaudit ulang, **tidak ada perubahan kode**: sejak Jobsheet 8 semua query sudah memakai prepared statement (`:parameter`).
- **`docs/security-checklist.md`** (baru) — laporan audit terstruktur dengan format Sebelum/Sesudah + bukti pengujian per kerentanan.

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

**Opsi 1 — PHP built-in server**, dari dalam folder `jobsheet-11/`:
```bash
php -S localhost:8000
```
Buka `http://localhost:8000/index.php`.

**Opsi 2 — Laragon (Apache)**: lewat virtual host langsung ke folder `jobsheet-11/` (mis. `http://jobsheet11.test/`), atau bersarang di bawah domain proyek (mis. `http://dp2026.test/kode-praktikum/jobsheet-11/`) — path CSS/JS/link/redirect sudah relatif otomatis (lihat `includes/header.php` & `includes/auth.php`), jadi keduanya jalan.

## Cara Menguji

Sesuai `docs/security-checklist.md`, ada 4 skenario verifikasi:

- **XSS**: tambah buku dengan judul `<script>alert(1)</script>` (atau `<iframe src="javascript:alert(1)">`) → di Daftar Buku harus **tampil sebagai teks**, bukan dieksekusi sebagai pop-up/iframe.
- **CSRF**: login, lalu kirim `curl -X POST http://localhost:8000/buku/proses_tambah.php -d "judul=x"` tanpa `csrf_token` → harus mendapat **HTTP 403** ("Permintaan ditolak: token CSRF tidak valid atau kedaluwarsa.").
- **Urutan guard**: akses `proses_tambah.php` lewat `POST` **tanpa login** sama sekali → tetap di-redirect ke Login (guard `auth.php` berjalan lebih dulu daripada `csrf_verify()`).
- **SQL Injection**: login dengan username `' OR '1'='1` dan password apa saja → tetap muncul "Username atau password salah." (prepared statement bekerja).

## Catatan

- **Jangan pernah percaya input dari luar.** XSS dan CSRF berakar dari prinsip yang sama: data dari `$_POST`/`$_GET` tidak boleh dipercaya begitu saja.
- **`POST` saja tidak cukup mencegah CSRF.** Proteksi metode (`REQUEST_METHOD !== 'POST'`) tetap bisa dipicu form dari situs lain; token per-sesi yang diverifikasi `hash_equals()` adalah lapisan yang benar-benar menutup celah itu.
- **Urutan `require` di halaman proses kritis**: `auth.php` → `csrf.php` → `csrf_verify()`. Pengunjung yang belum login di-redirect lebih dulu, sehingga tidak bisa memicu pengecekan CSRF sama sekali.
- **`e()` wajib dipakai setiap kali mencetak data yang pernah melewati input pengguna**, termasuk di dalam atribut `value="..."`. Kolom bertipe `INTEGER` (`tahun`, `stok`) tidak perlu di-escape karena tidak mungkin memuat HTML.
- **Audit bisa menghasilkan "sudah aman."** SQL Injection dan validasi input dikonfirmasi aman, bukan ditulis ulang dari nol.
- Lihat `docs/security-checklist.md` untuk rincian audit dan pemetaan tiap kerentanan ke perbaikannya.

## Refleksi

Jobsheet ini menutup janji yang sudah disinggung berkali-kali di jobsheet sebelumnya — misalnya celah pada form pencarian `method="get"` yang disebut "akan dibahas di Jobsheet 11". Beberapa hal yang dipelajari:

- **Output encoding adalah pertahanan XSS yang paling mendasar.** Satu fungsi `e()` yang konsisten dipakai di semua titik output menutup celah stored XSS (contoh nyata: `<iframe src="javascript:...">` yang tersimpan sebagai judul buku).
- **Token CSRF melindungi niat, bukan sekadar metode.** Metode `POST` memastikan aksi tidak terpicu tak sengaja; token memastikan aksi benar-benar berasal dari halaman aplikasi sendiri.
- **Solusi keamanan terbaik seringkali kecil dan presisi.** Session fixation cukup ditutup satu baris `session_regenerate_id(true)` — asalkan ditempatkan di titik yang tepat (tepat setelah login berhasil).
- **Audit sama pentingnya dengan menulis kode.** Memeriksa ulang kode lama memastikan klaim "sudah aman" berdiri di atas bukti, bukan asumsi.

Setelah Jobsheet 11, SIMPUS-Mini tidak hanya mengontrol **siapa** yang boleh mengakses data, tetapi juga memastikan data yang ditampilkan **aman** dan setiap aksi yang mengubah data **benar-benar diniatkan** oleh pengguna — fondasi keamanan yang tepat sebelum modul transaksi **Peminjaman/Pengembalian**.
