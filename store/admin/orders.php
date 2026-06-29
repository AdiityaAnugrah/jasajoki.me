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
<section class="rounded-3xl bg-white p-6 shadow-soft">
    <div class="mb-5 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h3 class="text-xl font-bold">Daftar Order</h3>
            <p class="text-sm text-slate-500">Filter status pembayaran dan cek detail customer secara cepat.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <?php foreach (['ALL', 'UNPAID', 'PAID', 'EXPIRED', 'FAILED'] as $status): ?>
                <a href="<?= e(route_url('admin/orders.php?status=' . $status)) ?>" class="rounded-2xl px-4 py-2 text-sm font-semibold <?= $statusFilter === $status ? 'bg-slate-900 text-white' : 'border border-slate-200 text-slate-700' ?>">
                    <?= e($status) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (!$orders): ?>
        <div class="rounded-2xl bg-slate-50 p-5 text-sm text-slate-600">Belum ada order untuk filter ini.</div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead>
                <tr class="border-b border-slate-100 text-slate-500">
                    <th class="pb-3 pr-4">Order</th>
                    <th class="pb-3 pr-4">Produk</th>
                    <th class="pb-3 pr-4">Customer</th>
                    <th class="pb-3 pr-4">Total</th>
                    <th class="pb-3 pr-4">Channel</th>
                    <th class="pb-3 pr-4">Status</th>
                    <th class="pb-3">Referensi</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr class="border-b border-slate-50 align-top">
                        <td class="py-4 pr-4">
                            <div class="font-semibold"><?= e($order['order_code']) ?></div>
                            <div class="text-xs text-slate-400"><?= e($order['created_at'] ?? '-') ?></div>
                        </td>
                        <td class="py-4 pr-4"><?= e($order['product_name']) ?></td>
                        <td class="py-4 pr-4">
                            <div class="font-medium"><?= e($order['customer_name']) ?></div>
                            <div class="text-xs text-slate-500"><?= e($order['customer_email'] ?? '-') ?></div>
                            <div class="text-xs text-slate-500"><?= e($order['customer_whatsapp']) ?></div>
                        </td>
                        <td class="py-4 pr-4"><?= e(money($order['amount'])) ?></td>
                        <td class="py-4 pr-4"><?= e($order['payment_channel'] ?: '-') ?></td>
                        <td class="py-4 pr-4">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold <?= e(admin_order_status_badge($order['payment_status'])) ?>">
                                <?= e($order['payment_status']) ?>
                            </span>
                        </td>
                        <td class="py-4 text-xs text-slate-500"><?= e($order['tripay_reference'] ?: '-') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/partials/layout-bottom.php'; ?>