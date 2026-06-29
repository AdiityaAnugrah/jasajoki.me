<?php

require_once __DIR__ . '/helpers.php';

function qrisify_config(): array
{
    return app_config()['qrisify'];
}

function qrisify_is_live(): bool
{
    return strtoupper((string) (qrisify_config()['mode'] ?? 'TEST')) === 'LIVE';
}

function qrisify_has_credentials(): bool
{
    return trim((string) (qrisify_config()['api_key'] ?? '')) !== '';
}

function qrisify_base_url(): string
{
    return 'https://qrisify.adihub.my.id/api/v1';
}

function qrisify_api_request(string $method, string $path, ?array $payload = null): array
{
    $url = rtrim(qrisify_base_url(), '/') . $path;
    $curl = curl_init();

    $headers = [
        'Accept: application/json',
        'x-api-key: ' . qrisify_config()['api_key'],
    ];

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => false,
        CURLOPT_FAILONERROR => false,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        CURLOPT_TIMEOUT => 45,
    ];

    if (strtoupper($method) === 'POST') {
        $headers[] = 'Content-Type: application/json';
        $options[CURLOPT_POST] = true;
        $options[CURLOPT_HTTPHEADER] = $headers;
        $options[CURLOPT_POSTFIELDS] = json_encode($payload ?? [], JSON_UNESCAPED_SLASHES);
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
            'message' => 'Invalid JSON response from QRISify',
            'http_code' => $statusCode,
            'raw' => $response,
            'data' => null,
        ];
    }

    $decoded['http_code'] = $statusCode;
    return $decoded;
}

function qrisify_normalize_status(string $status): string
{
    return match (strtoupper($status)) {
        'SUCCESS', 'PAID' => 'PAID',
        'EXPIRED' => 'EXPIRED',
        'FAILED' => 'FAILED',
        default => 'UNPAID',
    };
}

function qrisify_webhook_url(): string
{
    $configured = trim((string) (qrisify_config()['webhook_url'] ?? ''));
    if ($configured !== '') {
        return $configured;
    }

    return rtrim(app_config()['base_url'], '/') . '/callback-qrisify.php';
}

function qrisify_qr_image_url(?string $path): ?string
{
    $path = trim((string) $path);
    if ($path === '') {
        return null;
    }

    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }

    return 'https://qrisify.adihub.my.id' . $path;
}

function qrisify_create_transaction(array $payload): array
{
    if (!qrisify_has_credentials()) {
        return [
            'success' => false,
            'message' => 'Kredensial QRISify belum lengkap.',
            'data' => null,
        ];
    }

    $request = [
        'amount' => (int) $payload['amount'],
        'external_id' => (string) $payload['external_id'],
        'unique_code' => (int) ($payload['unique_code'] ?? 0),
        'expiry_minutes' => (int) ($payload['expiry_minutes'] ?? 15),
        'webhook_url' => (string) ($payload['webhook_url'] ?? qrisify_webhook_url()),
        'webhook_secret' => (string) ($payload['webhook_secret'] ?? qrisify_config()['webhook_secret']),
    ];

    return qrisify_api_request('POST', '/transactions', $request);
}

function qrisify_transaction_detail(string $transactionId): array
{
    if (!qrisify_has_credentials()) {
        return [
            'success' => false,
            'message' => 'Kredensial QRISify belum lengkap.',
            'data' => null,
        ];
    }

    return qrisify_api_request('GET', '/transactions/' . rawurlencode($transactionId));
}

function qrisify_test_pay(string $transactionId): array
{
    if (!qrisify_has_credentials()) {
        return [
            'success' => false,
            'message' => 'Kredensial QRISify belum lengkap.',
            'data' => null,
        ];
    }

    return qrisify_api_request('POST', '/transactions/' . rawurlencode($transactionId) . '/test-pay', []);
}
