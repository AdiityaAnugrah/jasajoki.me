<?php
require_once __DIR__ . '/../app/tripay.php';

$orderCode = (string) request_get('code', 'DEMO-001');
$order = app_is_installed() ? order_find_by_code($orderCode) : null;

if ($order && !empty($order['tripay_reference']) && tripay_has_credentials()) {
    $detail = tripay_detail_transaction($order['tripay_reference']);

    if (($detail['success'] ?? false) && !empty($detail['data'])) {
        $tripayData = $detail['data'];
        order_update_tripay_data($orderCode, [
            'tripay_reference' => $tripayData['reference'] ?? $order['tripay_reference'],
            'payment_channel' => $tripayData['payment_method_code'] ?? $order['payment_channel'],
            'payment_status' => $tripayData['status'] ?? $order['payment_status'],
            'tripay_checkout_url' => $tripayData['checkout_url'] ?? $order['tripay_checkout_url'],
            'tripay_pay_code' => $tripayData['pay_code'] ?? $order['tripay_pay_code'],
            'tripay_pay_url' => $tripayData['pay_url'] ?? $order['tripay_pay_url'],
            'tripay_qr_url' => $tripayData['qr_url'] ?? $order['tripay_qr_url'],
            'tripay_qr_string' => $tripayData['qr_string'] ?? $order['tripay_qr_string'],
            'expired_time' => $tripayData['expired_time'] ?? $order['expired_time'],
        ]);
        order_update_status_by_reference($orderCode, $tripayData['reference'] ?? $order['tripay_reference'], strtoupper((string) ($tripayData['status'] ?? $order['payment_status'])));
        $order = order_find_by_code($orderCode);
    }
}

$pageTitle = 'Invoice ' . $orderCode;
require __DIR__ . '/partials/header.php';
?>
<div class="mobile-shell min-h-screen">
<main class="px-4 pb-8 pt-4">
    <h1 class="text-[30px] font-black text-accent-900">Invoice pembayaran</h1>
    <p class="mt-1 text-sm text-slate-500">Pantau status transaksi dan selesaikan pembayaran dari halaman ini.</p>

    <section class="card-soft mt-5 rounded-[32px] p-5">
        <div class="flex items-center justify-between gap-3">
            <div>
                <div class="text-xs text-slate-500">Kode Order</div>
                <div class="text-lg font-bold text-accent-900"><?= e($orderCode) ?></div>
            </div>
            <span class="rounded-full px-3 py-1 text-xs font-semibold <?= e(admin_order_status_badge($order['payment_status'] ?? 'UNPAID')) ?>">
                <?= e($order['payment_status'] ?? 'UNPAID') ?>
            </span>
        </div>

        <div class="mt-5 grid gap-3 rounded-3xl bg-brand-100/60 p-4 text-sm">
            <div class="flex justify-between"><span>Produk</span><strong><?= e($order['product_name'] ?? 'Demo Product') ?></strong></div>
            <div class="flex justify-between"><span>Total</span><strong><?= e(money($order['amount'] ?? 22000)) ?></strong></div>
            <div class="flex justify-between"><span>Metode</span><strong><?= e($order['payment_channel'] ?? 'QRIS') ?></strong></div>
            <div class="flex justify-between"><span>Status Order</span><strong><?= e($order['order_status'] ?? 'PENDING') ?></strong></div>
        </div>

        <?php if ($order): ?>
            <div class="mt-4 rounded-3xl border border-brand-200 p-4 text-sm text-slate-600">
                <div><strong>Nama:</strong> <?= e($order['customer_name']) ?></div>
                <div class="mt-1"><strong>Email:</strong> <?= e($order['customer_email'] ?? '-') ?></div>
                <div class="mt-1"><strong>Account:</strong> <?= e($order['customer_account']) ?></div>
                <div class="mt-1"><strong>WhatsApp:</strong> <?= e($order['customer_whatsapp']) ?></div>
            </div>

            <?php if (!empty($order['tripay_qr_url'])): ?>
                <div class="mt-4 rounded-3xl border border-brand-200 p-4 text-center">
                    <div class="mb-3 text-sm font-semibold text-slate-700">Scan QR untuk bayar</div>
                    <img src="<?= e($order['tripay_qr_url']) ?>" alt="QRIS" class="mx-auto w-full max-w-[260px] rounded-2xl border border-brand-200">
                </div>
            <?php endif; ?>

            <?php if (!empty($order['tripay_pay_code'])): ?>
                <div class="mt-4 rounded-3xl border border-brand-200 p-4 text-sm text-slate-700">
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Kode bayar / VA</div>
                    <div class="mt-2 text-xl font-extrabold"><?= e($order['tripay_pay_code']) ?></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($order['tripay_checkout_url'])): ?>
                <a href="<?= e($order['tripay_checkout_url']) ?>" target="_blank" class="mt-4 inline-flex w-full justify-center rounded-2xl bg-accent-700 px-4 py-3 text-sm font-semibold text-white">Buka checkout Tripay</a>
            <?php endif; ?>

            <?php if (!empty($order['expired_time'])): ?>
                <div class="mt-4 text-sm text-slate-500">
                    Berlaku sampai: <strong><?= e(date('d M Y H:i', (int) $order['expired_time'])) ?></strong>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</main>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
