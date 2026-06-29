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
$products = products_all();
$editing = request_get('edit') ? product_find((int) request_get('edit')) : null;
$success = flash('success');
require __DIR__ . '/../partials/header.php';
?>
<main class="min-h-screen bg-slate-100 p-4 md:p-8">
    <div class="mx-auto max-w-7xl rounded-3xl bg-white p-6 shadow-soft">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Kelola Produk</h1>
                <p class="mt-1 text-sm text-slate-500">Stub awal untuk CRUD produk.</p>
            </div>
            <a href="<?= e(route_url('admin/index.php')) ?>" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold">Kembali</a>
        </div>
        <?php if ($success): ?>
            <div class="mb-4 rounded-2xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700"><?= e($success) ?></div>
        <?php endif; ?>
        <div class="grid gap-6 lg:grid-cols-[1fr_1.2fr]">
            <form method="post" class="space-y-4 rounded-3xl border border-slate-100 p-5">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" value="<?= e($editing['id'] ?? '') ?>">
                <h2 class="text-lg font-bold"><?= $editing ? 'Edit produk' : 'Tambah produk' ?></h2>
                <?php if (!app_is_installed()): ?>
                    <div class="rounded-2xl bg-amber-50 px-4 py-3 text-sm text-amber-700">Jalankan <strong>php setup.php</strong> dulu supaya CRUD aktif.</div>
                <?php endif; ?>
                <select name="category_id" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= e((string) $category['id']) ?>" <?= (string) ($editing['category_id'] ?? '') === (string) $category['id'] ? 'selected' : '' ?>>
                            <?= e($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input name="name" value="<?= e($editing['name'] ?? '') ?>" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" placeholder="Nama produk" required>
                <input name="slug" value="<?= e($editing['slug'] ?? '') ?>" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" placeholder="Slug produk">
                <input name="price" value="<?= e(isset($editing['price']) ? (string) $editing['price'] : '') ?>" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" placeholder="Harga" required>
                <input name="badge" value="<?= e($editing['badge'] ?? '') ?>" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" placeholder="Badge">
                <textarea name="description" class="min-h-[120px] w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" placeholder="Deskripsi"><?= e($editing['description'] ?? '') ?></textarea>
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_active" value="1" <?= !isset($editing['is_active']) || (int) $editing['is_active'] === 1 ? 'checked' : '' ?>>
                    Aktif
                </label>
                <button type="submit" class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white" <?= !app_is_installed() ? 'disabled' : '' ?>>Simpan</button>
            </form>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-500">
                            <th class="pb-3">Nama</th>
                            <th class="pb-3">Kategori</th>
                            <th class="pb-3">Harga</th>
                            <th class="pb-3">Badge</th>
                            <th class="pb-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr class="border-b border-slate-50">
                            <td class="py-4 font-semibold"><?= e($product['name']) ?></td>
                            <td class="py-4"><?= e($product['category_name'] ?? '') ?></td>
                            <td class="py-4"><?= e(money($product['price'])) ?></td>
                            <td class="py-4"><?= e($product['badge']) ?></td>
                            <td class="py-4">
                                <div class="flex gap-2">
                                    <a href="<?= e(route_url('admin/products.php?edit=' . $product['id'])) ?>" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold">Edit</a>
                                    <?php if (app_is_installed()): ?>
                                        <form method="post" onsubmit="return confirm('Hapus produk ini?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= e((string) $product['id']) ?>">
                                            <button type="submit" class="rounded-xl bg-rose-600 px-3 py-2 text-xs font-semibold text-white">Hapus</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
