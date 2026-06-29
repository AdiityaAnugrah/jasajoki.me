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
        <aside class="hidden border-r border-stone-200 bg-[#1c1c19] text-white xl:block">
            <div class="sticky top-0 p-6">
                <div class="rounded-3xl bg-white/5 p-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-stone-300">JasaJoki Admin</p>
                    <h1 class="mt-3 text-2xl font-bold">Panel Operasional</h1>
                    <p class="mt-2 text-sm text-slate-300">Desktop-first untuk kelola produk, order, dan konfigurasi bisnis.</p>
                </div>

                <nav class="mt-6 space-y-2">
                    <?php foreach ($adminNav as $item): ?>
                        <a href="<?= e($item['href']) ?>" class="flex items-center justify-between rounded-2xl px-4 py-3 text-sm font-semibold <?= $activeNav === $item['key'] ? 'bg-stone-100 text-slate-950' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                            <span><?= e($item['label']) ?></span>
                            <?php if ($activeNav === $item['key']): ?><span>•</span><?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </nav>

                <div class="mt-8 rounded-3xl bg-white/5 p-5 text-sm text-slate-300">
                    <div class="font-semibold text-white"><?= e($adminUsername) ?></div>
                    <div class="mt-1">Mode operasional aktif</div>
                    <a href="<?= e(route_url('admin/logout.php')) ?>" class="mt-4 inline-flex rounded-2xl bg-white px-4 py-2 font-semibold text-slate-950">Logout</a>
                </div>
            </div>
        </aside>

        <section class="p-4 md:p-6 xl:p-8">
            <header class="mb-6 flex flex-col gap-4 rounded-3xl border border-stone-200 bg-white p-5 shadow-soft md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Admin workspace</p>
                    <h2 class="mt-2 text-2xl font-bold text-slate-950"><?= e($pageTitle) ?></h2>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="<?= e(route_url()) ?>" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700">Lihat Store</a>
                    <a href="<?= e(route_url('admin/security.php')) ?>" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700">Keamanan</a>
                </div>
            </header>
