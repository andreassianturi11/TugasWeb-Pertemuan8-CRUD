<?php
session_start();

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/includes/flash.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('tambah.php');
}

$db = Database::getInstance()->getConnection();

$nama_produk = trim($_POST['nama_produk'] ?? '');
$id_kategori = $_POST['id_kategori'] ?? '';
$id_supplier = $_POST['id_supplier'] ?? '';
$stok = $_POST['stok'] ?? '';
$harga = $_POST['harga'] ?? '';
$satuan = trim($_POST['satuan'] ?? '');

$errors = [];

if ($nama_produk === '') {
    $errors[] = 'Nama produk wajib diisi.';
}

if ($id_kategori === '' || !ctype_digit((string) $id_kategori)) {
    $errors[] = 'Kategori wajib dipilih.';
}

if ($id_supplier === '' || !ctype_digit((string) $id_supplier)) {
    $errors[] = 'Supplier wajib dipilih.';
}

if ($stok === '' || !ctype_digit((string) $stok)) {
    $errors[] = 'Stok harus berupa bilangan bulat 0 atau lebih.';
}

if ($harga === '' || !is_numeric($harga) || (float) $harga < 0) {
    $errors[] = 'Harga harus berupa angka 0 atau lebih.';
}

if ($satuan === '') {
    $errors[] = 'Satuan wajib diisi.';
}

if ($errors) {
    set_flash('error', implode(' ', $errors));
    redirect('tambah.php');
}

try {
    $stmt = $db->prepare(
        'INSERT INTO produk (nama_produk, id_kategori, id_supplier, stok, harga, satuan)
         VALUES (:nama_produk, :id_kategori, :id_supplier, :stok, :harga, :satuan)'
    );

    $stmt->bindValue(':nama_produk', $nama_produk, PDO::PARAM_STR);
    $stmt->bindValue(':id_kategori', (int) $id_kategori, PDO::PARAM_INT);
    $stmt->bindValue(':id_supplier', (int) $id_supplier, PDO::PARAM_INT);
    $stmt->bindValue(':stok', (int) $stok, PDO::PARAM_INT);
    $stmt->bindValue(':harga', (float) $harga);
    $stmt->bindValue(':satuan', $satuan, PDO::PARAM_STR);
    $stmt->execute();

    set_flash('success', 'Produk berhasil ditambahkan.');
} catch (PDOException $e) {
    error_log('Create product error: ' . $e->getMessage());
    set_flash('error', 'Produk gagal ditambahkan. Periksa data dan koneksi database.');
}

redirect('index.php');
