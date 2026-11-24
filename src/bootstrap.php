<?php

declare(strict_types=1);

error_reporting(E_ALL);

$configFile = dirname(__DIR__) . '/config.php';

if (!is_file($configFile)) {
    http_response_code(500);
    exit('config.php is missing. Copy config.example.php to config.php.');
}

$config = require $configFile;

function config(): array
{
    global $config;

    return $config;
}

ini_set('display_errors', !empty($config['debug']) ? '1' : '0');
date_default_timezone_set($config['timezone'] ?? 'UTC');

if (PHP_SAPI !== 'cli') {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => !empty($_SERVER['HTTPS']),
    ]);
    session_start();
}

require __DIR__ . '/helpers.php';
require __DIR__ . '/db.php';
require __DIR__ . '/router.php';
require __DIR__ . '/messages.php';
require __DIR__ . '/auth.php';
