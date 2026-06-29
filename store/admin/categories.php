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
    <form method="post" class="admin-panel space-y-5 p-6">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= e($editing['id'] ?? '') ?>">
        <div>
            <p class="admin-panel-kicker">Kategori store</p>
            <h3 class="mt-2 text-2xl font-black text-accent-900"><?= $editing ? 'Edit kategori' : 'Tambah kategori' ?></h3>
            <p class="admin-panel-subtitle mt-2">Rapikan struktur kategori agar storefront lebih mudah dipahami pembeli.</p>
        </div>
        <?php if ($success): ?>
            <div class="rounded-[20px] bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"><?= e($success) ?></div>
        <?php endif; ?>
        <div class="admin-field-group">
            <label class="text-sm font-semibold">Nama kategori</label>
            <input name="name" value="<?= e($editing['name'] ?? '') ?>" class="admin-input" required>
        </div>
        <div class="admin-field-group">
            <label class="text-sm font-semibold">Slug</label>
            <input name="slug" value="<?= e($editing['slug'] ?? '') ?>" class="admin-input" placeholder="opsional-auto-generated">
        </div>
        <div class="admin-field-group">
            <label class="text-sm font-semibold">Urutan tampil</label>
            <input name="sort_order" value="<?= e(isset($editing['sort_order']) ? (string) $editing['sort_order'] : '0') ?>" class="admin-input">
        </div>
        <label class="flex items-center gap-3 rounded-[20px] bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700">
            <input type="checkbox" name="is_active" value="1" <?= !isset($editing['is_active']) || (int) $editing['is_active'] === 1 ? 'checked' : '' ?>>
            Kategori aktif
        </label>
        <div class="flex gap-3">
            <button type="submit" class="btn-primary-soft px-5 py-3 text-sm">Simpan kategori</button>
            <a href="<?= e(route_url('admin/categories.php')) ?>" class="btn-secondary-soft px-5 py-3 text-sm">Reset</a>
        </div>
    </form>

    <div class="admin-panel p-6">
        <div class="mb-5 flex items-center justify-between gap-4">
            <div>
                <p class="admin-panel-kicker">Struktur katalog</p>
                <h3 class="mt-2 text-xl font-black text-accent-900">Daftar kategori</h3>
                <p class="text-sm text-slate-500">Status aktif dan urutan tampil sekarang lebih mudah dipantau.</p>
            </div>
            <div class="admin-filter-chip"><?= count($categories) ?> kategori</div>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                <tr>
                    <th>Kategori</th>
                    <th>Slug</th>
                    <th>Urutan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td class="font-bold text-accent-900"><?= e($category['name']) ?></td>
                        <td class="text-slate-500"><?= e($category['slug']) ?></td>
                        <td class="font-semibold"><?= e((string) ($category['sort_order'] ?? 0)) ?></td>
                        <td>
                            <span class="admin-status-chip <?= !empty($category['is_active']) ? 'bg-emerald-50 text-emerald-700' : 'bg-stone-100 text-slate-700' ?>">
                                <?= !empty($category['is_active']) ? 'Aktif' : 'Nonaktif' ?>
                            </span>
                        </td>
                        <td>
                            <div class="flex gap-2">
                                <a href="<?= e(route_url('admin/categories.php?edit=' . $category['id'])) ?>" class="btn-secondary-soft px-3 py-2 text-xs">Edit</a>
                                <form method="post" onsubmit="return confirm('Hapus kategori ini? Produk di kategori ini juga bisa terdampak.');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= e((string) $category['id']) ?>">
                                    <button type="submit" class="rounded-2xl bg-rose-600 px-3 py-2 text-xs font-bold text-white">Hapus</button>
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
