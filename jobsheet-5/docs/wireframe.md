# Wireframe SIMPUS-Mini

## 1. Halaman Login

```
+-----------------------------------------+
|               SIMPUS-Mini               |
+-----------------------------------------+
|                                         |
|            [ Login Petugas ]            |
|                                         |
|   Username : [______________________]   |
|   Password : [______________________]   |
|                                         |
|              [    Masuk    ]            |
|                                         |
|    Belum punya akun? Daftar di sini     |
+-----------------------------------------+
```

---

## 2. Dashboard Petugas

```
+-----------------------------------------------------------------------------+
| SIMPUS-Mini   Beranda | Buku | Anggota | Peminjaman | (Nama Petugas) Logout |
+-----------------------------------------------------------------------------+
|                                                                             |
|   [ Total Buku ]     [ Total Anggota ]     [ Sedang Dipinjam ]              |
|                                                                             |
|   Aksi Cepat:                                                               |
|   [ + Peminjaman Baru ]     [ + Pengembalian ]                              |
|                                                                             |
|   Transaksi Terbaru                                                         |
|   --------------------------------------------------------------------------|
|   Anggota      | Buku            | Tgl Pinjam   |       Status              |
|                                                                             |
+-----------------------------------------------------------------------------+
```

---

## 3. Form Peminjaman

```
+------------------------------------------------+
|   Form Peminjaman Buku                         |
+------------------------------------------------+
|   Anggota        : [ dropdown pilih anggota ]  |
|   Buku           : [ dropdown, hanya stok>0 ]  |
|   Tanggal Pinjam : [ auto: hari ini ]          |
|                                                |
|            [  Simpan Peminjaman  ]             |
+------------------------------------------------+
```

---

## 4. Form Pengembalian

```
+--------------------------------------------------+
|   Pengembalian Buku                              |
+--------------------------------------------------+
|   Cari transaksi aktif:                          |
|   [ nama anggota / judul buku __________ ]       |
|                                                  |
|   Anggota   | Buku   | Tgl Pinjam | Aksi         |
|   --------------------------------------------   |
|   ...       | ...    | ...        | [Kembalikan] |
+--------------------------------------------------+
```

---

## 5. Riwayat Peminjaman per Anggota

```
+------------------------------------------------------+
|   Riwayat Peminjaman — Siti Aminah                   |
+------------------------------------------------------+
|   Buku            | Pinjam  | Kembali | Status       |
|   ---------------------------------------------------|
|   Laskar Pelangi  | 01/07   | 10/07   | Selesai      |
|   Bumi Manusia    | 15/07   | -       | Dipinjam     |
+------------------------------------------------------+
```

---

## 6. Kelola Buku

Halaman bagi petugas untuk menambah, mengubah, dan menghapus data buku, lengkap dengan pencarian dan pengelolaan stok.

```
+-------------------------------------------------------------------------+
|   Kelola Buku                              [ + Tambah Buku Baru ]       |
+-------------------------------------------------------------------------+
|   Cari judul / pengarang / ISBN : [____________________] [Cari]         |
|                                                                         |
|   Judul            | Pengarang     | ISBN        | Stok | Aksi          |
|   ----------------------------------------------------------------------|
|   Laskar Pelangi   | Andrea Hirata | 978-xxxxxxx | 3    | [Edit][Hapus] |
|   Bumi Manusia     | Pramoedya A.T.| 978-xxxxxxx | 0    | [Edit][Hapus] |
|                                                                         |
+-------------------------------------------------------------------------+

Form Tambah/Edit Buku (modal atau halaman terpisah):
+------------------------------------------+
|   Judul       : [______________________] |
|   Pengarang   : [______________________] |
|   ISBN        : [______________________] |
|   Jumlah Stok : [______]                 |
|                                          |
|          [  Simpan  ]   [  Batal  ]      |
+------------------------------------------+
```