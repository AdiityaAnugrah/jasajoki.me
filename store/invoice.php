<?php
require_once __DIR__ . '/../app/helpers.php';

$orderCode = (string) request_get('code', 'DEMO-001');
$order = app_is_installed() ? order_find_by_code($orderCode) : null;
$pageTitle = 'Invoice ' . $orderCode;
require __DIR__ . '/partials/header.php';
?>
<main class="px-4 pb-8 pt-4">
    <h1 class="text-2xl font-bold">Invoice</h1>
    <p class="mt-1 text-sm text-slate-500">Nanti halaman ini akan menampilkan status pembayaran Tripay secara real-time.</p>

    <section class="mt-5 rounded-3xl bg-white p-5 shadow-soft">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs text-slate-500">Kode Order</div>
                <div class="text-lg font-bold"><?= e($orderCode) ?></div>
            </div>
            <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-600"><?= e($order['payment_status'] ?? 'UNPAID') ?></span>
        </div>
        <div class="mt-5 grid gap-3 rounded-2xl bg-slate-50 p-4 text-sm">
            <div class="flex justify-between"><span>Produk</span><strong><?= e($order['product_name'] ?? 'Demo Product') ?></strong></div>
            <div class="flex justify-between"><span>Total</span><strong><?= e(money($order['amount'] ?? 22000)) ?></strong></div>
            <div class="flex justify-between"><span>Metode</span><strong><?= e($order['payment_channel'] ?? 'QRIS') ?></strong></div>
            <div class="flex justify-between"><span>Status Order</span><strong><?= e($order['order_status'] ?? 'PENDING') ?></strong></div>
        </div>
        <?php if ($order): ?>
            <div class="mt-4 rounded-2xl border border-slate-100 p-4 text-sm text-slate-600">
                <div><strong>Nama:</strong> <?= e($order['customer_name']) ?></div>
                <div class="mt-1"><strong>Account:</strong> <?= e($order['customer_account']) ?></div>
                <div class="mt-1"><strong>WhatsApp:</strong> <?= e($order['customer_whatsapp']) ?></div>
            </div>
        <?php endif; ?>
    </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
