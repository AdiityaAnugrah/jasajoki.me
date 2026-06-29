<?php

require_once __DIR__ . '/helpers.php';

function tripay_config(): array
{
    return app_config()['tripay'];
}

function tripay_base_url(): string
{
    return tripay_config()['sandbox']
        ? 'https://tripay.co.id/api-sandbox'
        : 'https://tripay.co.id/api';
}

function tripay_payment_channels(): array
{
    return [
        ['code' => 'QRIS', 'name' => 'QRIS'],
        ['code' => 'BRIVA', 'name' => 'BRI Virtual Account'],
        ['code' => 'BNIVA', 'name' => 'BNI Virtual Account'],
        ['code' => 'MANDIRIVA', 'name' => 'Mandiri Virtual Account'],
    ];
}

function tripay_create_transaction(array $payload): array
{
    return [
        'success' => false,
        'message' => 'Integrasi Tripay belum diaktifkan. Isi API key, private key, dan merchant code dulu.',
        'payload' => $payload,
    ];
}
