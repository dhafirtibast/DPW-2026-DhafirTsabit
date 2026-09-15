# Jobsheet 4 — UI/UX Design

Sub-CPMK: Merancang UI/UX aplikasi (proyek).

## Struktur Folder

```
jobsheet-4/
├── anggota/
│   ├── list.html
│   └── tambah.html
├── assets/css/
│   └── style.css
├── buku/
│   ├── list.html
│   └── tambah.html
├── docs/
│   └── wireframe.md
├── README.md
└── index.html
```

## Perubahan dari Jobsheet 3

- Tidak ada perubahan kode — halaman HTML/CSS tetap sama persis dengan Jobsheet 3.
- Tambah `docs/wireframe.md`: wireframe teks + user flow untuk fitur yang **belum dibangun** (Login, Dashboard Petugas, Peminjaman, Pengembalian, Riwayat).

## Cara Menjalankan

Sama seperti Jobsheet 3 — buka `index.html`.

## Catatan

Dokumen `docs/wireframe.md` menjadi acuan struktur HTML baru yang mulai diimplementasikan pada Jobsheet 5 dan seterusnya (interaktivitas JS, lalu PHP/PostgreSQL untuk fitur Login & Peminjaman).

## Refleksi

Jobsheet ini terasa berbeda dari jobsheet-jobsheet sebelumnya karena fokusnya bukan pada penulisan kode, melainkan pada tahap **perancangan (design thinking)** sebelum fitur benar-benar dibangun. Beberapa hal yang dipelajari:

- **Wireframe membantu berpikir sebelum ngoding.** Dengan menuliskan wireframe teks untuk fitur Login, Dashboard, Peminjaman, Pengembalian, dan Riwayat terlebih dahulu, alur data dan kebutuhan tiap halaman jadi lebih jelas sebelum masuk ke implementasi HTML/CSS/JS/PHP.
- **Memisahkan tahap desain dan tahap coding** ternyata memudahkan revisi — mengubah struktur wireframe jauh lebih murah daripada mengubah kode yang sudah jadi.
- **UI/UX bukan sekadar tampilan cantik**, tapi juga soal alur pengguna (user flow): bagaimana petugas berpindah dari login → dashboard → melakukan peminjaman/pengembalian → melihat riwayat, dan memastikan alur itu logis dan efisien.
- Menyimpan dokumentasi desain di folder `docs/` terpisah dari kode membuat proyek lebih rapi dan mudah dijadikan acuan tim (atau diri sendiri) saat implementasi di jobsheet berikutnya.
- Jobsheet ini menjadi jembatan penting: dari sekadar halaman statis (Jobsheet 2–3) menuju aplikasi dengan interaktivitas nyata (JS di Jobsheet 5, lalu backend PHP/PostgreSQL untuk Login & Peminjaman).

Secara keseluruhan, jobsheet ini mengajarkan pentingnya merancang sebelum membangun — kebiasaan yang akan sangat berguna saat proyek menjadi lebih kompleks di jobsheet-jobsheet selanjutnya.