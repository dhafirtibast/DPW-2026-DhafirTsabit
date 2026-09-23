# Jobsheet 7 — PHP Dasar & Form Handling

Sub-CPMK: Mengimplementasikan dasar PHP & pengolahan form.

## Struktur Folder

```
jobsheet-7/
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
│   ├── header.php
│   └── footer.php
├── README.md
└── index.php
```

## Perubahan dari Jobsheet 6

- Semua halaman `.html` diubah menjadi `.php`.
- Diperkenalkan `includes/header.php` & `includes/footer.php` untuk menghindari duplikasi navbar/footer di setiap halaman (dipakai lewat `include`).
- Path CSS/JS/menu memakai path **relatif** (`assets/css/style.css`, `index.php`, dst.), dihitung otomatis di `includes/header.php` berdasarkan kedalaman folder halaman yang sedang diakses (`$base` = `""` di root, `"../"` untuk halaman satu level ke dalam seperti `buku/`, `anggota/`). Jadi proyek ini tetap berjalan benar walau diakses dari root server (`php -S`) **maupun** lewat subfolder (mis. Laragon dengan document root di folder induk).
- `buku/tambah.php` & `anggota/tambah.php`: form kini `method="post"` mengarah ke `proses_tambah.php` masing-masing.
- `buku/proses_tambah.php` & `anggota/proses_tambah.php`: memvalidasi `$_POST` di server (validasi ini **terpisah** dari validasi JS di Jobsheet 5 — bisa berjalan sendiri walau JS dimatikan), lalu menyimpan sementara ke `$_SESSION['buku']` / `$_SESSION['anggota']` (array), redirect ke `list.php`.
- `buku/list.php` & `anggota/list.php`: tabel dirender dari `$_SESSION` via `foreach` (menggantikan pendekatan fetch/JSON di Jobsheet 6 — rendering utama sekarang di server).
- Flash message sukses/gagal ditampilkan lewat `$_SESSION['flash']`.
- File `assets/js/buku.js`, `assets/js/anggota.js`, dan folder `data/` dari Jobsheet 6 **dihapus** karena rendering sudah dipindah ke server-side PHP.
- `assets/css/style.css` ditambah gaya `.flash` (`.flash`, `.flash-success`, `.flash-error`) — satu-satunya perubahan CSS di jobsheet ini.

## Cara Menjalankan

Berbeda dari Jobsheet 6: file `.php` **wajib** diproses oleh PHP interpreter, jadi tidak bisa dibuka langsung sebagai berkas `file://` (browser akan menampilkan kode PHP mentah).

**Opsi 1 — PHP built-in server**, jalankan dari dalam folder `jobsheet-7/`:
```bash
php -S localhost:8000
```
Buka `http://localhost:8000/index.php`.

**Opsi 2 — Laragon (Apache)**: bisa lewat virtual host yang document root-nya langsung ke folder `jobsheet-7/` (mis. `http://jobsheet7.test/`), atau diakses bersarang di bawah domain proyek (mis. `http://dp2026.test/kode-praktikum/jobsheet-7/`) — dua-duanya jalan karena path CSS/JS/link sudah relatif otomatis (lihat catatan di atas).

## Catatan

- Data yang disimpan di `$_SESSION` akan hilang saat sesi browser berakhir — ini jembatan sementara. Mulai Jobsheet 8, penyimpanan dipindah ke PostgreSQL agar persisten.
- Coba nonaktifkan JavaScript di browser lalu submit form kosong: validasi server tetap mencegah data invalid tersimpan.
- Flash message bersifat "sekali tampil": setelah dibaca di `list.php`, `$_SESSION['flash']` langsung di-`unset` sehingga tidak muncul lagi saat halaman di-refresh.
- Fitur Login, Peminjaman, dan Pengembalian dari `docs/wireframe.md` baru akan berfungsi ketika backend PHP/PostgreSQL ditambahkan pada jobsheet berikutnya.

## Refleksi

Jobsheet ini adalah titik paling besar sejauh ini: aplikasi berpindah dari yang sepenuhnya berjalan di **browser** (HTML/CSS/JS statis) menjadi aplikasi yang punya **server sungguhan** di baliknya, memakai **PHP**. Beberapa hal yang dipelajari:

- **Server-side vs client-side.** Untuk pertama kalinya ada kode yang benar-benar dijalankan di server *sebelum* halaman dikirim: PHP memproses data, mengambil keputusan (misalnya "apakah form ini valid?"), dan menghasilkan HTML yang berbeda-beda tergantung situasinya — bukan sekadar mengirim file statis apa adanya.
- **`include` menghapus duplikasi.** Memindahkan `<header>` dan `<footer>` yang sebelumnya diulang di setiap file ke `includes/` membuat satu perubahan cukup dilakukan sekali; semua halaman yang meng-include ikut berubah.
- **Path relatif otomatis.** `$base` dihitung dari struktur folder di disk (lewat `__DIR__` dan `SCRIPT_FILENAME`), bukan dari alamat domain, sehingga navbar dan CSS tetap benar di kedalaman folder yang berbeda-beda.
- **`$_SESSION` menjembatani permintaan HTTP.** HTTP bersifat *stateless*, jadi session adalah satu-satunya cara data (buku/anggota yang baru ditambah, pesan flash) "menyeberang" antar halaman. Tapi sifatnya sementara — hilang saat sesi browser berakhir.
- **Validasi server yang benar-benar bisa diandalkan.** Validasi HTML dan JavaScript sama-sama berjalan di browser dan bisa dilewati; validasi di `proses_tambah.php` berjalan di server dan selalu dijalankan untuk setiap data yang masuk.
- **Redirect setelah POST.** Pola `header('Location: ...')` + `exit` mencegah data ter-submit ulang saat halaman di-refresh, sekaligus mengarahkan ke halaman yang relevan (form kalau error, daftar kalau sukses).
- **Flash message sekali pakai.** Disimpan ke `$_SESSION['flash']`, lalu segera di-`unset` setelah dibaca supaya tidak muncul berulang di kunjungan berikutnya.
- **Rendering pindah dari browser ke server.** Menggantikan `fetch`/JSON Jobsheet 6 dengan `foreach` PHP berarti HTML yang diterima browser sudah terisi penuh sejak awal — tidak perlu lagi loading indicator, dan tidak ada jeda yang terlihat pengguna.

Secara keseluruhan, jobsheet ini menunjukkan bagaimana data mengalir dari form → diproses server → disimpan sementara → dirender kembali menjadi tabel — fondasi yang tepat sebelum data benar-benar disimpan permanen di PostgreSQL pada jobsheet berikutnya.
