<?php
// DIAGNOSTIK SEMENTARA — akan dihapus setelah debugging.
header('Content-Type: text/plain; charset=utf-8');

function tryConnect($label, $host, $port, $db, $user, $pass, $sslmode)
{
    try {
        $dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=$sslmode";
        $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_TIMEOUT => 10]);
        $t = $pdo->query("SELECT tablename FROM pg_tables WHERE schemaname='public' ORDER BY tablename")->fetchAll(PDO::FETCH_COLUMN);
        echo "[$label] OK host=$host user=$user tables=" . (empty($t) ? '(none)' : implode(',', $t)) . PHP_EOL;
    } catch (Throwable $e) {
        echo "[$label] FAIL host=$host user=$user :: " . $e->getMessage() . PHP_EOL;
    }
}

$host = getenv('DB_HOST');
$port = getenv('DB_PORT') ?: '5432';
$db   = getenv('DB_NAME') ?: '';
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$ssl  = getenv('DB_SSLMODE') ?: 'prefer';

tryConnect('DB_*', $host, $port, $db, $user, $pass, $ssl);

$du = getenv('DATABASE_URL');
if ($du) {
    $u = parse_url($du);
    echo 'DATABASE_URL host = ' . ($u['host'] ?? '?') . ' user = ' . ($u['user'] ?? '?') . ' db = ' . ltrim($u['path'] ?? '', '/') . PHP_EOL;
    tryConnect('DATABASE_URL', $u['host'] ?? '', $u['port'] ?? 5432, ltrim($u['path'] ?? '', '/'), $u['user'] ?? '', $u['pass'] ?? '', 'require');
} else {
    echo "DATABASE_URL not set\n";
}

$ref = '';
if (preg_match('/postgres\.([a-z0-9]+)/', (string)$user, $m)) {
    $ref = $m[1];
}
echo "ref = $ref\n";
if ($ref !== '') {
    tryConnect('direct', "db.$ref.supabase.co", 5432, 'postgres', 'postgres', $pass, 'require');
}
