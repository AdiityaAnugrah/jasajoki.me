<?php

require_once __DIR__ . '/db.php';

function asset_url(string $path): string
{
    return rtrim(app_config()['base_path'], '/') . '/assets/' . ltrim($path, '/');
}

function route_url(string $path = ''): string
{
    return rtrim(app_config()['base_path'], '/') . '/' . ltrim($path, '/');
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function money(float|int|string $amount): string
{
    return 'Rp' . number_format((float) $amount, 0, ',', '.');
}

function request_get(string $key, mixed $default = null): mixed
{
    return $_GET[$key] ?? $default;
}

function request_post(string $key, mixed $default = null): mixed
{
    return $_POST[$key] ?? $default;
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function ensure_session_started(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function flash(string $key, ?string $value = null): ?string
{
    ensure_session_started();

    if ($value !== null) {
        $_SESSION['_flash'][$key] = $value;
        return null;
    }

    $message = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);

    return $message;
}

function table_exists(string $table): bool
{
    $pdo = db();

    if (db_driver() === 'sqlite') {
        $statement = $pdo->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name = :table");
        $statement->execute(['table' => $table]);
        return (bool) $statement->fetchColumn();
    }

    $statement = $pdo->prepare('SHOW TABLES LIKE :table');
    $statement->execute(['table' => $table]);
    return (bool) $statement->fetchColumn();
}

function app_is_installed(): bool
{
    return table_exists('products') && table_exists('admins');
}

function app_setting(string $key, mixed $default = null): mixed
{
    static $cache = [];

    if (isset($cache[$key])) {
        return $cache[$key];
    }

    if (!app_is_installed()) {
        $fallback = [
            'store_tagline' => 'Top up, joki, dan layanan digital cepat.',
            'store_whatsapp' => '6281234567890',
            'store_email' => app_config()['mail']['username'] ?: 'admin@jasajoki.me',
        ];

        return $fallback[$key] ?? $default;
    }

    $statement = db()->prepare('SELECT setting_value FROM settings WHERE setting_key = :key LIMIT 1');
    $statement->execute(['key' => $key]);
    $value = $statement->fetchColumn();
    $cache[$key] = $value !== false ? $value : $default;

    return $cache[$key];
}

function settings_upsert(array $pairs): void
{
    if (!app_is_installed()) {
        return;
    }

    foreach ($pairs as $key => $value) {
        if (db_driver() === 'sqlite') {
            $sql = 'INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value)
                    ON CONFLICT(setting_key) DO UPDATE SET setting_value = excluded.setting_value';
        } else {
            $sql = 'INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value)
                    ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)';
        }

        $statement = db()->prepare($sql);
        $statement->execute([
            'key' => $key,
            'value' => (string) $value,
        ]);
    }
}

function categories_all(): array
{
    if (!app_is_installed()) {
        return sample_categories();
    }

    $statement = db()->query('SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order ASC, id ASC');
    return $statement->fetchAll();
}

function products_all(?string $categorySlug = null): array
{
    if (!app_is_installed()) {
        $products = sample_products();

        if ($categorySlug) {
            $categoryId = null;
            foreach (sample_categories() as $category) {
                if ($category['slug'] === $categorySlug) {
                    $categoryId = $category['id'];
                    break;
                }
            }

            if ($categoryId) {
                $products = array_values(array_filter($products, fn ($product) => (int) $product['category_id'] === (int) $categoryId));
            }
        }

        return $products;
    }

    if ($categorySlug) {
        $sql = 'SELECT p.*, c.name AS category_name, c.slug AS category_slug
                FROM products p
                JOIN categories c ON c.id = p.category_id
                WHERE p.is_active = 1 AND c.slug = :slug
                ORDER BY p.id DESC';
        $statement = db()->prepare($sql);
        $statement->execute(['slug' => $categorySlug]);
        return $statement->fetchAll();
    }

    $sql = 'SELECT p.*, c.name AS category_name, c.slug AS category_slug
            FROM products p
            JOIN categories c ON c.id = p.category_id
            WHERE p.is_active = 1
            ORDER BY p.id DESC';
    return db()->query($sql)->fetchAll();
}

function product_find(int $id): ?array
{
    if (!app_is_installed()) {
        foreach (sample_products() as $product) {
            if ((int) $product['id'] === $id) {
                return $product;
            }
        }

        return null;
    }

    $statement = db()->prepare('SELECT * FROM products WHERE id = :id LIMIT 1');
    $statement->execute(['id' => $id]);
    return $statement->fetch() ?: null;
}

function find_product_by_slug(string $slug): ?array
{
    if (!app_is_installed()) {
        foreach (sample_products() as $product) {
            if ($product['slug'] === $slug) {
                return $product;
            }
        }

        return null;
    }

    $statement = db()->prepare('SELECT * FROM products WHERE slug = :slug AND is_active = 1 LIMIT 1');
    $statement->execute(['slug' => $slug]);
    return $statement->fetch() ?: null;
}

function slugify(string $text): string
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text) ?? '';
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text) ?: $text;
    $text = preg_replace('~[^-\w]+~', '', $text) ?? '';
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text) ?? '';
    $text = strtolower($text);
    return $text ?: 'item';
}

function products_save(array $data, ?int $id = null): void
{
    $payload = [
        'category_id' => (int) $data['category_id'],
        'name' => trim((string) $data['name']),
        'slug' => trim((string) ($data['slug'] ?: slugify((string) $data['name']))),
        'description' => trim((string) $data['description']),
        'price' => (float) $data['price'],
        'badge' => trim((string) $data['badge']),
        'is_active' => !empty($data['is_active']) ? 1 : 0,
    ];

    if ($id) {
        $payload['id'] = $id;
        $sql = 'UPDATE products SET category_id = :category_id, name = :name, slug = :slug, description = :description, price = :price, badge = :badge, is_active = :is_active WHERE id = :id';
    } else {
        $sql = 'INSERT INTO products (category_id, name, slug, description, price, badge, is_active) VALUES (:category_id, :name, :slug, :description, :price, :badge, :is_active)';
    }

    $statement = db()->prepare($sql);
    $statement->execute($payload);
}

function products_delete(int $id): void
{
    $statement = db()->prepare('DELETE FROM products WHERE id = :id');
    $statement->execute(['id' => $id]);
}

function orders_create(array $payload): string
{
    $orderCode = 'JJ-' . date('YmdHis') . '-' . random_int(100, 999);
    $statement = db()->prepare('INSERT INTO orders (order_code, product_id, customer_name, customer_email, customer_account, customer_whatsapp, customer_notes, amount, payment_channel, payment_status, order_status, tripay_reference)
        VALUES (:order_code, :product_id, :customer_name, :customer_email, :customer_account, :customer_whatsapp, :customer_notes, :amount, :payment_channel, :payment_status, :order_status, :tripay_reference)');

    $statement->execute([
        'order_code' => $orderCode,
        'product_id' => (int) $payload['product_id'],
        'customer_name' => trim((string) $payload['customer_name']),
        'customer_email' => trim((string) ($payload['customer_email'] ?? '')),
        'customer_account' => trim((string) $payload['customer_account']),
        'customer_whatsapp' => trim((string) $payload['customer_whatsapp']),
        'customer_notes' => trim((string) ($payload['customer_notes'] ?? '')),
        'amount' => (float) $payload['amount'],
        'payment_channel' => trim((string) $payload['payment_channel']),
        'payment_status' => 'UNPAID',
        'order_status' => 'PENDING',
        'tripay_reference' => trim((string) ($payload['tripay_reference'] ?? $orderCode)),
    ]);

    return $orderCode;
}

function order_update_tripay_data(string $orderCode, array $data): void
{
    $statement = db()->prepare('UPDATE orders
        SET tripay_reference = :tripay_reference,
            payment_channel = :payment_channel,
            payment_status = :payment_status,
            tripay_checkout_url = :tripay_checkout_url,
            tripay_pay_code = :tripay_pay_code,
            tripay_pay_url = :tripay_pay_url,
            tripay_qr_url = :tripay_qr_url,
            tripay_qr_string = :tripay_qr_string,
            expired_time = :expired_time
        WHERE order_code = :order_code');

    $statement->execute([
        'order_code' => $orderCode,
        'tripay_reference' => $data['tripay_reference'] ?? null,
        'payment_channel' => $data['payment_channel'] ?? null,
        'payment_status' => $data['payment_status'] ?? 'UNPAID',
        'tripay_checkout_url' => $data['tripay_checkout_url'] ?? null,
        'tripay_pay_code' => $data['tripay_pay_code'] ?? null,
        'tripay_pay_url' => $data['tripay_pay_url'] ?? null,
        'tripay_qr_url' => $data['tripay_qr_url'] ?? null,
        'tripay_qr_string' => $data['tripay_qr_string'] ?? null,
        'expired_time' => $data['expired_time'] ?? null,
    ]);
}

function order_update_status_by_reference(string $merchantRef, string $tripayReference, string $paymentStatus): void
{
    $orderStatus = match ($paymentStatus) {
        'PAID' => 'PAID',
        'EXPIRED' => 'EXPIRED',
        'FAILED' => 'FAILED',
        'REFUND' => 'REFUND',
        default => 'PENDING',
    };

    $statement = db()->prepare('UPDATE orders
        SET payment_status = :payment_status,
            order_status = :order_status,
            tripay_reference = :tripay_reference
        WHERE order_code = :order_code');

    $statement->execute([
        'payment_status' => $paymentStatus,
        'order_status' => $orderStatus,
        'tripay_reference' => $tripayReference,
        'order_code' => $merchantRef,
    ]);
}

function payment_log_create(?int $orderId, string $source, string $payload): void
{
    if (!$orderId) {
        return;
    }

    $statement = db()->prepare('INSERT INTO payment_logs (order_id, source, payload_json) VALUES (:order_id, :source, :payload_json)');
    $statement->execute([
        'order_id' => $orderId,
        'source' => $source,
        'payload_json' => $payload,
    ]);
}

function order_find_by_code(string $orderCode): ?array
{
    $sql = 'SELECT o.*, p.name AS product_name
            FROM orders o
            JOIN products p ON p.id = o.product_id
            WHERE o.order_code = :order_code LIMIT 1';
    $statement = db()->prepare($sql);
    $statement->execute(['order_code' => $orderCode]);
    return $statement->fetch() ?: null;
}

function orders_all(): array
{
    if (!app_is_installed()) {
        return [];
    }

    $sql = 'SELECT o.*, p.name AS product_name
            FROM orders o
            JOIN products p ON p.id = o.product_id
            ORDER BY o.id DESC';
    return db()->query($sql)->fetchAll();
}

function admin_stats(): array
{
    if (!app_is_installed()) {
        return [
            'products' => count(sample_products()),
            'orders_today' => 0,
            'total_orders' => 0,
        ];
    }

    $products = (int) db()->query('SELECT COUNT(*) FROM products')->fetchColumn();

    if (db_driver() === 'sqlite') {
        $ordersToday = (int) db()->query("SELECT COUNT(*) FROM orders WHERE date(created_at) = date('now', 'localtime')")->fetchColumn();
    } else {
        $ordersToday = (int) db()->query('SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()')->fetchColumn();
    }

    $totalOrders = (int) db()->query('SELECT COUNT(*) FROM orders')->fetchColumn();

    return [
        'products' => $products,
        'orders_today' => $ordersToday,
        'total_orders' => $totalOrders,
    ];
}

function sample_categories(): array
{
    return [
        ['id' => 1, 'name' => 'Top Up Game', 'slug' => 'top-up-game', 'is_active' => 1, 'sort_order' => 1],
        ['id' => 2, 'name' => 'Joki Rank', 'slug' => 'joki-rank', 'is_active' => 1, 'sort_order' => 2],
        ['id' => 3, 'name' => 'Akun Premium', 'slug' => 'akun-premium', 'is_active' => 1, 'sort_order' => 3],
    ];
}

function sample_products(): array
{
    return [
        [
            'id' => 1,
            'category_id' => 1,
            'category_name' => 'Top Up Game',
            'category_slug' => 'top-up-game',
            'name' => 'Mobile Legends 86 Diamond',
            'slug' => 'mobile-legends-86-diamond',
            'price' => 22000,
            'badge' => 'Best Seller',
            'description' => 'Proses cepat 1-5 menit setelah pembayaran.',
            'is_active' => 1,
        ],
        [
            'id' => 2,
            'category_id' => 2,
            'category_name' => 'Joki Rank',
            'category_slug' => 'joki-rank',
            'name' => 'Joki Rank Epic ke Legend',
            'slug' => 'joki-rank-epic-ke-legend',
            'price' => 150000,
            'badge' => 'Populer',
            'description' => 'Aman, update progres harian, bisa request hero.',
            'is_active' => 1,
        ],
        [
            'id' => 3,
            'category_id' => 3,
            'category_name' => 'Akun Premium',
            'category_slug' => 'akun-premium',
            'name' => 'Canva Pro 1 Bulan',
            'slug' => 'canva-pro-1-bulan',
            'price' => 25000,
            'badge' => 'Murah',
            'description' => 'Aktivasi cepat, garansi replace.',
            'is_active' => 1,
        ],
    ];
}
