<?php

require_once __DIR__ . '/helpers.php';

function tripay_config(): array
{
    return app_config()['tripay'];
}

function tripay_is_production(): bool
{
    $mode = strtolower((string) (tripay_config()['mode'] ?? 'sandbox'));

    if (in_array($mode, ['production', 'prod', 'live'], true)) {
        return true;
    }

    return !(bool) tripay_config()['sandbox'];
}

function tripay_base_url(): string
{
    return tripay_is_production()
        ? 'https://tripay.co.id/api'
        : 'https://tripay.co.id/api-sandbox';
}

function tripay_default_method(): string
{
    return (string) (tripay_config()['payment_method'] ?? 'QRIS');
}

function tripay_has_credentials(): bool
{
    $config = tripay_config();

    return $config['api_key'] !== ''
        && $config['private_key'] !== ''
        && $config['merchant_code'] !== '';
}

function tripay_payment_channels(): array
{
    return [
        ['code' => 'QRIS', 'name' => 'QRIS'],
        ['code' => 'QRISC', 'name' => 'QRIS Customizable'],
        ['code' => 'QRIS2', 'name' => 'QRIS 2'],
        ['code' => 'BRIVA', 'name' => 'BRI Virtual Account'],
        ['code' => 'BNIVA', 'name' => 'BNI Virtual Account'],
        ['code' => 'MANDIRIVA', 'name' => 'Mandiri Virtual Account'],
        ['code' => 'BCAVA', 'name' => 'BCA Virtual Account'],
    ];
}

function tripay_signature(string $merchantRef, int $amount): string
{
    $config = tripay_config();
    return hash_hmac('sha256', $config['merchant_code'] . $merchantRef . $amount, $config['private_key']);
}

function tripay_api_request(string $method, string $path, array $payload = []): array
{
    $config = tripay_config();
    $url = rtrim(tripay_base_url(), '/') . $path;
    $curl = curl_init();

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => false,
        CURLOPT_FAILONERROR => false,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $config['api_key'],
        ],
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        CURLOPT_TIMEOUT => 45,
    ];

    if (strtoupper($method) === 'POST') {
        $options[CURLOPT_POST] = true;
        $options[CURLOPT_POSTFIELDS] = http_build_query($payload);
    } elseif (!empty($payload)) {
        $options[CURLOPT_URL] .= '?' . http_build_query($payload);
    }

    curl_setopt_array($curl, $options);
    $response = curl_exec($curl);
    $error = curl_error($curl);
    $statusCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($response === false || $error !== '') {
        return [
            'success' => false,
            'message' => $error ?: 'Unknown cURL error',
            'http_code' => $statusCode,
            'data' => null,
        ];
    }

    $decoded = json_decode($response, true);

    if (!is_array($decoded)) {
        return [
            'success' => false,
            'message' => 'Invalid JSON response from Tripay',
            'http_code' => $statusCode,
            'raw' => $response,
            'data' => null,
        ];
    }

    $decoded['http_code'] = $statusCode;
    return $decoded;
}

function tripay_create_transaction(array $payload): array
{
    if (!tripay_has_credentials()) {
        return [
            'success' => false,
            'message' => 'Kredensial Tripay belum lengkap.',
            'data' => null,
        ];
    }

    $merchantRef = (string) $payload['merchant_ref'];
    $amount = (int) $payload['amount'];

    $request = [
        'method' => $payload['method'] ?? tripay_default_method(),
        'merchant_ref' => $merchantRef,
        'amount' => $amount,
        'customer_name' => $payload['customer_name'],
        'customer_email' => $payload['customer_email'],
        'customer_phone' => $payload['customer_phone'],
        'order_items' => $payload['order_items'],
        'callback_url' => $payload['callback_url'],
        'return_url' => $payload['return_url'],
        'expired_time' => $payload['expired_time'] ?? (time() + 24 * 60 * 60),
        'signature' => tripay_signature($merchantRef, $amount),
    ];

    return tripay_api_request('POST', '/transaction/create', $request);
}

function tripay_detail_transaction(string $reference): array
{
    if (!tripay_has_credentials()) {
        return [
            'success' => false,
            'message' => 'Kredensial Tripay belum lengkap.',
            'data' => null,
        ];
    }

    return tripay_api_request('GET', '/transaction/detail', [
        'reference' => $reference,
    ]);
}
