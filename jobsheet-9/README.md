# Jobsheet 9 — Edit, Hapus & Pencarian Data

Sub-CPMK: Melengkapi operasi CRUD (Edit/Hapus) serta menambahkan pencarian dan paginasi data pada aplikasi.

## Struktur Folder

```
jobsheet-9/
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
│   ├── footer.php
│   ├── header.php
│   └── koneksi.php
├── sql/
│   └── 01_buku_anggota.sql
├── README.md
└── index.php
```

## Perubahan dari Jobsheet 8

- Tambah `buku/edit.php`, `buku/proses_edit.php`, `buku/hapus.php` — dan pasangannya di `anggota/` — sehingga siklus CRUD kini lengkap (Create, Read, Update, Delete).
- `edit.php`: ambil satu baris lewat `SELECT * FROM ... WHERE id = :id`, lalu isikan nilainya kembali ke form; `id` dikirim via `<input type="hidden">`.
- `proses_edit.php`: validasi di sisi server (flash `error` bila gagal), lalu `UPDATE ... WHERE id = :id` via prepared statement, diakhiri redirect ke `list.php` dengan flash `success`.
- `hapus.php`: hanya menerima `POST`, jalankan `DELETE FROM ... WHERE id = :id`, lalu redirect + flash. Penghapusan kini **benar-benar di server**, bukan lagi menghapus baris di sisi klien.
- `buku/list.php` & `anggota/list.php`: tombol Edit/Hapus dummy diganti aksi nyata — link `<a href="edit.php?id=...">` dan form `method="post"` ke `hapus.php`.
- `buku/list.php` & `anggota/list.php`: tambah **pencarian** (`?q=...` memakai `ILIKE`) dan **paginasi** (`LIMIT`/`OFFSET`, 5 baris per halaman) — keduanya lewat prepared statement.
- `assets/js/app.js`: `initHapusConfirm()` berubah dari listener `click` + `row.remove()` menjadi konfirmasi pada event `submit` form `.form-hapus`; `preventDefault()` dipanggil hanya saat pengguna membatalkan.
- `assets/css/style.css`: tambah style tautan `a.btn-edit`, `form.form-hapus` (inline), `.pagination`, dan tombol pada `.search-box`.

## Persiapan database

1. Pastikan PostgreSQL berjalan dan ekstensi PHP `pdo_pgsql` aktif (`php -m | grep pgsql`; di Windows: `php -m | findstr pgsql`; bila belum ada, aktifkan `extension=pdo_pgsql` dan `extension=pgsql` di `php.ini` lalu restart server).
2. Buat database:
   ```bash
   createdb -p 5433 simpus_mini
   ```
3. Jalankan skema:
   ```bash
   psql -p 5433 -d simpus_mini -f sql/01_buku_anggota.sql
   ```
4. Sesuaikan kredensial di `includes/koneksi.php` (`$user`, `$pass`, `$port`) dengan environment lokal.

> **Catatan port:** `includes/koneksi.php` pada environment ini memakai `$port = "5433"` — bukan `5432` default — karena port `5432` sudah dipakai instalasi PostgreSQL lain. Karena itu semua perintah `createdb`/`psql` di atas disertai `-p 5433`. Bila instalasi yang dipakai menggunakan port `5432`, hilangkan `-p 5433` dan sesuaikan `$port` di `koneksi.php`.

## Cara menjalankan

**Opsi 1 — PHP built-in server**, jalankan dari dalam folder `jobsheet-9/`:
```bash
php -S localhost:8000
```
Buka `http://localhost:8000/index.php`.

**Opsi 2 — Laragon (Apache)**: lewat virtual host langsung ke folder `jobsheet-9/` (mis. `http://jobsheet09.test/`), atau bersarang di bawah domain proyek (mis. `http://dp2026.test/kode-praktikum/jobsheet-9/`) — path CSS/JS/link sudah relatif otomatis (lihat `includes/header.php`), jadi keduanya jalan.

## Catatan

- Hapus kini **permanen di database** — konfirmasi JavaScript hanyalah "gerbang" sebelum form dikirim ke `hapus.php`; tidak ada lagi `row.remove()` di sisi klien.
- Setiap operasi tulis memakai pola **PRG (Post/Redirect/Get)**: `proses_*.php` menyimpan lalu redirect ke `list.php`, sehingga me-refresh halaman tidak mengulang operasi.
- Pencarian memakai `ILIKE` (pencocokan teks tanpa membedakan huruf besar/kecil — khas PostgreSQL) sehingga "buku" dan "Buku" sama-sama cocok.
- `LIMIT`/`OFFSET` di-bind sebagai `PDO::PARAM_INT` karena PostgreSQL menolak parameter integer yang dikirim sebagai string.
- `index.php` masih menampilkan `0` pada kartu "Sedang Dipinjam" — fitur Peminjaman/Pengembalian baru datang di jobsheet berikutnya.

## Refleksi

Jobsheet ini melengkapi satu siklus yang sudah dimulai sejak Jobsheet 7: setelah data bisa **disimpan** (Jobsheet 8), kini data juga bisa **diubah, dihapus, dicari, dan ditelusuri halaman per halaman**. Beberapa hal yang dipelajari:

- **CRUD lengkap menuntut identitas baris.** Edit dan Hapus perlu tahu *baris mana* yang dimaksud. `PRIMARY KEY id` yang sudah disiapkan di Jobsheet 8 (di-fetch lewat `SELECT *`) ternyata memang kunci yang membuat fitur ini mungkin — persiapan struktur di jobsheet sebelumnya baru terasa gunanya sekarang.
- **Pola PRG mencegah aksi ganda.** Mengganti "tampilkan hasil simpan" menjadi "redirect ke daftar" membuat refresh tidak mengulang `INSERT`/`UPDATE`/`DELETE`, dan flash message memberi umpan balik sekali-tampil.
- **Pencarian & paginasi adalah soal efisiensi.** Alih-alih menarik semua baris lalu memfilternya, `WHERE ... ILIKE` + `LIMIT`/`OFFSET` menyerahkan pekerjaan itu ke database — tetap ringan walau data terus bertambah.
- **Validasi berlapis, server tetap penentu.** Atribut HTML `required` dan validasi JavaScript mempercepat umpan balik, tetapi validasi di `proses_*.php` tetap sumber kebenaran — klien bisa dilewati, server tidak.
- **Keamanan yang sama berlaku pada UPDATE/DELETE.** Prepared statement tidak hanya untuk `INSERT`; setiap nilai dari pengguna (`id`, `q`, kolom form) tetap dikirim sebagai parameter, bukan disambung ke string SQL.
- **Konfirmasi hapus yang benar bukan `row.remove()`.** JavaScript kini hanya mencegah form terkirim bila pengguna menekan "Batal"; penghapusan asli dilakukan server. Ini contoh pertama pemisahan tegas antara *tampilan* dan *sumber data*.

Secara keseluruhan, jobsheet ini menjadikan aplikasi benar-benar dapat **dikelola**: data masuk, tampil, bisa diperbaiki, bisa dibuang, dan bisa ditemukan kembali. Ini fondasi yang tepat sebelum modul transaksi **Peminjaman/Pengembalian** ditambahkan pada jobsheet berikutnya.
