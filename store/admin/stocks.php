<?php
require_once __DIR__ . '/../../app/auth.php';
require_admin();

$products = products_all();
$productId = (int) request_get('product_id', $products[0]['id'] ?? 0);
$statusFilter = (string) request_get('status', 'ALL');

if (is_post() && app_is_installed()) {
    $action = (string) request_post('action');

    if ($action === 'import') {
        $result = stocks_import_lines((int) request_post('product_id'), (string) request_post('bulk_stock'));
        $message = $result['imported'] . ' stok berhasil diimport.';
        if ($result['failed']) {
            $message .= ' Gagal: ' . implode(' ', $result['failed']);
        }
        flash('success', $message);
        redirect(route_url('admin/stocks.php?product_id=' . (int) request_post('product_id')));
    }

    if ($action === 'status') {
        stock_update_status((int) request_post('stock_id'), (string) request_post('stock_status'));
        flash('success', 'Status stok diperbarui.');
        redirect(route_url('admin/stocks.php?product_id=' . $productId . '&status=' . urlencode($statusFilter)));
    }

    if ($action === 'delete') {
        stock_delete((int) request_post('stock_id'));
        flash('success', 'Stok dihapus.');
        redirect(route_url('admin/stocks.php?product_id=' . $productId . '&status=' . urlencode($statusFilter)));
    }
}

$pageTitle = 'Manajemen Stok';
$activeNav = 'stocks';
$stocks = stocks_all($productId ?: null, $statusFilter);
$stockSummary = stock_counts();
$success = flash('success');
require __DIR__ . '/partials/layout-top.php';
?>
<section class="grid gap-6 2xl:grid-cols-[460px_1fr]">
    <div class="space-y-6">
        <div class="admin-panel p-6">
            <div>
                <p class="admin-panel-kicker">Inventory</p>
                <h3 class="mt-2 text-2xl font-black text-accent-900">Import stok akun</h3>
                <p class="admin-panel-subtitle mt-2">Format per baris: <strong>email | password | 2fa</strong></p>
            </div>
            <?php if ($success): ?>
                <div class="mt-4 rounded-[20px] bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"><?= e($success) ?></div>
            <?php endif; ?>
            <form method="post" class="mt-5 space-y-4">
                <input type="hidden" name="action" value="import">
                <div class="admin-field-group">
                    <label class="text-sm font-semibold">Produk tujuan</label>
                    <select name="product_id" class="admin-select">
                        <?php foreach ($products as $product): ?>
                            <option value="<?= e((string) $product['id']) ?>" <?= $productId === (int) $product['id'] ? 'selected' : '' ?>><?= e($product['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="admin-field-group">
                    <label class="text-sm font-semibold">Bulk stok</label>
                    <textarea name="bulk_stock" class="admin-textarea min-h-[280px] font-mono" placeholder="email@contoh.com | password123 | KODE2FA"></textarea>
                    <p class="admin-field-help">Satu baris untuk satu akun. Ini sekarang lebih cocok dipakai untuk import banyak data sekaligus.</p>
                </div>
                <button type="submit" class="btn-primary-soft px-5 py-3 text-sm">Import stok</button>
            </form>
        </div>

        <div class="admin-dark-card p-6">
            <h3 class="text-lg font-black">Ringkasan stok</h3>
            <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                <div class="rounded-2xl bg-white/5 p-4">
                    <div class="text-slate-300">Available</div>
                    <div class="mt-2 text-2xl font-bold"><?= e((string) $stockSummary['available']) ?></div>
                </div>
                <div class="rounded-2xl bg-white/5 p-4">
                    <div class="text-slate-300">Reserved</div>
                    <div class="mt-2 text-2xl font-bold"><?= e((string) $stockSummary['reserved']) ?></div>
                </div>
                <div class="rounded-2xl bg-white/5 p-4">
                    <div class="text-slate-300">Sold</div>
                    <div class="mt-2 text-2xl font-bold"><?= e((string) $stockSummary['sold']) ?></div>
                </div>
                <div class="rounded-2xl bg-white/5 p-4">
                    <div class="text-slate-300">Total</div>
                    <div class="mt-2 text-2xl font-bold"><?= e((string) $stockSummary['total']) ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-panel p-6">
        <div class="mb-5 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="admin-panel-kicker">Daftar inventory</p>
                <h3 class="mt-2 text-xl font-black text-accent-900">Stok akun</h3>
                <p class="text-sm text-slate-500">Kelola stok akun per produk dengan status available, reserved, atau sold.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <?php foreach (['ALL', 'available', 'reserved', 'sold'] as $status): ?>
                    <a href="<?= e(route_url('admin/stocks.php?product_id=' . $productId . '&status=' . $status)) ?>" class="admin-filter-chip <?= strtolower($statusFilter) === strtolower($status) ? 'admin-filter-chip-active' : '' ?>">
                        <?= e(strtoupper($status)) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                <tr>
                    <th>Produk</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>2FA</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($stocks as $stock): ?>
                    <tr>
                        <td class="font-bold text-accent-900"><?= e($stock['product_name']) ?></td>
                        <td><?= e($stock['account_email']) ?></td>
                        <td class="font-mono text-xs"><?= e($stock['account_password']) ?></td>
                        <td class="font-mono text-xs"><?= e($stock['account_2fa'] ?: '-') ?></td>
                        <td>
                            <span class="admin-status-chip <?= e(stock_status_badge($stock['stock_status'])) ?>">
                                <?= e(strtoupper($stock['stock_status'])) ?>
                            </span>
                        </td>
                        <td>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach (['available', 'reserved', 'sold'] as $status): ?>
                                    <form method="post">
                                        <input type="hidden" name="action" value="status">
                                        <input type="hidden" name="stock_id" value="<?= e((string) $stock['id']) ?>">
                                        <input type="hidden" name="stock_status" value="<?= e($status) ?>">
                                        <button type="submit" class="btn-secondary-soft px-3 py-2 text-xs"><?= e(ucfirst($status)) ?></button>
                                    </form>
                                <?php endforeach; ?>
                                <form method="post" onsubmit="return confirm('Hapus stok ini?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="stock_id" value="<?= e((string) $stock['id']) ?>">
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
