<?php
// DIAGNOSTIK SEMENTARA — membuat tabel dari sql/*.sql, lalu dihapus.
header('Content-Type: text/plain; charset=utf-8');
require __DIR__ . '/includes/koneksi.php';

$files = [__DIR__ . '/sql/01_buku_anggota.sql', __DIR__ . '/sql/02_users.sql'];
foreach ($files as $f) {
    $sql = file_get_contents($f);
    try {
        $pdo->exec($sql);
        echo 'ran: ' . basename($f) . PHP_EOL;
    } catch (Throwable $e) {
        echo 'FAIL ' . basename($f) . ': ' . $e->getMessage() . PHP_EOL;
    }
}
$t = $pdo->query("SELECT tablename FROM pg_tables WHERE schemaname='public' ORDER BY tablename")->fetchAll(PDO::FETCH_COLUMN);
echo 'tables: ' . (empty($t) ? '(none)' : implode(', ', $t)) . PHP_EOL;
