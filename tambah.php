<?php
session_start();

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/includes/flash.php';

$db = Database::getInstance()->getConnection();

$kategoriStmt = $db->prepare('SELECT id_kategori, nama_kategori FROM kategori ORDER BY nama_kategori ASC');
$kategoriStmt->execute();
$kategoriList = $kategoriStmt->fetchAll();

$supplierStmt = $db->prepare('SELECT id_supplier, nama_supplier FROM supplier ORDER BY nama_supplier ASC');
$supplierStmt->execute();
$supplierList = $supplierStmt->fetchAll();

$pageTitle = 'Tambah Produk - Sistem Inventaris';
include __DIR__ . '/includes/header.php';
?>

<section class="form-page-heading">
    <div>
        <a class="back-link" href="index.php">← Kembali ke Daftar Produk</a>
        <p class="eyebrow">INVENTARIS</p>
        <h1>Tambah Produk Baru</h1>
        <p class="page-description">Masukkan informasi produk dengan lengkap agar data inventaris tetap konsisten.</p>
    </div>
</section>

<section class="form-card form-card-wide">
    <div class="form-card-top">
        <div>
            <h2>Informasi Produk</h2>
            <p>Field bertanda <span class="required-mark">*</span> wajib diisi.</p>
        </div>
        <span class="form-chip">CREATE</span>
    </div>

    <form method="post" action="proses_tambah.php" class="product-form">
        <div class="form-group form-group-full">
            <label for="nama_produk">Nama Produk <span class="required-mark">*</span></label>
            <input type="text" id="nama_produk" name="nama_produk" required maxlength="150"
                   placeholder="Contoh: Pulpen Standard AE7" autocomplete="off">
        </div>

        <div class="form-group">
            <label for="id_kategori">Kategori <span class="required-mark">*</span></label>
            <select id="id_kategori" name="id_kategori" required>
                <option value="">Pilih kategori</option>
                <?php foreach ($kategoriList as $kategori): ?>
                    <option value="<?php echo e($kategori['id_kategori']); ?>"><?php echo e($kategori['nama_kategori']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="id_supplier">Supplier <span class="required-mark">*</span></label>
            <select id="id_supplier" name="id_supplier" required>
                <option value="">Pilih supplier</option>
                <?php foreach ($supplierList as $supplier): ?>
                    <option value="<?php echo e($supplier['id_supplier']); ?>"><?php echo e($supplier['nama_supplier']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="harga">Harga (Rp) <span class="required-mark">*</span></label>
            <input type="number" id="harga" name="harga" required min="0" step="0.01" placeholder="Contoh: 250000">
        </div>

        <div class="form-group">
            <label for="stok">Stok <span class="required-mark">*</span></label>
            <input type="number" id="stok" name="stok" required min="0" step="1" placeholder="Contoh: 25">
        </div>

        <div class="form-group">
            <label for="satuan">Satuan <span class="required-mark">*</span></label>
            <input type="text" id="satuan" name="satuan" required maxlength="30" placeholder="pcs / unit / rim / botol">
        </div>

        <div class="form-actions form-group-full">
            <a href="index.php" class="btn btn-ghost">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Produk</button>
        </div>
    </form>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
