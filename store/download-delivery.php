<?php
require_once __DIR__ . '/../app/helpers.php';

$orderCode = (string) request_get('code', '');
$order = $orderCode !== '' ? order_find_by_code($orderCode) : null;

if (!$order || strtoupper((string) ($order['payment_status'] ?? '')) !== 'PAID') {
    http_response_code(404);
    exit('Delivery not available');
}

$stock = stock_find_by_order_id((int) $order['id']);
if (!$stock) {
    http_response_code(404);
    exit('Stock not assigned');
}

$filename = 'jasajoki-delivery-' . preg_replace('/[^A-Za-z0-9\-]/', '-', $orderCode) . '.txt';
$content = stock_delivery_text($stock) . PHP_EOL;

header('Content-Type: text/plain; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . strlen($content));

echo $content;
