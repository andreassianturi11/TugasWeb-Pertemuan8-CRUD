<?php
session_start();

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/includes/flash.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

$id = $_POST['id_produk'] ?? '';

if ($id === '' || !ctype_digit((string) $id)) {
    set_flash('error', 'ID produk tidak valid.');
    redirect('index.php');
}

$id = (int) $id;
$db = Database::getInstance()->getConnection();

try {
    $db->beginTransaction();

    $selectStmt = $db->prepare('SELECT nama_produk FROM produk WHERE id_produk = :id');
    $selectStmt->bindValue(':id', $id, PDO::PARAM_INT);
    $selectStmt->execute();
    $produk = $selectStmt->fetch();

    if (!$produk) {
        $db->rollBack();
        set_flash('error', 'Produk tidak ditemukan atau sudah dihapus.');
        redirect('index.php');
    }

    $deleteStmt = $db->prepare('DELETE FROM produk WHERE id_produk = :id');
    $deleteStmt->bindValue(':id', $id, PDO::PARAM_INT);
    $deleteStmt->execute();

    $logStmt = $db->prepare(
        'INSERT INTO log_aktivitas (aksi, nama_produk, keterangan)
         VALUES (:aksi, :nama_produk, :keterangan)'
    );
    $logStmt->bindValue(':aksi', 'DELETE', PDO::PARAM_STR);
    $logStmt->bindValue(':nama_produk', $produk['nama_produk'], PDO::PARAM_STR);
    $logStmt->bindValue(':keterangan', 'Produk dihapus melalui halaman inventaris', PDO::PARAM_STR);
    $logStmt->execute();

    $db->commit();
    set_flash('success', 'Produk berhasil dihapus.');
} catch (PDOException $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }

    error_log('Delete product error: ' . $e->getMessage());
    set_flash('error', 'Produk gagal dihapus. Perubahan dibatalkan.');
}

redirect('index.php');
