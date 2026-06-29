<?php
require_once __DIR__ . '/../../app/auth.php';
require_admin();

$pageTitle = 'Order';
$orders = orders_all();
require __DIR__ . '/../partials/header.php';
?>
<main class="min-h-screen bg-slate-100 p-4 md:p-8">
    <div class="mx-auto max-w-7xl rounded-3xl bg-white p-6 shadow-soft">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Manajemen Order</h1>
                <p class="mt-1 text-sm text-slate-500">Nanti dipakai untuk memantau order masuk dari Tripay.</p>
            </div>
            <a href="<?= e(route_url('admin/index.php')) ?>" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold">Kembali</a>
        </div>
        <?php if (!$orders): ?>
            <div class="rounded-2xl bg-slate-50 p-5 text-sm text-slate-600">
                Belum ada data order. Setelah checkout dijalankan, order akan tampil di sini.
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead>
                    <tr class="border-b border-slate-100 text-slate-500">
                        <th class="pb-3 pr-4">Order</th>
                        <th class="pb-3 pr-4">Produk</th>
                        <th class="pb-3 pr-4">Customer</th>
                        <th class="pb-3 pr-4">Total</th>
                        <th class="pb-3 pr-4">Payment</th>
                        <th class="pb-3">Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr class="border-b border-slate-50">
                            <td class="py-4 pr-4 font-semibold"><?= e($order['order_code']) ?></td>
                            <td class="py-4 pr-4"><?= e($order['product_name']) ?></td>
                            <td class="py-4 pr-4">
                                <div><?= e($order['customer_name']) ?></div>
                                <div class="text-xs text-slate-500"><?= e($order['customer_whatsapp']) ?></div>
                            </td>
                            <td class="py-4 pr-4"><?= e(money($order['amount'])) ?></td>
                            <td class="py-4 pr-4"><?= e($order['payment_channel']) ?></td>
                            <td class="py-4"><?= e($order['payment_status']) ?> / <?= e($order['order_status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
