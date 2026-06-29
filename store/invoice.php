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
<main class="store-container px-4 pb-10 pt-5 md:px-6 lg:px-8">
    <section class="grid gap-6 xl:grid-cols-[0.92fr_1.08fr]">
        <div class="card-soft rounded-[32px] p-5 md:p-7">
            <p class="store-kicker text-xs font-bold uppercase tracking-[0.24em]">Invoice pembayaran</p>
            <h1 class="store-heading mt-4 text-[34px] font-black leading-[0.95] text-[#f7f3ea] md:text-[48px]">Scan QR dan pantau status transaksi secara real-time.</h1>
            <p class="store-muted mt-4 text-sm leading-7">Begitu pembayaran terdeteksi, status order akan ikut berubah otomatis.</p>

            <?php if ($flashSuccess): ?>
                <div class="mt-4 rounded-[24px] bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"><?= e($flashSuccess) ?></div>
            <?php endif; ?>
            <?php if ($flashError): ?>
                <div class="mt-4 rounded-[24px] bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700"><?= e($flashError) ?></div>
            <?php endif; ?>

            <div class="store-line mt-6 border-t pt-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <div class="text-xs uppercase tracking-[0.18em] text-[#cab38f]">Kode order</div>
                        <div class="mt-2 text-xl font-black text-[#f7f3ea]"><?= e($orderCode) ?></div>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-semibold <?= e(admin_order_status_badge($order['payment_status'] ?? 'UNPAID')) ?>">
                        <?= e($order['payment_status'] ?? 'UNPAID') ?>
                    </span>
                </div>

                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    <div class="store-stat p-4">
                        <div class="text-xs uppercase tracking-[0.18em] text-[#cab38f]">Nominal bayar</div>
                        <div class="mt-2 text-2xl font-black text-[#f7f3ea]"><?= e(money((float) ($order['tripay_pay_code'] ?: $order['amount'] ?: 0))) ?></div>
                    </div>
                    <div class="store-stat p-4">
                        <div class="text-xs uppercase tracking-[0.18em] text-[#cab38f]">Provider</div>
                        <div class="mt-2 text-2xl font-black text-[#f7f3ea]">QRISify</div>
                    </div>
                </div>

                <div class="mt-4 rounded-[24px] border border-white/8 bg-white/4 p-4 text-sm">
                    <div class="flex justify-between gap-4"><span class="store-muted">Produk</span><strong class="text-[#f7f3ea]"><?= e($order['product_name'] ?? 'Demo Product') ?></strong></div>
                    <div class="mt-2 flex justify-between gap-4"><span class="store-muted">Metode</span><strong class="text-[#f7f3ea]">QRISify QRIS</strong></div>
                    <div class="mt-2 flex justify-between gap-4"><span class="store-muted">Status order</span><strong class="text-[#f7f3ea]"><?= e($order['order_status'] ?? 'PENDING') ?></strong></div>
                </div>
            </div>
        </div>

        <div class="card-soft rounded-[32px] p-5 md:p-7">
            <?php if ($order): ?>
                <div class="grid gap-4 xl:grid-cols-[0.95fr_1.05fr]">
                    <div class="rounded-[28px] border border-white/8 bg-white/4 p-4 text-center">
                        <div class="mb-3 text-sm font-semibold text-[#f7f3ea]">Scan QR untuk bayar</div>
                        <?php if (!empty($order['tripay_qr_url'])): ?>
                            <img src="<?= e($order['tripay_qr_url']) ?>" alt="QRIS" class="mx-auto w-full max-w-[280px] rounded-2xl border border-white/10 bg-white p-2">
                        <?php else: ?>
                            <div class="store-media-fallback flex !h-[280px] items-center justify-center p-6">
                                <div class="text-center">
                                    <div class="text-sm font-bold uppercase tracking-[0.2em] text-brand-100">QR belum tersedia</div>
                                    <div class="mt-3 text-2xl font-black">Coba refresh invoice</div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="space-y-4">
                        <div class="rounded-[24px] border border-white/8 bg-white/4 p-4 text-sm">
                            <div><strong class="text-[#f7f3ea]">Nama:</strong> <span class="store-muted"><?= e($order['customer_name']) ?></span></div>
                            <div class="mt-2"><strong class="text-[#f7f3ea]">Email:</strong> <span class="store-muted"><?= e($order['customer_email'] ?? '-') ?></span></div>
                            <div class="mt-2"><strong class="text-[#f7f3ea]">Account:</strong> <span class="store-muted"><?= e($order['customer_account']) ?></span></div>
                            <div class="mt-2"><strong class="text-[#f7f3ea]">WhatsApp:</strong> <span class="store-muted"><?= e($order['customer_whatsapp']) ?></span></div>
                        </div>

                        <?php if (!empty($order['tripay_pay_code'])): ?>
                            <div class="rounded-[24px] border border-white/8 bg-white/4 p-4 text-sm">
                                <div class="text-xs uppercase tracking-[0.2em] text-[#cab38f]">Nominal final QRIS</div>
                                <div class="mt-2 text-2xl font-black text-[#f7f3ea]"><?= e(money((float) $order['tripay_pay_code'])) ?></div>
                                <div class="store-muted mt-2 text-xs">Sudah termasuk unique code dari gateway jika ada.</div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($order['expired_time'])): ?>
                            <div class="rounded-[24px] bg-[#f0dfbf] px-4 py-3 text-sm text-[#171717]">
                                Berlaku sampai: <strong><?= e(date('d M Y H:i', (int) $order['expired_time'])) ?></strong>
                            </div>
                        <?php endif; ?>

                        <div class="grid gap-3">
                            <a href="<?= e(route_url('invoice.php?code=' . urlencode($orderCode))) ?>" class="store-cta inline-flex w-full justify-center rounded-2xl px-4 py-3 text-sm font-semibold">Refresh status pembayaran</a>
                            <?php if (!qrisify_is_live() && !empty($order['tripay_reference'])): ?>
                                <form method="post">
                                    <input type="hidden" name="action" value="test_pay">
                                    <button type="submit" class="store-outline inline-flex w-full justify-center rounded-2xl px-4 py-3 text-sm font-semibold">Simulasikan pembayaran test</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php if (!empty($order['tripay_qr_string'])): ?>
                    <div class="store-line mt-5 rounded-[24px] border border-white/8 bg-white/4 p-4 text-xs leading-6 text-[#d9d1c4] break-all">
                        <div class="mb-2 text-xs uppercase tracking-[0.2em] text-[#cab38f]">QR String</div>
                        <?= e($order['tripay_qr_string']) ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>
</main>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
