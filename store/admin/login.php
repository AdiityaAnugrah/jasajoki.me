<?php
require_once __DIR__ . '/../../app/auth.php';

if (admin_is_logged_in()) {
    redirect(route_url('admin/index.php'));
}

if (is_post()) {
    $success = admin_login((string) request_post('username'), (string) request_post('password'));

    if ($success) {
        redirect(route_url('admin/index.php'));
    }

    flash('error', 'Username atau password salah.');
    redirect(route_url('admin/login.php'));
}

$pageTitle = 'Login Admin';
$error = flash('error');
require __DIR__ . '/../partials/header.php';
?>
<main class="admin-surface admin-login-shell px-4 py-8 md:px-10">
    <div class="admin-login-card mx-auto grid max-w-6xl md:grid-cols-[1.12fr_0.88fr]">
        <section class="admin-login-hero hidden p-10 md:block">
            <p class="text-xs font-bold uppercase tracking-[0.28em] text-sky-200">Admin workspace</p>
            <h1 class="mt-4 text-5xl font-black leading-[1.02] tracking-[-0.04em]">Kelola store dengan panel yang lebih modern, fokus, dan enak dipakai.</h1>
            <p class="mt-4 max-w-lg text-sm leading-7 text-slate-200">Cocok untuk memantau order, mengelola stok akun, produk digital, dan pengaturan bisnis harian tanpa tampilan yang terasa kuno atau penuh.</p>
            <div class="mt-8 grid gap-3 text-sm">
                <div class="rounded-2xl bg-white/10 px-4 py-3 text-white">Manajemen produk & kategori lebih rapi</div>
                <div class="rounded-2xl bg-white/10 px-4 py-3 text-white">Import stok akun lebih nyaman dibaca</div>
                <div class="rounded-2xl bg-white/10 px-4 py-3 text-white">Monitoring order & pembayaran lebih cepat</div>
            </div>
        </section>
        <section class="p-6 md:p-10">
            <p class="admin-panel-kicker">Akses operator</p>
            <h2 class="admin-panel-title mt-2">Login Admin</h2>
            <p class="admin-panel-subtitle mt-2">Masuk untuk mengelola store dan seluruh operasional harian dari satu panel.</p>
            <?php if ($error): ?>
                <div class="mt-4 rounded-2xl bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700"><?= e($error) ?></div>
            <?php endif; ?>
            <form method="post" class="mt-6 space-y-4">
                <div class="admin-field-group">
                    <label class="text-sm font-semibold">Username</label>
                    <input type="text" name="username" class="admin-input" required>
                </div>
                <div class="admin-field-group">
                    <label class="text-sm font-semibold">Password</label>
                    <input type="password" name="password" class="admin-input" required>
                </div>
                <button type="submit" class="btn-primary-soft w-full px-4 py-3 text-sm">Masuk ke admin</button>
            </form>
            <div class="admin-note-card mt-6 p-4 text-sm text-slate-600">
                Gunakan akun operator yang benar. Setelah login, segera cek produk, order, stok, dan pengaturan payment agar flow store tetap lancar.
            </div>
        </section>
    </div>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
