<?php
// DIAGNOSTIK SEMENTARA — akan dihapus setelah debugging.
header('Content-Type: text/plain; charset=utf-8');

function attempt($label, $dsn, $user, $pass)
{
    try {
        $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_TIMEOUT => 10]);
        $t = $pdo->query("SELECT tablename FROM pg_tables WHERE schemaname='public' ORDER BY tablename")->fetchAll(PDO::FETCH_COLUMN);
        echo "[$label] OK tables=" . (empty($t) ? '(none)' : implode(',', $t)) . PHP_EOL;
        return $pdo;
    } catch (Throwable $e) {
        echo "[$label] FAIL :: " . $e->getMessage() . PHP_EOL;
        return null;
    }
}

$host = getenv('DB_HOST');
$port = getenv('DB_PORT') ?: '5432';
$db   = getenv('DB_NAME') ?: '';
$user = getenv('DB_USER');
$pass = (string) getenv('DB_PASS');
$ssl  = getenv('DB_SSLMODE') ?: 'prefer';
attempt('DB_*', "pgsql:host=$host;port=$port;dbname=$db;sslmode=$ssl", $user, $pass);

$du = (string) getenv('DATABASE_URL');
if ($du !== '') {
    $u = parse_url($du);
    attempt(
        'DATABASE_URL',
        "pgsql:host=" . ($u['host'] ?? '') . ";port=" . ($u['port'] ?? 5432) . ";dbname=" . ltrim($u['path'] ?? '', '/') . ";sslmode=require",
        (string) ($u['user'] ?? ''),
        (string) ($u['pass'] ?? '')
    );
}
