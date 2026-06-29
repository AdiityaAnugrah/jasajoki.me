<?php
require_once __DIR__ . '/../app/helpers.php';

$slug = (string) request_get('slug', '');
$product = find_product_by_slug($slug);

if (!$product) {
    http_response_code(404);
    $pageTitle = 'Produk tidak ditemukan';
    require __DIR__ . '/partials/header.php';
    echo '<main class="px-4 py-10"><h1 class="text-xl font-bold">Produk tidak ditemukan</h1><p class="mt-2 text-sm text-slate-500">Silakan kembali ke katalog store.</p><a class="mt-4 inline-flex rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white" href="' . e(route_url()) . '">Kembali</a></main>';
    require __DIR__ . '/partials/footer.php';
    exit;
}

$pageTitle = $product['name'];
require __DIR__ . '/partials/header.php';
?>
<main class="px-4 pb-8 pt-4">
    <a href="<?= e(route_url()) ?>" class="text-sm font-semibold text-slate-600">← Kembali ke store</a>

    <section class="mt-4 rounded-[28px] border border-stone-200 bg-white p-5 shadow-soft">
        <span class="inline-flex rounded-full bg-stone-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600"><?= e($product['badge']) ?></span>
        <h1 class="mt-3 text-2xl font-bold leading-tight text-slate-900"><?= e($product['name']) ?></h1>
        <p class="mt-3 text-sm leading-6 text-slate-600"><?= e($product['description']) ?></p>
        <div class="mt-5 rounded-3xl bg-stone-100 p-4">
            <div class="text-xs text-slate-500">Harga</div>
            <div class="mt-1 text-2xl font-extrabold text-slate-900"><?= e(money($product['price'])) ?></div>
        </div>
    </section>

    <section class="mt-5 rounded-[28px] border border-stone-200 bg-white p-5 shadow-soft">
        <h2 class="text-lg font-bold text-slate-900">Form pemesanan</h2>
        <p class="mt-1 text-sm text-slate-500">Isi data dengan benar supaya proses bisa langsung dilanjutkan setelah pembayaran.</p>
        <form action="<?= e(route_url('checkout.php')) ?>" method="post" class="mt-5 space-y-4">
            <input type="hidden" name="product_slug" value="<?= e($product['slug']) ?>">
            <div>
                <label class="mb-2 block text-sm font-semibold">Nama pelanggan</label>
                <input type="text" name="customer_name" class="w-full rounded-2xl border border-stone-300 bg-[#fcfcfa] px-4 py-3 text-sm outline-none focus:border-slate-600" placeholder="Nama kamu" required>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold">Email</label>
                <input type="email" name="customer_email" class="w-full rounded-2xl border border-stone-300 bg-[#fcfcfa] px-4 py-3 text-sm outline-none focus:border-slate-600" placeholder="nama@email.com" required>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold">UID / Username Akun</label>
                <input type="text" name="customer_account" class="w-full rounded-2xl border border-stone-300 bg-[#fcfcfa] px-4 py-3 text-sm outline-none focus:border-slate-600" placeholder="Masukkan UID atau username" required>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold">Nomor WhatsApp</label>
                <input type="text" name="customer_whatsapp" class="w-full rounded-2xl border border-stone-300 bg-[#fcfcfa] px-4 py-3 text-sm outline-none focus:border-slate-600" placeholder="08xxxxxxxxxx" required>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold">Catatan</label>
                <textarea name="customer_notes" class="min-h-[100px] w-full rounded-2xl border border-stone-300 bg-[#fcfcfa] px-4 py-3 text-sm outline-none focus:border-slate-600" placeholder="Contoh: request login, jam pengerjaan, atau catatan khusus lain."></textarea>
            </div>
            <button type="submit" class="w-full rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white">Lanjut ke checkout</button>
        </form>
    </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
