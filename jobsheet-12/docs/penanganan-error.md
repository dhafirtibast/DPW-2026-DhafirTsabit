# Penanganan Error — Jobsheet 12 (SIMPUS-Mini)

Dokumen ini merangkum **setiap penanganan error** di aplikasi `jobsheet-12/`:
apa yang ditangani, mengapa bisa terjadi, bagaimana penanganannya, dan di
berkas mana. Disusun setelah audit ulang kode Jobsheet 12 (integrasi modul
peminjaman).

> Dokumen ini terpisah dari `README.md` dan `DEPLOYMENT.md`.

---

## 1. Ringkasan

| # | Jenis error | Penyebab umum | Penanganan | Lokasi |
|---|---|---|---|---|
| 1 | Gagal koneksi database | host/port/password/db salah, server mati | `try/catch PDOException` + `die()` pesan jelas | `includes/koneksi.php` |
| 2 | CSRF tidak valid | POST tanpa token / token kedaluwarsa | HTTP **403** + hentikan eksekusi | `includes/csrf.php` |
| 3 | Akses tanpa login | belum ada `$_SESSION['user_id']` | redirect ke login + `exit` | `includes/auth.php` |
| 4 | Input tidak valid | field kosong, tahun/stok di luar rentang | kumpulkan pesan → flash → redirect | `*/proses_*.php` |
| 5 | Duplikat `no_anggota` | nomor anggota sudah dipakai | pre-check `SELECT` → flash error | `anggota/proses_tambah.php`, `anggota/proses_edit.php` |
| 6 | Duplikat `username` | username sudah terdaftar | pre-check `SELECT` → flash error | `auth/proses_register.php` |
| 7 | FK violation saat hapus | buku/anggota masih dirujuk `peminjaman` | pre-check riwayat + `try/catch PDOException` | `buku/hapus.php`, `anggota/hapus.php` |
| 8 | Stok tidak konsisten / race condition | dua peminjaman bersamaan | transaction + `SELECT ... FOR UPDATE` + `rollBack()` | `peminjaman/proses_tambah.php`, `peminjaman/proses_kembali.php` |
| 9 | `id` bukan angka | `?id=abc` / hidden field dimanipulasi | cast `(int)` + guard `<= 0` | `*/edit.php`, `*/proses_edit.php`, `*/hapus.php`, `peminjaman/riwayat.php` |
| 10 | Login gagal | user tidak ada / password salah | pesan **generik** (anti user-enumeration) | `auth/proses_login.php` |
| 11 | Error tidak terlihat user | — | flash message + escape `e()` | semua view |
| 12 | Input salah sisi klien | field kosong sebelum submit | validasi JS + `preventDefault()` | `assets/js/app.js` |
| 13 | Sesi tersisa setelah logout | — | kosongkan `$_SESSION`, hapus cookie, `session_destroy()` | `auth/logout.php` |

---

## 2. Detail per penanganan

### 2.1 Gagal koneksi database
**`includes/koneksi.php`**
```php
try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
```
- Menangkap kegagalan koneksi dan berhenti dengan pesan yang bisa dibaca,
  bukan stack trace mentah.
- `ERRMODE_EXCEPTION` membuat semua error query berikutnya melempar
  `PDOException` — dasar bagi penanganan di poin 2.7 dan 2.8.

### 2.2 CSRF (form POST dipalsukan / kedaluwarsa)
**`includes/csrf.php`**
```php
function csrf_verify() {
    $token = $_POST['csrf_token'] ?? '';
    if ($token === '' || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('Permintaan ditolak: token CSRF tidak valid atau kedaluwarsa.');
    }
}
```
- Dipanggil di semua `proses_*.php` dan `hapus.php`, **setelah** `auth.php`,
  sehingga pengunjung belum-login di-redirect lebih dulu (tidak sampai cek CSRF).
- Membalas HTTP **403** dan menghentikan eksekusi, tidak memproses data.

### 2.3 Guard autentikasi
**`includes/auth.php`**
```php
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}
```
- Mencegah akses halaman terkunci. `exit` wajib agar kode di bawahnya tidak
  tetap dieksekusi setelah `header()`.

### 2.4 Validasi input (aturan bisnis)
**`buku/proses_tambah.php`, `buku/proses_edit.php`, `anggota/proses_*.php`, `auth/proses_register.php`**
```php
$errors = [];
if ($judul === '') { $errors[] = "Judul wajib diisi."; }
if (!is_numeric($tahun) || $tahun < 1900 || $tahun > 2026) { $errors[] = "..."; }
if (!in_array($kategori, ['fiksi', 'non-fiksi', 'referensi'], true)) { $errors[] = "Kategori tidak valid."; }
if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: tambah.php'); exit;
}
```
- Mengumpulkan **semua** error sekaligus lalu dikembalikan ke form (flash).
- Redirect-after-POST mencegah resubmit saat halaman di-refresh.

### 2.5 Duplikat `no_anggota`
**`anggota/proses_tambah.php`** dan **`anggota/proses_edit.php`**
```php
$cek = $pdo->prepare("SELECT id FROM anggota WHERE no_anggota = :no_anggota");
$cek->execute(['no_anggota' => $noAnggota]);
if ($cek->fetch()) { /* flash "No. Anggota sudah digunakan." */ }
```
- **Pre-check** sebelum `INSERT`/`UPDATE` supaya pengguna menerima pesan ramah,
  bukan `PDOException` dari constraint `UNIQUE` (`anggota.no_anggota`).
- Versi edit menambah `AND id <> :id` agar menyimpan tanpa mengubah nomor tidak
  dianggap duplikat.

### 2.6 Duplikat `username`
**`auth/proses_register.php`**
```php
$cek = $pdo->prepare("SELECT id FROM users WHERE username = :username");
$cek->execute(['username' => $username]);
if ($cek->fetch()) { /* flash "Username sudah digunakan" */ }
```
- Pola sama dengan 2.5, mencegah pelanggaran `UNIQUE` pada `users.username`.

### 2.7 Foreign key violation saat hapus
**`buku/hapus.php`** dan **`anggota/hapus.php`**
```php
$id = (int) ($_POST['id'] ?? 0);
if ($id > 0) {
    try {
        $cek = $pdo->prepare("SELECT COUNT(*) FROM peminjaman WHERE buku_id = :id");
        $cek->execute(['id' => $id]);
        if ($cek->fetchColumn() > 0) {
            $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Buku tidak bisa dihapus karena masih punya riwayat peminjaman.'];
        } else {
            $stmt = $pdo->prepare("DELETE FROM buku WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Buku berhasil dihapus.'];
        }
    } catch (PDOException $e) {
        $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Gagal menghapus buku: ' . $e->getMessage()];
    }
}
```
- **Latar masalah:** `peminjaman.buku_id`/`anggota_id` memakai FK tanpa
  `ON DELETE` (default `NO ACTION`). Menghapus buku/anggota yang sudah punya
  riwayat peminjaman akan ditolak PostgreSQL:
  `SQLSTATE[23503] ... violates foreign key constraint`.
- **Penanganan berlapis:** (a) pre-check jumlah riwayat → pesan ramah tanpa
  menyentuh `DELETE`; (b) `try/catch PDOException` sebagai jaring pengaman bila
  tetap lolos. Hasilnya flash error, **bukan** fatal error mentah.

### 2.8 Transaksi stok (atomik & anti race condition)
**`peminjaman/proses_tambah.php`** dan **`peminjaman/proses_kembali.php`**
```php
try {
    $pdo->beginTransaction();
    $cek = $pdo->prepare("SELECT stok FROM buku WHERE id = :id FOR UPDATE");
    $cek->execute(['id' => $bukuId]);
    $buku = $cek->fetch(PDO::FETCH_ASSOC);
    if (!$buku || $buku['stok'] < 1) {
        throw new Exception('Stok buku tidak tersedia.');
    }
    // INSERT peminjaman + UPDATE stok - 1
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Gagal ...: ' . $e->getMessage()];
}
```
- `beginTransaction/commit/rollBack`: bila salah satu langkah gagal, seluruh
  perubahan dibatalkan (stok & transaksi tidak setengah jalan).
- `FOR UPDATE`: mengunci baris buku agar stok tidak dibaca ganda oleh proses
  bersamaan, mencegah stok menjadi negatif.
- `throw new Exception(...)` sengaja dipakai untuk memicu `rollBack()`.

### 2.9 Cast `(int)` pada `id`
**`buku/edit.php`, `buku/proses_edit.php`, `anggota/edit.php`, `anggota/proses_edit.php`, `*/hapus.php`, `peminjaman/riwayat.php`**
```php
$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: list.php'); exit; }
```
- Mencegah `?id=abc` memicu
  `SQLSTATE[22P02]: invalid input syntax for type integer` (fatal error).
- Pada `peminjaman/riwayat.php`, guard memakai `if ($anggotaId > 0)` karena
  `$anggotaId` kini bertipe integer.

### 2.10 Login gagal
**`auth/proses_login.php`**
```php
if ($user && password_verify($password, $user['password'])) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    ...
}
$_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Username atau password salah.'];
```
- Pesan **generik** untuk "user tidak ada" maupun "password salah" — mencegah
  user enumeration.
- `session_regenerate_id(true)` setelah login sukses menutup session fixation.

### 2.11 Flash message (menyalurkan error ke UI)
- **Set** di `proses_*.php`/`hapus.php`: `$_SESSION['flash'] = ['type' => ..., 'pesan' => ...]`.
- **Ambil & hapus** di view: `$flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);`
- **Tampilkan** dengan `e($flash['pesan'])` — output di-escape untuk mencegah XSS.

### 2.12 Validasi sisi klien
**`assets/js/app.js`**
- `initHapusConfirm()`: `confirm()` membatalkan submit bila pengguna menolak.
- `initValidasiForm()`: cek field wajib, rentang tahun/harga, tampilkan `.error`.
- Ini hanya lapisan UX; validasi server (poin 2.4) tetap otoritatif.

### 2.13 Logout aman
**`auth/logout.php`** — kosongkan `$_SESSION`, hapus cookie sesi, lalu
`session_destroy()`, agar tidak ada sesi tersisa.

---

## 3. Catatan bug yang ditemukan saat audit & perbaikannya

Selama audit ulang Jobsheet 12 ditemukan dua bug yang membuat penanganan error
**tidak berjalan sebagaimana mestinya**:

### 3.1 Array key salah pada pre-check hapus
**Gejala:** hapus buku/anggota selalu gagal; pesan "tidak bisa dihapus karena
masih punya riwayat" tidak pernah muncul, yang tampil justru pesan SQLSTATE.

**Penyebab** (`buku/hapus.php`, `anggota/hapus.php`):
```php
$cek->execute(['id => $id']);   // salah: string literal "id => $id"
```
Parameter `:id` menerima string `"id => $id"` → PostgreSQL menolak:
`SQLSTATE[22P02]: invalid input syntax for type integer: "id => $id"`.

**Perbaikan:**
```php
$cek->execute(['id' => $id]);
```

### 3.2 Kolom "Aksi" ganda / tidak konsisten
**Gejala:** saat login, tabel Daftar Buku punya 6 header tetapi 5 sel (tabel
rusak); saat belum login, header Aksi tetap tampil.

**Penyebab** (`buku/list.php`): ada dua `<th>Aksi</th>` — satu di dalam guard
`$sudahLogin` dan satu statis. Selain itu `colspan` baris kosong masih tetap
`5`, dan `anggota/list.php` menampilkan sel Aksi tanpa guard.

**Perbaikan:**
- Hapus `<th>Aksi</th>` statis; sisakan yang di dalam `if ($sudahLogin)`.
- `colspan` baris kosong dijadikan kondisional:
  `<?php echo $sudahLogin ? 5 : 4; ?>`.
- Bungkus sel `<td>` Aksi di `anggota/list.php` dengan `if ($sudahLogin)`.

Setelah perbaikan: `php -l` seluruh berkas PHP lolos, dan jumlah kolom
header = jumlah sel pada kedua tabel, baik saat login maupun tidak.

---

## 4. Verifikasi

| Uji | Ekspektasi |
|---|---|
| Hapus buku/anggota berriwayat peminjaman | flash error "tidak bisa dihapus…" (bukan SQLSTATE) |
| Hapus buku/anggota tanpa riwayat | sukses |
| Buka `/buku/edit.php?id=abc` | redirect ke daftar (bukan fatal) |
| `/buku/list.php` tanpa login | tepat 4 kolom, tanpa Aksi |
| `/buku/list.php` saat login | 5 kolom, satu Aksi, header = sel |
| `/anggota/list.php` tanpa login | tepat 4 kolom, tanpa Aksi |
| POST `proses_tambah.php` tanpa `csrf_token` | HTTP 403 |
| Tambah anggota dengan `no_anggota` duplikat | flash "No. Anggota sudah digunakan." |
| Riwayat tanpa pilih anggota | tidak menjalankan query, tanpa error |
