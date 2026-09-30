<?php
// Kredensial database dibaca berlapis supaya aman dan portabel:
//   1. Environment variable (mis. platform cloud)
//   2. includes/config.local.php (khusus server, TIDAK di-commit)
//   3. DATABASE_URL (mis. Neon)
//   4. Default lokal Laragon
$config = [];

$envHost = getenv('DB_HOST');
if ($envHost !== false && $envHost !== '') {
    $config = [
        'host'    => $envHost,
        'port'    => getenv('DB_PORT') ?: '5432',
        'dbname'  => getenv('DB_NAME') ?: '',
        'user'    => getenv('DB_USER') ?: '',
        'pass'    => getenv('DB_PASS') ?: '',
        'sslmode' => getenv('DB_SSLMODE') ?: '',
    ];
} elseif (is_file(__DIR__ . '/config.local.php')) {
    $config = require __DIR__ . '/config.local.php';
} elseif (($envUrl = getenv('DATABASE_URL')) !== false && $envUrl !== '') {
    $u = parse_url($envUrl);
    $config = [
        'host'    => $u['host'] ?? 'localhost',
        'port'    => $u['port'] ?? '5432',
        'dbname'  => ltrim($u['path'] ?? '', '/'),
        'user'    => $u['user'] ?? '',
        'pass'    => $u['pass'] ?? '',
        'sslmode' => 'require',
    ];
} else {
    $config = [
        'host'    => 'localhost',
        'port'    => '5433',
        'dbname'  => 'simpus_mini',
        'user'    => 'postgres',
        'pass'    => 'postgres',
        'sslmode' => '',
    ];
}

$host    = $config['host'] ?? 'localhost';
$port    = $config['port'] ?? '5432';
$db      = $config['dbname'] ?? '';
$user    = $config['user'] ?? '';
$pass    = $config['pass'] ?? '';
$sslmode = ($config['sslmode'] ?? '') !== '' ? $config['sslmode'] : 'prefer';

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
