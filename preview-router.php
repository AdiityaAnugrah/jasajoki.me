<?php

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$baseDir = __DIR__;

if ($uri === '/') {
    readfile($baseDir . '/root/index.html');
    return true;
}

$filePath = realpath($baseDir . $uri);
if ($filePath && str_starts_with($filePath, $baseDir) && is_file($filePath)) {
    return false;
}

$storeAsset = $baseDir . $uri;
if (is_file($storeAsset)) {
    return false;
}

http_response_code(404);
echo 'Not Found';
