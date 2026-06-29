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
<div class="mobile-shell min-h-screen">
<main class="px-4 pb-8 pt-4">
    <a href="<?= e(route_url()) ?>" class="text-sm font-semibold text-accent-700">← Kembali ke store</a>

    <section class="card-soft mt-4 overflow-hidden rounded-[32px] p-3">
        <?php if (!empty($product['image_url'])): ?>
            <img src="<?= e($product['image_url']) ?>" alt="<?= e($product['name']) ?>" class="store-media">
        <?php else: ?>
            <div class="store-media-fallback flex items-end p-5">
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-[0.22em] text-brand-100"><?= e($product['category_name'] ?? 'Produk') ?></div>
                    <div class="mt-2 text-3xl font-black leading-tight"><?= e($product['name']) ?></div>
                </div>
            </div>
        <?php endif; ?>
        <div class="p-2 pt-5">
            <span class="inline-flex rounded-full bg-accent-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-accent-700"><?= e($product['badge']) ?></span>
            <h1 class="mt-3 text-[30px] font-black leading-tight text-accent-900"><?= e($product['name']) ?></h1>
            <p class="mt-3 text-sm leading-6 text-slate-600"><?= e($product['description']) ?></p>
            <div class="mt-5 rounded-3xl bg-brand-100/60 p-4">
                <div class="text-xs text-slate-500">Harga mulai</div>
                <div class="mt-1 text-2xl font-extrabold text-accent-900"><?= e(money($product['price'])) ?></div>
            </div>
        </div>
    </section>

    <section class="card-soft mt-5 rounded-[32px] p-5">
        <h2 class="text-lg font-bold text-accent-900">Form pemesanan</h2>
        <p class="mt-1 text-sm text-slate-500">Isi data dengan benar supaya proses bisa langsung dilanjutkan setelah pembayaran.</p>
        <form action="<?= e(route_url('checkout.php')) ?>" method="post" class="mt-5 space-y-4">
            <input type="hidden" name="product_slug" value="<?= e($product['slug']) ?>">
            <div>
                <label class="mb-2 block text-sm font-semibold">Nama pelanggan</label>
                <input type="text" name="customer_name" class="w-full rounded-2xl border border-brand-200 bg-[#fffdf8] px-4 py-3 text-sm outline-none focus:border-accent-500" placeholder="Nama kamu" required>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold">Email</label>
                <input type="email" name="customer_email" class="w-full rounded-2xl border border-brand-200 bg-[#fffdf8] px-4 py-3 text-sm outline-none focus:border-accent-500" placeholder="nama@email.com" required>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold">UID / Username Akun</label>
                <input type="text" name="customer_account" class="w-full rounded-2xl border border-brand-200 bg-[#fffdf8] px-4 py-3 text-sm outline-none focus:border-accent-500" placeholder="Masukkan UID atau username" required>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold">Nomor WhatsApp</label>
                <input type="text" name="customer_whatsapp" class="w-full rounded-2xl border border-brand-200 bg-[#fffdf8] px-4 py-3 text-sm outline-none focus:border-accent-500" placeholder="08xxxxxxxxxx" required>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold">Catatan</label>
                <textarea name="customer_notes" class="min-h-[100px] w-full rounded-2xl border border-brand-200 bg-[#fffdf8] px-4 py-3 text-sm outline-none focus:border-accent-500" placeholder="Contoh: request login, jam pengerjaan, atau catatan khusus lain."></textarea>
            </div>
            <button type="submit" class="w-full rounded-2xl bg-accent-700 px-4 py-3 text-sm font-semibold text-white">Lanjut ke checkout</button>
        </form>
    </section>
</main>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
