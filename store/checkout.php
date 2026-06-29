<?php
require_once __DIR__ . '/../app/tripay.php';

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

$selectedChannel = (string) request_post('payment_channel', tripay_default_method());
$channels = tripay_payment_channels();
$validChannelCodes = array_column($channels, 'code');

if (!in_array($selectedChannel, $validChannelCodes, true)) {
    $selectedChannel = 'QRIS';
}

$createdOrderCode = null;
$tripayResponse = null;

if (app_is_installed()) {
    $createdOrderCode = orders_create([
        'product_id' => $product['id'],
        'customer_name' => $customerName,
        'customer_email' => $customerEmail,
        'customer_account' => $customerAccount,
        'customer_whatsapp' => $customerWhatsapp,
        'customer_notes' => $customerNotes,
        'amount' => $product['price'],
        'payment_channel' => $selectedChannel,
    ]);

    if (tripay_has_credentials()) {
        $callbackUrl = rtrim(app_config()['base_url'], '/') . '/callback-tripay.php';
        $returnUrl = rtrim(app_config()['base_url'], '/') . '/invoice.php?code=' . urlencode($createdOrderCode);

        $tripayResponse = tripay_create_transaction([
            'method' => $selectedChannel,
            'merchant_ref' => $createdOrderCode,
            'amount' => (int) $product['price'],
            'customer_name' => $customerName,
            'customer_email' => $customerEmail,
            'customer_phone' => $customerWhatsapp,
            'order_items' => [[
                'sku' => $product['slug'],
                'name' => $product['name'],
                'price' => (int) $product['price'],
                'quantity' => 1,
                'product_url' => rtrim(app_config()['base_url'], '/') . '/product.php?slug=' . $product['slug'],
            ]],
            'callback_url' => $callbackUrl,
            'return_url' => $returnUrl,
            'expired_time' => time() + (24 * 60 * 60),
        ]);

        $order = order_find_by_code($createdOrderCode);
        if ($order) {
            payment_log_create((int) $order['id'], 'tripay_create', json_encode($tripayResponse, JSON_UNESCAPED_SLASHES));
        }

        if (($tripayResponse['success'] ?? false) && !empty($tripayResponse['data'])) {
            $tripayData = $tripayResponse['data'];
            order_update_tripay_data($createdOrderCode, [
                'tripay_reference' => $tripayData['reference'] ?? null,
                'payment_channel' => $tripayData['payment_method'] ?? $selectedChannel,
                'payment_status' => $tripayData['status'] ?? 'UNPAID',
                'tripay_checkout_url' => $tripayData['checkout_url'] ?? null,
                'tripay_pay_code' => $tripayData['pay_code'] ?? null,
                'tripay_pay_url' => $tripayData['pay_url'] ?? null,
                'tripay_qr_url' => $tripayData['qr_url'] ?? null,
                'tripay_qr_string' => $tripayData['qr_string'] ?? null,
                'expired_time' => $tripayData['expired_time'] ?? null,
            ]);
        }
    } else {
        $tripayResponse = [
            'success' => false,
            'message' => 'Kredensial Tripay belum diisi.',
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
        <div class="mt-3 grid gap-3">
            <?php foreach ($channels as $channel): ?>
                <label class="flex items-center justify-between rounded-2xl border border-brand-200 bg-[#fffdf8] px-4 py-4">
                    <div>
                        <div class="font-semibold text-accent-900"><?= e($channel['name']) ?></div>
                        <div class="text-xs text-slate-500"><?= e($channel['code']) ?></div>
                    </div>
                    <input type="radio" name="channel" <?= $channel['code'] === $selectedChannel ? 'checked' : '' ?> disabled>
                </label>
            <?php endforeach; ?>
        </div>
        <div class="mt-4 rounded-2xl <?= ($tripayResponse['success'] ?? false) ? 'bg-accent-50 text-accent-700' : 'bg-brand-100/60 text-slate-700' ?> p-4 text-sm">
            <?php if ($createdOrderCode): ?>
                Order berhasil dibuat dengan kode <strong><?= e($createdOrderCode) ?></strong>.
                <?php if ($tripayResponse && !($tripayResponse['success'] ?? false)): ?>
                    <div class="mt-2"><?= e($tripayResponse['message']) ?></div>
                <?php elseif (!empty($tripayResponse['data']['checkout_url'])): ?>
                    <div class="mt-2">Transaksi berhasil dibuat. Lanjut ke invoice atau buka halaman pembayaran Tripay.</div>
                <?php endif; ?>
            <?php else: ?>
                Integrasi pembayaran belum aktif sempurna.
            <?php endif; ?>
        </div>
        <div class="mt-4 grid gap-3">
            <a href="<?= e(route_url('invoice.php?code=' . urlencode($createdOrderCode ?: ('DEMO-' . $product['id'])))) ?>" class="inline-flex w-full justify-center rounded-2xl bg-accent-700 px-4 py-3 text-sm font-semibold text-white">Lihat invoice</a>
            <?php if (!empty($tripayResponse['data']['checkout_url'])): ?>
                <a href="<?= e($tripayResponse['data']['checkout_url']) ?>" target="_blank" class="inline-flex w-full justify-center rounded-2xl border border-brand-300 px-4 py-3 text-sm font-semibold text-accent-800">Buka checkout Tripay</a>
            <?php endif; ?>
        </div>
    </section>
</main>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
