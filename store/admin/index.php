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
<section class="admin-stat-grid">
    <div class="admin-stat-card p-5">
        <div class="text-xs font-bold uppercase tracking-[0.22em] text-slate-400">Produk aktif</div>
        <div class="mt-3 text-3xl font-black text-accent-900"><?= e((string) $stats['products']) ?></div>
        <div class="mt-2 text-sm text-slate-500">Item yang tampil di storefront.</div>
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
            <div class="mb-5 flex items-center justify-between gap-4">
                <div>
                    <p class="admin-panel-kicker">Snapshot</p>
                    <h3 class="mt-2 text-xl font-black text-accent-900">Produk terbaru</h3>
                    <p class="text-sm text-slate-500">Produk aktif yang paling baru tampil di store.</p>
                </div>
                <a href="<?= e(route_url('admin/products.php')) ?>" class="btn-secondary-soft px-4 py-3 text-sm">Kelola produk</a>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Badge</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td class="font-bold text-accent-900"><?= e($product['name']) ?></td>
                            <td><?= e($product['category_name'] ?? '-') ?></td>
                            <td class="font-semibold"><?= e(money($product['price'])) ?></td>
                            <td><span class="admin-status-chip bg-sky-50 text-sky-700"><?= e($product['badge'] ?: '-') ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-panel p-6">
            <div class="mb-5 flex items-center justify-between gap-4">
                <div>
                    <p class="admin-panel-kicker">Transaksi</p>
                    <h3 class="mt-2 text-xl font-black text-accent-900">Order terbaru</h3>
                    <p class="text-sm text-slate-500">Biar operator cepat melihat status pembayaran terbaru.</p>
                </div>
                <a href="<?= e(route_url('admin/orders.php')) ?>" class="btn-secondary-soft px-4 py-3 text-sm">Lihat semua</a>
            </div>
            <?php if (!$orders): ?>
                <div class="admin-empty-state">Belum ada order masuk.</div>
            <?php else: ?>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Produk</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td class="font-bold text-accent-900"><?= e($order['order_code']) ?></td>
                                <td><?= e($order['product_name']) ?></td>
                                <td class="font-semibold"><?= e(money($order['amount'])) ?></td>
                                <td>
                                    <span class="admin-status-chip <?= e(admin_order_status_badge($order['payment_status'])) ?>">
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
        <div class="admin-dark-card p-6">
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-sky-200">Integrasi</p>
            <h3 class="mt-3 text-2xl font-black">Status pembayaran</h3>
            <p class="mt-3 text-sm leading-7 text-slate-200"><?= e(admin_payment_health()) ?></p>
            <div class="mt-4 grid gap-3">
                <a class="rounded-2xl bg-white px-4 py-3 text-center text-sm font-bold text-slate-950" href="<?= e(route_url('admin/settings.php')) ?>">Cek kredensial</a>
                <a class="rounded-2xl border border-white/20 px-4 py-3 text-center text-sm font-bold text-white" href="<?= e(route_url('admin/orders.php')) ?>">Pantau order</a>
            </div>
        </div>

        <div class="admin-panel p-6">
            <p class="admin-panel-kicker">Quick actions</p>
            <h3 class="mt-2 text-lg font-black text-accent-900">Aksi cepat operator</h3>
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
