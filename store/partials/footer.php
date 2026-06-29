<?php $isAdminLayout = $isAdminLayout ?? false; ?>
<?php if (!$isAdminLayout): ?>
    <footer class="site-footer">
        <div class="store-container px-5 pb-10 pt-8 md:px-7 lg:px-8">
            <div class="site-footer-card">
                <div class="grid gap-8 lg:grid-cols-[1.2fr_0.8fr] lg:items-start">
                    <div>
                        <p class="eyebrow text-[11px] font-semibold">Butuh proses yang simple?</p>
                        <h2 class="title-display mt-3 max-w-2xl text-[30px] leading-tight text-slate-950 md:text-[42px]">Beli produk digital tanpa ribet, tanpa bingung, dan tanpa alur yang muter-muter.</h2>
                        <p class="store-muted mt-4 max-w-2xl text-sm leading-7 md:text-base">Kami fokus bikin pengalaman belanja yang jelas: pilih produk, isi data, bayar QRIS, lalu ambil detail akun atau layanan dengan lebih tenang.</p>
                        <div class="mt-6 flex flex-wrap gap-3">
                            <a href="<?= e(route_url('index.php')) ?>" class="btn-primary px-5 py-3 text-sm font-semibold">Jelajahi katalog</a>
                            <a href="<?= e(app_setting('store_whatsapp') ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', (string) app_setting('store_whatsapp')) : '#') ?>" class="btn-secondary px-5 py-3 text-sm font-semibold">Chat WhatsApp</a>
                        </div>
                    </div>
                    <div class="grid gap-4">
                        <div class="info-block p-5">
                            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Kontak bantuan</div>
                            <div class="mt-3 text-sm font-semibold text-slate-950"><?= e((string) app_setting('store_email')) ?></div>
                            <div class="store-muted mt-1 text-sm"><?= e((string) app_setting('store_whatsapp')) ?></div>
                        </div>
                        <div class="info-block p-5">
                            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Kenapa lebih nyaman</div>
                            <ul class="mt-3 space-y-2 text-sm text-slate-700">
                                <li>• Alur beli jelas dari awal sampai akhir</li>
                                <li>• Tampilan lebih mudah dibaca di HP</li>
                                <li>• Status pembayaran lebih mudah dipantau</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="minimal-divider mt-8"></div>
                <div class="mt-5 flex flex-col gap-2 text-xs text-slate-500 md:flex-row md:items-center md:justify-between">
                    <span>© <?= date('Y') ?> Jasajoki Store</span>
                    <span>Digital product storefront • QRIS ready • Mobile friendly</span>
                </div>
            </div>
        </div>
    </footer>
<?php else: ?>
    <footer class="admin-footer-note px-5 pb-8 pt-2 text-center text-xs text-slate-500 md:px-7 lg:px-8">
        <span>© <?= date('Y') ?> Jasajoki Admin Workspace</span>
    </footer>
<?php endif; ?>
</div>
<script src="<?= e(asset_url('js/app.js')) ?>"></script>
</body>
</html>
