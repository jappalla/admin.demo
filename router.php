<?php
declare(strict_types=1);

$requestPath = (string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
$absolutePath = __DIR__ . DIRECTORY_SEPARATOR . ltrim($requestPath, '/');

if ($requestPath !== '/' && is_file($absolutePath)) {
    return false;
}

require __DIR__ . '/index.php';
