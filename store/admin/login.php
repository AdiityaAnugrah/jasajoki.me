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
<main class="admin-surface min-h-screen px-4 py-8 md:px-10">
    <div class="mx-auto grid max-w-6xl overflow-hidden rounded-[36px] border border-brand-200 bg-white shadow-soft md:grid-cols-[1.15fr_0.85fr]">
        <section class="hidden bg-[#123c36] p-10 text-white md:block">
            <p class="text-xs font-bold uppercase tracking-[0.28em] text-brand-100">Admin workspace</p>
            <h1 class="mt-4 text-5xl font-black leading-[1.02] tracking-[-0.04em]">Kelola store dengan panel yang lebih tenang dan profesional.</h1>
            <p class="mt-4 max-w-lg text-sm leading-6 text-brand-100">Cocok untuk memantau order, mengelola stok akun, produk digital, dan pengaturan bisnis harian tanpa tampilan yang ramai.</p>
            <div class="mt-8 grid gap-3 text-sm text-slate-600">
                <div class="rounded-2xl bg-white/10 px-4 py-3 text-white">Manajemen produk & kategori</div>
                <div class="rounded-2xl bg-white/10 px-4 py-3 text-white">Import stok akun format email | password | 2fa</div>
                <div class="rounded-2xl bg-white/10 px-4 py-3 text-white">Monitoring order & status pembayaran</div>
            </div>
        </section>
        <section class="p-6 md:p-10">
            <h2 class="text-3xl font-black text-accent-900">Login Admin</h2>
            <p class="mt-2 text-sm text-slate-500">Masuk untuk mengelola store dan operasional.</p>
            <?php if ($error): ?>
                <div class="mt-4 rounded-2xl bg-rose-50 px-4 py-3 text-sm text-rose-700"><?= e($error) ?></div>
            <?php endif; ?>
            <form method="post" class="mt-6 space-y-4">
                <div>
                    <label class="mb-2 block text-sm font-semibold">Username</label>
                    <input type="text" name="username" class="admin-input" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold">Password</label>
                    <input type="password" name="password" class="admin-input" required>
                </div>
                <button type="submit" class="btn-primary-soft w-full px-4 py-3 text-sm">Masuk ke admin</button>
            </form>
        </section>
    </div>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
