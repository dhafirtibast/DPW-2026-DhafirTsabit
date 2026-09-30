<?php
// Template konfigurasi khusus server (Alwaysdata).
//
// SALIN file ini menjadi "config.local.php" di server (via SFTP/SSH),
// isi kredensial sungguhan, lalu JANGAN commit — sudah masuk .gitignore.
//
// Nilai contoh di bawah diambil dari Neon (lihat DATABASE_URL_UNPOOLED
// di jobsheet-10/.env.local atau dashboard Neon > Connect).
return [
    'host'    => 'ep-xxxxxxxx-xxxxx.c-4.ap-southeast-1.aws.neon.tech',
    'port'    => '5432',
    'dbname'  => 'neondb',
    'user'    => 'neondb_owner',
    'pass'    => 'ISI_PASSWORD_NEON',
    'sslmode' => 'require',
];
