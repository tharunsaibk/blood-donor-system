<?php
declare(strict_types=1);

$configPath = dirname(__DIR__) . '/config.php';
if (!file_exists($configPath)) {
    http_response_code(500);
    exit('config.php is missing. Copy config.example.php to config.php and edit it.');
}

$config = require $configPath;

date_default_timezone_set($config['app']['timezone'] ?? 'UTC');

if ($config['app']['debug'] ?? false) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ERROR | E_PARSE);
}

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/flash.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/mailer.php';

$GLOBALS['config'] = $config;
$GLOBALS['db']     = db_connect($config['db']);
