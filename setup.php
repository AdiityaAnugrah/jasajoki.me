<?php

require_once __DIR__ . '/app/helpers.php';

$driver = db_driver();
$schemaFile = __DIR__ . '/sql/' . ($driver === 'sqlite' ? 'sqlite_schema.sql' : 'mysql_schema.sql');

if ($driver === 'mysql') {
    $dbConfig = app_config()['db'];
    $dsn = sprintf('mysql:host=%s;port=%d;charset=%s', $dbConfig['host'], $dbConfig['port'], $dbConfig['charset']);
    $bootstrapPdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $bootstrapPdo->exec('CREATE DATABASE IF NOT EXISTS `' . str_replace('`', '``', $dbConfig['database']) . '` CHARACTER SET ' . $dbConfig['charset'] . ' COLLATE utf8mb4_unicode_ci');
}

$pdo = db();

if (!is_file($schemaFile)) {
    die('Schema file tidak ditemukan: ' . $schemaFile);
}

$schema = file_get_contents($schemaFile);
if ($schema === false) {
    die('Gagal membaca schema.');
}

$pdo->exec($schema);

$productColumns = [
    'image_url' => db_driver() === 'sqlite' ? 'TEXT NULL' : 'TEXT NULL',
];

foreach ($productColumns as $column => $definition) {
    try {
        $pdo->query("SELECT $column FROM products LIMIT 1");
    } catch (Throwable $exception) {
        $pdo->exec("ALTER TABLE products ADD COLUMN $column $definition");
    }
}

$orderColumns = [
    'customer_email' => db_driver() === 'sqlite' ? 'TEXT NULL' : 'VARCHAR(150) NULL',
    'tripay_checkout_url' => db_driver() === 'sqlite' ? 'TEXT NULL' : 'TEXT NULL',
    'tripay_pay_code' => db_driver() === 'sqlite' ? 'TEXT NULL' : 'VARCHAR(150) NULL',
    'tripay_pay_url' => db_driver() === 'sqlite' ? 'TEXT NULL' : 'TEXT NULL',
    'tripay_qr_url' => db_driver() === 'sqlite' ? 'TEXT NULL' : 'TEXT NULL',
    'tripay_qr_string' => db_driver() === 'sqlite' ? 'TEXT NULL' : 'LONGTEXT NULL',
    'expired_time' => db_driver() === 'sqlite' ? 'INTEGER NULL' : 'BIGINT NULL',
];

foreach ($orderColumns as $column => $definition) {
    try {
        $pdo->query("SELECT $column FROM orders LIMIT 1");
    } catch (Throwable $exception) {
        $pdo->exec("ALTER TABLE orders ADD COLUMN $column $definition");
    }
}

$stockColumns = [
    'notes' => db_driver() === 'sqlite' ? 'TEXT NULL' : 'TEXT NULL',
    'sold_order_id' => db_driver() === 'sqlite' ? 'INTEGER NULL' : 'BIGINT UNSIGNED NULL',
];

if (table_exists('product_stocks')) {
    foreach ($stockColumns as $column => $definition) {
        try {
            $pdo->query("SELECT $column FROM product_stocks LIMIT 1");
        } catch (Throwable $exception) {
            $pdo->exec("ALTER TABLE product_stocks ADD COLUMN $column $definition");
        }
    }
}

if ((int) $pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn() === 0) {
    $statement = $pdo->prepare('INSERT INTO admins (username, password_hash, full_name) VALUES (:username, :password_hash, :full_name)');
    $statement->execute([
        'username' => app_config()['admin']['username'],
        'password_hash' => password_hash(app_config()['admin']['password'], PASSWORD_DEFAULT),
        'full_name' => 'Administrator',
    ]);
}

if ((int) $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn() === 0) {
    foreach (sample_categories() as $category) {
        $statement = $pdo->prepare('INSERT INTO categories (name, slug, is_active, sort_order) VALUES (:name, :slug, :is_active, :sort_order)');
        $statement->execute([
            'name' => $category['name'],
            'slug' => $category['slug'],
            'is_active' => $category['is_active'],
            'sort_order' => $category['sort_order'],
        ]);
    }
}

if ((int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn() === 0) {
    foreach (sample_products() as $product) {
        $statement = $pdo->prepare('INSERT INTO products (category_id, name, slug, description, price, badge, image_url, is_active) VALUES (:category_id, :name, :slug, :description, :price, :badge, :image_url, :is_active)');
        $statement->execute([
            'category_id' => $product['category_id'],
            'name' => $product['name'],
            'slug' => $product['slug'],
            'description' => $product['description'],
            'price' => $product['price'],
            'badge' => $product['badge'],
            'image_url' => $product['image_url'] ?? '',
            'is_active' => $product['is_active'],
        ]);
    }
}

settings_upsert([
    'store_tagline' => 'Top up, joki, dan layanan digital cepat.',
    'store_whatsapp' => '6281234567890',
    'store_email' => app_config()['mail']['username'] ?: 'admin@jasajoki.me',
]);

echo 'Setup selesai menggunakan driver: ' . $driver . PHP_EOL;
