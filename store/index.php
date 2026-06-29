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
<main class="store-container px-5 pb-12 pt-5 md:px-7 lg:px-8">
    <?php if (!$appInstalled): ?>
        <section class="mb-4 rounded-3xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
            Database belum di-setup. Jalankan <strong>php setup.php</strong> dulu supaya produk, admin, dan order aktif penuh.
        </section>
    <?php endif; ?>

    <section class="section-card interactive-panel overflow-hidden p-5 md:p-8 lg:p-10">
        <span class="floating-orb orb-a"></span>
        <span class="floating-orb orb-b"></span>
        <div class="hero-grid">
            <div>
                <div class="hero-badge-row">
                    <span class="store-chip px-4 py-2 text-xs font-semibold">Store digital yang lebih modern</span>
                    <span class="store-chip px-4 py-2 text-xs font-semibold">Ramah pengguna awam</span>
                </div>
                <h2 class="title-display mt-5 max-w-4xl text-[40px] leading-[0.94] text-slate-950 md:text-[62px] lg:text-[80px]">Beli produk digital dengan alur yang lebih jelas, cepat, dan terasa meyakinkan.</h2>
                <p class="store-muted mt-5 max-w-2xl text-sm leading-7 md:text-base">Kami sederhanakan pengalaman belanja supaya orang awam pun langsung paham: pilih produk, isi data, bayar via QRIS, lalu ambil hasilnya tanpa bingung.</p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="#catalog" class="btn-primary px-5 py-3 text-sm font-semibold">Lihat katalog</a>
                    <a href="<?= e(app_setting('store_whatsapp') ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', (string) app_setting('store_whatsapp')) : '#') ?>" class="btn-secondary px-5 py-3 text-sm font-semibold">Tanya via WhatsApp</a>
                </div>
                <div class="trust-badge-row mt-7">
                    <span class="store-chip px-4 py-2 text-sm font-semibold">QRIS real-time</span>
                    <span class="store-chip px-4 py-2 text-sm font-semibold">Checkout simpel</span>
                    <span class="store-chip px-4 py-2 text-sm font-semibold">Mobile-first</span>
                </div>
            </div>
            <div class="hero-visual">
                <div class="hero-visual-card">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-[0.2em] text-sky-200">Checkout flow</div>
                            <div class="mt-2 text-2xl font-semibold">Pilih → Isi data → Bayar → Ambil hasil</div>
                        </div>
                        <span class="status-pill px-3 py-2 text-xs font-semibold !border-white/15 !bg-white/10 !text-white">Simple</span>
                    </div>
                    <div class="mt-5 hero-progress">
                        <div class="hero-progress-bar"></div>
                    </div>
                    <div class="mt-4 grid gap-3 text-sm text-slate-200 sm:grid-cols-3">
                        <div>
                            <div class="text-2xl font-bold text-white"><?= count($products) ?></div>
                            <div>Katalog aktif</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-white">24/7</div>
                            <div>Halaman invoice</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-white">QRIS</div>
                            <div>Pembayaran cepat</div>
                        </div>
                    </div>
                </div>
                <div class="hero-mini-grid">
                    <div class="highlight-card p-5">
                        <div class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-700">Tampilan baru</div>
                        <div class="mt-3 text-lg font-semibold text-slate-950">Lebih enak dibaca, lebih rapi, lebih meyakinkan.</div>
                    </div>
                    <div class="highlight-card p-5">
                        <div class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-700">Fokus UX</div>
                        <div class="mt-3 text-lg font-semibold text-slate-950">Biar pembeli baru tidak bingung saat checkout.</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-8 grid gap-3 md:grid-cols-3">
            <div class="info-block p-5">
                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">01 • Cepat</div>
                <div class="mt-2 text-lg font-semibold text-slate-950">Langsung fokus ke produk yang mau dibeli</div>
            </div>
            <div class="info-block p-5">
                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">02 • Jelas</div>
                <div class="mt-2 text-lg font-semibold text-slate-950">Flow pembayaran dan status order lebih mudah dipahami</div>
            </div>
            <div class="info-block p-5">
                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">03 • Nyaman</div>
                <div class="mt-2 text-lg font-semibold text-slate-950">Nyaman dipakai di HP tanpa terasa penuh dan berantakan</div>
            </div>
        </div>
    </section>

    <section class="mt-8" id="catalog">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="eyebrow text-[11px] font-semibold">Katalog produk</p>
                <h3 class="mt-1 text-2xl font-semibold text-slate-950 md:text-3xl">Pilih akun, layanan, atau produk digital yang kamu butuhkan</h3>
                <p class="store-muted mt-2 text-sm">Filter kategori dibuat lebih sederhana supaya user lebih cepat menemukan produk.</p>
            </div>
            <a href="<?= e(route_url('index.php')) ?>" class="btn-soft px-4 py-3 text-sm font-semibold">Reset filter</a>
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
        <?php if (!$products): ?>
            <div class="empty-state p-8 text-center">
                <div class="mx-auto max-w-lg">
                    <div class="text-lg font-semibold text-slate-950">Produk di kategori ini belum tersedia.</div>
                    <p class="store-muted mt-2 text-sm leading-7">Coba pilih kategori lain atau reset filter untuk melihat semua katalog yang aktif.</p>
                    <a href="<?= e(route_url('index.php')) ?>" class="btn-primary mt-5 px-5 py-3 text-sm font-semibold">Lihat semua produk</a>
                </div>
            </div>
        <?php else: ?>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <article class="product-card p-3">
                    <a href="<?= e(route_url('product.php?slug=' . $product['slug'])) ?>" class="product-card-link">
                        <div class="relative">
                            <?php if (!empty($product['image_url'])): ?>
                                <img src="<?= e($product['image_url']) ?>" alt="<?= e($product['name']) ?>" class="store-media">
                            <?php else: ?>
                                <div class="store-media-fallback flex items-end p-5">
                                    <span class="fallback-glow"></span>
                                    <div>
                                        <div class="eyebrow text-[11px] font-semibold !text-sky-100"><?= e($product['category_name'] ?? 'Produk') ?></div>
                                        <div class="mt-2 text-2xl font-semibold leading-tight"><?= e($product['name']) ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex h-full flex-col px-2 pb-2 pt-5">
                            <div class="flex flex-wrap items-center gap-2">
                                <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500"><?= e($product['category_name'] ?? 'Store') ?></div>
                                <span class="store-chip px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.14em]">
                                    <?= e($product['badge'] ?: 'Ready') ?>
                                </span>
                            </div>
                            <h4 class="mt-3 text-[24px] font-semibold leading-tight text-slate-950"><?= e($product['name']) ?></h4>
                            <p class="store-muted mt-3 text-sm leading-7"><?= e($product['description']) ?></p>
                            <div class="mt-auto">
                                <div class="minimal-divider mt-5"></div>
                                <div class="card-price-row mt-4">
                                    <div>
                                        <div class="text-xs uppercase tracking-[0.16em] text-slate-500">Harga mulai</div>
                                        <div class="mt-1 text-2xl font-semibold text-slate-950"><?= e(money($product['price'])) ?></div>
                                    </div>
                                    <div class="btn-secondary px-4 py-2 text-xs font-semibold">Detail</div>
                                </div>
                            </div>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </section>
</main>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
