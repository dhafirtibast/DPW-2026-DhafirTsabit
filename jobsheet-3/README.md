# Jobsheet 3 — Responsive Design

Sub-CPMK: Membangun tampilan responsif.

## Struktur Folder

```
jobsheet-3/
├── anggota/
│   └── list.html
├── assets/css/
│   └── style.css
├── buku/
│   └── list.html
└── index.html
```

## Perubahan dari Jobsheet 2

- Tambah `<meta name="viewport">` di semua halaman.
- Navbar: hamburger menu memakai teknik **checkbox hack** murni CSS (`input[type=checkbox] + label`), aktif di layar ≤480px.
- Tabel dibungkus `<div class="table-responsive">` agar bisa di-scroll horizontal di layar sempit.
- Tambah media query di `style.css`: grid kartu statistik 3 → 2 → 1 kolom mengikuti breakpoint tablet/mobile.

## Cara Menjalankan

Buka `index.html` di browser, uji dengan DevTools responsive mode pada 3 breakpoint (mobile ≤480px, tablet ~768px, desktop ≥1024px).

## Catatan

- Hamburger di jobsheet ini masih murni CSS (checkbox hack). Di Jobsheet 5 akan diganti dengan toggle berbasis JavaScript.

## Refleksi

Jobsheet ini membantu memahami bagaimana membuat tampilan web yang benar-benar responsif tanpa bergantung pada JavaScript maupun framework CSS pihak ketiga. Beberapa hal yang dipelajari:

- **Meta viewport itu wajib.** Tanpa `<meta name="viewport">`, media query yang sudah dibuat tidak akan berfungsi sebagaimana mestinya di perangkat mobile karena browser akan merender halaman pada lebar default desktop.
- **Checkbox hack** ternyata cukup elegan untuk kasus sederhana seperti toggle menu hamburger, karena tidak memerlukan satu baris JavaScript pun. Namun pendekatan ini punya keterbatasan (misalnya sulit menambahkan animasi kompleks atau logika tambahan), sehingga wajar jika nanti digantikan JavaScript di jobsheet berikutnya.
- **Tabel responsif** dengan `.table-responsive` (scroll horizontal) adalah solusi praktis dibanding memaksa tabel menyempit dan merusak keterbacaan data.
- **Mobile-first vs desktop-first** dalam menyusun media query cukup berpengaruh pada kerapian kode; grid 3→2→1 kolom lebih mudah dipahami alurnya ketika breakpoint disusun berurutan dari layar besar ke kecil.
- Menguji langsung dengan DevTools pada tiga breakpoint (mobile, tablet, desktop) sangat membantu menemukan elemen yang "patah" atau tidak proporsional, dan mendorong kebiasaan checking desain di banyak ukuran layar sebelum dianggap selesai.