<?php
session_start();

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/includes/flash.php';

$db = Database::getInstance()->getConnection();

$search = trim($_GET['search'] ?? '');
$limit = 5;
$page = max(1, (int) ($_GET['page'] ?? 1));

$where = '';
$params = [];

if ($search !== '') {
    $where = 'WHERE p.nama_produk LIKE :search_produk
              OR k.nama_kategori LIKE :search_kategori
              OR s.nama_supplier LIKE :search_supplier';

    $keyword = '%' . $search . '%';
    $params = [
        ':search_produk' => $keyword,
        ':search_kategori' => $keyword,
        ':search_supplier' => $keyword,
    ];
}

$countSql = "SELECT COUNT(*)
             FROM produk p
             INNER JOIN kategori k ON p.id_kategori = k.id_kategori
             INNER JOIN supplier s ON p.id_supplier = s.id_supplier
             {$where}";

$countStmt = $db->prepare($countSql);
foreach ($params as $key => $value) {
    $countStmt->bindValue($key, $value, PDO::PARAM_STR);
}
$countStmt->execute();
$totalRows = (int) $countStmt->fetchColumn();
$totalPages = max(1, (int) ceil($totalRows / $limit));
$page = min($page, $totalPages);
$offset = ($page - 1) * $limit;

$sql = "SELECT p.id_produk,
               p.id_kategori,
               p.nama_produk,
               p.stok,
               p.harga,
               p.satuan,
               k.nama_kategori,
               s.nama_supplier
        FROM produk p
        INNER JOIN kategori k ON p.id_kategori = k.id_kategori
        INNER JOIN supplier s ON p.id_supplier = s.id_supplier
        {$where}
        ORDER BY p.id_produk DESC
        LIMIT :limit OFFSET :offset";

$stmt = $db->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$produkList = $stmt->fetchAll();

$rangeStart = $totalRows > 0 ? $offset + 1 : 0;
$rangeEnd = min($offset + $limit, $totalRows);

$pageTitle = 'Daftar Produk - Sistem Inventaris';
include __DIR__ . '/includes/header.php';
?>

<section class="page-heading">
    <div>
        <p class="eyebrow">INVENTARIS</p>
        <h1>Daftar Produk Inventaris</h1>
        <p class="page-description">Kelola data produk, kategori, supplier, stok, dan harga dalam satu halaman.</p>
    </div>
    <div class="stat-card">
        <span class="stat-icon" aria-hidden="true"><img src="assets/logo-inventaris.svg" alt=""></span>
        <div class="stat-content">
            <span class="stat-label">Total Produk</span>
            <strong><?php echo $totalRows; ?></strong>
        </div>
    </div>
</section>

<div class="toolbar">
    <form class="search-form" method="get" action="index.php" role="search">
        <label class="sr-only" for="search">Cari produk</label>
        <div class="search-input-wrap">
            <span class="search-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="10.8" cy="10.8" r="6.4"></circle>
                    <path d="m16 16 4.2 4.2"></path>
                </svg>
            </span>
            <input id="search" type="search" name="search" placeholder="Cari produk, kategori, atau supplier..."
                   value="<?php echo e($search); ?>">
        </div>
        <button type="submit" class="btn btn-secondary">Cari</button>
        <?php if ($search !== ''): ?>
            <a href="index.php" class="btn btn-ghost">Reset</a>
        <?php endif; ?>
    </form>

</div>

<?php if ($totalRows === 0): ?>
    <section class="empty-state">
        <div class="empty-icon"><img src="assets/logo-inventaris.svg" alt=""></div>
        <h2>Tidak ada produk ditemukan</h2>
        <p><?php echo $search !== '' ? 'Coba gunakan kata kunci lain.' : 'Belum ada data produk di database.'; ?></p>
        <?php if ($search !== ''): ?>
            <a href="index.php" class="btn btn-secondary">Lihat Semua Produk</a>
        <?php else: ?>
            <a href="tambah.php" class="btn btn-primary">Tambah Produk Pertama</a>
        <?php endif; ?>
    </section>
<?php else: ?>
    <section class="table-card">
        <div class="table-head">
            <div class="table-title">
                <div class="table-title-icon" aria-hidden="true"><img src="assets/logo-inventaris.svg" alt=""></div>
                <div>
                <h2>Data Produk</h2>
                <p>Menampilkan <?php echo $rangeStart; ?>–<?php echo $rangeEnd; ?> dari <?php echo $totalRows; ?> produk</p>
                </div>
            </div>
            <?php if ($search !== ''): ?>
                <span class="search-badge">Pencarian: “<?php echo e($search); ?>”</span>
            <?php endif; ?>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Supplier</th>
                    <th>Stok</th>
                    <th>Harga</th>
                    <th>Satuan</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php $no = $offset + 1; ?>
                <?php foreach ($produkList as $row): ?>
                    <tr>
                        <td class="muted-cell"><?php echo $no++; ?></td>
                        <td>
                            <div class="product-name"><?php echo e($row['nama_produk']); ?></div>
                        </td>
                        <td><span class="badge <?php echo categoryBadgeClass($row['nama_kategori']); ?>"><?php echo e($row['nama_kategori']); ?></span></td>
                        <td><?php echo e($row['nama_supplier']); ?></td>
                        <td>
                            <span class="stock-value <?php echo ((int) $row['stok'] < 15) ? 'stock-low' : ''; ?>">
                                <?php echo e($row['stok']); ?>
                            </span>
                        </td>
                        <td class="price">Rp <?php echo number_format((float) $row['harga'], 0, ',', '.'); ?></td>
                        <td class="muted-cell"><?php echo e($row['satuan']); ?></td>
                        <td>
                            <div class="action-group">
                                <a class="btn btn-edit btn-sm" href="edit.php?id=<?php echo urlencode((string) $row['id_produk']); ?>">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m4 16.5-.7 3.9 3.9-.7L18.6 8.3a1.7 1.7 0 0 0 0-2.4l-.5-.5a1.7 1.7 0 0 0-2.4 0L4 16.5Z"/><path d="m14.7 6.8 2.5 2.5"/></svg>
                                    Edit
                                </a>
                                <form method="post" action="hapus.php" class="inline-form"
                                      onsubmit="return confirm('Yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan.');">
                                    <input type="hidden" name="id_produk" value="<?php echo e($row['id_produk']); ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 7h14"/><path d="M9 7V4h6v3"/><path d="m7 7 .8 13h8.4L17 7"/><path d="M10 11v5M14 11v5"/></svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <?php if ($totalPages > 1): ?>
        <nav class="pagination-bar" aria-label="Pagination">
            <span class="pagination-info">Halaman <?php echo $page; ?> dari <?php echo $totalPages; ?></span>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="index.php?<?php echo http_build_query(['search' => $search, 'page' => $page - 1]); ?>">‹</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="index.php?<?php echo http_build_query(['search' => $search, 'page' => $i]); ?>"
                       class="<?php echo $i === $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="index.php?<?php echo http_build_query(['search' => $search, 'page' => $page + 1]); ?>">›</a>
                <?php endif; ?>
            </div>
        </nav>
    <?php endif; ?>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
