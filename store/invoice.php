<?php
require_once __DIR__ . '/../app/qrisify.php';

$orderCode = (string) request_get('code', 'DEMO-001');
$order = app_is_installed() ? order_find_by_code($orderCode) : null;
$flashSuccess = null;
$flashError = null;

if (is_post() && (string) request_post('action') === 'test_pay' && $order && !empty($order['tripay_reference']) && !qrisify_is_live()) {
    $testPay = qrisify_test_pay((string) $order['tripay_reference']);

    if (($testPay['success'] ?? false) === true) {
        flash('success', 'Simulasi pembayaran test berhasil dijalankan.');
    } else {
        flash('error', (string) ($testPay['message'] ?? 'Simulasi pembayaran test gagal.'));
    }

    redirect(route_url('invoice.php?code=' . urlencode($orderCode)));
}

if ($order && !empty($order['tripay_reference']) && qrisify_has_credentials()) {
    $detail = qrisify_transaction_detail($order['tripay_reference']);

    if (($detail['success'] ?? false) && !empty($detail['data'])) {
        $qrisifyData = $detail['data'];
        order_update_tripay_data($orderCode, [
            'tripay_reference' => $qrisifyData['transaction_id'] ?? $order['tripay_reference'],
            'payment_channel' => 'QRIS',
            'payment_status' => qrisify_normalize_status((string) ($qrisifyData['status'] ?? $order['payment_status'])),
            'tripay_checkout_url' => null,
            'tripay_pay_code' => (string) ($qrisifyData['amount_total'] ?? $order['tripay_pay_code']),
            'tripay_pay_url' => null,
            'tripay_qr_url' => qrisify_qr_image_url($qrisifyData['qr_image_url'] ?? $order['tripay_qr_url']),
            'tripay_qr_string' => $qrisifyData['qris_string'] ?? $order['tripay_qr_string'],
            'expired_time' => !empty($qrisifyData['expires_at']) ? strtotime((string) $qrisifyData['expires_at']) : $order['expired_time'],
        ]);
        order_update_status_by_reference($orderCode, (string) ($qrisifyData['transaction_id'] ?? $order['tripay_reference']), qrisify_normalize_status((string) ($qrisifyData['status'] ?? $order['payment_status'])));
        $order = order_find_by_code($orderCode);
    }
}

$flashSuccess = flash('success');
$flashError = flash('error');

$pageTitle = 'Invoice ' . $orderCode;
require __DIR__ . '/partials/header.php';
?>
<div class="mobile-shell min-h-screen">
<main class="px-4 pb-8 pt-4">
    <div class="rounded-[32px] bg-[#143c36] p-5 text-white shadow-soft">
        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-brand-100">Pembayaran QRIS</p>
        <h1 class="mt-3 text-[34px] font-black leading-[0.95]">Invoice pembayaran</h1>
        <p class="mt-3 text-sm leading-6 text-brand-100">Scan QR, pantau status transaksi, dan selesaikan pembayaran dari halaman ini.</p>
    </div>

    <?php if ($flashSuccess): ?>
        <div class="mt-4 rounded-[24px] bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"><?= e($flashSuccess) ?></div>
    <?php endif; ?>
    <?php if ($flashError): ?>
        <div class="mt-4 rounded-[24px] bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700"><?= e($flashError) ?></div>
    <?php endif; ?>

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
            <div class="flex justify-between"><span>Metode</span><strong>QRISify QRIS</strong></div>
            <div class="flex justify-between"><span>Status Order</span><strong><?= e($order['order_status'] ?? 'PENDING') ?></strong></div>
        </div>

        <?php if ($order): ?>
            <div class="mt-4 grid grid-cols-2 gap-3">
                <div class="rounded-[24px] bg-[#faf4e6] p-4">
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Nominal bayar</div>
                    <div class="mt-2 text-2xl font-black text-accent-900"><?= e(money((float) ($order['tripay_pay_code'] ?: $order['amount']))) ?></div>
                </div>
                <div class="rounded-[24px] bg-[#edf2ec] p-4">
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Provider</div>
                    <div class="mt-2 text-2xl font-black text-accent-900">QRISify</div>
                </div>
            </div>

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

            <div class="mt-4 grid gap-3">
                <a href="<?= e(route_url('invoice.php?code=' . urlencode($orderCode))) ?>" class="inline-flex w-full justify-center rounded-2xl bg-accent-700 px-4 py-3 text-sm font-semibold text-white">Refresh status pembayaran</a>
                <?php if (!qrisify_is_live() && !empty($order['tripay_reference'])): ?>
                    <form method="post">
                        <input type="hidden" name="action" value="test_pay">
                        <button type="submit" class="inline-flex w-full justify-center rounded-2xl border border-brand-300 bg-white px-4 py-3 text-sm font-semibold text-accent-800">Simulasikan pembayaran test</button>
                    </form>
                <?php endif; ?>
            </div>

            <?php if (!empty($order['tripay_pay_code'])): ?>
                <div class="mt-4 rounded-3xl border border-brand-200 p-4 text-sm text-slate-700">
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Nominal final QRIS</div>
                    <div class="mt-2 text-xl font-extrabold"><?= e(money((float) $order['tripay_pay_code'])) ?></div>
                    <div class="mt-2 text-xs text-slate-500">Sudah termasuk unique code dari gateway jika ada.</div>
                </div>
            <?php endif; ?>

            <?php if (!empty($order['tripay_qr_string'])): ?>
                <div class="mt-4 rounded-3xl border border-brand-200 p-4 text-xs leading-6 text-slate-600 break-all">
                    <div class="mb-2 text-xs uppercase tracking-[0.2em] text-slate-400">QR String</div>
                    <?= e($order['tripay_qr_string']) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($order['expired_time'])): ?>
                <div class="mt-4 rounded-[24px] bg-[#fff7e8] px-4 py-3 text-sm text-slate-500">
                    Berlaku sampai: <strong><?= e(date('d M Y H:i', (int) $order['expired_time'])) ?></strong>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</main>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
