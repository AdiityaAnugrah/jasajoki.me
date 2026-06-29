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
    <div class="store-container flex items-center justify-between gap-3">
        <div>
            <p class="store-kicker text-[11px] font-bold uppercase tracking-[0.24em]">JasaJoki Store</p>
            <h1 class="mt-1 text-[18px] font-extrabold text-[#f7f3ea]">Curated digital catalog</h1>
        </div>
        <div class="hidden items-center gap-2 lg:flex">
            <span class="store-outline rounded-full px-4 py-2 text-xs font-semibold">Secure checkout</span>
            <span class="store-outline rounded-full px-4 py-2 text-xs font-semibold">Premium support</span>
        </div>
    </div>
</header>

<main class="store-container px-4 pb-12 pt-4 md:px-6 lg:px-8">
    <?php if (!$appInstalled): ?>
        <section class="mb-4 rounded-3xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
            Database belum di-setup. Jalankan <strong>php setup.php</strong> dulu supaya produk, admin, dan order aktif penuh.
        </section>
    <?php endif; ?>

    <section class="store-card overflow-hidden p-5 md:p-8">
        <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr] xl:items-end">
            <div>
                <p class="store-kicker text-[11px] font-bold uppercase tracking-[0.26em]">Curated digital products</p>
                <h2 class="store-heading mt-4 max-w-4xl text-[42px] font-black leading-[0.9] text-[#f7f3ea] md:text-[64px] lg:text-[82px]">Digital store yang lebih terasa seperti curated showcase, bukan template toko biasa.</h2>
                <p class="store-muted mt-6 max-w-2xl text-sm leading-7 md:text-base"><?= e(app_setting('store_tagline')) ?></p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="#catalog" class="store-cta rounded-full px-5 py-3 text-sm font-bold">Lihat katalog</a>
                    <a href="<?= e(app_setting('store_whatsapp') ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', (string) app_setting('store_whatsapp')) : '#') ?>" class="store-outline rounded-full px-5 py-3 text-sm font-bold">Hubungi kami</a>
                </div>
            </div>
            <div class="store-feature-grid">
                <div class="store-stat p-5">
                    <div class="text-[11px] font-bold uppercase tracking-[0.22em] text-[#cab38f]">Selected collection</div>
                    <div class="mt-3 text-3xl font-black text-[#f7f3ea]"><?= count($products) ?> produk</div>
                    <div class="store-muted mt-2 text-sm">Katalog dipilih untuk pembelian cepat, jelas, dan tanpa layout ramai.</div>
                </div>
                <div class="store-minor-card p-5">
                    <div class="text-[11px] font-bold uppercase tracking-[0.22em] text-[#cab38f]">Store notes</div>
                    <div class="mt-3 text-lg font-black text-[#f7f3ea]">Responsive di semua layar, QRIS-ready, dan visual lebih clean.</div>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-8" id="catalog">
        <div class="flex items-center justify-between">
            <div>
                <p class="store-kicker text-xs font-bold uppercase tracking-[0.2em]">Collection</p>
                <h3 class="mt-1 text-2xl font-black text-[#f7f3ea] md:text-3xl">Explore products</h3>
            </div>
            <a href="<?= e(route_url('index.php')) ?>" class="text-sm font-bold text-[#f0dfbf]">Reset</a>
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

    <section class="mt-6">
        <div class="grid gap-5 lg:grid-cols-2">
            <?php foreach ($products as $product): ?>
                <article class="store-grid-card p-3 md:p-4">
                    <a href="<?= e(route_url('product.php?slug=' . $product['slug'])) ?>" class="block">
                        <div class="grid gap-4 lg:grid-cols-[0.95fr_1.05fr] lg:items-stretch">
                            <div class="relative">
                                <?php if (!empty($product['image_url'])): ?>
                                    <img src="<?= e($product['image_url']) ?>" alt="<?= e($product['name']) ?>" class="store-media lg:h-full">
                                <?php else: ?>
                                    <div class="store-media-fallback flex items-end p-5 lg:h-full">
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
                            <div class="flex min-h-full flex-col px-2 py-2">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[#cab38f]"><?= e($product['category_name'] ?? 'Store') ?></div>
                                        <h4 class="mt-3 text-[24px] font-black leading-[1] text-[#f7f3ea]"><?= e($product['name']) ?></h4>
                                        <p class="store-muted mt-4 line-clamp-3 text-sm leading-7"><?= e($product['description']) ?></p>
                                    </div>
                                    <div class="store-chevron mt-1 shrink-0">›</div>
                                </div>
                                <div class="mt-auto pt-6">
                                    <div class="store-line flex items-center justify-between border-t pt-4">
                                        <div>
                                            <div class="text-xs uppercase tracking-[0.18em] text-[#cab38f]">Mulai dari</div>
                                            <div class="mt-1 text-2xl font-black text-[#f7f3ea]"><?= e(money($product['price'])) ?></div>
                                        </div>
                                        <div class="store-cta rounded-full px-4 py-2 text-xs font-bold">Open product</div>
                                    </div>
                                </div>
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
