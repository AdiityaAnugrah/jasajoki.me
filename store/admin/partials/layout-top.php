<?php
require_once __DIR__ . '/../../../app/auth.php';
$pageTitle = $pageTitle ?? 'Admin';
$isAdminLayout = true;
$adminUsername = $_SESSION['admin_username'] ?? 'admin';
$adminNav = [
    ['label' => 'Dashboard', 'href' => route_url('admin/index.php'), 'key' => 'dashboard'],
    ['label' => 'Produk', 'href' => route_url('admin/products.php'), 'key' => 'products'],
    ['label' => 'Kategori', 'href' => route_url('admin/categories.php'), 'key' => 'categories'],
    ['label' => 'Stok', 'href' => route_url('admin/stocks.php'), 'key' => 'stocks'],
    ['label' => 'Order', 'href' => route_url('admin/orders.php'), 'key' => 'orders'],
    ['label' => 'Pengaturan', 'href' => route_url('admin/settings.php'), 'key' => 'settings'],
    ['label' => 'Keamanan', 'href' => route_url('admin/security.php'), 'key' => 'security'],
];
$activeNav = $activeNav ?? 'dashboard';
require __DIR__ . '/../../partials/header.php';
?>
<main class="admin-surface min-h-screen">
    <div class="admin-layout-grid">
        <aside class="admin-sidebar border-r border-slate-200/70 text-white">
            <div class="admin-sidebar-shell p-6">
                <div class="admin-sidebar-hero p-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-sky-200">JasaJoki Admin</p>
                    <h1 class="mt-3 text-2xl font-bold">Panel operasional yang lebih rapi</h1>
                    <p class="mt-3 text-sm leading-7 text-slate-200">Kelola produk, stok, order, dan konfigurasi bisnis dari workspace yang lebih nyaman dipakai harian.</p>
                </div>

                <nav class="admin-sidebar-nav">
                    <?php foreach ($adminNav as $item): ?>
                        <a href="<?= e($item['href']) ?>" class="admin-nav-item <?= $activeNav === $item['key'] ? 'admin-nav-item-active' : 'text-slate-200' ?>">
                            <span><?= e($item['label']) ?></span>
                            <?php if ($activeNav === $item['key']): ?><span>●</span><?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </nav>

                <div class="admin-dark-card p-5 text-sm text-slate-200">
                    <div class="font-semibold text-white"><?= e($adminUsername) ?></div>
                    <div class="mt-1">Mode operasional aktif</div>
                    <a href="<?= e(route_url('admin/logout.php')) ?>" class="mt-4 inline-flex rounded-2xl bg-white px-4 py-2 font-semibold text-slate-950">Logout</a>
                </div>
            </div>
        </aside>

        <section class="p-4 md:p-6 xl:p-8">
            <header class="admin-page-header mb-6">
                <div>
                    <p class="admin-panel-kicker">Admin workspace</p>
                    <h2 class="admin-panel-title mt-2"><?= e($pageTitle) ?></h2>
                    <p class="admin-panel-subtitle mt-2">Panel ini dirapikan agar lebih cepat dipindai, nyaman dipakai, dan selaras dengan storefront baru.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="<?= e(route_url()) ?>" class="btn-secondary-soft px-4 py-3 text-sm">Lihat Store</a>
                    <a href="<?= e(route_url('admin/security.php')) ?>" class="btn-secondary-soft px-4 py-3 text-sm">Keamanan</a>
                </div>
            </header>

            <div class="admin-mobile-nav mb-6 xl:hidden">
                <?php foreach ($adminNav as $item): ?>
                    <a href="<?= e($item['href']) ?>" class="admin-mobile-chip <?= $activeNav === $item['key'] ? 'admin-mobile-chip-active' : '' ?>">
                        <?= e($item['label']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
