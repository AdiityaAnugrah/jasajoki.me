<?php

require_once __DIR__ . '/helpers.php';

function r2_config(): array
{
    return app_config()['r2'];
}

function r2_is_enabled(): bool
{
    $config = r2_config();
    return trim((string) ($config['account_id'] ?? '')) !== ''
        && trim((string) ($config['bucket'] ?? '')) !== ''
        && trim((string) ($config['access_key_id'] ?? '')) !== ''
        && trim((string) ($config['secret_access_key'] ?? '')) !== '';
}

function r2_public_base_url(): string
{
    return rtrim((string) (r2_config()['public_base_url'] ?? ''), '/');
}

function r2_object_url(string $objectKey): string
{
    return r2_public_base_url() . '/' . ltrim($objectKey, '/');
}

function r2_upload_from_tmp(string $tmpPath, string $objectKey, string $contentType): array
{
    if (!r2_is_enabled()) {
        return ['success' => false, 'message' => 'R2 belum dikonfigurasi.'];
    }

    if (!is_file($tmpPath)) {
        return ['success' => false, 'message' => 'File upload tidak ditemukan.'];
    }

    $config = r2_config();
    $bucket = (string) $config['bucket'];
    $accountId = (string) $config['account_id'];
    $accessKey = (string) $config['access_key_id'];
    $secretKey = (string) $config['secret_access_key'];
    $host = $accountId . '.r2.cloudflarestorage.com';
    $region = 'auto';
    $service = 's3';
    $method = 'PUT';
    $amzDate = gmdate('Ymd\THis\Z');
    $dateStamp = gmdate('Ymd');
    $body = file_get_contents($tmpPath);

    if ($body === false) {
        return ['success' => false, 'message' => 'Gagal membaca file upload.'];
    }

    $payloadHash = hash('sha256', $body);
    $canonicalUri = '/' . $bucket . '/' . implode('/', array_map('rawurlencode', explode('/', ltrim($objectKey, '/'))));
    $canonicalHeaders = 'host:' . $host . "\n"
        . 'x-amz-content-sha256:' . $payloadHash . "\n"
        . 'x-amz-date:' . $amzDate . "\n";
    $signedHeaders = 'host;x-amz-content-sha256;x-amz-date';
    $canonicalRequest = $method . "\n"
        . $canonicalUri . "\n\n"
        . $canonicalHeaders . "\n"
        . $signedHeaders . "\n"
        . $payloadHash;

    $credentialScope = $dateStamp . '/' . $region . '/' . $service . '/aws4_request';
    $stringToSign = 'AWS4-HMAC-SHA256' . "\n"
        . $amzDate . "\n"
        . $credentialScope . "\n"
        . hash('sha256', $canonicalRequest);

    $kDate = hash_hmac('sha256', $dateStamp, 'AWS4' . $secretKey, true);
    $kRegion = hash_hmac('sha256', $region, $kDate, true);
    $kService = hash_hmac('sha256', $service, $kRegion, true);
    $kSigning = hash_hmac('sha256', 'aws4_request', $kService, true);
    $signature = hash_hmac('sha256', $stringToSign, $kSigning);
    $authorization = 'AWS4-HMAC-SHA256 Credential=' . $accessKey . '/' . $credentialScope
        . ', SignedHeaders=' . $signedHeaders
        . ', Signature=' . $signature;

    $curl = curl_init('https://' . $host . $canonicalUri);
    curl_setopt_array($curl, [
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => false,
        CURLOPT_HTTPHEADER => [
            'Authorization: ' . $authorization,
            'Content-Type: ' . $contentType,
            'Content-Length: ' . strlen($body),
            'Host: ' . $host,
            'x-amz-content-sha256: ' . $payloadHash,
            'x-amz-date: ' . $amzDate,
        ],
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_TIMEOUT => 60,
    ]);

    $response = curl_exec($curl);
    $error = curl_error($curl);
    $statusCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($response === false || $error !== '') {
        return ['success' => false, 'message' => $error ?: 'Upload ke R2 gagal.'];
    }

    if ($statusCode < 200 || $statusCode >= 300) {
        return ['success' => false, 'message' => 'Upload ke R2 gagal dengan HTTP ' . $statusCode];
    }

    return [
        'success' => true,
        'object_key' => ltrim($objectKey, '/'),
        'public_url' => r2_object_url($objectKey),
    ];
}

function r2_upload_product_image(array $file, string $productName): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'message' => 'Tidak ada file yang diupload.'];
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload file gagal.'];
    }

    $tmpPath = (string) ($file['tmp_name'] ?? '');
    $originalName = (string) ($file['name'] ?? 'image');
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($extension, $allowed, true)) {
        return ['success' => false, 'message' => 'Format gambar harus jpg, jpeg, png, atau webp.'];
    }

    $mime = mime_content_type($tmpPath) ?: 'application/octet-stream';
    $objectKey = 'products/' . date('Y/m') . '/' . slugify($productName) . '-' . time() . '.' . $extension;

    return r2_upload_from_tmp($tmpPath, $objectKey, $mime);
}
