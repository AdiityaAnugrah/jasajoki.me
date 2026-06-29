<?php
require_once __DIR__ . '/../../app/auth.php';
require_admin();

$pageTitle = 'Dashboard Admin';
$products = products_all();
$stats = admin_stats();
require __DIR__ . '/../partials/header.php';
?>
<main class="min-h-screen bg-slate-100 p-4 md:p-8">
    <div class="mx-auto max-w-7xl">
        <header class="mb-6 flex items-center justify-between rounded-3xl bg-white p-6 shadow-soft">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-blue-600">Desktop-first Admin</p>
                <h1 class="mt-2 text-3xl font-bold">Dashboard Store</h1>
            </div>
            <div class="flex gap-3">
                <a href="<?= e(route_url()) ?>" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold">Lihat Store</a>
                <a href="<?= e(route_url('admin/logout.php')) ?>" class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white">Logout</a>
            </div>
        </header>

        <section class="grid gap-4 md:grid-cols-3">
            <div class="rounded-3xl bg-white p-5 shadow-soft">
                <div class="text-sm text-slate-500">Produk aktif</div>
                <div class="mt-2 text-3xl font-bold"><?= e((string) $stats['products']) ?></div>
            </div>
            <div class="rounded-3xl bg-white p-5 shadow-soft">
                <div class="text-sm text-slate-500">Order hari ini</div>
                <div class="mt-2 text-3xl font-bold"><?= e((string) $stats['orders_today']) ?></div>
            </div>
            <div class="rounded-3xl bg-white p-5 shadow-soft">
                <div class="text-sm text-slate-500">Total order</div>
                <div class="mt-2 text-3xl font-bold"><?= e((string) $stats['total_orders']) ?></div>
            </div>
        </section>

        <section class="mt-6 grid gap-6 xl:grid-cols-[1.4fr_0.6fr]">
            <div class="rounded-3xl bg-white p-6 shadow-soft">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-xl font-bold">Produk</h2>
                    <a href="<?= e(route_url('admin/products.php')) ?>" class="text-sm font-semibold text-blue-600">Kelola</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-500">
                                <th class="pb-3 pr-4">Produk</th>
                                <th class="pb-3 pr-4">Kategori</th>
                                <th class="pb-3 pr-4">Harga</th>
                                <th class="pb-3">Badge</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr class="border-b border-slate-50">
                                <td class="py-4 pr-4 font-semibold"><?= e($product['name']) ?></td>
                                <td class="py-4 pr-4"><?= e($product['category_name'] ?? (string) $product['category_id']) ?></td>
                                <td class="py-4 pr-4"><?= e(money($product['price'])) ?></td>
                                <td class="py-4"><?= e($product['badge']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <aside class="space-y-6">
                <div class="rounded-3xl bg-white p-6 shadow-soft">
                    <h3 class="text-lg font-bold">Menu cepat</h3>
                    <div class="mt-4 grid gap-3">
                        <a class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold" href="<?= e(route_url('admin/products.php')) ?>">Produk</a>
                        <a class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold" href="<?= e(route_url('admin/orders.php')) ?>">Order</a>
                        <a class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold" href="<?= e(route_url('admin/settings.php')) ?>">Pengaturan</a>
                    </div>
                </div>
                <div class="rounded-3xl bg-slate-900 p-6 text-white">
                    <h3 class="text-lg font-bold">Next milestone</h3>
                    <ul class="mt-3 space-y-2 text-sm text-slate-300">
                        <li>• CRUD database asli</li>
                        <li>• login admin pakai tabel users</li>
                        <li>• callback Tripay live</li>
                    </ul>
                </div>
            </aside>
        </section>
    </div>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
