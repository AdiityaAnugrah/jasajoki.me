<?php
require_once __DIR__ . '/../../app/auth.php';
require_admin();

$pageTitle = 'Manajemen Order';
$activeNav = 'orders';
$statusFilter = strtoupper((string) request_get('status', 'ALL'));
$orders = orders_all();

if ($statusFilter !== 'ALL') {
    $orders = array_values(array_filter($orders, fn ($order) => strtoupper((string) $order['payment_status']) === $statusFilter));
}

require __DIR__ . '/partials/layout-top.php';
?>
<section class="admin-panel p-6">
    <div class="mb-5 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="admin-panel-kicker">Transaksi</p>
            <h3 class="mt-2 text-2xl font-black text-accent-900">Daftar order</h3>
            <p class="text-sm text-slate-500">Filter status pembayaran dan cek detail customer secara cepat dari tabel yang lebih rapi.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <?php foreach (['ALL', 'UNPAID', 'PAID', 'EXPIRED', 'FAILED'] as $status): ?>
                <a href="<?= e(route_url('admin/orders.php?status=' . $status)) ?>" class="admin-filter-chip <?= $statusFilter === $status ? 'admin-filter-chip-active' : '' ?>">
                    <?= e($status) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (!$orders): ?>
        <div class="admin-empty-state">Belum ada order untuk filter ini.</div>
    <?php else: ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                <tr>
                    <th>Order</th>
                    <th>Produk</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Channel</th>
                    <th>Status</th>
                    <th>Transaction ID</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>
                            <div class="font-bold text-accent-900"><?= e($order['order_code']) ?></div>
                            <div class="text-xs text-slate-400"><?= e($order['created_at'] ?? '-') ?></div>
                        </td>
                        <td class="font-semibold"><?= e($order['product_name']) ?></td>
                        <td>
                            <div class="font-medium"><?= e($order['customer_name']) ?></div>
                            <div class="text-xs text-slate-500"><?= e($order['customer_email'] ?? '-') ?></div>
                            <div class="text-xs text-slate-500"><?= e($order['customer_whatsapp']) ?></div>
                        </td>
                        <td class="font-semibold"><?= e(money($order['amount'])) ?></td>
                        <td><?= e($order['payment_channel'] ?: '-') ?></td>
                        <td>
                            <span class="admin-status-chip <?= e(admin_order_status_badge($order['payment_status'])) ?>">
                                <?= e($order['payment_status']) ?>
                            </span>
                        </td>
                        <td class="text-xs text-slate-500"><?= e($order['tripay_reference'] ?: '-') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/partials/layout-bottom.php'; ?>
