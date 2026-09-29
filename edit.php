<?php
session_start();

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/includes/flash.php';

$db = Database::getInstance()->getConnection();

$id = $_GET['id'] ?? '';

if ($id === '' || !ctype_digit((string) $id)) {
    set_flash('error', 'ID produk tidak valid.');
    redirect('index.php');
}

$stmt = $db->prepare('SELECT * FROM produk WHERE id_produk = :id');
$stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
$stmt->execute();
$produk = $stmt->fetch();

if (!$produk) {
    set_flash('error', 'Produk tidak ditemukan.');
    redirect('index.php');
}

$kategoriStmt = $db->prepare('SELECT id_kategori, nama_kategori FROM kategori ORDER BY nama_kategori ASC');
$kategoriStmt->execute();
$kategoriList = $kategoriStmt->fetchAll();

$supplierStmt = $db->prepare('SELECT id_supplier, nama_supplier FROM supplier ORDER BY nama_supplier ASC');
$supplierStmt->execute();
$supplierList = $supplierStmt->fetchAll();

$pageTitle = 'Edit Produk - Sistem Inventaris';
include __DIR__ . '/includes/header.php';
?>

<section class="form-page-heading">
    <div>
        <a class="back-link" href="index.php">← Kembali ke Daftar Produk</a>
        <p class="eyebrow">INVENTARIS</p>
        <h1>Edit Produk</h1>
        <p class="page-description">Perbarui data produk. Form sudah diisi dengan data yang tersimpan di database.</p>
    </div>
</section>

<section class="form-card form-card-wide">
    <div class="form-card-top">
        <div>
            <h2>Informasi Produk</h2>
            <p>ID Produk: <strong>#<?php echo e($produk['id_produk']); ?></strong></p>
        </div>
        <span class="form-chip form-chip-edit">UPDATE</span>
    </div>

    <form method="post" action="proses_edit.php" class="product-form">
        <input type="hidden" name="id_produk" value="<?php echo e($produk['id_produk']); ?>">

        <div class="form-group form-group-full">
            <label for="nama_produk">Nama Produk <span class="required-mark">*</span></label>
            <input type="text" id="nama_produk" name="nama_produk" required maxlength="150"
                   value="<?php echo e($produk['nama_produk']); ?>" autocomplete="off">
        </div>

        <div class="form-group">
            <label for="id_kategori">Kategori <span class="required-mark">*</span></label>
            <select id="id_kategori" name="id_kategori" required>
                <?php foreach ($kategoriList as $kategori): ?>
                    <option value="<?php echo e($kategori['id_kategori']); ?>"
                        <?php echo (int) $kategori['id_kategori'] === (int) $produk['id_kategori'] ? 'selected' : ''; ?> >
                        <?php echo e($kategori['nama_kategori']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="id_supplier">Supplier <span class="required-mark">*</span></label>
            <select id="id_supplier" name="id_supplier" required>
                <?php foreach ($supplierList as $supplier): ?>
                    <option value="<?php echo e($supplier['id_supplier']); ?>"
                        <?php echo (int) $supplier['id_supplier'] === (int) $produk['id_supplier'] ? 'selected' : ''; ?> >
                        <?php echo e($supplier['nama_supplier']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="harga">Harga (Rp) <span class="required-mark">*</span></label>
            <input type="number" id="harga" name="harga" required min="0" step="0.01"
                   value="<?php echo e($produk['harga']); ?>">
        </div>

        <div class="form-group">
            <label for="stok">Stok <span class="required-mark">*</span></label>
            <input type="number" id="stok" name="stok" required min="0" step="1"
                   value="<?php echo e($produk['stok']); ?>">
        </div>

        <div class="form-group">
            <label for="satuan">Satuan <span class="required-mark">*</span></label>
            <input type="text" id="satuan" name="satuan" required maxlength="30"
                   value="<?php echo e($produk['satuan']); ?>">
        </div>

        <div class="form-actions form-group-full">
            <a href="index.php" class="btn btn-ghost">Batal</a>
            <button type="submit" class="btn btn-primary">Update Produk</button>
        </div>
    </form>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
