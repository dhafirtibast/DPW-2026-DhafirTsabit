# Jobsheet 1: Struktur Semantic HTML5
Learning Outcome: Menyusun struktur halaman web dengan HTML5 semantic.  
(Belum ada CSS/JS)  
  
Jobsheet ini berfokus pada penyusunan arsitektur halaman web **SIMPUS-Mini** menggunakan standar **Semantic HTML5** (tanpa elemen non-semantic seperti `<div>`) serta penerapan alur navigasi berbasis *relative path* pada struktur direktori bertingkat.

---

## Struktur File dan Direktori

```text
simpus-mini/
├── index.html             # Dashboard & Ringkasan Statistik
├── buku/                  # Modul Entitas Buku
│   ├── list.html          # Data Grid / Tabel Buku (7 Data)
│   └── tambah.html        # Form Input Buku
└── anggota/               # Modul Entitas Anggota
    ├── list.html          # Data Grid / Tabel Anggota (+ Kolom Email)
    └── tambah.html        # Form Input Anggota (+ Input Email)
```  

## Overview Konsep Dasar
* HTML ditulis memakai tag yang biasanya berpasangan: **tag** pembuka <namatag> dan tag penutup </namatag>. Isi di antara keduanya disebut **elemen**.
* Setiap halaman HTML wajib memiliki struktur berikut:  

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SIMPUS-Mini | Beranda</title>
</head>
<body>
    ...
</body>
</html>
```  
* "Semantic" artinya nama tag yang menjelaskan **makna/peran** kontennya, tidak seperti `<div>` yang merupakan kotak kosong. 
* Jobsheet ini tidak menggunakan `<div>` karena `<div>` tidak mendefinisikan apapun untuk browser, tetapi dengan semantic, kode lebih mudah untuk dibaca dan diakses oleh SEO.  

### Tag Semantic HTML5
- **`<header>`** — bagian kepala halaman, biasanya berisi judul situs dan
  navigasi.
- **`<nav>`** — kelompok tautan navigasi (menu). Di dalamnya dipakai
  `<ul>`/`<li>`/`<a>` biasa, tapi dibungkus `<nav>` supaya browser/screen
  reader tahu ini adalah "menu", bukan daftar isi biasa.
- **`<main>`** — konten **utama** halaman. Hanya boleh ada **satu**
  `<main>` per halaman.
- **`<section>`** — mengelompokkan konten yang temanya sama, biasanya
  diawali sebuah heading (`<h2>`, dst.).
- **`<article>`** — potongan konten yang berdiri sendiri/bisa dipisah dari
  konteks sekitarnya.
- **`<footer>`** — bagian kaki halaman, biasanya berisi copyright/info
  tambahan. Sama di semua halaman: `© 2026 SIMPUS-Mini — Jobsheet 1`.

### Navigasi Antar Halaman

| Lokasi File (`Source`) | Target Navigasi | Relative Path | Keterangan Mechanism |
| :--- | :--- | :--- | :--- |
| `index.html` | Daftar Buku | `buku/list.html` | Turun 1 level ke folder `buku/` |
| `buku/list.html` | Beranda | `../index.html` | Naik 1 level via `../` ke root |
| `buku/list.html` | Tambah Buku | `tambah.html` | Sejajar (dalam folder `buku/`) |
| `anggota/list.html` | Daftar Buku | `../buku/list.html` | Naik ke root `../`, lalu masuk ke `buku/` |



## Modifikasi 
* Modifikasi menu pada `<nav>` agar lebih lengkap dan konsisten pada file `index.html`, `buku/list.html`, `buku/tambah.html`, `anggota/list.html`, dan `anggota/tambah.html`.  
* Menambahkan dua baris data buku baru di `buku/list.html`
```html
<tr>
    <td>Prinsipil Ekonomi</td>
    <td>Ferry Irwandi</td>
    <td>2026</td>
    <td>7</td>
    <td>
        <button type="button">Edit</button>
        <button type="button">Hapus</button>
    </td>
</tr>
<tr>
    <td>The Alchemist</td>
    <td>Paulo Coelho</td>
    <td>1988</td>
    <td>3</td>
    <td>
        <button type="button">Edit</button>
        <button type="button">Hapus</button>
    </td>
</tr>
```
* Menambahkan kolom **Email** pada file `anggota/list.html`
```diff
<thead>
    <tr>
        <th>No. Anggota</th>
        <th>Nama</th>
        <th>Alamat</th>
        <th>No. HP</th>
+       <th>Email</th> 
        <th>Aksi</th>
    </tr>
</thead>
```
* Menambahkan field "Email" setelah field "No. HP" pada form tambah anggota (file `anggota/tambah.html`)
```diff
  <p>
      <label for="no_hp">No. HP</label><br>
      <input type="text" name="no_hp" id="no_hp">
  </p>
+ <p>
+     <label for="email">Email</label><br>
+     <input type="email" name="email" id="email">
+ </p>
  <p>
      <button type="submit">Simpan</button>
  </p>
```
### Insight yang Baru Dipelajari
- Pentingnya Semantic HTML dalam membentuk struktur Pemrograman Web
- Kesalahan pada penulisan path pada struktur folder yang bertingkat akan langsung memutus alur navigasi.
- HTML5 mampu menangani validasi tipe data (seperti format email, range angka) secara independen di sisi klien sebelum data dikirimkan ke backend.