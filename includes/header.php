<?php
$currentPage = basename($_SERVER['PHP_SELF'] ?? 'index.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle ?? 'Sistem Inventaris'); ?></title>
    <meta name="description" content="Sistem inventaris berbasis PHP Native, PDO, dan MySQL.">
    <link rel="icon" href="assets/logo-inventaris.svg" type="image/svg+xml">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="navbar">
    <div class="navbar-inner">
        <a class="brand" href="index.php" aria-label="Sistem Inventaris">
            <span class="brand-mark" aria-hidden="true">
                <img src="assets/logo-inventaris.svg" alt="">
            </span>
            <span class="brand-text">Sistem <strong>Inventaris</strong></span>
        </a>

        <nav class="navbar-links" aria-label="Navigasi utama">
            <a href="index.php" class="nav-link <?php echo $currentPage === 'index.php' ? 'active' : ''; ?>">
                <span class="nav-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M3.8 11.2 12 4l8.2 7.2"/>
                        <path d="M6.8 10.5V20h10.4v-9.5"/>
                        <path d="M9.2 20v-5.2h5.6V20"/>
                    </svg>
                </span>
                <span>Daftar Produk</span>
            </a>
            <a class="nav-action" href="tambah.php">
                <span class="plus-icon" aria-hidden="true">+</span>
                <span>Tambah Produk</span>
            </a>
        </nav>
    </div>
</header>

<main class="container">
    <?php $flash = get_flash(); ?>
    <?php if (!empty($flash)): ?>
        <div class="alert alert-<?php echo e($flash['type']); ?>" role="status">
            <span class="alert-icon" aria-hidden="true">
                <?php if ($flash['type'] === 'success'): ?>✓<?php else: ?>!<?php endif; ?>
            </span>
            <span><?php echo e($flash['message']); ?></span>
        </div>
    <?php endif; ?>
