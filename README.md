# Desain dan Pemrograman Web 2026

Repositori ini berisi pengerjaan dan dokumentasi Jobsheet **Desain dan Pemrograman Web** semester 3. Proyek yang dikembangkan sepanjang jobsheet adalah **SIMPUS-Mini** — aplikasi perpustakaan mini yang tumbuh bertahap dari halaman HTML statis hingga aplikasi PHP yang terhubung ke basis data PostgreSQL.

---

## Biodata

**Nama:** Dhafir Tsabit  
**Kelas:** TI-2F  
**No. Absen:** 10  

---

## Ringkasan Progres

| Jobsheet | Topik | Sub-CPMK / Learning Outcome | Teknologi |
|---|---|---|---|
| [Jobsheet 1](jobsheet-1/) | Struktur Semantic HTML5 | Menyusun struktur halaman web dengan HTML5 semantic | HTML5 |
| [Jobsheet 2](jobsheet-2/) | Styling Dasar CSS3 | Memahami cara kerja CSS dalam menentukan visual halaman web | HTML5, CSS3 |
| [Jobsheet 3](jobsheet-3/) | Responsive Design | Membangun tampilan responsif | HTML5, CSS3 (media query) |
| [Jobsheet 3 (Bootstrap)](jobsheet-3%20(bootstrap)/) | Responsive Design — varian Bootstrap | Membangun tampilan responsif dengan framework | HTML5, Bootstrap 5.3.3 |
| [Jobsheet 4](jobsheet-4/) | UI/UX Design | Merancang UI/UX aplikasi (proyek) | HTML5, CSS3, wireframe |
| [Jobsheet 5](jobsheet-5/) | Interaktivitas JavaScript | Menerapkan interaktivitas JavaScript pada aplikasi (proyek) | HTML5, CSS3, JavaScript |
| [Jobsheet 6](jobsheet-6/) | Fetch & JSON | Menerapkan pengambilan data asinkron pada aplikasi (proyek) | HTML5, CSS3, JavaScript, JSON |
| [Jobsheet 7](jobsheet-7/) | PHP Dasar & Form Handling | Mengimplementasikan dasar PHP & pengolahan form | PHP, `$_SESSION` |
| [Jobsheet 8](jobsheet-8/) | Koneksi PostgreSQL | Menghubungkan aplikasi dengan basis data PostgreSQL | PHP, PDO, PostgreSQL 15 |

Proyek tambahan di luar jobsheet: [**Fatar's Garage**](fatars-garage/) — website jual beli motor bekas (lihat bagian [Proyek Eksplorasi](#proyek-eksplorasi)).

---

## Alur Pengembangan SIMPUS-Mini

### Jobsheet 1 — Struktur Semantic HTML5
Menyusun arsitektur halaman beranda, daftar/tambah buku, dan daftar/tambah anggota memakai elemen HTML5 semantic (`<header>`, `<nav>`, `<main>`, `<section>`, `<article>`, `<table>`, `<form>`) tanpa `<div>` dan tanpa CSS/JS. Navigasi antar halaman menggunakan *relative path* pada struktur direktori bertingkat.

### Jobsheet 2 — Styling Dasar CSS3
Struktur HTML tidak berubah; ditambahkan `assets/css/style.css` dan satu baris `<link rel="stylesheet">` di setiap halaman. Mencakup pengaturan warna tema, tipografi, dan tata letak dasar.

### Jobsheet 3 — Responsive Design
Tampilan disesuaikan untuk berbagai ukuran layar: `<meta name="viewport">`, media query, *grid* kartu statistik, tabel responsif, serta menu *hamburger* berbasis *checkbox hack*.

### Jobsheet 3 (Bootstrap) — Varian Bootstrap
Alternatif implementasi Jobsheet 3 memakai **Bootstrap 5.3.3** (CDN) untuk komponen dan sistem grid, sebagai perbandingan dengan CSS murni.

### Jobsheet 4 — UI/UX Design
Perancangan antarmuka lewat dokumen `docs/wireframe.md` (alur Login, Peminjaman, Pengembalian), penambahan kartu statistik berbasis *grid*, dan tombol "Detail" pada daftar buku.

### Jobsheet 5 — Interaktivitas JavaScript
Interaktivitas sisi klien dipindahkan ke JavaScript: menu *hamburger* berbasis tombol asli, konfirmasi hapus, filter tabel *real-time*, dan validasi form *client-side*. Gaya kolom pencarian serta pesan error ditambahkan pada CSS.

### Jobsheet 6 — Fetch & JSON
Data buku dan anggota dipindahkan ke `data/buku.json` dan `data/anggota.json`, lalu dimuat secara asinkron memakai `fetch()`. Ditambahkan indikator *loading* dan penanganan kegagalan muat data (`Gagal memuat data: ...`). Tabel dikosongkan lebih dulu di HTML, lalu dirender oleh JavaScript.

### Jobsheet 7 — PHP Dasar & Form Handling
Seluruh halaman `.html` diubah menjadi `.php`. `includes/header.php` dan `includes/footer.php` diperkenalkan untuk menghapus duplikasi navbar/footer. Form dikirim `method="post"` ke `proses_tambah.php`, divalidasi di sisi server, lalu disimpan sementara ke `$_SESSION` dan dirender kembali lewat `foreach` di `list.php`. Flash message sekali-tampil memakai `$_SESSION['flash']`.

### Jobsheet 8 — Koneksi PostgreSQL
Sumber data berpindah dari `$_SESSION` ke database **PostgreSQL**:
- `sql/01_buku_anggota.sql` — DDL tabel `buku` dan `anggota`.
- `includes/koneksi.php` — koneksi **PDO** driver `pgsql` (port `5433`), dengan `try`/`catch` untuk kegagalan koneksi.
- `proses_tambah.php` — `INSERT ... RETURNING id` via **prepared statement**.
- `list.php` — `SELECT * FROM ... ORDER BY id DESC`, hasil dibaca lewat `fetchAll(PDO::FETCH_ASSOC)`.
- `index.php` — kartu statistik memakai `SELECT COUNT(*)` + `fetchColumn()`.

Data kini **persisten**: tetap ada setelah browser ditutup. Detail lengkap ada di [README Jobsheet 8](jobsheet-8/README.md).

---

## Proyek Eksplorasi

### Fatar's Garage
Website sederhana jual beli motor bekas (terinspirasi OLX), dibangun dengan HTML, CSS, dan JavaScript murni tanpa framework. Data iklan motor dan penjual dimuat secara asinkron dari `data/motor.json` dan `data/penjual.json` memakai `fetch()`.

Fitur: pencarian *real-time*, chip kategori merk, harga berformat Rupiah, form pasang iklan/tambah penjual dengan validasi *client-side*, hapus baris dengan konfirmasi (*event delegation*), serta indikator loading dan penanganan gagal muat data. Detail ada di [README Fatar's Garage](fatars-garage/README.md).

---

## Struktur Repositori

```text
DPW-2026-DhafirTsabit/
├── jobsheet-1/                 # Struktur semantic HTML5
├── jobsheet-2/                 # Styling dasar CSS3
├── jobsheet-3/                 # Responsive design (CSS murni)
├── jobsheet-3 (bootstrap)/     # Responsive design (Bootstrap 5)
├── jobsheet-4/                 # UI/UX design + wireframe
├── jobsheet-5/                 # Interaktivitas JavaScript
├── jobsheet-6/                 # Fetch & JSON
├── jobsheet-7/                 # PHP dasar & form handling
├── jobsheet-8/                 # Koneksi PostgreSQL (PDO)
├── fatars-garage/              # Proyek eksplorasi jual beli motor
└── README.md
```

Setiap folder jobsheet memiliki `README.md` masing-masing yang menjelaskan perubahan dan konsep pada jobsheet tersebut.

---

## Cara Menjalankan

**Jobsheet 1–5** (HTML/CSS/JS statis): buka `index.html` langsung di browser, atau lewat server lokal.

**Jobsheet 6 & Fatar's Garage** (`fetch` JSON): wajib lewat server lokal karena `fetch()` ke berkas lokal diblokir kebijakan CORS jika dibuka dengan `file://`:
```bash
php -S localhost:8000
```

**Jobsheet 7–8** (PHP): wajib diproses PHP interpreter, tidak dapat dibuka langsung sebagai `file://`:
```bash
php -S localhost:8000
```

**Jobsheet 8** membutuhkan persiapan tambahan sebelum dijalankan (PostgreSQL berjalan, ekstensi `pdo_pgsql` aktif, database `simpus_mini` dan skema dibuat). Langkah lengkap ada di [README Jobsheet 8](jobsheet-8/README.md).

---

## Lingkungan Pengembangan

- **Editor:** Visual Studio Code
- **Web server lokal:** Laragon (Apache) / PHP built-in server
- **PHP:** 8.1.10 (ekstensi `pdo_pgsql`, `pgsql`)
- **Database:** PostgreSQL 15 (port `5433`)
- **UI framework:** Bootstrap 5.3.3 (Jobsheet 3 varian Bootstrap)
