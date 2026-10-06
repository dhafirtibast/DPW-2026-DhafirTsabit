# Deployment SIMPUS-Mini (Jobsheet 11) — dari Jobsheet Murni ke Hosting Publik

Dokumen ini menjelaskan **secara lengkap dan berurutan** bagaimana `jobsheet-11/`
(aplikasi PHP + PostgreSQL murni untuk praktikum, dengan perbaikan keamanan
XSS/CSRF/session fixation) diubah menjadi aplikasi yang **live dan dapat
diakses publik**, tanpa mengubah fitur/materi jobsheet-nya.

> Dokumen ini **terpisah** dari README jobsheet. Isinya memuat langkah
> operasional + rujukan kredensial, jadi jangan dipublikasikan apa adanya.

---

## 0. Hasil Akhir

| Item | Nilai |
|---|---|
| Hosting aplikasi | **Railway** (Web Service, Docker) |
| Database | **Supabase** (PostgreSQL, Session Pooler) |
| Repositori | `https://github.com/dhafirtibast/DPW-2026-DhafirTsabit` |
| Folder aplikasi | `jobsheet-11/` (Root Directory di Railway) |
| Runtime | PHP 8.1 + Apache (`php:8.1-apache`) + ekstensi `pdo_pgsql` |

> **Catatan:** `jobsheet-11/` adalah **service terpisah** dari `jobsheet-10/`
> (Root Directory berbeda). Bila ingin keduanya live, buat **service Railway
> baru** dengan Root Directory `jobsheet-11` (jangan mengubah service lama),
> lalu **Generate Domain** untuk mendapat URL publiknya sendiri.

Arsitektur:

```
GitHub repo ──push──► Railway (Docker: PHP 8.1 + Apache) ──PDO/pgsql──► Supabase Postgres
                        https://<service>.up.railway.app                 (Session Pooler, IPv4)
```

---

## 1. Konsep: Kenapa Butuh Docker + Supabase

Aplikasi jobsheet adalah **PHP murni** yang:
- butuh **runtime PHP** (bukan hosting statis seperti GitHub Pages/Netlify),
- memakai **PostgreSQL** (`ILIKE`, `SERIAL`, `RETURNING`, PDO driver `pgsql`),
- memakai **session file-based** (`$_SESSION` untuk autentikasi & token CSRF).

Maka dipilih:
- **Railway** menjalankan aplikasi PHP **via Dockerfile** (bisa inject env var, gratis dengan kredit trial).
- **Supabase** menyediakan PostgreSQL terkelola (gratis, tanpa kartu kredit).

> Catatan: hosting gratis MySQL (InfinityFree dll.) **tidak cocok** — MySQL tidak
> mendukung sintaks yang dipakai, dan beberapa memblokir koneksi DB keluar.

---

## 2. Prasyarat

- Git terpasang, repo sudah ada di GitHub.
- Akun **Railway** (login via GitHub) — https://railway.com
- Akun **Supabase** — https://supabase.com
- (Opsional, untuk seed) `psql` lokal.

---

## 3. Langkah 1 — Siapkan Database di Supabase

1. Daftar/masuk https://supabase.com → **New Project**.
2. Isi **Name**, **Database Password** (simpan!), dan **Region** terdekat
   (contoh: `ap-northeast-1` / Tokyo).
3. Setelah project jadi, catat **Project Reference ID**:
   **Project Settings → General → Reference ID**.
4. Ambil **connection string pooler**: **Connect → Session pooler**.
   Formatnya:
   ```
   postgresql://postgres.<project-ref>:<PASSWORD>@aws-0-<region>.pooler.supabase.com:5432/postgres
   ```
   - Gunakan **Session pooler (port 5432)** — mendukung **prepared statement**
     yang dipakai PDO. **Jangan** Transaction pooler (port 6543).
   - **Jangan** pakai direct `db.<ref>.supabase.co` (IPv6-only; butuh add-on IPv4).

> **Bisa pakai project Supabase yang sama** dengan `jobsheet-10` (skema tabel
> identik). Bila memakai project yang sama, tabel sudah ada → lewati Langkah 2.
> Bila ingin isolasi, buat project Supabase baru.

### Nilai yang dipakai pada deployment ini

| Field | Nilai |
|---|---|
| Region | `ap-northeast-1` |
| Host | `aws-0-ap-northeast-1.pooler.supabase.com` |
| Port | `5432` |
| Database | `postgres` |
| User | `postgres.<project-ref>` |
| Password | (password database Supabase — dari dashboard) |
| SSL | wajib `sslmode=require` |

---

## 4. Langkah 2 — Seed Skema ke Supabase

Aplikasi **tidak** membuat tabel otomatis; tabel harus dibuat manual.
Dua file skema ada di `jobsheet-11/sql/`:

- `01_buku_anggota.sql` → tabel `buku`, `anggota`
- `02_users.sql` → tabel `users`

### Cara A — Supabase SQL Editor (tanpa kredensial)
1. Dashboard Supabase → **SQL Editor → New query**.
2. Tempel isi `sql/01_buku_anggota.sql` → **Run**.
3. Query baru → tempel isi `sql/02_users.sql` → **Run**.

### Cara B — `psql` dari lokal
```bash
psql "postgresql://postgres.<ref>:<PASSWORD>@aws-0-ap-northeast-1.pooler.supabase.com:5432/postgres?sslmode=require" -f sql/01_buku_anggota.sql
psql "postgresql://postgres.<ref>:<PASSWORD>@aws-0-ap-northeast-1.pooler.supabase.com:5432/postgres?sslmode=require" -f sql/02_users.sql
```

### Verifikasi
```sql
SELECT tablename FROM pg_tables WHERE schemaname='public' ORDER BY tablename;
-- harus: anggota, buku, users
```

---

## 5. Langkah 3 — Adaptasi Kode di `jobsheet-11/`

Tiga berkas deployment (semuanya sudah ada di repo):

### 5.1 `Dockerfile` (BARU)
Menjalankan PHP+Apache, memasang driver PostgreSQL, memaksa satu MPM, dan
mendengarkan `$PORT` yang disuntik Railway.

```dockerfile
FROM php:8.1-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev \
    && docker-php-ext-install pdo_pgsql pgsql \
    && rm -rf /var/lib/apt/lists/*

# Pastikan hanya satu MPM (prefork) termuat, mencegah error
# "AH00534: apache2: Configuration error: More than one MPM loaded."
RUN a2dismod mpm_event 2>/dev/null || true; \
    a2dismod mpm_worker 2>/dev/null || true; \
    a2enmod mpm_prefork

COPY . /var/www/html/

EXPOSE 10000

CMD ["sh", "-c", "a2dismod mpm_event 2>/dev/null; a2dismod mpm_worker 2>/dev/null; a2enmod mpm_prefork; sed -i \"s/Listen 80/Listen ${PORT:-10000}/g\" /etc/apache2/ports.conf && sed -i \"s/:80>/:${PORT:-10000}>/g\" /etc/apache2/sites-available/000-default.conf && apache2-foreground"]
```

Poin penting:
- `pdo_pgsql` → driver PostgreSQL untuk Supabase.
- `a2dismod mpm_event/worker` + `a2enmod mpm_prefork` → mencegah MPM ganda.
- `COPY . /var/www/html/` → document root = root proyek, sehingga path
  relatif (`$base` di `header.php`, guard `../auth/login.php`) tetap valid.
- Apache aslinya listen `:80`; di-*rewrite* ke `${PORT:-10000}`.

### 5.2 `.dockerignore` (BARU)
Mengecualikan berkas sensitif/tidak perlu agar image ramping:
```
.git
.gitignore
.gitattributes
.env
.env.*
.neon
node_modules
.agents
skills-lock.json
package.json
package-lock.json
neon.ts
README.md
docs
sql
```

### 5.3 `includes/koneksi.php` (DIUBAH)
Membaca kredensial berlapis, **`DATABASE_URL` diprioritaskan**:
1. `DATABASE_URL` (mis. Supabase/Railway/Neon — paling otoritatif)
2. `DB_HOST`/`DB_PORT`/`DB_NAME`/`DB_USER`/`DB_PASS`/`DB_SSLMODE`
3. `includes/config.local.php` (khusus server, tidak di-commit)
4. Default lokal Laragon (`localhost:5433/simpus_mini`)

Setelah itu DSN dibangun:
```php
$dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=$sslmode$endpoint";
$pdo = new PDO($dsn, $user, $pass);
```
(`$endpoint` hanya ditambahkan untuk host `*.neon.tech`, tidak mengganggu Supabase.)

### 5.4 `.gitignore` (BARU)
Menutup berkas rahasia/lokal dari git:
```
.neon
.env
.env.local
node_modules
includes/config.local.php
```

> **Tidak ada perubahan khusus deployment untuk perbaikan keamanan Jobsheet 11**
> (`includes/helpers.php`, `includes/csrf.php`, `session_regenerate_id`). Semua
> berjalan normal di container. Perlu diingat: **token CSRF disimpan di
> `$_SESSION`**, jadi bila session hilang saat redeploy, pengguna perlu login ulang.

---

## 6. Langkah 4 — Push ke GitHub

```bash
git add jobsheet-11
git commit -m "deploy: Railway + Supabase untuk jobsheet-11 (Dockerfile, koneksi env)"
git push origin main
```
Pastikan `.env.local`, `includes/config.local.php` **tidak** ikut ter-commit
(sudah di-`.gitignore`).

---

## 7. Langkah 5 — Deploy di Railway

1. https://railway.com/new → **Deploy from GitHub repo** → pilih repo
   `dhafirtibast/DPW-2026-DhafirTsabit`.
   - **Bila `jobsheet-10` sudah ter-deploy**: buat **service baru** dalam
     project yang sama (atau project baru) — jangan ubah Root Directory
     service lama.
2. Buka service → **Settings → Source/Build**:
   - **Builder**: `Dockerfile`
   - **Root Directory**: `jobsheet-11`
   - **Dockerfile Path**: `Dockerfile`
   - **Branch**: `main`
3. **Settings → Networking → Generate Domain** → dapat
   `https://<nama>.up.railway.app`.
   Pastikan **target port = 10000** (samakan dengan yang didengarkan app).
4. **Variables** (lihat Langkah 6).
5. Railway build image Docker → status **Active**.

> Port: aplikasi listen di `${PORT:-10000}`. Railway menyuntik `PORT` sendiri;
> `EXPOSE 10000` hanya metadata. Bila target port domain tidak cocok,
> muncul *"Application failed to respond"* (502).

---

## 8. Langkah 6 — Environment Variables di Railway

Set di **Variables** service:

| Key | Value |
|---|---|
| `DATABASE_URL` | `postgresql://postgres.<ref>:<PASSWORD>@aws-0-ap-northeast-1.pooler.supabase.com:5432/postgres?sslmode=require` |

Cukup `DATABASE_URL` (diprioritaskan `koneksi.php`). Bila ingin eksplisit,
`DB_*` boleh ditambahkan sebagai cadangan — tetapi **harus konsisten**, karena
nilai `DB_*` yang salah dapat membingungkan saat debugging.

---

## 9. Langkah 7 — Verifikasi

| Uji | Ekspektasi |
|---|---|
| `GET /` | 200 — Beranda + kartu statistik |
| `GET /buku/list.php` | 200 — katalog publik (bisa diakses tanpa login) |
| `GET /auth/login.php`, `/auth/register.php` | 200 |
| `GET /buku/tambah.php` (tanpa login) | **302 → `/auth/login.php`** (guard) |
| `GET /anggota/list.php` (tanpa login) | **302 → `/auth/login.php`** |
| `/auth/register.php` → daftar akun | berhasil, lalu bisa Login |
| Login → `/buku/tambah.php` | 200 (halaman terkunci terbuka) |
| POST `/buku/proses_tambah.php` tanpa `csrf_token` | **403** (proteksi CSRF) |
| Tambah buku judul `<script>alert(1)</script>` | tampil sebagai **teks** (proteksi XSS) |

Contoh cek cepat:
```bash
curl -s -o /dev/null -w "%{http_code} %{redirect_url}\n" https://<domain>/buku/tambah.php
curl -s -o /dev/null -w "%{http_code}\n" -X POST https://<domain>/buku/proses_tambah.php -d "judul=x"
```

---

## 10. Troubleshooting (Masalah Nyata yang Ditemui)

### 10.1 `502 Application failed to respond`
Penyebab: **deployment/domain tidak mengarah ke service app** atau **port mismatch**.
- Cek **Settings → Networking → target port** = `10000`.
- Cek tab **Deployments**: apakah deployment aktif? Bila perlu **Redeploy**.
- Pastikan **Root Directory** = `jobsheet-11` dan **Builder** = `Dockerfile`.

### 10.2 `404 Not Found` + header `x-railway-fallback: true`
Railway Edge menjawab fallback → **tidak ada deployment aktif** untuk domain itu
atau domain menempel ke service yang salah. Perbaiki deployment/domain.

### 10.3 `AH00534: apache2: Configuration error: More than one MPM loaded`
Apache memuat lebih dari satu Multi-Processing Module. Solusi: pada build **dan**
runtime, nonaktifkan `mpm_event`/`mpm_worker` lalu aktifkan `mpm_prefork`
(sudah ada di `Dockerfile`). Muncul juga bila Railway mengabaikan Dockerfile
dan membangun sendiri → pastikan **Builder = Dockerfile**.

### 10.4 `password authentication failed for user "postgres"`
- Pada Supabase pooler, pesan selalu menyebut base user `postgres`
  (suffix `<ref>` di-strip). Jadi error ini berarti **password salah**, bukan
  username salah.
- Penyebab umum: password di Railway berbeda dengan yang berhasil di lokal,
  atau di-copy dengan spasi/kutip.
- Solusi: **Project Settings → Database → Reset database password**, lalu
  perbarui `DATABASE_URL` di Railway.

### 10.5 Error: `relation "buku" does not exist`
Tabel belum dibuat → jalankan **Langkah 2 (Seed Skema)**.

### 10.6 `file_get_contents(.../sql/...): No such file` (saat seed via web)
`sql/` dikecualikan `.dockerignore` sehingga tidak ada di image.
Seed via Supabase SQL Editor / `psql`, atau inline SQL-nya.

### 10.7 `403 Permintaan ditolak: token CSRF tidak valid`
Bukan bug — ini proteksi CSRF bekerja. Terjadi bila POST dilakukan tanpa
`csrf_token` (mis. via `curl`/situs lain) atau **session hilang** (redeploy).
Login ulang di browser, lalu submit form normal.

### 10.8 Perbedaan kredensial `DATABASE_URL` vs `DB_*`
Bila keduanya ada dan saling bertentangan, `koneksi.php`
**memprioritaskan `DATABASE_URL`**. Untuk menghindari kebingungan, isi
hanya salah satu.

---

## 11. Catatan Operasional

- **Supabase Free** di-*pause* setelah **7 hari idle**. Buka dashboard untuk
  mengaktifkan kembali. Kuota: 500 MB database.
- **Railway** memakai **kredit trial $5**; setelah habis perlu upgrade (kartu)
  atau service berhenti. Bukan gratis permanen.
- **Session** bersifat file-based di container → bisa hilang saat redeploy/restart
  (pengguna ter-logout, token CSRF ikut ter-reset).
- **Keamanan**: jangan commit `.env.local` / `config.local.php` / kredensial DB.
  Bila password DB bocor, reset di dashboard Supabase.
- **`sql/` tidak ikut image**; perubahan skema harus dijalankan manual ke Supabase.
- **Footer** aplikasi masih tertulis "Jobsheet 7" (kosmetik, tidak memengaruhi fungsi).

---

## 12. Alternatif yang Dipertimbangkan (dan Ditinggalkan)

| Opsi | Alasan tidak dipakai |
|---|---|
| **GitHub Pages + Supabase** | GitHub Pages statis, tidak menjalankan PHP. |
| **InfinityFree + Supabase** | Ekstensi `pdo_pgsql` mati + koneksi DB keluar diblokir (dikonfirmasi admin). |
| **Vercel + Supabase** | PHP hanya community runtime, butuh rewrite serverless + session eksternal. |
| **Render + Neon** | Render butuh kartu kredit (ditolak); kredit habis. |
| **Alwaysdata + Neon** | Butuh SSH/password; berhenti di tahap upload. |
| **Railway + Supabase** ✅ | PHP via Docker, env var mudah, tanpa kartu (kredit trial), PostgreSQL terkelola. |

---

## 13. Ringkasan Perintah (Cheat Sheet)

```bash
# 1) Seed skema ke Supabase (dari lokal)
psql "postgresql://postgres.<ref>:<PASS>@aws-0-ap-northeast-1.pooler.supabase.com:5432/postgres?sslmode=require" -f jobsheet-11/sql/01_buku_anggota.sql
psql "postgresql://postgres.<ref>:<PASS>@aws-0-ap-northeast-1.pooler.supabase.com:5432/postgres?sslmode=require" -f jobsheet-11/sql/02_users.sql

# 2) Push kode
git add jobsheet-11 && git commit -m "deploy" && git push origin main

# 3) Railway
#    New Project / Service baru → Deploy from GitHub
#    Settings: Builder=Dockerfile, Root Directory=jobsheet-11, Branch=main
#    Variables: DATABASE_URL=<conn string Supabase>
#    Networking → Generate Domain

# 4) Verifikasi
curl -s -o /dev/null -w "%{http_code}\n" https://<domain>/
curl -s -o /dev/null -w "%{http_code} %{redirect_url}\n" https://<domain>/buku/tambah.php
```
