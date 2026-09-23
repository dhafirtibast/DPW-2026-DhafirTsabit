# Fatar's Garage

Website sederhana jual beli motor bekas (terinspirasi OLX), dibangun dengan HTML, CSS, dan JavaScript murni tanpa framework. Data iklan motor dan penjual dimuat secara asinkron dari berkas JSON menggunakan `fetch()`.

## Struktur Folder

fatars-garage/
├── motor/
│   ├── list.html
│   └── tambah.html
├── penjual/
│   ├── list.html
│   └── tambah.html
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       ├── app.js
│       ├── motor.js
│       └── penjual.js
├── data/
│   ├── motor.json
│   └── penjual.json
├── index.html
└── README.md

## Fitur

- **Beranda** dengan pencarian, chip kategori merk, dan kartu iklan terbaru.
- **Daftar Motor** dengan pencarian realtime dan harga berformat Rupiah.
- **Daftar Penjual** dengan pencarian realtime.
- **Form Pasang Iklan** dan **Tambah Penjual** dengan validasi client-side.
- **Hapus** baris pakai konfirmasi (event delegation).
- Indikator loading dan penanganan gagal muat data (`Gagal memuat data: ...`).

## Cara Menjalankan

**Penting:** `fetch()` ke file lokal akan diblokir kebijakan CORS jika dibuka langsung dengan `file://`. Jalankan lewat server lokal, misalnya:

```bash
php -S localhost:8000
lalu buka http://localhost:8000/index.html. Bisa juga memakai ekstensi "Live Server" di VSCode.
Catatan
- Data masih dummy dan hanya dibaca (GET); halaman tidak menulis ke data/*.json, jadi baris yang dihapus tetap muncul lagi saat refresh.
- Fitur Edit belum berfungsi (baru tombol), menunggu backend pada jobsheet berikutnya.

---