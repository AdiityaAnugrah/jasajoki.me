<?php
require_once __DIR__ . '/../../app/auth.php';
require_admin();

if (is_post() && app_is_installed()) {
    settings_upsert([
        'store_tagline' => (string) request_post('store_tagline'),
        'store_whatsapp' => (string) request_post('store_whatsapp'),
        'store_email' => (string) request_post('store_email'),
    ]);

    flash('success', 'Pengaturan store berhasil disimpan.');
    redirect(route_url('admin/settings.php'));
}

$pageTitle = 'Pengaturan Store';
$activeNav = 'settings';
$tripay = tripay_config();
$success = flash('success');
require __DIR__ . '/partials/layout-top.php';
?>
<section class="grid gap-6 2xl:grid-cols-[1fr_420px]">
    <div class="space-y-6">
        <div class="rounded-3xl bg-white p-6 shadow-soft">
            <div class="mb-5">
                <h3 class="text-xl font-bold">Brand & kontak store</h3>
                <p class="text-sm text-slate-500">Informasi ini tampil dan dipakai untuk operasional toko.</p>
            </div>
            <?php if ($success): ?>
                <div class="mb-4 rounded-2xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700"><?= e($success) ?></div>
            <?php endif; ?>
            <form method="post" class="space-y-4">
                <div>
                    <label class="mb-2 block text-sm font-semibold">Base path</label>
                    <input class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" value="<?= e(app_config()['base_path']) ?>" disabled>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold">WhatsApp</label>
                    <input name="store_whatsapp" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" value="<?= e(app_setting('store_whatsapp')) ?>">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold">Email store</label>
                    <input name="store_email" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" value="<?= e(app_setting('store_email')) ?>">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold">Tagline</label>
                    <textarea name="store_tagline" class="min-h-[120px] w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm"><?= e(app_setting('store_tagline')) ?></textarea>
                </div>
                <button type="submit" class="rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white">Simpan pengaturan</button>
            </form>
        </div>
    </div>

    <aside class="space-y-6">
        <div class="rounded-3xl bg-slate-900 p-6 text-white shadow-soft">
            <h3 class="text-lg font-bold">Tripay</h3>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt>Mode</dt><dd><?= e(strtoupper($tripay['mode'] ?? 'sandbox')) ?></dd></div>
                <div class="flex justify-between gap-4"><dt>Method</dt><dd><?= e($tripay['payment_method'] ?? 'QRIS') ?></dd></div>
                <div class="flex justify-between gap-4"><dt>Merchant</dt><dd><?= e($tripay['merchant_code'] ?: 'Kosong') ?></dd></div>
                <div class="flex justify-between gap-4"><dt>Status</dt><dd><?= e(admin_payment_health()) ?></dd></div>
            </dl>
        </div>

        <div class="rounded-3xl bg-white p-6 shadow-soft">
            <h3 class="text-lg font-bold">Email / SMTP</h3>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt>SMTP Host</dt><dd><?= e(app_config()['mail']['host'] ?: '-') ?></dd></div>
                <div class="flex justify-between gap-4"><dt>SMTP Port</dt><dd><?= e((string) app_config()['mail']['port']) ?></dd></div>
                <div class="flex justify-between gap-4"><dt>Email User</dt><dd><?= e(app_config()['mail']['username'] ?: '-') ?></dd></div>
                <div class="flex justify-between gap-4"><dt>Garansi</dt><dd><?= e((string) app_config()['store']['warranty_hours']) ?> jam</dd></div>
            </dl>
        </div>
    </aside>
</section>
<?php require __DIR__ . '/partials/layout-bottom.php'; ?>
