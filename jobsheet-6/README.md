# Jobsheet 6 — Mengambil Data Secara Asinkron (fetch & JSON)

Sub-CPMK: Menerapkan pengambilan data asinkron pada aplikasi (proyek).

## Struktur Folder

```
jobsheet-6/
├── anggota/
│   ├── list.html
│   └── tambah.html
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       ├── anggota.js
│       ├── app.js
│       └── buku.js
├── buku/
│   ├── list.html
│   └── tambah.html
├── data/
│   ├── anggota.json
│   └── buku.json
├── docs/
│   └── wireframe.md
├── README.md
└── index.html
```

## Perubahan dari Jobsheet 5

- Tambah folder `data/` berisi `buku.json` (10 buku) dan `anggota.json` (4 anggota) sebagai sumber data *dummy* — menggantikan baris tabel yang sebelumnya ditulis langsung di HTML.
- Tambah `assets/js/buku.js` dan `assets/js/anggota.js`: mengambil data dengan `fetch()` + `async/await`, lalu membangun baris `<tbody>` secara dinamis lewat DOM.
- Kedua skrip menampilkan **indikator loading** (`#loading-indicator`, dengan jeda simulasi 600 ms), menangani kegagalan lewat `try/catch/finally`, dan menampilkan pesan `Gagal memuat data: ...` bila `fetch` gagal.
- `<tbody>` di `buku/list.html` dan `anggota/list.html` dikosongkan — hanya menyisakan komentar penanda bahwa barisnya diisi oleh JS.
- Tombol **Detail** dihapus dari kolom Aksi (tinggal Edit & Hapus); footer halaman list diperbarui menjadi "Jobsheet 6".
- `app.js`: konfirmasi hapus diubah dari memasang listener ke setiap `.btn-hapus` (`querySelectorAll`) menjadi **event delegation** di `document` (`closest(".btn-hapus")`), supaya tombol Hapus pada baris hasil `fetch` tetap berfungsi.
- `buku/list.html` dan `anggota/list.html` memuat `app.js` **dan** skrip pengambil data (`buku.js`/`anggota.js`); halaman lain cukup `app.js`.
- `assets/css/style.css` dan `docs/wireframe.md` tidak berubah dari Jobsheet 5.

## Cara Menjalankan

Berbeda dari Jobsheet 5: karena data diambil lewat `fetch()`, halaman **tidak bisa** dibuka langsung sebagai berkas `file://` (diblokir CORS). Jalankan server lokal dari dalam folder `jobsheet-6`, misalnya `python -m http.server`, lalu buka `http://localhost:8000`.

## Catatan

- Data masih *dummy* dan hanya dibaca (GET); halaman **tidak menulis** ke `data/*.json`, jadi baris yang dihapus tetap muncul lagi saat refresh.
- Kolom **Email** di Daftar Anggota belum terisi: header sudah ditambah, tetapi `anggota.json` dan `anggota.js` belum memiliki field email.
- Fitur Login, Peminjaman, dan Pengembalian dari `docs/wireframe.md` baru akan berfungsi ketika backend PHP/PostgreSQL ditambahkan pada jobsheet berikutnya.

## Refleksi

Jobsheet ini membawa aplikasi selangkah lebih dekat ke aplikasi nyata: data tidak lagi "menempel" di HTML, melainkan diambil dari berkas terpisah. Beberapa hal yang dipelajari:

- **Memisahkan data dari tampilan.** Memindahkan isi tabel ke `data/*.json` membuat HTML kembali bersih; kalau data berubah, cukup ubah JSON tanpa menyentuh markup.
- **`fetch()` itu asinkron.** Hasilnya tidak langsung tersedia, sehingga butuh `async/await`, penanda "sedang memuat" (`#loading-indicator`), dan penanganan error (`try/catch/finally`) agar halaman tidak diam saja ketika gagal.
- **Event delegation menyelamatkan fitur lama.** Tombol Hapus yang tadinya dipasangi listener saat `DOMContentLoaded` tidak berlaku untuk baris yang baru dibuat JS; memindahkan listener ke `document` membuat fitur hapus tetap jalan.
- **`file://` bukan server.** Saya belajar bahwa `fetch` ke berkas lokal harus dijalankan lewat server — pengalaman yang akan terpakai saat memanggil API PHP nanti.
- **Konsistensi struktur itu penting.** Kolom Email yang belum sinkron antara header, JSON, dan skrip jadi pengingat bahwa ketiganya harus dijaga tetap selaras.

Secara keseluruhan, jobsheet ini menunjukkan bahwa halaman web bisa mengambil dan menampilkan data dari luar dokumennya sendiri — fondasi yang tepat sebelum data benar-benar diambil dari backend PHP/PostgreSQL, bukan lagi dari JSON statis.
