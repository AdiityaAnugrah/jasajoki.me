<?php
require_once __DIR__ . '/../../../app/auth.php';
$pageTitle = $pageTitle ?? 'Admin';
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
    <div class="mx-auto grid min-h-screen max-w-[1600px] xl:grid-cols-[280px_1fr]">
        <aside class="admin-sidebar hidden border-r border-brand-200 text-white xl:block">
            <div class="sticky top-0 p-6">
                <div class="admin-sidebar-card rounded-[28px] p-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-brand-100">JasaJoki Admin</p>
                    <h1 class="mt-3 text-2xl font-bold">Panel Operasional</h1>
                    <p class="mt-2 text-sm text-slate-300">Desktop-first untuk kelola produk, order, dan konfigurasi bisnis.</p>
                </div>

                <nav class="mt-6 space-y-2">
                    <?php foreach ($adminNav as $item): ?>
                        <a href="<?= e($item['href']) ?>" class="admin-nav-item flex items-center justify-between px-4 py-3 text-sm font-semibold <?= $activeNav === $item['key'] ? 'admin-nav-item-active' : 'text-brand-100 hover:bg-white/5 hover:text-white' ?>">
                            <span><?= e($item['label']) ?></span>
                            <?php if ($activeNav === $item['key']): ?><span>•</span><?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </nav>

                <div class="admin-sidebar-card mt-8 rounded-[28px] p-5 text-sm text-slate-300">
                    <div class="font-semibold text-white"><?= e($adminUsername) ?></div>
                    <div class="mt-1">Mode operasional aktif</div>
                    <a href="<?= e(route_url('admin/logout.php')) ?>" class="mt-4 inline-flex rounded-2xl bg-white px-4 py-2 font-semibold text-slate-950">Logout</a>
                </div>
            </div>
        </aside>

        <section class="p-4 md:p-6 xl:p-8">
            <header class="admin-shell mb-6 flex flex-col gap-4 rounded-[32px] p-5 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-accent-600">Admin workspace</p>
                    <h2 class="mt-2 text-[28px] font-black text-accent-900"><?= e($pageTitle) ?></h2>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="<?= e(route_url()) ?>" class="btn-secondary-soft px-4 py-3 text-sm">Lihat Store</a>
                    <a href="<?= e(route_url('admin/security.php')) ?>" class="btn-secondary-soft px-4 py-3 text-sm">Keamanan</a>
                </div>
            </header>
