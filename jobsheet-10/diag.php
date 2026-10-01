<?php
// DIAGNOSTIK SEMENTARA — akan dihapus setelah debugging.
header('Content-Type: text/plain; charset=utf-8');

$pass = getenv('DB_PASS');
echo 'Railway DB_PASS length = ' . strlen((string)$pass) . PHP_EOL;

$trouble = ['$', '\\', '{', '}', '#', '%', '@', ':', '/', '?', '&', '`', '"', "'"];
foreach ($trouble as $ch) {
    $c = substr_count((string)$pass, $ch);
    if ($c > 0) {
        echo "contains '$ch' x$c" . PHP_EOL;
    }
}

echo 'has_non_alnum = ' . (preg_match('/[^A-Za-z0-9]/', (string)$pass) ? 'yes' : 'no') . PHP_EOL;
echo 'len_before_trim = ' . strlen((string)$pass) . ', len_trimmed = ' . strlen(trim((string)$pass)) . PHP_EOL;
