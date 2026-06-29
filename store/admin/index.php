<?php
require_once __DIR__ . '/../../app/auth.php';
require_admin();

$pageTitle = 'Dashboard Admin';
$activeNav = 'dashboard';
$products = array_slice(products_all(), 0, 6);
$orders = array_slice(orders_all(), 0, 8);
$stats = admin_stats();
require __DIR__ . '/partials/layout-top.php';
?>
<section class="grid gap-4 md:grid-cols-2 2xl:grid-cols-5">
    <div class="admin-stat-card p-5">
        <div class="text-xs font-bold uppercase tracking-[0.22em] text-slate-400">Produk aktif</div>
        <div class="mt-3 text-3xl font-black text-accent-900"><?= e((string) $stats['products']) ?></div>
        <div class="mt-2 text-sm text-slate-500">Item yang tampil di etalase store.</div>
    </div>
    <div class="admin-stat-card p-5">
        <div class="text-xs font-bold uppercase tracking-[0.22em] text-slate-400">Kategori</div>
        <div class="mt-3 text-3xl font-black text-accent-900"><?= e((string) $stats['categories']) ?></div>
        <div class="mt-2 text-sm text-slate-500">Navigasi produk biar lebih rapi.</div>
    </div>
    <div class="admin-stat-card p-5">
        <div class="text-xs font-bold uppercase tracking-[0.22em] text-slate-400">Order hari ini</div>
        <div class="mt-3 text-3xl font-black text-accent-900"><?= e((string) $stats['orders_today']) ?></div>
        <div class="mt-2 text-sm text-slate-500">Pantau transaksi terbaru dengan cepat.</div>
    </div>
    <div class="admin-stat-card p-5">
        <div class="text-xs font-bold uppercase tracking-[0.22em] text-slate-400">Revenue paid</div>
        <div class="mt-3 text-3xl font-black text-accent-900"><?= e(money($stats['revenue'])) ?></div>
        <div class="mt-2 text-sm text-slate-500"><?= e((string) $stats['paid_orders']) ?> order berhasil dibayar.</div>
    </div>
    <div class="admin-stat-card p-5">
        <div class="text-xs font-bold uppercase tracking-[0.22em] text-slate-400">Stok siap jual</div>
        <div class="mt-3 text-3xl font-black text-accent-900"><?= e((string) $stats['stocks_available']) ?></div>
        <div class="mt-2 text-sm text-slate-500">Dari total <?= e((string) $stats['stocks_total']) ?> data akun.</div>
    </div>
</section>

<section class="mt-6 grid gap-6 2xl:grid-cols-[1.2fr_0.8fr]">
    <div class="space-y-6">
        <div class="admin-panel p-6">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-black text-accent-900">Snapshot Produk</h3>
                    <p class="text-sm text-slate-500">Produk terbaru yang aktif di store.</p>
                </div>
                <a href="<?= e(route_url('admin/products.php')) ?>" class="text-sm font-bold text-accent-700">Kelola produk</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead>
                    <tr class="border-b border-stone-200 text-slate-500">
                        <th class="pb-3 pr-4">Produk</th>
                        <th class="pb-3 pr-4">Kategori</th>
                        <th class="pb-3 pr-4">Harga</th>
                        <th class="pb-3">Badge</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr class="border-b border-stone-100 last:border-b-0">
                            <td class="py-4 pr-4 font-bold text-accent-900"><?= e($product['name']) ?></td>
                            <td class="py-4 pr-4"><?= e($product['category_name'] ?? '-') ?></td>
                            <td class="py-4 pr-4 font-semibold"><?= e(money($product['price'])) ?></td>
                            <td class="py-4">
                                <span class="rounded-full bg-[#edf2ec] px-3 py-1 text-xs font-bold text-accent-800"><?= e($product['badge'] ?: '-') ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-panel p-6">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-black text-accent-900">Order Terbaru</h3>
                    <p class="text-sm text-slate-500">Cocok untuk cek status transaksi cepat.</p>
                </div>
                <a href="<?= e(route_url('admin/orders.php')) ?>" class="text-sm font-bold text-accent-700">Lihat semua</a>
            </div>
            <?php if (!$orders): ?>
                <div class="rounded-[22px] bg-[#faf4e6] p-5 text-sm text-slate-500">Belum ada order masuk.</div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead>
                        <tr class="border-b border-stone-200 text-slate-500">
                            <th class="pb-3 pr-4">Kode</th>
                            <th class="pb-3 pr-4">Produk</th>
                            <th class="pb-3 pr-4">Total</th>
                            <th class="pb-3">Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr class="border-b border-stone-100 last:border-b-0">
                                <td class="py-4 pr-4 font-bold text-accent-900"><?= e($order['order_code']) ?></td>
                                <td class="py-4 pr-4"><?= e($order['product_name']) ?></td>
                                <td class="py-4 pr-4 font-semibold"><?= e(money($order['amount'])) ?></td>
                                <td class="py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold <?= e(admin_order_status_badge($order['payment_status'])) ?>">
                                        <?= e($order['payment_status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <aside class="space-y-6">
        <div class="rounded-[30px] bg-[#163933] p-6 text-white shadow-soft">
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-brand-100">Integrasi</p>
            <h3 class="mt-3 text-2xl font-black">Status pembayaran</h3>
            <p class="mt-3 text-sm text-slate-300"><?= e(admin_payment_health()) ?></p>
            <div class="mt-4 grid gap-3">
                <a class="rounded-2xl bg-white px-4 py-3 text-center text-sm font-bold text-slate-950" href="<?= e(route_url('admin/settings.php')) ?>">Cek kredensial</a>
                <a class="rounded-2xl border border-white/20 px-4 py-3 text-center text-sm font-bold text-white" href="<?= e(route_url('admin/orders.php')) ?>">Pantau order</a>
            </div>
        </div>

        <div class="admin-panel p-6">
            <h3 class="text-lg font-black text-accent-900">Quick actions</h3>
            <div class="mt-4 grid gap-3">
                <a class="btn-secondary-soft px-4 py-3 text-sm text-center" href="<?= e(route_url('admin/products.php')) ?>">Tambah / edit produk</a>
                <a class="btn-secondary-soft px-4 py-3 text-sm text-center" href="<?= e(route_url('admin/categories.php')) ?>">Atur kategori</a>
                <a class="btn-secondary-soft px-4 py-3 text-sm text-center" href="<?= e(route_url('admin/stocks.php')) ?>">Import & kelola stok</a>
                <a class="btn-secondary-soft px-4 py-3 text-sm text-center" href="<?= e(route_url('admin/security.php')) ?>">Ganti password admin</a>
            </div>
        </div>
    </aside>
</section>
<?php require __DIR__ . '/partials/layout-bottom.php'; ?>
