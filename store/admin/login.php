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
<main class="min-h-screen bg-slate-100 px-4 py-8 md:px-10">
    <div class="mx-auto grid max-w-5xl overflow-hidden rounded-3xl bg-white shadow-soft md:grid-cols-[1.2fr_0.8fr]">
        <section class="hidden bg-slate-900 p-10 text-white md:block">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-blue-200">Admin Panel</p>
            <h1 class="mt-4 text-4xl font-bold leading-tight">Kelola produk, order, dan konfigurasi store dalam satu dashboard.</h1>
            <p class="mt-4 text-sm text-slate-300">Tampilan desktop-first, cocok untuk operasional harian.</p>
        </section>
        <section class="p-6 md:p-10">
            <h2 class="text-2xl font-bold">Login Admin</h2>
            <p class="mt-2 text-sm text-slate-500">Default awal: admin / admin123</p>
            <?php if ($error): ?>
                <div class="mt-4 rounded-2xl bg-rose-50 px-4 py-3 text-sm text-rose-700"><?= e($error) ?></div>
            <?php endif; ?>
            <form method="post" class="mt-6 space-y-4">
                <div>
                    <label class="mb-2 block text-sm font-semibold">Username</label>
                    <input type="text" name="username" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold">Password</label>
                    <input type="password" name="password" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" required>
                </div>
                <button type="submit" class="w-full rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white">Masuk</button>
            </form>
        </section>
    </div>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
