<footer class="site-footer">
    <div class="store-container px-5 pb-10 pt-6 md:px-7 lg:px-8">
        <div class="site-footer-card">
            <div class="grid gap-8 md:grid-cols-[1.2fr_0.8fr]">
                <div>
                    <p class="eyebrow text-[11px] font-semibold">Jasajoki Store</p>
                    <h2 class="title-display mt-3 text-[28px] leading-tight text-[#16120f] md:text-[36px]">Checkout simpel, QRIS cepat, dan pengambilan data akun lebih jelas.</h2>
                    <p class="store-muted mt-3 max-w-xl text-sm leading-7">Store ini dibuat fokus ke pengalaman beli yang ringkas: pilih produk, isi data, bayar, lalu ambil detail akun tanpa alur yang membingungkan.</p>
                </div>
                <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-1">
                    <div class="info-block p-4">
                        <div class="text-xs font-semibold uppercase tracking-[0.18em] text-[#6d6054]">Bantuan</div>
                        <div class="mt-2 text-sm font-semibold text-[#16120f]"><?= e(app_setting('store_email')) ?></div>
                        <div class="store-muted mt-1 text-sm"><?= e(app_setting('store_whatsapp')) ?></div>
                    </div>
                    <div class="info-block p-4">
                        <div class="text-xs font-semibold uppercase tracking-[0.18em] text-[#6d6054]">Navigasi</div>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <a href="<?= e(route_url('index.php')) ?>" class="store-chip px-3 py-2 text-xs font-semibold">Katalog</a>
                            <a href="<?= e(route_url('admin/login.php')) ?>" class="store-chip px-3 py-2 text-xs font-semibold">Admin</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="minimal-divider mt-6"></div>
            <div class="mt-5 flex flex-col gap-2 text-xs text-[#6d6054] md:flex-row md:items-center md:justify-between">
                <span>© <?= date('Y') ?> Jasajoki Store</span>
                <span>Minimal storefront • QRIS ready • Mobile friendly</span>
            </div>
        </div>
    </div>
</footer>
</div>
<script src="<?= e(asset_url('js/app.js')) ?>"></script>
</body>
</html>
