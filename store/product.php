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
<div class="min-h-screen">
<main class="store-container px-5 pb-10 pt-5 md:px-7 lg:px-8">
    <a href="<?= e(route_url()) ?>" class="subtle-link text-sm font-semibold">← Kembali ke store</a>

    <section class="detail-shell mt-4 xl:grid-cols-[1fr_0.95fr]">
        <div class="section-card interactive-panel overflow-hidden p-3">
            <?php if (!empty($product['image_url'])): ?>
                <img src="<?= e($product['image_url']) ?>" alt="<?= e($product['name']) ?>" class="store-media !h-[260px] md:!h-[340px]">
            <?php else: ?>
                <div class="store-media-fallback flex !h-[260px] items-end p-5 md:!h-[340px]">
                    <span class="fallback-glow"></span>
                    <div>
                        <div class="eyebrow text-[11px] font-semibold !text-sky-100"><?= e($product['category_name'] ?? 'Produk') ?></div>
                        <div class="mt-2 text-3xl font-semibold leading-tight"><?= e($product['name']) ?></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="section-card p-5 md:p-7">
            <div class="flex flex-wrap items-center gap-2">
                <span class="store-chip px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em]"><?= e($product['badge']) ?></span>
                <span class="store-chip px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em]"><?= e($product['category_name'] ?? 'Store') ?></span>
            </div>
            <h1 class="title-display mt-4 text-[34px] leading-[0.95] text-slate-950 md:text-[50px]"><?= e($product['name']) ?></h1>
            <p class="store-muted mt-4 text-sm leading-7 md:text-base"><?= e($product['description']) ?></p>
            <div class="store-line mt-6 grid gap-4 border-t pt-6 sm:grid-cols-2">
                <div class="info-block p-4">
                    <div class="text-xs uppercase tracking-[0.18em] text-slate-500">Harga</div>
                    <div class="mt-2 text-2xl font-semibold text-slate-950"><?= e(money($product['price'])) ?></div>
                </div>
                <div class="info-block p-4">
                    <div class="text-xs uppercase tracking-[0.18em] text-slate-500">Kategori</div>
                    <div class="mt-2 text-lg font-semibold text-slate-950"><?= e($product['category_name'] ?? 'Store') ?></div>
                </div>
            </div>
            <div class="mt-6 info-block p-5">
                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Kenapa halaman ini dibuat lebih jelas</div>
                <div class="step-list mt-4">
                    <div class="step-item">
                        <span class="step-index">1</span>
                        <div>
                            <div class="font-semibold text-slate-950">Informasi produk dibuat lebih menonjol</div>
                            <div class="store-muted mt-1 text-sm">Nama produk, harga, dan kategori langsung mudah dipindai dalam beberapa detik.</div>
                        </div>
                    </div>
                    <div class="step-item">
                        <span class="step-index">2</span>
                        <div>
                            <div class="font-semibold text-slate-950">Form order lebih ramah untuk user awam</div>
                            <div class="store-muted mt-1 text-sm">Field dibikin lebih jelas agar user tidak salah isi data penting.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section class="section-card p-5 md:p-7 xl:col-span-2">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-950 md:text-xl">Form pemesanan</h2>
                    <p class="store-muted mt-1 text-sm">Isi data dengan benar supaya proses checkout dan pengiriman hasil berjalan lebih lancar.</p>
                </div>
                <div class="store-chip px-4 py-2 text-xs font-semibold uppercase tracking-[0.14em]">Langkah berikutnya: checkout QRIS</div>
            </div>
            <form action="<?= e(route_url('checkout.php')) ?>" method="post" class="mt-5 grid gap-4 md:grid-cols-2">
                <input type="hidden" name="product_slug" value="<?= e($product['slug']) ?>">
                <div class="field-group md:col-span-1">
                    <label class="field-label">Nama pelanggan</label>
                    <input type="text" name="customer_name" class="field-input" placeholder="Nama kamu" required>
                    <p class="field-help">Nama ini dipakai untuk identitas pesanan.</p>
                </div>
                <div class="field-group md:col-span-1">
                    <label class="field-label">Email</label>
                    <input type="email" name="customer_email" class="field-input" placeholder="nama@email.com" required>
                    <p class="field-help">Dipakai untuk informasi order jika dibutuhkan.</p>
                </div>
                <div class="field-group">
                    <label class="field-label">UID / Username Akun</label>
                    <input type="text" name="customer_account" class="field-input" placeholder="Masukkan UID atau username" required>
                    <p class="field-help">Isi sesuai akun tujuan supaya pesanan tidak salah proses.</p>
                </div>
                <div class="field-group">
                    <label class="field-label">Nomor WhatsApp</label>
                    <input type="text" name="customer_whatsapp" class="field-input" placeholder="08xxxxxxxxxx" required>
                    <p class="field-help">Nomor aktif memudahkan follow-up jika ada kendala.</p>
                </div>
                <div class="field-group md:col-span-2">
                    <label class="field-label">Catatan</label>
                    <textarea name="customer_notes" class="field-input min-h-[120px]" placeholder="Contoh: request login, jam pengerjaan, atau catatan khusus lain."></textarea>
                    <p class="field-help">Opsional, tapi berguna jika ada request khusus.</p>
                </div>
                <div class="md:col-span-2 flex flex-col gap-3 pt-2 lg:flex-row lg:items-center lg:justify-between">
                    <div class="step-list max-w-xl">
                        <div class="step-item">
                            <span class="step-index">1</span>
                            <div class="text-sm text-slate-700">Setelah submit, kamu masuk ke halaman ringkasan checkout.</div>
                        </div>
                        <div class="step-item">
                            <span class="step-index">2</span>
                            <div class="text-sm text-slate-700">Lalu buka invoice untuk scan QRIS dan pantau status pembayarannya.</div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <button type="submit" class="btn-primary w-full px-5 py-3 text-sm font-semibold sm:w-auto sm:min-w-[220px]">Lanjut ke checkout</button>
                        <div class="store-muted flex items-center text-sm">Pembayaran via QRIS, proses cepat, dan aman dibuka dari HP maupun desktop.</div>
                    </div>
                </div>
            </form>
        </section>
    </section>
</main>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
