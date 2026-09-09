<?php

declare(strict_types=1);

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$routes = [
    '/empty' => [204, ''],
    '/zero'  => [200, '0'],
    '/boom'  => [500, 'error'],
];

if (isset($routes[$path])) {
    [$code, $body] = $routes[$path];
    http_response_code($code);
    header('Content-Type: text/plain');
    echo $body;
    return;
}

if ($path === '/echo') {
    header('Content-Type: text/plain');
    echo file_get_contents('php://input');
    return;
}

if ($path === '/cookie') {
    header('Content-Type: text/plain');
    echo $_SERVER['HTTP_COOKIE'] ?? '';
    return;
}

http_response_code(404);
echo 'not found';
