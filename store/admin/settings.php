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
$qrisify = app_config()['qrisify'];
$success = flash('success');
require __DIR__ . '/partials/layout-top.php';
?>
<section class="grid gap-6 2xl:grid-cols-[1fr_420px]">
    <div class="space-y-6">
        <div class="admin-panel p-6">
            <div class="mb-5">
                <p class="admin-panel-kicker">Pengaturan utama</p>
                <h3 class="mt-2 text-2xl font-black text-accent-900">Brand & kontak store</h3>
                <p class="admin-panel-subtitle mt-2">Informasi ini dipakai di storefront dan operasional toko, jadi dibuat lebih mudah dibaca dan diubah.</p>
            </div>
            <?php if ($success): ?>
                <div class="mb-4 rounded-[20px] bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"><?= e($success) ?></div>
            <?php endif; ?>
            <form method="post" class="space-y-4">
                <div class="admin-field-group">
                    <label class="text-sm font-semibold">Base path</label>
                    <input class="admin-input bg-slate-50" value="<?= e(app_config()['base_path']) ?>" disabled>
                </div>
                <div class="admin-field-group">
                    <label class="text-sm font-semibold">WhatsApp</label>
                    <input name="store_whatsapp" class="admin-input" value="<?= e(app_setting('store_whatsapp')) ?>">
                </div>
                <div class="admin-field-group">
                    <label class="text-sm font-semibold">Email store</label>
                    <input name="store_email" class="admin-input" value="<?= e(app_setting('store_email')) ?>">
                </div>
                <div class="admin-field-group">
                    <label class="text-sm font-semibold">Tagline</label>
                    <textarea name="store_tagline" class="admin-textarea min-h-[120px]"><?= e(app_setting('store_tagline')) ?></textarea>
                </div>
                <button type="submit" class="btn-primary-soft px-5 py-3 text-sm">Simpan pengaturan</button>
            </form>
        </div>
    </div>

    <aside class="space-y-6">
        <div class="admin-dark-card p-6">
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-sky-200">Payment gateway</p>
            <h3 class="mt-2 text-2xl font-black">QRISify</h3>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt>Mode</dt><dd><?= e(strtoupper($qrisify['mode'] ?? 'TEST')) ?></dd></div>
                <div class="flex justify-between gap-4"><dt>Method</dt><dd>QRIS Dinamis</dd></div>
                <div class="flex justify-between gap-4"><dt>API Key</dt><dd><?= !empty($qrisify['api_key']) ? 'Tersimpan' : 'Kosong' ?></dd></div>
                <div class="flex justify-between gap-4"><dt>Status</dt><dd><?= e(admin_payment_health()) ?></dd></div>
                <div class="flex justify-between gap-4"><dt>Webhook</dt><dd class="text-right"><?= e($qrisify['webhook_url'] ?: rtrim(app_config()['base_url'], '/') . '/callback-qrisify.php') ?></dd></div>
            </dl>
            <div class="mt-4 rounded-[22px] bg-white/10 px-4 py-3 text-xs leading-6 text-slate-100">
                Kalau mode <strong>TEST</strong>, invoice akan menampilkan tombol simulasi bayar agar webhook bisa dites tanpa uang asli.
            </div>
        </div>

        <div class="admin-panel p-6">
            <p class="admin-panel-kicker">SMTP</p>
            <h3 class="mt-2 text-lg font-black text-accent-900">Email / SMTP</h3>
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
