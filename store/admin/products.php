<?php
require_once __DIR__ . '/../../app/auth.php';
require_admin();

$categories = categories_all();

if (is_post() && app_is_installed()) {
    $action = (string) request_post('action');

    if ($action === 'save') {
        $id = request_post('id') ? (int) request_post('id') : null;
        products_save([
            'category_id' => request_post('category_id'),
            'name' => request_post('name'),
            'slug' => request_post('slug'),
            'description' => request_post('description'),
            'price' => request_post('price'),
            'badge' => request_post('badge'),
            'is_active' => request_post('is_active'),
        ], $id);
        flash('success', $id ? 'Produk berhasil diperbarui.' : 'Produk berhasil ditambahkan.');
        redirect(route_url('admin/products.php'));
    }

    if ($action === 'delete') {
        products_delete((int) request_post('id'));
        flash('success', 'Produk berhasil dihapus.');
        redirect(route_url('admin/products.php'));
    }
}

$pageTitle = 'Kelola Produk';
$activeNav = 'products';
$products = products_all();
$editing = request_get('edit') ? product_find((int) request_get('edit')) : null;
$success = flash('success');
require __DIR__ . '/partials/layout-top.php';
?>
<section class="grid gap-6 2xl:grid-cols-[420px_1fr]">
    <form method="post" class="admin-panel space-y-5 p-6">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= e($editing['id'] ?? '') ?>">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-accent-700">Produk digital</p>
            <h3 class="mt-2 text-2xl font-black text-accent-900"><?= $editing ? 'Edit produk' : 'Tambah produk' ?></h3>
            <p class="mt-2 text-sm text-slate-500">Buat produk baru atau ubah produk yang sudah ada.</p>
        </div>
        <?php if ($success): ?>
            <div class="rounded-[20px] bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"><?= e($success) ?></div>
        <?php endif; ?>
        <div>
            <label class="mb-2 block text-sm font-semibold">Kategori</label>
            <select name="category_id" class="admin-select" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= e((string) $category['id']) ?>" <?= (string) ($editing['category_id'] ?? '') === (string) $category['id'] ? 'selected' : '' ?>><?= e($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold">Nama produk</label>
            <input name="name" value="<?= e($editing['name'] ?? '') ?>" class="admin-input" required>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold">Slug</label>
            <input name="slug" value="<?= e($editing['slug'] ?? '') ?>" class="admin-input" placeholder="opsional-auto-generated">
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold">Harga</label>
                <input name="price" value="<?= e(isset($editing['price']) ? (string) $editing['price'] : '') ?>" class="admin-input" required>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold">Badge</label>
                <input name="badge" value="<?= e($editing['badge'] ?? '') ?>" class="admin-input" placeholder="Best Seller / Promo">
            </div>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold">Deskripsi</label>
            <textarea name="description" class="admin-textarea min-h-[140px]"><?= e($editing['description'] ?? '') ?></textarea>
        </div>
        <label class="flex items-center gap-3 rounded-[20px] bg-[#faf4e6] px-4 py-3 text-sm font-semibold text-slate-700">
            <input type="checkbox" name="is_active" value="1" <?= !isset($editing['is_active']) || (int) $editing['is_active'] === 1 ? 'checked' : '' ?>>
            Produk aktif
        </label>
        <div class="flex gap-3">
            <button type="submit" class="btn-primary-soft px-5 py-3 text-sm">Simpan produk</button>
            <a href="<?= e(route_url('admin/products.php')) ?>" class="btn-secondary-soft px-5 py-3 text-sm">Reset</a>
        </div>
    </form>

    <div class="admin-panel p-6">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h3 class="text-xl font-black text-accent-900">Daftar Produk</h3>
                <p class="text-sm text-slate-500">Produk aktif dan nonaktif.</p>
            </div>
            <div class="rounded-full bg-[#edf2ec] px-4 py-2 text-sm font-bold text-accent-800"><?= count($products) ?> produk</div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead>
                <tr class="border-b border-stone-200 text-slate-500">
                    <th class="pb-3 pr-4">Produk</th>
                    <th class="pb-3 pr-4">Kategori</th>
                    <th class="pb-3 pr-4">Harga</th>
                    <th class="pb-3 pr-4">Status</th>
                    <th class="pb-3">Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($products as $product): ?>
                    <tr class="border-b border-stone-100 last:border-b-0">
                        <td class="py-4 pr-4">
                            <div class="font-bold text-accent-900"><?= e($product['name']) ?></div>
                            <div class="text-xs text-slate-500"><?= e($product['badge']) ?></div>
                        </td>
                        <td class="py-4 pr-4"><?= e($product['category_name'] ?? '-') ?></td>
                        <td class="py-4 pr-4 font-semibold"><?= e(money($product['price'])) ?></td>
                        <td class="py-4 pr-4">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold <?= !empty($product['is_active']) ? 'bg-emerald-50 text-emerald-700' : 'bg-stone-100 text-slate-700' ?>">
                                <?= !empty($product['is_active']) ? 'Aktif' : 'Nonaktif' ?>
                            </span>
                        </td>
                        <td class="py-4">
                            <div class="flex gap-2">
                                <a href="<?= e(route_url('admin/products.php?edit=' . $product['id'])) ?>" class="btn-secondary-soft px-3 py-2 text-xs">Edit</a>
                                <form method="post" onsubmit="return confirm('Hapus produk ini?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= e((string) $product['id']) ?>">
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
