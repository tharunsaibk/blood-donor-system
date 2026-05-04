<?php
$uri  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $uri;

if (preg_match('#^/(includes|database)(/|$)#', $uri)) {
    http_response_code(404);
    return true;
}

if ($uri !== '/' && is_file($file)) {
    return false;
}

if (!is_dir($file) && is_file($file . '.php')) {
    require $file . '.php';
    return true;
}

if (is_dir($file) && is_file(rtrim($file, '/') . '/index.php')) {
    require rtrim($file, '/') . '/index.php';
    return true;
}

http_response_code(404);
require __DIR__ . '/index.php';
