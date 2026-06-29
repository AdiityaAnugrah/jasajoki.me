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
<header class="store-topbar sticky top-0 z-20 px-4 py-5 backdrop-blur-sm">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-accent-700">JasaJoki Store</p>
            <h1 class="mt-1 text-[18px] font-extrabold text-accent-900">Digital products curated</h1>
        </div>
        <a href="<?= e(route_url('admin/login.php')) ?>" class="store-pill rounded-full px-4 py-2 text-xs font-semibold">Admin</a>
    </div>
</header>

<main class="px-4 pb-10 pt-2">
    <?php if (!$appInstalled): ?>
        <section class="mb-4 rounded-3xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
            Database belum di-setup. Jalankan <strong>php setup.php</strong> dulu supaya produk, admin, dan order aktif penuh.
        </section>
    <?php endif; ?>

    <section class="store-card overflow-hidden p-5">
        <div class="rounded-[26px] bg-[#143c36] p-5 text-white">
            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-brand-100">Pilihan terbaik</p>
            <h2 class="mt-3 text-[36px] font-black leading-[0.95]">Store yang akhirnya terlihat seperti brand beneran.</h2>
            <p class="mt-3 text-sm leading-6 text-brand-100"><?= e(app_setting('store_tagline')) ?></p>
        </div>
        <div class="mt-4 grid grid-cols-3 gap-3 text-center">
            <div class="rounded-[22px] bg-[#faf4e6] px-3 py-4">
                <div class="text-lg font-black text-accent-900"><?= count($products) ?></div>
                <div class="mt-1 text-[11px] font-semibold text-slate-500">Produk</div>
            </div>
            <div class="rounded-[22px] bg-[#edf2ec] px-3 py-4">
                <div class="text-lg font-black text-accent-900"><?= count($categories) ?></div>
                <div class="mt-1 text-[11px] font-semibold text-slate-500">Kategori</div>
            </div>
            <div class="rounded-[22px] bg-[#faf4e6] px-3 py-4">
                <div class="text-lg font-black text-accent-900">QRIS</div>
                <div class="mt-1 text-[11px] font-semibold text-slate-500">Payment</div>
            </div>
        </div>
    </section>

    <section class="mt-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-accent-700">Kategori</p>
                <h3 class="mt-1 text-2xl font-black text-accent-900">Browse produk</h3>
            </div>
            <a href="<?= e(route_url('index.php')) ?>" class="text-sm font-bold text-accent-700">Reset</a>
        </div>
        <div class="mt-3 flex gap-2 overflow-x-auto pb-2">
            <?php foreach ($categories as $category): ?>
                <a href="<?= e(route_url('index.php?category=' . $category['slug'])) ?>"
                   class="whitespace-nowrap rounded-full px-4 py-3 text-sm font-bold <?= $activeCategory === $category['slug'] ? 'store-pill-active' : 'store-pill' ?>">
                    <?= e($category['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="mt-5">
        <div class="grid gap-5">
            <?php foreach ($products as $product): ?>
                <article class="store-grid-card p-3">
                    <a href="<?= e(route_url('product.php?slug=' . $product['slug'])) ?>" class="block">
                        <div class="relative">
                            <?php if (!empty($product['image_url'])): ?>
                                <img src="<?= e($product['image_url']) ?>" alt="<?= e($product['name']) ?>" class="store-media">
                            <?php else: ?>
                                <div class="store-media-fallback flex items-end p-5">
                                    <div>
                                        <div class="text-[11px] font-bold uppercase tracking-[0.22em] text-brand-100"><?= e($product['category_name'] ?? 'Produk') ?></div>
                                        <div class="mt-2 text-2xl font-black leading-tight"><?= e($product['name']) ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="store-floating-chip absolute left-3 top-3 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.16em]">
                                <?= e($product['badge'] ?: 'Ready') ?>
                            </div>
                        </div>
                        <div class="px-2 pb-2 pt-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h4 class="text-[20px] font-black leading-tight text-accent-900"><?= e($product['name']) ?></h4>
                                    <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-500"><?= e($product['description']) ?></p>
                                </div>
                                <div class="store-chevron mt-1 shrink-0">›</div>
                            </div>
                            <div class="mt-4 flex items-center justify-between">
                                <div>
                                    <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400"><?= e($product['category_name'] ?? 'Store') ?></div>
                                    <div class="mt-1 text-xl font-black text-accent-900"><?= e(money($product['price'])) ?></div>
                                </div>
                                <div class="rounded-full bg-[#edf2ec] px-4 py-2 text-xs font-bold text-accent-800">Lihat detail</div>
                            </div>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
