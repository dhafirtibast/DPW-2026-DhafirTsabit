<?php
// DIAGNOSTIK SEMENTARA — akan dihapus setelah debugging.
$keys = ['DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_USER', 'DB_SSLMODE'];
header('Content-Type: text/plain; charset=utf-8');
foreach ($keys as $k) {
    $v = getenv($k);
    echo $k . ' = ' . var_export($v, true) . PHP_EOL;
}
$pass = getenv('DB_PASS');
echo 'DB_PASS = ' . ($pass === false ? 'false' : '(set, length=' . strlen($pass) . ')') . PHP_EOL;
$user = (string) getenv('DB_USER');
echo 'DB_USER contains_dot = ' . (strpos($user, '.') !== false ? 'yes' : 'no') . PHP_EOL;
echo 'DB_USER length = ' . strlen($user) . PHP_EOL;
$du = getenv('DATABASE_URL');
echo 'DATABASE_URL = ' . ($du === false ? 'false' : 'set') . PHP_EOL;

echo PHP_EOL . '=== attempt PDO ===' . PHP_EOL;
$host = getenv('DB_HOST');
$port = getenv('DB_PORT') ?: '5432';
$db = getenv('DB_NAME') ?: '';
$sslmode = getenv('DB_SSLMODE') ?: 'prefer';
try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=$sslmode";
    $pdo = new PDO($dsn, $user, $pass);
    echo "OK connected\n";
    $t = $pdo->query("SELECT tablename FROM pg_tables WHERE schemaname='public' ORDER BY tablename")->fetchAll(PDO::FETCH_COLUMN);
    echo 'tables: ' . (empty($t) ? '(none)' : implode(', ', $t)) . PHP_EOL;
} catch (Throwable $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
}
