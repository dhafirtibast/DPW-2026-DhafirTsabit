# Jobsheet 2: Styling Dasar CSS3  
Learning Outcome: Memahami cara kerja CSS dalam menentukan visual halaman web.  
Pada jobsheet ini, struktur HTML tidak berubah sama sekali dari jobsheet-01 — hanya ditambahkan satu file baru, `assets/css/style.css`, dan satu baris `<link rel="stylesheet">` di tiap halaman HTML.  

## Struktur File dan Direktori

```text
jobsheet-02/
├── index.html
├── assets/
│   └── css/
│       └── style.css      # File baru di jobsheet ini
├── buku/
│   ├── list.html
│   └── tambah.html
├── anggota/
│   ├── list.html
│   └── tambah.html
└── README.md
```  

### Overview `assets/css/style.css`

* Box Model & Universal Reset: Menggunakan `* { box-sizing: border-box; margin: 0; padding: 0; }` untuk mematikan *margin/padding* bawaan browser agar ukuran elemen konsisten.
* Selector CSS mengombinasikan selektor elemen (`header`, `table`), selektor universal (`*`), serta pseudo-class untuk menargetkan elemen HTML secara spesifik tanpa mengubah strukturnya.
* Layouting Flexbox & CSS Grid:
  * Flexbox: Diterapkan pada `<header>` dan `<nav> ul` (`display: flex`) untuk meratakan judul dan menu navigasi secara horizontal.
  * CSS Grid: Diterapkan pada section kartu statistik (`display: grid; grid-template-columns: repeat(3, 1fr)`) untuk membagi kartu ringkasan secara proporsional.
* Pseudo-Class:
  * `:nth-child(even)` dan `:hover` pada tabel untuk membuat efek *zebra-striping* serta *highlight* baris data.
  * `:first-of-type` dan `:last-of-type` untuk memberi warna berbeda pada tombol Edit (`#f0ad4e`) dan Hapus (`#d9534f`) tanpa perlu menambahkan class baru di HTML.
* Konsistensi Form UI: Menata komponen `<label>` menjadi `display: block` dan membatasi lebar elemen `<input>`/`<select>` (`max-width: 400px`) agar bentuk form tetap konsisten dan nyaman digunakan.    

## Modifikasi 
* Mengubah warna tema utama dari biru (`#1d5b8a`) menjadi hijau (`#197706`) pada `header`, `h2`, `thead`, dan tombol submit.  
```diff
  header {
-     background-color: #1d5b8a;
+     background-color: #197706;
      color: #fff;
  }
```    
* Mengubah jumlah kolom statistik dari 3 menjadi 4, serta menambahkan `grid-column: 1 / -1` pada `<h2>` agar judul tidak terikut di dalam kolom grid.  
```diff
  main section:nth-of-type(2) {
      display: grid;
-     grid-template-columns: repeat(3, 1fr);
+     grid-template-columns: repeat(4, 1fr);
      gap: 1rem;
  }

+ main section:nth-of-type(2) h2 {
+     grid-column: 1 / -1;
+ }
```  
* Mengubah warna latar kartu statistik dan efek hover baris tabel dari biru muda (#eef4fa) ke warna krem/merah muda (#fef4fa).
```diff
  tbody tr:hover {
-     background-color: #eef4fa;
+     background-color: #fef4fa;
  }
```
* Menambahkan class `.btn-primary` untuk variasi tambahan tombol detail dengan latar hijau terang (#1ad879).
```diff
  td button:first-of-type {
    background-color: #f0ad4e;
    color: #fff;
  }
  
  td button:last-of-type {
      background-color: #d9534f;
      color: #fff;
  }
  
+ td button.btn-primary {
+     background-color: #1ad879;
+ }
```
### Insight Baru
- CSS memungkinkan perubahan total pada tampilan visual dan warna tanpa mengubah satu baris pun struktur semantic HTML.
- Flexbox sangat ideal untuk layout 1 dimensi seperti navigasi di `<header>`, sedangkan CSS Grid lebih efektif untuk mengatur komponen 2 dimensi seperti kartu statistik di dashboard.
- Penggunaan `:nth-child()`, `:hover`, `:first-of-type`, dan `:last-of-type` mengurangi kebutuhan untuk mengubah elemen HTML dengan class atau id eksternal.
- Aturan `grid-column: 1 / -1` berfungsi elemen judul `<h2>` membentang penuh di atas kolom grid, alih-alih terjebak dan merusak aliran item grid di bawahnya.