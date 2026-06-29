<?php
require_once __DIR__ . '/../../app/auth.php';
require_admin();

if (is_post() && app_is_installed()) {
    $result = admin_change_password(
        (string) ($_SESSION['admin_username'] ?? 'admin'),
        (string) request_post('current_password'),
        (string) request_post('new_password')
    );

    flash($result['success'] ? 'success' : 'error', $result['message']);
    redirect(route_url('admin/security.php'));
}

$pageTitle = 'Keamanan Admin';
$activeNav = 'security';
$success = flash('success');
$error = flash('error');
require __DIR__ . '/partials/layout-top.php';
?>
<section class="grid gap-6 2xl:grid-cols-[460px_1fr]">
    <form method="post" class="space-y-4 rounded-3xl bg-white p-6 shadow-soft">
        <div>
            <h3 class="text-xl font-bold">Ganti Password Admin</h3>
            <p class="text-sm text-slate-500">Disarankan segera mengganti password default setelah store live.</p>
        </div>
        <?php if ($success): ?><div class="rounded-2xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700"><?= e($success) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="rounded-2xl bg-rose-50 px-4 py-3 text-sm text-rose-700"><?= e($error) ?></div><?php endif; ?>
        <div>
            <label class="mb-2 block text-sm font-semibold">Password saat ini</label>
            <input type="password" name="current_password" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" required>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold">Password baru</label>
            <input type="password" name="new_password" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" required>
        </div>
        <button type="submit" class="rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white">Perbarui password</button>
    </form>

    <div class="space-y-6">
        <div class="rounded-3xl bg-white p-6 shadow-soft">
            <h3 class="text-xl font-bold">Checklist keamanan</h3>
            <ul class="mt-4 space-y-3 text-sm text-slate-600">
                <li>• Ganti password admin default</li>
                <li>• Simpan kredensial Tripay hanya di `.env` server</li>
                <li>• Batasi akses panel admin hanya untuk operator tepercaya</li>
                <li>• Rutin cek order yang gagal / expired</li>
            </ul>
        </div>
        <div class="rounded-3xl bg-slate-900 p-6 text-white shadow-soft">
            <h3 class="text-lg font-bold">Status sesi</h3>
            <p class="mt-3 text-sm text-slate-300">Login sebagai <strong><?= e($_SESSION['admin_username'] ?? 'admin') ?></strong>.</p>
        </div>
    </div>
</section>
<?php require __DIR__ . '/partials/layout-bottom.php'; ?>
