<?php
require_once __DIR__ . '/../../app/auth.php';
require_admin();

if (is_post() && app_is_installed()) {
    settings_upsert([
        'store_tagline' => (string) request_post('store_tagline'),
        'store_whatsapp' => (string) request_post('store_whatsapp'),
    ]);

    flash('success', 'Pengaturan store berhasil disimpan.');
    redirect(route_url('admin/settings.php'));
}

$pageTitle = 'Pengaturan';
$tripay = tripay_config();
$success = flash('success');
require __DIR__ . '/../partials/header.php';
?>
<main class="min-h-screen bg-slate-100 p-4 md:p-8">
    <div class="mx-auto max-w-7xl rounded-3xl bg-white p-6 shadow-soft">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Pengaturan Store</h1>
                <p class="mt-1 text-sm text-slate-500">Isi konfigurasi Tripay dan store di sini nanti.</p>
            </div>
            <a href="<?= e(route_url('admin/index.php')) ?>" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold">Kembali</a>
        </div>
        <?php if ($success): ?>
            <div class="mb-4 rounded-2xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700"><?= e($success) ?></div>
        <?php endif; ?>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-3xl border border-slate-100 p-5">
                <h2 class="text-lg font-bold">Tripay</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between gap-4"><dt>Sandbox</dt><dd><?= $tripay['sandbox'] ? 'Ya' : 'Tidak' ?></dd></div>
                    <div class="flex justify-between gap-4"><dt>API Key</dt><dd><?= $tripay['api_key'] ? 'Sudah diisi' : 'Kosong' ?></dd></div>
                    <div class="flex justify-between gap-4"><dt>Merchant Code</dt><dd><?= $tripay['merchant_code'] ? e($tripay['merchant_code']) : 'Kosong' ?></dd></div>
                </dl>
            </div>
            <div class="rounded-3xl border border-slate-100 p-5">
                <h2 class="text-lg font-bold">Store</h2>
                <form method="post" class="mt-4 space-y-4 text-sm">
                    <div>
                        <label class="mb-2 block font-semibold">Base path</label>
                        <input class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm bg-slate-50" value="<?= e(app_config()['base_path']) ?>" disabled>
                    </div>
                    <div>
                        <label class="mb-2 block font-semibold">WhatsApp</label>
                        <input name="store_whatsapp" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" value="<?= e(app_setting('store_whatsapp')) ?>">
                    </div>
                    <div>
                        <label class="mb-2 block font-semibold">Tagline</label>
                        <textarea name="store_tagline" class="min-h-[110px] w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm"><?= e(app_setting('store_tagline')) ?></textarea>
                    </div>
                    <button type="submit" class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white" <?= !app_is_installed() ? 'disabled' : '' ?>>Simpan pengaturan</button>
                </form>
            </div>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
