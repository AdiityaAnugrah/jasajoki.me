<?php
require_once __DIR__ . '/../app/qrisify.php';

if (!is_post()) {
    redirect(route_url());
}

$customerName = trim((string) request_post('customer_name'));
$customerEmail = trim((string) request_post('customer_email'));
$customerAccount = trim((string) request_post('customer_account'));
$customerWhatsapp = trim((string) request_post('customer_whatsapp'));
$customerNotes = trim((string) request_post('customer_notes'));
$product = find_product_by_slug((string) request_post('product_slug'));

if (
    !$product
    || $customerName === ''
    || $customerEmail === ''
    || $customerAccount === ''
    || $customerWhatsapp === ''
) {
    flash('error', 'Produk tidak valid.');
    redirect(route_url());
}

$createdOrderCode = null;
$qrisifyResponse = null;

if (app_is_installed()) {
    $createdOrderCode = orders_create([
        'product_id' => $product['id'],
        'customer_name' => $customerName,
        'customer_email' => $customerEmail,
        'customer_account' => $customerAccount,
        'customer_whatsapp' => $customerWhatsapp,
        'customer_notes' => $customerNotes,
        'amount' => $product['price'],
        'payment_channel' => 'QRIS',
    ]);

    if (qrisify_has_credentials()) {
        $qrisifyResponse = qrisify_create_transaction([
            'amount' => (int) $product['price'],
            'external_id' => $createdOrderCode,
            'expiry_minutes' => 15,
            'webhook_url' => qrisify_webhook_url(),
            'webhook_secret' => qrisify_config()['webhook_secret'],
        ]);

        $order = order_find_by_code($createdOrderCode);
        if ($order) {
            payment_log_create((int) $order['id'], 'qrisify_create', json_encode($qrisifyResponse, JSON_UNESCAPED_SLASHES));
        }

        if (($qrisifyResponse['success'] ?? false) && !empty($qrisifyResponse['data'])) {
            $qrisifyData = $qrisifyResponse['data'];
            order_update_tripay_data($createdOrderCode, [
                'tripay_reference' => $qrisifyData['transaction_id'] ?? null,
                'payment_channel' => 'QRIS',
                'payment_status' => qrisify_normalize_status((string) ($qrisifyData['status'] ?? 'PENDING')),
                'tripay_checkout_url' => null,
                'tripay_pay_code' => (string) ($qrisifyData['amount_total'] ?? ''),
                'tripay_pay_url' => null,
                'tripay_qr_url' => qrisify_qr_image_url($qrisifyData['qr_image_url'] ?? null),
                'tripay_qr_string' => $qrisifyData['qris_string'] ?? null,
                'expired_time' => !empty($qrisifyData['expires_at']) ? strtotime((string) $qrisifyData['expires_at']) : null,
            ]);
        }
    } else {
        $qrisifyResponse = [
            'success' => false,
            'message' => 'Kredensial QRISify belum diisi.',
            'data' => null,
        ];
    }
}

$pageTitle = 'Checkout - ' . $product['name'];
require __DIR__ . '/partials/header.php';
?>
<div class="min-h-screen">
<main class="store-container px-4 pb-10 pt-5 md:px-6 lg:px-8">
    <a href="<?= e(route_url('product.php?slug=' . $product['slug'])) ?>" class="subtle-link text-sm font-semibold">Kembali ke detail produk</a>

    <section class="mt-4 grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
        <div class="section-card p-5 md:p-7">
            <p class="eyebrow text-[11px] font-semibold">Checkout summary</p>
            <h1 class="title-display mt-4 text-[34px] leading-[0.95] text-[#171411] md:text-[50px]">Review pesananmu sebelum lanjut ke pembayaran.</h1>
            <p class="store-muted mt-4 text-sm leading-7">Semua data sudah kami siapkan. Lanjutkan ke invoice untuk scan QRIS dan pantau status transaksi.</p>

            <div class="store-line mt-6 space-y-4 border-t pt-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-xs uppercase tracking-[0.18em] text-[#6d6054]">Produk</div>
                        <div class="mt-2 text-xl font-semibold text-[#171411]"><?= e($product['name']) ?></div>
                    </div>
                    <div class="store-chip px-4 py-2 text-xs font-semibold"><?= e($product['badge'] ?: 'Ready') ?></div>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="info-block p-4">
                        <div class="text-xs uppercase tracking-[0.18em] text-[#6d6054]">Harga produk</div>
                        <div class="mt-2 text-2xl font-semibold text-[#171411]"><?= e(money($product['price'])) ?></div>
                    </div>
                    <div class="info-block p-4">
                        <div class="text-xs uppercase tracking-[0.18em] text-[#6d6054]">Metode</div>
                        <div class="mt-2 text-2xl font-semibold text-[#171411]">QRIS</div>
                    </div>
                </div>
                <div class="info-block p-4">
                    <div class="text-sm font-semibold text-[#171411]"><?= e($customerName) ?></div>
                    <div class="store-muted mt-1 text-sm"><?= e($customerEmail) ?></div>
                    <div class="store-muted mt-1 text-sm"><?= e($customerWhatsapp) ?></div>
                    <div class="store-muted mt-2 text-sm">Akun/UID: <?= e($customerAccount) ?></div>
                </div>
            </div>
        </div>

        <div class="section-card p-5 md:p-7">
            <p class="eyebrow text-[11px] font-semibold">Payment status</p>
            <h2 class="mt-3 text-2xl font-semibold text-[#171411]">QRISify QRIS</h2>
            <div class="mt-4 rounded-[24px] <?= ($qrisifyResponse['success'] ?? false) ? 'bg-[#171411] text-[#f8f4ed]' : 'bg-white/70 text-[#171411]' ?> p-4 text-sm leading-6">
                <?php if ($createdOrderCode): ?>
                    Order berhasil dibuat dengan kode <strong><?= e($createdOrderCode) ?></strong>.
                    <?php if ($qrisifyResponse && !($qrisifyResponse['success'] ?? false)): ?>
                        <div class="mt-2"><?= e($qrisifyResponse['message']) ?></div>
                    <?php elseif (!empty($qrisifyResponse['data']['transaction_id'])): ?>
                        <div class="mt-2">QR pembayaran berhasil dibuat. Buka invoice untuk scan QR dan refresh status pembayaran.</div>
                    <?php endif; ?>
                <?php else: ?>
                    Integrasi pembayaran belum aktif sempurna.
                <?php endif; ?>
            </div>

            <div class="store-line mt-6 border-t pt-6">
                <div class="grid gap-3">
                    <a href="<?= e(route_url('invoice.php?code=' . urlencode($createdOrderCode ?: ('DEMO-' . $product['id'])))) ?>" class="btn-primary w-full px-4 py-3 text-sm font-semibold">Buka invoice pembayaran</a>
                    <a href="<?= e(route_url()) ?>" class="btn-secondary w-full px-4 py-3 text-sm font-semibold">Kembali ke katalog</a>
                </div>
            </div>
        </div>
    </section>
</main>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
