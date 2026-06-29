<?php

require_once __DIR__ . '/../app/qrisify.php';

header('Content-Type: application/json');

$rawBody = file_get_contents('php://input');
$signature = (string) ($_SERVER['HTTP_X_QRIS_SIGNATURE'] ?? '');
$event = (string) ($_SERVER['HTTP_X_QRIS_EVENT'] ?? '');
$expected = hash_hmac('sha256', $rawBody, (string) qrisify_config()['webhook_secret']);

if ($signature === '' || !hash_equals($expected, $signature)) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid signature',
    ]);
    exit;
}

if ($event !== 'qris.payment.success') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Unsupported event',
    ]);
    exit;
}

$payload = json_decode($rawBody, true);

if (!is_array($payload) || !is_array($payload['data'] ?? null)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid payload',
    ]);
    exit;
}

$data = $payload['data'];
$orderCode = (string) ($data['external_id'] ?? '');
$transactionId = (string) ($data['transaction_id'] ?? '');
$paymentStatus = qrisify_normalize_status((string) ($data['status'] ?? 'SUCCESS'));
$order = order_find_by_code($orderCode);

if (!$order) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'Order not found',
    ]);
    exit;
}

payment_log_create((int) $order['id'], 'qrisify_webhook', $rawBody);
order_update_status_by_reference($orderCode, $transactionId, $paymentStatus);

echo json_encode([
    'success' => true,
    'message' => 'OK',
]);
