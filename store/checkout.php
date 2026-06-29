<?php
require_once __DIR__ . '/../app/tripay.php';

if (!is_post()) {
    redirect(route_url());
}

$customerName = trim((string) request_post('customer_name'));
$customerAccount = trim((string) request_post('customer_account'));
$customerWhatsapp = trim((string) request_post('customer_whatsapp'));
$customerNotes = trim((string) request_post('customer_notes'));
$product = find_product_by_slug((string) request_post('product_slug'));

if (!$product || $customerName === '' || $customerAccount === '' || $customerWhatsapp === '') {
    flash('error', 'Produk tidak valid.');
    redirect(route_url());
}

$selectedChannel = (string) request_post('payment_channel', 'QRIS');
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
        'customer_account' => $customerAccount,
        'customer_whatsapp' => $customerWhatsapp,
        'customer_notes' => $customerNotes,
        'amount' => $product['price'],
        'payment_channel' => $selectedChannel,
    ]);

    $tripayResponse = tripay_create_transaction([
        'merchant_ref' => $createdOrderCode,
        'amount' => $product['price'],
        'customer_name' => $customerName,
        'customer_email' => '',
        'customer_phone' => $customerWhatsapp,
        'order_items' => [[
            'sku' => $product['slug'],
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => 1,
        ]],
        'return_url' => app_config()['base_url'] . '/invoice.php?code=' . urlencode($createdOrderCode),
    ]);
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
        <div class="mt-4 rounded-2xl <?= $createdOrderCode ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' ?> p-4 text-sm">
            <?php if ($createdOrderCode): ?>
                Order berhasil dibuat dengan kode <strong><?= e($createdOrderCode) ?></strong>.
                <?php if ($tripayResponse && !$tripayResponse['success']): ?>
                    <div class="mt-2"><?= e($tripayResponse['message']) ?></div>
                <?php endif; ?>
            <?php else: ?>
                Integrasi Tripay masih stub. Setelah database di-setup dan API key diisi, halaman ini akan membuat transaksi real.
            <?php endif; ?>
        </div>
        <a href="<?= e(route_url('invoice.php?code=' . urlencode($createdOrderCode ?: ('DEMO-' . $product['id'])))) ?>" class="mt-4 inline-flex w-full justify-center rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white">Lihat Invoice</a>
    </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
