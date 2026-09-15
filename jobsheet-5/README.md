# Jobsheet 5 — Interaktivitas JavaScript

Sub-CPMK: Menerapkan interaktivitas JavaScript pada aplikasi (proyek).

## Struktur Folder

```
jobsheet-5/
├── anggota/
│   ├── list.html
│   └── tambah.html
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── app.js
├── buku/
│   ├── list.html
│   └── tambah.html
├── docs/
│   └── wireframe.md
├── README.md
└── index.html
```

## Perubahan dari Jobsheet 4

- Tambah `assets/js/app.js` — satu file JavaScript yang menangani semua interaktivitas halaman.
- **Hamburger menu** tidak lagi memakai *checkbox hack* (`input.nav-toggle` + `label`), diganti `button#nav-toggle-btn` yang dikendalikan JS dan membuka menu lewat class `.nav-open`.
- **Konfirmasi hapus**: tombol hapus diberi class `.btn-hapus`, JS menampilkan `confirm()` lalu menghapus baris tabel jika disetujui.
- **Filter tabel**: tambah `input#search-input` di halaman Daftar Buku dan Daftar Anggota, JS menyaring baris secara langsung (`keyup`).
- **Validasi form client-side** pada `#form-tambah` (buku & anggota): field wajib, tahun 1900–2026, dan stok tidak negatif — pesan error tampil di bawah input.
- CSS ditambah aturan `.error`, `.search-box`, dan penyesuaian `.nav-open`; `<meta viewport>` dipindah ke baris atas `<head>`.
- `docs/wireframe.md` tetap ada — belum ada halaman baru, fokus jobsheet ini pada interaktivitas halaman yang sudah ada.

## Cara Menjalankan

Sama seperti Jobsheet 4 — buka `index.html` (JavaScript dimuat otomatis oleh setiap halaman).

## Catatan

Interaktivitas di `app.js` masih sepenuhnya *client-side* dan hanya bekerja pada data yang tampil di halaman (perubahan hilang saat refresh). Fitur Login, Peminjaman, dan Pengembalian dari `docs/wireframe.md` baru akan berfungsi ketika backend PHP/PostgreSQL ditambahkan pada jobsheet berikutnya.

## Refleksi

Jobsheet ini adalah langkah pertama menuju aplikasi yang benar-benar interaktif. Halaman statis dari Jobsheet 2–4 mulai diberi "nyawa" lewat JavaScript. Beberapa hal yang dipelajari:

- **Satu file JS untuk banyak halaman.** Dengan memuat `app.js` di semua halaman dan mengawali setiap fungsi dengan pengecekan elemen (`if (!el) return`), satu skrip bisa dipakai di Beranda, Daftar Buku, Daftar Anggota, maupun form Tambah tanpa error — pola yang jauh lebih rapi daripada menaruh skrip di tiap file.
- **DOM membuat halaman terasa hidup.** Filter tabel, konfirmasi hapus, dan validasi form membuat pengguna langsung mendapat umpan balik tanpa memuat ulang halaman.
- **Memperbaiki solusi lama.** Mengganti *checkbox hack* pada hamburger menu dengan tombol yang dikendalikan JS terasa lebih benar secara aksesibilitas (tombol asli + `aria-label`) dan lebih mudah dikembangkan.
- **Validasi client-side bukan pengaman.** JS hanya membantu pengguna mengisi form; data tetap harus divalidasi ulang di sisi server agar aman — kesadaran yang penting sebelum masuk ke PHP/PostgreSQL.
- **Interaktivitas perlu desain.** Karena wireframe di `docs/wireframe.md` sudah dibuat di Jobsheet 4, saya tinggal menambahkan perilaku pada struktur yang sudah ada, bukan merombak HTML.

Secara keseluruhan, jobsheet ini menunjukkan bahwa HTML/CSS hanyalah tampilan — JavaScript-lah yang membuatnya merespons tindakan pengguna, dan itu menjadi fondasi penting sebelum aplikasi terhubung ke basis data.
