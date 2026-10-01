<?php
// DIAGNOSTIK SEMENTARA — membuat tabel, lalu dihapus.
header('Content-Type: text/plain; charset=utf-8');
require __DIR__ . '/includes/koneksi.php';

$statements = [
    "CREATE TABLE IF NOT EXISTS buku (
        id SERIAL PRIMARY KEY,
        judul VARCHAR(255) NOT NULL,
        pengarang VARCHAR(255) NOT NULL,
        tahun INTEGER NOT NULL,
        isbn VARCHAR(50),
        stok INTEGER NOT NULL DEFAULT 0,
        kategori VARCHAR(50)
    )",
    "CREATE TABLE IF NOT EXISTS anggota (
        id SERIAL PRIMARY KEY,
        nama VARCHAR(255) NOT NULL,
        no_anggota VARCHAR(50) NOT NULL UNIQUE,
        alamat VARCHAR(255),
        no_hp VARCHAR(30)
    )",
    "CREATE TABLE IF NOT EXISTS users (
        id SERIAL PRIMARY KEY,
        nama VARCHAR(255) NOT NULL,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(20) NOT NULL DEFAULT 'petugas'
    )",
];

foreach ($statements as $i => $sql) {
    try {
        $pdo->exec($sql);
        echo "ran #$i\n";
    } catch (Throwable $e) {
        echo "FAIL #$i: " . $e->getMessage() . "\n";
    }
}
$t = $pdo->query("SELECT tablename FROM pg_tables WHERE schemaname='public' ORDER BY tablename")->fetchAll(PDO::FETCH_COLUMN);
echo 'tables: ' . (empty($t) ? '(none)' : implode(', ', $t)) . PHP_EOL;
