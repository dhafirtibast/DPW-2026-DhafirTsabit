# Jobsheet 8 — Koneksi PostgreSQL

Sub-CPMK: Menghubungkan aplikasi dengan basis data PostgreSQL.

## Struktur Folder

```
jobsheet-8/
├── anggota/
│   ├── list.php
│   ├── tambah.php
│   └── proses_tambah.php
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── app.js
├── buku/
│   ├── list.php
│   ├── tambah.php
│   └── proses_tambah.php
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

## Perubahan dari Jobsheet 7

- Tambah `sql/01_buku_anggota.sql` — DDL tabel `buku` dan `anggota` (ERD dasar).
- Tambah `includes/koneksi.php` — koneksi `PDO` driver `pgsql`.
- `buku/proses_tambah.php` & `anggota/proses_tambah.php`: `$_SESSION['buku'][] = ...` (Jobsheet 7) diganti `INSERT ... RETURNING id` via prepared statement.
- `buku/list.php` & `anggota/list.php`: sumber data diganti dari `$_SESSION` menjadi `SELECT * FROM ... ORDER BY id DESC`.
- `index.php`: kartu statistik Total Buku/Anggota kini `SELECT COUNT(*)` dari database (bukan dummy/session lagi).

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

**Opsi 1 — PHP built-in server**, jalankan dari dalam folder `jobsheet-8/`:
```bash
php -S localhost:8000
```
Buka `http://localhost:8000/index.php`.

**Opsi 2 — Laragon (Apache)**: lewat virtual host langsung ke folder `jobsheet-8/` (mis. `http://jobsheet08.test/`), atau bersarang di bawah domain proyek (mis. `http://dp2026.test/kode-praktikum/jobsheet-8/`) — path CSS/JS/link sudah relatif otomatis (lihat `includes/header.php`), jadi keduanya jalan.

## Catatan

- Data yang diinput sekarang **persisten** — coba tutup-buka browser, data tetap ada (beda dengan Jobsheet 7 yang hilang saat sesi berakhir).
- Query memakai prepared statement (`:nama_parameter`) — bukan concatenation string — sebagai fondasi keamanan yang diperdalam di Jobsheet 11.
- Kolom `id` sudah ikut ter-fetch dari `SELECT *` meski belum dipakai di tampilan — akan digunakan untuk link Edit/Hapus mulai Jobsheet 9.

## Refleksi

Jobsheet ini menutup satu "lubang" yang sudah disinggung sejak Jobsheet 7: data yang **benar-benar tersimpan**. Aplikasi yang sebelumnya hanya "hidup" selama sesi browser kini punya memori yang menetap di PostgreSQL. Beberapa hal yang dipelajari:

- **Database menyelesaikan masalah data yang hilang.** `$_SESSION` bersifat sementara (hilang saat sesi berakhir); PostgreSQL menyimpan data secara permanen, terpisah dari sesi browser mana pun.
- **Skema SQL mendefinisikan aturan data.** `NOT NULL`, `UNIQUE`, `PRIMARY KEY`, dan tipe data yang tepat menjaga kualitas data langsung dari lapisan database — bukan hanya dari validasi aplikasi.
- **PDO adalah jembatan seragam.** Satu objek `$pdo` (dibuat lewat DSN `pgsql:host=...;port=...;dbname=...`) cukup untuk menjalankan `SELECT`, `INSERT`, dst., dengan `try`/`catch` untuk menangani kegagalan koneksi secara rapi.
- **Prepared statement = cara aman memasukkan data.** `prepare()` + `execute()` memisahkan struktur query dari nilai yang dikirim, sehingga nilai dari `$_POST` tidak pernah diperlakukan sebagai perintah SQL — fondasi pertahanan terhadap SQL injection.
- **Struktur data yang konsisten membuat kode tampilan tidak berubah.** Karena `fetchAll(PDO::FETCH_ASSOC)` menghasilkan array yang bentuknya identik dengan `$_SESSION` sebelumnya, kode `foreach` di `list.php` sama sekali tidak perlu diubah — hanya sumber datanya yang berpindah.
- **`query()` vs `prepare()`.** Query tanpa nilai dari luar cukup memakai `query()`; begitu ada nilai yang berasal dari pengguna, wajib memakai `prepare()`+`execute()`.

Secara keseluruhan, jobsheet ini menunjukkan bagaimana data mengalir dari form → diproses server → **disimpan permanen di PostgreSQL** → dibaca kembali dan dirender menjadi tabel — fondasi yang tepat sebelum fitur Edit/Hapus dan Peminjaman/Pengembalian ditambahkan pada jobsheet berikutnya.
