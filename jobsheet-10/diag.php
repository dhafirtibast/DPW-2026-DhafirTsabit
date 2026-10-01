<?php
// DIAGNOSTIK SEMENTARA — akan dihapus setelah debugging.
header('Content-Type: text/plain; charset=utf-8');

$pass = (string) getenv('DB_PASS');
$du = (string) getenv('DATABASE_URL');
$duPass = '';
if ($du !== '') {
    $u = parse_url($du);
    $duPass = (string) ($u['pass'] ?? '');
}

echo 'DB_PASS length = ' . strlen($pass) . PHP_EOL;
echo 'DATABASE_URL password length = ' . strlen($duPass) . PHP_EOL;
echo 'DB_PASS == DATABASE_URL pass : ' . ($pass === $duPass ? 'YES (same)' : 'NO (different)') . PHP_EOL;
echo 'DB_PASS non-alnum : ' . (preg_match('/[^A-Za-z0-9]/', $pass) ? 'yes' : 'no') . PHP_EOL;
echo 'DATABASE_URL non-alnum : ' . (preg_match('/[^A-Za-z0-9]/', $duPass) ? 'yes' : 'no') . PHP_EOL;
