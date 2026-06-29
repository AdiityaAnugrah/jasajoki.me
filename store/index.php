<?php
require_once __DIR__ . '/../app/helpers.php';

$pageTitle = 'Store - ' . app_config()['app_name'];
$activeCategory = request_get('category');
$categories = categories_all();
$products = products_all($activeCategory ?: null);
$appInstalled = app_is_installed();
require __DIR__ . '/partials/header.php';
?>
<header class="sticky top-0 z-20 border-b border-stone-200 bg-[#fcfcfa]/95 px-4 py-4 backdrop-blur">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">JasaJoki Store</p>
            <h1 class="mt-1 text-xl font-bold text-slate-900"><?= e(app_config()['app_name']) ?></h1>
        </div>
        <a href="<?= e(route_url('admin/login.php')) ?>" class="rounded-full border border-stone-300 px-3 py-2 text-xs font-semibold text-slate-700">Admin</a>
    </div>
    <p class="mt-3 text-sm text-slate-500"><?= e(app_setting('store_tagline')) ?></p>
</header>

<main class="px-4 pb-8 pt-4">
    <?php if (!$appInstalled): ?>
        <section class="mb-4 rounded-3xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
            Database belum di-setup. Jalankan <strong>php setup.php</strong> dulu supaya produk, admin, dan order aktif penuh.
        </section>
    <?php endif; ?>

    <section class="overflow-hidden rounded-[28px] border border-stone-200 bg-white p-5 shadow-soft">
        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Belanja cepat & aman</p>
        <h2 class="mt-3 text-2xl font-bold leading-tight text-slate-900">Top up, joki, dan akun digital dengan alur checkout yang rapi.</h2>
        <p class="mt-3 text-sm leading-6 text-slate-600">Desain fokus mobile, tampilan bersih, dan pembayaran sudah siap ke Tripay begitu akun merchant sepenuhnya aktif.</p>
        <div class="mt-5 grid grid-cols-3 gap-3 text-center text-xs">
            <div class="rounded-2xl bg-stone-100 px-3 py-3">
                <div class="text-base font-bold text-slate-900">24/7</div>
                <div class="mt-1 text-slate-500">Open order</div>
            </div>
            <div class="rounded-2xl bg-stone-100 px-3 py-3">
                <div class="text-base font-bold text-slate-900">QRIS</div>
                <div class="mt-1 text-slate-500">Pembayaran</div>
            </div>
            <div class="rounded-2xl bg-stone-100 px-3 py-3">
                <div class="text-base font-bold text-slate-900">Fast</div>
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
                   class="whitespace-nowrap rounded-full px-4 py-2 text-sm font-semibold <?= $activeCategory === $category['slug'] ? 'bg-slate-900 text-white' : 'border border-stone-300 bg-white text-slate-700' ?>">
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
                <article class="overflow-hidden rounded-[28px] border border-stone-200 bg-white p-4 shadow-soft">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <span class="inline-flex rounded-full bg-stone-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600"><?= e($product['badge']) ?></span>
                            <h4 class="mt-3 text-[17px] font-bold leading-snug text-slate-900"><?= e($product['name']) ?></h4>
                            <p class="mt-2 text-sm leading-6 text-slate-500"><?= e($product['description']) ?></p>
                            <?php if (!empty($product['category_name'])): ?>
                                <p class="mt-3 text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-400"><?= e($product['category_name']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="rounded-2xl bg-emerald-50 px-3 py-2 text-[11px] font-semibold text-emerald-700">Ready</div>
                    </div>
                    <div class="mt-5 flex items-center justify-between">
                        <div>
                            <div class="text-xs text-slate-400">Harga mulai</div>
                            <div class="text-xl font-extrabold text-slate-900"><?= e(money($product['price'])) ?></div>
                        </div>
                        <a href="<?= e(route_url('product.php?slug=' . $product['slug'])) ?>" class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white">Pilih produk</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
