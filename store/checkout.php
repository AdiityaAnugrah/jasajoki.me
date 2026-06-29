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
<main class="px-4 pb-8 pt-4">
    <a href="<?= e(route_url('product.php?slug=' . $product['slug'])) ?>" class="text-sm font-semibold text-blue-600">← Edit data</a>
    <h1 class="mt-4 text-2xl font-bold">Checkout</h1>
    <p class="mt-1 text-sm text-slate-500">Tahap berikutnya tinggal pilih metode pembayaran Tripay.</p>

    <section class="mt-5 rounded-3xl bg-white p-5 shadow-soft">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs text-slate-500">Produk</div>
                <div class="font-bold"><?= e($product['name']) ?></div>
            </div>
            <div class="text-lg font-extrabold"><?= e(money($product['price'])) ?></div>
        </div>
    </section>

    <section class="mt-5 rounded-3xl border border-slate-100 bg-white p-5">
        <h2 class="text-base font-bold">Metode pembayaran</h2>
        <div class="mt-3 grid gap-3">
            <?php foreach ($channels as $channel): ?>
                <label class="flex items-center justify-between rounded-2xl border border-slate-200 px-4 py-4">
                    <div>
                        <div class="font-semibold"><?= e($channel['name']) ?></div>
                        <div class="text-xs text-slate-500"><?= e($channel['code']) ?></div>
                    </div>
                    <input type="radio" name="channel" <?= $channel['code'] === $selectedChannel ? 'checked' : '' ?> disabled>
                </label>
            <?php endforeach; ?>
        </div>
        <div class="mt-4 rounded-2xl <?= ($tripayResponse['success'] ?? false) ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' ?> p-4 text-sm">
            <?php if ($createdOrderCode): ?>
                Order berhasil dibuat dengan kode <strong><?= e($createdOrderCode) ?></strong>.
                <?php if ($tripayResponse && !($tripayResponse['success'] ?? false)): ?>
                    <div class="mt-2"><?= e($tripayResponse['message']) ?></div>
                <?php elseif (!empty($tripayResponse['data']['checkout_url'])): ?>
                    <div class="mt-2">Transaksi Tripay berhasil dibuat. Lanjut ke invoice atau buka halaman pembayaran Tripay.</div>
                <?php endif; ?>
            <?php else: ?>
                Integrasi Tripay masih stub. Setelah database di-setup dan API key diisi, halaman ini akan membuat transaksi real.
            <?php endif; ?>
        </div>
        <div class="mt-4 grid gap-3">
            <a href="<?= e(route_url('invoice.php?code=' . urlencode($createdOrderCode ?: ('DEMO-' . $product['id'])))) ?>" class="inline-flex w-full justify-center rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white">Lihat Invoice</a>
            <?php if (!empty($tripayResponse['data']['checkout_url'])): ?>
                <a href="<?= e($tripayResponse['data']['checkout_url']) ?>" target="_blank" class="inline-flex w-full justify-center rounded-2xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-800">Buka Halaman Pembayaran Tripay</a>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
