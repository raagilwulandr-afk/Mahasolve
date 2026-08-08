<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

if (!defined('LARAVEL_START')) {
    define('LARAVEL_START', microtime(true));
}

// Ensure Vercel /tmp storage directories exist for Blade view compilation
if (is_dir('/tmp')) {
    $dirs = [
        '/tmp/storage',
        '/tmp/storage/framework',
        '/tmp/storage/framework/views',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/framework/cache',
        '/tmp/storage/framework/cache/data',
        '/tmp/storage/logs',
        '/tmp/bootstrap',
        '/tmp/bootstrap/cache',
    ];

    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
    }

    putenv('TMPDIR=/tmp/storage');
    $_ENV['TMPDIR'] = '/tmp/storage';
    $_SERVER['TMPDIR'] = '/tmp/storage';

    putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
    $_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
    $_SERVER['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

if (is_dir('/tmp')) {
    $app->useStoragePath('/tmp/storage');
}

$app->handleRequest(Request::capture());
