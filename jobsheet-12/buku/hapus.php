<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/csrf.php';
require __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

csrf_verify();

$id = (int) ($_POST['id'] ?? 0);
if ($id > 0) {
    try {
        $cek = $pdo->prepare("SELECT COUNT(*) FROM peminjaman WHERE buku_id = :id");
        $cek->execute(['id' => $id]);
        if ($cek->fetchColumn() > 0) {
            $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Buku tidak bisa dihapus karena masih punya riwayat peminjaman.'];
        } else {
            $stmt = $pdo->prepare("DELETE FROM buku WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Buku berhasil dihapus.'];
        }
    } catch (PDOException $e) {
        $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Gagal menghapus buku: ' . $e->getMessage()];
    }
}

header('Location: list.php');
exit;