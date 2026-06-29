<?php

require_once __DIR__ . '/../app/tripay.php';

header('Content-Type: application/json');

$json = file_get_contents('php://input');
$callbackSignature = $_SERVER['HTTP_X_CALLBACK_SIGNATURE'] ?? '';
$callbackEvent = $_SERVER['HTTP_X_CALLBACK_EVENT'] ?? '';
$expectedSignature = hash_hmac('sha256', $json, tripay_config()['private_key']);

if ($callbackSignature !== $expectedSignature) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid signature',
    ]);
    exit;
}

if ($callbackEvent !== 'payment_status') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Unrecognized callback event',
    ]);
    exit;
}

$data = json_decode($json, true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid payload',
    ]);
    exit;
}

$merchantRef = (string) ($data['merchant_ref'] ?? '');
$tripayReference = (string) ($data['reference'] ?? '');
$paymentStatus = strtoupper((string) ($data['status'] ?? ''));

$order = order_find_by_code($merchantRef);

if (!$order) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'Order not found',
    ]);
    exit;
}

payment_log_create((int) $order['id'], 'tripay_callback', $json);
order_update_status_by_reference($merchantRef, $tripayReference, $paymentStatus);

echo json_encode([
    'success' => true,
]);
