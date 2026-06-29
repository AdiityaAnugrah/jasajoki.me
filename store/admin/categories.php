<?php
require_once __DIR__ . '/../../app/auth.php';
require_admin();

if (is_post() && app_is_installed()) {
    $action = (string) request_post('action');

    if ($action === 'save') {
        $id = request_post('id') ? (int) request_post('id') : null;
        categories_save([
            'name' => request_post('name'),
            'slug' => request_post('slug'),
            'sort_order' => request_post('sort_order'),
            'is_active' => request_post('is_active'),
        ], $id);
        flash('success', $id ? 'Kategori diperbarui.' : 'Kategori ditambahkan.');
        redirect(route_url('admin/categories.php'));
    }

    if ($action === 'delete') {
        categories_delete((int) request_post('id'));
        flash('success', 'Kategori dihapus.');
        redirect(route_url('admin/categories.php'));
    }
}

$pageTitle = 'Kelola Kategori';
$activeNav = 'categories';
$categories = categories_all();
$editing = request_get('edit') ? category_find((int) request_get('edit')) : null;
$success = flash('success');
require __DIR__ . '/partials/layout-top.php';
?>
<section class="grid gap-6 2xl:grid-cols-[380px_1fr]">
    <form method="post" class="space-y-4 rounded-3xl bg-white p-6 shadow-soft">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= e($editing['id'] ?? '') ?>">
        <div>
            <h3 class="text-xl font-bold"><?= $editing ? 'Edit kategori' : 'Tambah kategori' ?></h3>
            <p class="text-sm text-slate-500">Atur struktur kategori produk agar navigasi store lebih rapi.</p>
        </div>
        <?php if ($success): ?>
            <div class="rounded-2xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700"><?= e($success) ?></div>
        <?php endif; ?>
        <div>
            <label class="mb-2 block text-sm font-semibold">Nama kategori</label>
            <input name="name" value="<?= e($editing['name'] ?? '') ?>" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" required>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold">Slug</label>
            <input name="slug" value="<?= e($editing['slug'] ?? '') ?>" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" placeholder="opsional-auto-generated">
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold">Urutan tampil</label>
            <input name="sort_order" value="<?= e(isset($editing['sort_order']) ? (string) $editing['sort_order'] : '0') ?>" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
        </div>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" <?= !isset($editing['is_active']) || (int) $editing['is_active'] === 1 ? 'checked' : '' ?>>
            Kategori aktif
        </label>
        <div class="flex gap-3">
            <button type="submit" class="rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white">Simpan kategori</button>
            <a href="<?= e(route_url('admin/categories.php')) ?>" class="rounded-2xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700">Reset</a>
        </div>
    </form>

    <div class="rounded-3xl bg-white p-6 shadow-soft">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h3 class="text-xl font-bold">Daftar Kategori</h3>
                <p class="text-sm text-slate-500">Pastikan kategori aktif dan urutan tampil sesuai kebutuhan.</p>
            </div>
            <div class="text-sm text-slate-500"><?= count($categories) ?> kategori</div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead>
                <tr class="border-b border-slate-100 text-slate-500">
                    <th class="pb-3 pr-4">Kategori</th>
                    <th class="pb-3 pr-4">Slug</th>
                    <th class="pb-3 pr-4">Urutan</th>
                    <th class="pb-3 pr-4">Status</th>
                    <th class="pb-3">Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr class="border-b border-slate-50">
                        <td class="py-4 pr-4 font-semibold"><?= e($category['name']) ?></td>
                        <td class="py-4 pr-4 text-slate-500"><?= e($category['slug']) ?></td>
                        <td class="py-4 pr-4"><?= e((string) ($category['sort_order'] ?? 0)) ?></td>
                        <td class="py-4 pr-4">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold <?= !empty($category['is_active']) ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-700' ?>">
                                <?= !empty($category['is_active']) ? 'Aktif' : 'Nonaktif' ?>
                            </span>
                        </td>
                        <td class="py-4">
                            <div class="flex gap-2">
                                <a href="<?= e(route_url('admin/categories.php?edit=' . $category['id'])) ?>" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold">Edit</a>
                                <form method="post" onsubmit="return confirm('Hapus kategori ini? Produk di kategori ini juga bisa terdampak.');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= e((string) $category['id']) ?>">
                                    <button type="submit" class="rounded-xl bg-rose-600 px-3 py-2 text-xs font-semibold text-white">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php require __DIR__ . '/partials/layout-bottom.php'; ?>
