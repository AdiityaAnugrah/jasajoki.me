<?php
require_once __DIR__ . '/../app/helpers.php';

$pageTitle = 'Store - ' . app_config()['app_name'];
$activeCategory = request_get('category');
$categories = categories_all();
$products = products_all($activeCategory ?: null);
$appInstalled = app_is_installed();

require __DIR__ . '/partials/header.php';
?>
<header class="sticky top-0 z-20 border-b border-slate-100 bg-white/95 px-4 py-4 backdrop-blur">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-xs font-medium uppercase tracking-[0.2em] text-blue-600">Mobile-first Store</p>
            <h1 class="text-xl font-bold"><?= e(app_config()['app_name']) ?></h1>
        </div>
        <a href="<?= e(route_url('admin/login.php')) ?>" class="rounded-full border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700">Admin</a>
    </div>
    <p class="mt-3 text-sm text-slate-500"><?= e(app_setting('store_tagline')) ?></p>
</header>

<main class="px-4 pb-8 pt-4">
    <?php if (!$appInstalled): ?>
        <section class="mb-4 rounded-3xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
            Database belum di-setup. Jalankan <strong>php setup.php</strong> dulu supaya produk, admin, dan order aktif penuh.
        </section>
    <?php endif; ?>
    <section class="rounded-3xl bg-gradient-to-br from-blue-600 to-slate-900 p-5 text-white">
        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-blue-100">jasajoki.me/store</p>
        <h2 class="mt-2 text-2xl font-bold leading-tight">Top up, joki, dan layanan digital dalam satu tempat.</h2>
        <p class="mt-3 text-sm text-blue-100">Fokus mobile, checkout cepat, dan siap dihubungkan ke Tripay.</p>
        <div class="mt-4 grid grid-cols-2 gap-3 text-center text-xs">
            <div class="rounded-2xl bg-white/10 px-3 py-3">
                <div class="text-lg font-bold">24/7</div>
                <div class="text-blue-100">Order masuk</div>
            </div>
            <div class="rounded-2xl bg-white/10 px-3 py-3">
                <div class="text-lg font-bold">Fast</div>
                <div class="text-blue-100">Checkout flow</div>
            </div>
        </div>
    </section>

    <section class="mt-6">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-bold">Kategori</h3>
            <a href="<?= e(route_url('index.php')) ?>" class="text-xs font-semibold text-blue-600">Lihat semua</a>
        </div>
        <div class="mt-3 flex gap-2 overflow-x-auto pb-2">
            <?php foreach ($categories as $category): ?>
                <a href="<?= e(route_url('index.php?category=' . $category['slug'])) ?>"
                   class="whitespace-nowrap rounded-full border px-4 py-2 text-sm <?= $activeCategory === $category['slug'] ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-200 bg-white text-slate-700' ?>">
                    <?= e($category['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="mt-6">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-bold">Produk</h3>
            <span class="text-xs text-slate-500"><?= count($products) ?> item</span>
        </div>

        <div class="mt-3 grid gap-4">
            <?php foreach ($products as $product): ?>
                <article class="rounded-3xl border border-slate-100 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <span class="inline-flex rounded-full bg-orange-50 px-2.5 py-1 text-xs font-semibold text-orange-600"><?= e($product['badge']) ?></span>
                            <h4 class="mt-2 text-base font-bold leading-snug"><?= e($product['name']) ?></h4>
                            <p class="mt-1 text-sm text-slate-500"><?= e($product['description']) ?></p>
                            <?php if (!empty($product['category_name'])): ?>
                                <p class="mt-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400"><?= e($product['category_name']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="rounded-2xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600">Ready</div>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <div>
                            <div class="text-xs text-slate-500">Mulai dari</div>
                            <div class="text-lg font-extrabold text-slate-900"><?= e(money($product['price'])) ?></div>
                        </div>
                        <a href="<?= e(route_url('product.php?slug=' . $product['slug'])) ?>" class="rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white">Pilih</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    
    <section class="mt-6 rounded-3xl border border-slate-100 bg-slate-50 p-4">
        <h3 class="text-sm font-bold">Rencana next step</h3>
        <ul class="mt-2 space-y-2 text-sm text-slate-600">
            <li>• Hubungkan produk ke database MariaDB</li>
            <li>• Tambahkan checkout Tripay</li>
            <li>• Buat invoice & callback otomatis</li>
        </ul>
    </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
