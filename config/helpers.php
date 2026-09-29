<?php

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $location): void
{
    header('Location: ' . $location);
    exit;
}

function categoryBadgeClass(string $category): string
{
    return match (mb_strtolower(trim($category), 'UTF-8')) {
        'alat tulis kantor' => 'badge-cat-stationery',
        'elektronik' => 'badge-cat-electronic',
        'furniture' => 'badge-cat-furniture',
        'bahan baku' => 'badge-cat-material',
        'kebersihan' => 'badge-cat-cleaning',
        default => 'badge-cat-default',
    };
}
