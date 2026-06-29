<?php
require_once __DIR__ . '/../app/helpers.php';

http_response_code(200);
header('Content-Type: application/json');

echo json_encode([
    'success' => true,
    'message' => 'Callback endpoint placeholder. Nanti dipakai untuk validasi signature Tripay dan update status order.',
]);
