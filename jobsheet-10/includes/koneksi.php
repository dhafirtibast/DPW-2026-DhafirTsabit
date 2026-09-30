<?php
// Kredensial dibaca dari environment variable agar aman saat deploy
// (tidak ikut ter-commit). Fallback ke pengaturan lokal Laragon.
$host    = getenv('DB_HOST');
$port    = getenv('DB_PORT');
$db      = getenv('DB_NAME');
$user    = getenv('DB_USER');
$pass    = getenv('DB_PASS');
$sslmode = getenv('DB_SSLMODE');
$envUrl  = getenv('DATABASE_URL');

if ($host === false || $host === '') {
    if ($envUrl) {
        $u    = parse_url($envUrl);
        $host = $u['host'] ?? 'localhost';
        $port = $u['port'] ?? 5432;
        $db   = ltrim($u['path'] ?? '', '/');
        $user = $u['user'] ?? '';
        $pass = $u['pass'] ?? '';
        if ($sslmode === false || $sslmode === '') {
            $sslmode = 'require';
        }
    } else {
        $host = 'localhost';
        $port = '5433';
        $db   = 'simpus_mini';
        $user = 'postgres';
        $pass = 'postgres';
    }
}

if ($sslmode === false || $sslmode === '') {
    $sslmode = 'prefer';
}

// Neon membutuhkan endpoint ID pada libpq lama (SNI). Aman ditambahkan
// untuk libpq baru, jadi selalu disertakan bila host berupa domain Neon.
$endpoint = '';
if (strpos($host, 'neon.tech') !== false) {
    $endpoint = ';options=endpoint=' . explode('.', $host)[0];
}

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=$sslmode$endpoint";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}