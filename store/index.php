<?php
require_once __DIR__ . '/../app/helpers.php';

$pageTitle = 'Store - ' . app_config()['app_name'];
$activeCategory = request_get('category');
$categories = categories_all();
$products = products_all($activeCategory ?: null);
$appInstalled = app_is_installed();
require __DIR__ . '/partials/header.php';
?>
<div class="mobile-shell min-h-screen">
<header class="store-topbar sticky top-0 z-20 px-4 py-5">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-accent-700">JasaJoki Store</p>
            <h1 class="mt-1 text-[18px] font-extrabold text-accent-900"><?= e(app_config()['app_name']) ?></h1>
        </div>
        <a href="<?= e(route_url('admin/login.php')) ?>" class="store-pill rounded-full px-4 py-2 text-xs font-semibold">Admin</a>
    </div>
</header>

<main class="px-4 pb-8 pt-4">
    <?php if (!$appInstalled): ?>
        <section class="mb-4 rounded-3xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
            Database belum di-setup. Jalankan <strong>php setup.php</strong> dulu supaya produk, admin, dan order aktif penuh.
        </section>
    <?php endif; ?>

    <section class="store-hero px-1 pb-3">
        <p class="text-[14px] font-bold text-accent-700">JasaJoki</p>
        <h2 class="mt-3 text-[48px] font-black leading-[0.95] tracking-[-0.04em] text-accent-900">Pilih produk yang cocok</h2>
        <p class="mt-4 pr-8 text-sm leading-6 text-slate-600"><?= e(app_setting('store_tagline')) ?></p>
        <div class="mt-5">
            <a href="<?= e(route_url('index.php')) ?>" class="store-pill inline-flex items-center gap-3 rounded-full px-5 py-3 text-base font-bold">
                <span>Semua produk</span>
                <span>⌄</span>
            </a>
        </div>
    </section>

    <section class="mt-6">
        <div class="mt-3 flex gap-2 overflow-x-auto pb-2">
            <?php foreach ($categories as $category): ?>
                <a href="<?= e(route_url('index.php?category=' . $category['slug'])) ?>"
                   class="whitespace-nowrap rounded-full px-4 py-3 text-sm font-bold <?= $activeCategory === $category['slug'] ? 'store-pill-active' : 'store-pill' ?>">
                    <?= e($category['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="mt-4">
        <div class="mt-3 grid gap-4">
            <?php foreach ($products as $product): ?>
                <article class="store-card p-4">
                    <a href="<?= e(route_url('product.php?slug=' . $product['slug'])) ?>" class="block">
                        <div class="flex items-center gap-4">
                            <div class="store-icon-bubble">
                                <?= strtoupper(substr($product['name'], 0, 1)) ?>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="truncate text-[18px] font-extrabold leading-tight text-accent-900"><?= e($product['name']) ?></h4>
                                <p class="mt-1 text-sm font-semibold text-slate-500">
                                    <?= !empty($product['category_name']) ? e($product['category_name']) . ' · ' : '' ?>
                                    mulai <?= e(money($product['price'])) ?>
                                </p>
                                <p class="mt-1 line-clamp-1 text-xs text-slate-400"><?= e($product['description']) ?></p>
                            </div>
                            <div class="store-chevron">›</div>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<div class="px-4 pb-4 pt-2">
    <div class="store-bottom-nav grid grid-cols-3 rounded-[30px] px-4 py-4 text-center">
        <div>
            <div class="text-sm font-extrabold text-accent-900">Beranda</div>
            <div class="mt-1 text-[11px] text-slate-500">Store</div>
        </div>
        <div>
            <div class="text-sm font-extrabold text-slate-400">Kategori</div>
            <div class="mt-1 text-[11px] text-slate-400">Filter</div>
        </div>
        <div>
            <div class="text-sm font-extrabold text-slate-400">Admin</div>
            <div class="mt-1 text-[11px] text-slate-400">Panel</div>
        </div>
    </div>
</div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
