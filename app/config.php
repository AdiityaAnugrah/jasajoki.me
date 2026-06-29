<?php

function env_value(string $key, mixed $default = null): mixed
{
    static $env = null;

    if ($env === null) {
        $env = [];
        $envFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env';

        if (is_file($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];

            foreach ($lines as $line) {
                $line = trim($line);

                if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                    continue;
                }

                [$keyPart, $valuePart] = explode('=', $line, 2);
                $keyPart = trim($keyPart);
                $valuePart = trim($valuePart);
                $valuePart = trim($valuePart, "\"'");
                $env[$keyPart] = $valuePart;
            }
        }
    }

    return $env[$key] ?? $_ENV[$key] ?? $_SERVER[$key] ?? $default;
}

return [
    'app_name' => env_value('APP_NAME', 'JasaJoki Store'),
    'app_env' => env_value('APP_ENV', 'local'),
    'base_path' => env_value('APP_BASE_PATH', '/jasajoki.me/store'),
    'base_url' => env_value('APP_BASE_URL', 'http://localhost/jasajoki.me/store'),
    'db' => [
        'driver' => env_value('DB_CONNECTION', 'sqlite'),
        'host' => env_value('DB_HOST', '127.0.0.1'),
        'port' => (int) env_value('DB_PORT', 3306),
        'database' => env_value('DB_DATABASE', 'storage/database.sqlite'),
        'username' => env_value('DB_USERNAME', 'root'),
        'password' => env_value('DB_PASSWORD', ''),
        'charset' => env_value('DB_CHARSET', 'utf8mb4'),
    ],
    'admin' => [
        'username' => env_value('ADMIN_USERNAME', 'admin'),
        'password' => env_value('ADMIN_PASSWORD', 'admin123'),
    ],
    'tripay' => [
        'api_key' => env_value('TRIPAY_API_KEY', ''),
        'private_key' => env_value('TRIPAY_PRIVATE_KEY', ''),
        'merchant_code' => env_value('TRIPAY_MERCHANT_CODE', ''),
        'mode' => env_value('TRIPAY_MODE', 'sandbox'),
        'payment_method' => env_value('TRIPAY_PAYMENT_METHOD', 'QRIS'),
        'sandbox' => filter_var(env_value('TRIPAY_SANDBOX', 'true'), FILTER_VALIDATE_BOOL),
        'callback_url' => env_value('TRIPAY_CALLBACK_URL', '/store/callback-tripay.php'),
        'return_url' => env_value('TRIPAY_RETURN_URL', '/store/invoice.php'),
    ],
    'qrisify' => [
        'api_key' => env_value('QRISIFY_API_KEY', ''),
        'mode' => env_value('QRISIFY_MODE', 'TEST'),
        'webhook_secret' => env_value('QRISIFY_WEBHOOK_SECRET', ''),
        'webhook_url' => env_value('QRISIFY_WEBHOOK_URL', ''),
    ],
    'r2' => [
        'account_id' => env_value('CF_R2_ACCOUNT_ID', ''),
        'bucket' => env_value('CF_R2_BUCKET', ''),
        'public_base_url' => env_value('CF_R2_PUBLIC_BASE_URL', ''),
        'access_key_id' => env_value('CF_R2_ACCESS_KEY_ID', ''),
        'secret_access_key' => env_value('CF_R2_SECRET_ACCESS_KEY', ''),
    ],
    'mail' => [
        'username' => env_value('EMAIL_USER', ''),
        'password' => env_value('EMAIL_PASS', ''),
        'host' => env_value('SMTP_HOST', ''),
        'port' => (int) env_value('SMTP_PORT', 465),
    ],
    'store' => [
        'warranty_hours' => (int) env_value('STORE_WARRANTY_HOURS', 24),
    ],
];
