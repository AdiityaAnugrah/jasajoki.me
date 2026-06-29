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
<div class="mobile-shell min-h-screen">
<main class="px-4 pb-8 pt-4">
    <a href="<?= e(route_url('product.php?slug=' . $product['slug'])) ?>" class="text-sm font-semibold text-accent-700">← Edit data</a>
    <h1 class="mt-4 text-[30px] font-black text-accent-900">Checkout</h1>
    <p class="mt-1 text-sm text-slate-500">Review pesanan dan lanjutkan ke pembayaran dengan tampilan yang lebih sederhana.</p>

    <section class="card-soft mt-5 rounded-[32px] p-5">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs text-slate-500">Produk</div>
                <div class="font-bold"><?= e($product['name']) ?></div>
            </div>
            <div class="text-lg font-extrabold text-accent-900"><?= e(money($product['price'])) ?></div>
        </div>
    </section>

    <section class="card-soft mt-5 rounded-[32px] p-5">
        <h2 class="text-base font-bold text-accent-900">Metode pembayaran</h2>
        <div class="mt-3 rounded-2xl border border-brand-200 bg-[#fffdf8] px-4 py-4">
            <div class="font-semibold text-accent-900">QRIS Dinamis</div>
            <div class="text-xs text-slate-500">Diproses melalui QRISify</div>
        </div>
        <div class="mt-4 rounded-2xl <?= ($qrisifyResponse['success'] ?? false) ? 'bg-accent-50 text-accent-700' : 'bg-brand-100/60 text-slate-700' ?> p-4 text-sm">
            <?php if ($createdOrderCode): ?>
                Order berhasil dibuat dengan kode <strong><?= e($createdOrderCode) ?></strong>.
                <?php if ($qrisifyResponse && !($qrisifyResponse['success'] ?? false)): ?>
                    <div class="mt-2"><?= e($qrisifyResponse['message']) ?></div>
                <?php elseif (!empty($qrisifyResponse['data']['transaction_id'])): ?>
                    <div class="mt-2">QR pembayaran berhasil dibuat. Lanjut ke invoice untuk scan dan pantau statusnya.</div>
                <?php endif; ?>
            <?php else: ?>
                Integrasi pembayaran belum aktif sempurna.
            <?php endif; ?>
        </div>
        <div class="mt-4 grid gap-3">
            <a href="<?= e(route_url('invoice.php?code=' . urlencode($createdOrderCode ?: ('DEMO-' . $product['id'])))) ?>" class="inline-flex w-full justify-center rounded-2xl bg-accent-700 px-4 py-3 text-sm font-semibold text-white">Lihat invoice</a>
        </div>
    </section>
</main>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
