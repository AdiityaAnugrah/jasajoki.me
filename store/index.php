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
<header class="sticky top-0 z-20 px-4 py-4 backdrop-blur">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-accent-600">JasaJoki Store</p>
            <h1 class="mt-1 text-[20px] font-extrabold text-accent-900"><?= e(app_config()['app_name']) ?></h1>
        </div>
        <a href="<?= e(route_url('admin/login.php')) ?>" class="pill-soft rounded-full px-4 py-2 text-xs font-semibold text-accent-700">Admin</a>
    </div>
    <p class="mt-3 text-sm text-slate-600"><?= e(app_setting('store_tagline')) ?></p>
</header>

<main class="px-4 pb-8 pt-4">
    <?php if (!$appInstalled): ?>
        <section class="mb-4 rounded-3xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
            Database belum di-setup. Jalankan <strong>php setup.php</strong> dulu supaya produk, admin, dan order aktif penuh.
        </section>
    <?php endif; ?>

    <section class="rounded-[34px] border border-brand-200 bg-[#fff9ee] p-6 shadow-floaty">
        <p class="text-[12px] font-semibold uppercase tracking-[0.24em] text-accent-600">Belanja cepat & aman</p>
        <h2 class="mt-3 text-[36px] font-black leading-[1.05] text-accent-900">Pilih produk yang cocok</h2>
        <p class="mt-3 text-sm leading-6 text-slate-600">Store dibuat fokus mobile dengan pengalaman belanja yang lebih tenang, simpel, dan premium.</p>
        <div class="mt-5 grid grid-cols-3 gap-3 text-center text-xs">
            <div class="rounded-2xl bg-white/80 px-3 py-3">
                <div class="text-base font-bold text-accent-900">24/7</div>
                <div class="mt-1 text-slate-500">Open order</div>
            </div>
            <div class="rounded-2xl bg-white/80 px-3 py-3">
                <div class="text-base font-bold text-accent-900">QRIS</div>
                <div class="mt-1 text-slate-500">Pembayaran</div>
            </div>
            <div class="rounded-2xl bg-white/80 px-3 py-3">
                <div class="text-base font-bold text-accent-900">Fast</div>
                <div class="mt-1 text-slate-500">Checkout</div>
            </div>
        </div>
    </section>

    <section class="mt-6">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Kategori</h3>
            <a href="<?= e(route_url('index.php')) ?>" class="text-xs font-semibold text-slate-600">Reset filter</a>
        </div>
        <div class="mt-3 flex gap-2 overflow-x-auto pb-2">
            <?php foreach ($categories as $category): ?>
                <a href="<?= e(route_url('index.php?category=' . $category['slug'])) ?>"
                   class="whitespace-nowrap rounded-full px-4 py-3 text-sm font-semibold <?= $activeCategory === $category['slug'] ? 'bg-accent-700 text-white' : 'pill-soft text-accent-700' ?>">
                    <?= e($category['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="mt-6">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Produk tersedia</h3>
            <span class="text-xs text-slate-500"><?= count($products) ?> item</span>
        </div>

        <div class="mt-3 grid gap-4">
            <?php foreach ($products as $product): ?>
                <article class="card-soft overflow-hidden rounded-[32px] p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <span class="inline-flex rounded-full bg-accent-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-accent-700"><?= e($product['badge']) ?></span>
                            <h4 class="mt-3 text-[18px] font-extrabold leading-snug text-accent-900"><?= e($product['name']) ?></h4>
                            <p class="mt-2 text-sm leading-6 text-slate-500"><?= e($product['description']) ?></p>
                            <?php if (!empty($product['category_name'])): ?>
                                <p class="mt-3 text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-400"><?= e($product['category_name']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="rounded-2xl bg-accent-50 px-3 py-2 text-[11px] font-semibold text-accent-700">Ready</div>
                    </div>
                    <div class="mt-5 flex items-center justify-between">
                        <div>
                            <div class="text-xs text-slate-400">Harga mulai</div>
                            <div class="text-xl font-extrabold text-accent-900"><?= e(money($product['price'])) ?></div>
                        </div>
                        <a href="<?= e(route_url('product.php?slug=' . $product['slug'])) ?>" class="rounded-2xl bg-accent-700 px-4 py-3 text-sm font-semibold text-white">Pilih produk</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<div class="px-4 pb-4 pt-2">
    <div class="nav-soft grid grid-cols-3 rounded-[28px] px-4 py-4 text-center">
        <div>
            <div class="text-sm font-bold text-accent-900">Beranda</div>
            <div class="mt-1 text-[11px] text-slate-500">Store</div>
        </div>
        <div>
            <div class="text-sm font-bold text-slate-400">Kategori</div>
            <div class="mt-1 text-[11px] text-slate-400">Filter</div>
        </div>
        <div>
            <div class="text-sm font-bold text-slate-400">Admin</div>
            <div class="mt-1 text-[11px] text-slate-400">Panel</div>
        </div>
    </div>
</div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
