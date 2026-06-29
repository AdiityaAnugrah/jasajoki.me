<?php
require_once __DIR__ . '/../app/helpers.php';

$pageTitle = 'Store - ' . app_config()['app_name'];
$activeCategory = request_get('category');
$categories = categories_all();
$products = products_all($activeCategory ?: null);
$appInstalled = app_is_installed();
require __DIR__ . '/partials/header.php';
?>
<div class="min-h-screen">
<header class="store-topbar sticky top-0 z-20 px-4 py-5 backdrop-blur-sm">
    <div class="store-container flex items-center justify-between gap-3">
        <div>
            <p class="eyebrow text-[11px] font-semibold">JasaJoki Store</p>
            <h1 class="mt-1 text-[18px] font-semibold text-[#171411]">Digital account store</h1>
        </div>
        <div class="hidden items-center gap-2 md:flex">
            <span class="btn-secondary px-4 py-2 text-xs font-semibold">QRIS checkout</span>
            <span class="btn-secondary px-4 py-2 text-xs font-semibold">Auto delivery</span>
        </div>
    </div>
</header>

<main class="store-container px-4 pb-12 pt-4 md:px-6 lg:px-8">
    <?php if (!$appInstalled): ?>
        <section class="mb-4 rounded-3xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
            Database belum di-setup. Jalankan <strong>php setup.php</strong> dulu supaya produk, admin, dan order aktif penuh.
        </section>
    <?php endif; ?>

    <section class="section-card overflow-hidden p-5 md:p-8 lg:p-10">
        <div class="grid gap-8 lg:grid-cols-[1.15fr_0.85fr] lg:items-end">
            <div>
                <p class="eyebrow text-[11px] font-semibold">Minimal storefront</p>
                <h2 class="title-display mt-4 max-w-4xl text-[40px] leading-[0.92] text-[#171411] md:text-[58px] lg:text-[78px]">Akun digital yang siap dibeli, dibayar, lalu langsung diambil.</h2>
                <p class="store-muted mt-5 max-w-2xl text-sm leading-7 md:text-base">Super minimal. Fokus ke produk, detail singkat, checkout cepat, QRIS, lalu data akun tampil otomatis setelah pembayaran berhasil.</p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="#catalog" class="btn-primary px-5 py-3 text-sm font-semibold">Lihat katalog</a>
                    <a href="<?= e(app_setting('store_whatsapp') ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', (string) app_setting('store_whatsapp')) : '#') ?>" class="btn-secondary px-5 py-3 text-sm font-semibold">Hubungi kami</a>
                </div>
            </div>
            <div class="grid gap-4">
                <div class="info-block p-5">
                    <div class="eyebrow text-[11px] font-semibold">Catalog</div>
                    <div class="mt-3 text-3xl font-semibold text-[#171411]"><?= count($products) ?> produk</div>
                    <div class="store-muted mt-2 text-sm">Tampilan ringan dan fokus ke list produk yang benar-benar dijual.</div>
                </div>
                <div class="info-block p-5">
                    <div class="eyebrow text-[11px] font-semibold">Flow</div>
                    <div class="mt-3 text-lg font-semibold text-[#171411]">Pilih produk → isi data → scan QRIS → ambil file akun.</div>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-8" id="catalog">
        <div class="flex items-center justify-between">
            <div>
                <p class="eyebrow text-[11px] font-semibold">Collection</p>
                <h3 class="mt-1 text-2xl font-semibold text-[#171411] md:text-3xl">Pilih akun atau layanan</h3>
            </div>
            <a href="<?= e(route_url('index.php')) ?>" class="text-sm font-semibold text-[#171411]">Reset</a>
        </div>
        <div class="mt-3 flex gap-2 overflow-x-auto pb-2">
            <?php foreach ($categories as $category): ?>
                <a href="<?= e(route_url('index.php?category=' . $category['slug'])) ?>"
                   class="store-chip whitespace-nowrap px-4 py-3 text-sm font-semibold <?= $activeCategory === $category['slug'] ? 'store-chip-active' : '' ?>">
                    <?= e($category['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="mt-6">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <?php foreach ($products as $product): ?>
                <article class="product-card p-3">
                    <a href="<?= e(route_url('product.php?slug=' . $product['slug'])) ?>" class="block">
                        <div class="relative">
                            <div class="absolute left-3 top-3 z-10">
                                <span class="store-chip px-3 py-2 text-[11px] font-semibold"><?= e($product['badge'] ?: 'Ready') ?></span>
                            </div>
                            <?php if (!empty($product['image_url'])): ?>
                                <img src="<?= e($product['image_url']) ?>" alt="<?= e($product['name']) ?>" class="store-media">
                            <?php else: ?>
                                <div class="store-media-fallback flex items-end p-5">
                                    <div>
                                        <div class="eyebrow text-[11px] font-semibold"><?= e($product['category_name'] ?? 'Produk') ?></div>
                                        <div class="mt-2 text-2xl font-semibold leading-tight"><?= e($product['name']) ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="px-2 pb-2 pt-5">
                            <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[#7a6c5d]"><?= e($product['category_name'] ?? 'Store') ?></div>
                            <h4 class="mt-2 text-[24px] font-semibold leading-tight text-[#171411]"><?= e($product['name']) ?></h4>
                            <p class="store-muted mt-3 text-sm leading-7"><?= e($product['description']) ?></p>
                            <div class="minimal-divider mt-5"></div>
                            <div class="mt-4 flex items-end justify-between gap-3">
                                <div>
                                    <div class="text-xs uppercase tracking-[0.16em] text-[#7a6c5d]">Harga</div>
                                    <div class="mt-1 text-2xl font-semibold text-[#171411]"><?= e(money($product['price'])) ?></div>
                                </div>
                                <div class="btn-secondary px-4 py-2 text-xs font-semibold">Detail</div>
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
