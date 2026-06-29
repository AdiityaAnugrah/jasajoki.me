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
<main class="store-container px-4 pb-10 pt-5 md:px-6 lg:px-8">
    <a href="<?= e(route_url()) ?>" class="text-sm font-semibold text-[#f0dfbf]">← Kembali ke store</a>

    <section class="mt-4 grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
        <div class="card-soft overflow-hidden rounded-[32px] p-3">
            <?php if (!empty($product['image_url'])): ?>
                <img src="<?= e($product['image_url']) ?>" alt="<?= e($product['name']) ?>" class="store-media !h-[260px] md:!h-[340px]">
            <?php else: ?>
                <div class="store-media-fallback flex !h-[260px] items-end p-5 md:!h-[340px]">
                    <div>
                        <div class="text-[11px] font-bold uppercase tracking-[0.22em] text-brand-100"><?= e($product['category_name'] ?? 'Produk') ?></div>
                        <div class="mt-2 text-3xl font-black leading-tight"><?= e($product['name']) ?></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="card-soft rounded-[32px] p-5 md:p-7">
            <span class="inline-flex rounded-full bg-[#f0dfbf] px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-[#171717]"><?= e($product['badge']) ?></span>
            <h1 class="store-heading mt-4 text-[34px] font-black leading-[0.95] text-[#f7f3ea] md:text-[48px]"><?= e($product['name']) ?></h1>
            <p class="store-muted mt-4 text-sm leading-7 md:text-base"><?= e($product['description']) ?></p>
            <div class="store-line mt-6 grid gap-4 border-t pt-6 sm:grid-cols-2">
                <div class="store-stat p-4">
                    <div class="text-xs uppercase tracking-[0.18em] text-[#cab38f]">Harga</div>
                    <div class="mt-2 text-2xl font-black text-[#f7f3ea]"><?= e(money($product['price'])) ?></div>
                </div>
                <div class="store-stat p-4">
                    <div class="text-xs uppercase tracking-[0.18em] text-[#cab38f]">Kategori</div>
                    <div class="mt-2 text-lg font-black text-[#f7f3ea]"><?= e($product['category_name'] ?? 'Store') ?></div>
                </div>
            </div>
        </div>

        <section class="card-soft rounded-[32px] p-5 md:p-7 xl:col-span-2">
        <h2 class="text-lg font-bold text-[#f7f3ea] md:text-xl">Form pemesanan</h2>
        <p class="store-muted mt-1 text-sm">Isi data dengan benar supaya proses bisa langsung dilanjutkan setelah pembayaran.</p>
        <form action="<?= e(route_url('checkout.php')) ?>" method="post" class="mt-5 grid gap-4 md:grid-cols-2">
            <input type="hidden" name="product_slug" value="<?= e($product['slug']) ?>">
            <div class="md:col-span-1">
                <label class="mb-2 block text-sm font-semibold">Nama pelanggan</label>
                <input type="text" name="customer_name" class="store-input" placeholder="Nama kamu" required>
            </div>
            <div class="md:col-span-1">
                <label class="mb-2 block text-sm font-semibold">Email</label>
                <input type="email" name="customer_email" class="store-input" placeholder="nama@email.com" required>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold">UID / Username Akun</label>
                <input type="text" name="customer_account" class="store-input" placeholder="Masukkan UID atau username" required>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold">Nomor WhatsApp</label>
                <input type="text" name="customer_whatsapp" class="store-input" placeholder="08xxxxxxxxxx" required>
            </div>
            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-semibold">Catatan</label>
                <textarea name="customer_notes" class="store-input min-h-[120px]" placeholder="Contoh: request login, jam pengerjaan, atau catatan khusus lain."></textarea>
            </div>
            <div class="md:col-span-2 flex flex-col gap-3 pt-2 sm:flex-row">
                <button type="submit" class="store-cta w-full rounded-2xl px-4 py-3 text-sm font-semibold sm:w-auto sm:min-w-[220px]">Lanjut ke checkout</button>
                <div class="store-muted flex items-center text-sm">Pembayaran via QRIS, proses cepat, tampilan aman di mobile maupun desktop.</div>
            </div>
        </form>
        </section>
    </section>
</main>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
