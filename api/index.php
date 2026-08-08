<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

try {
    if (!defined('LARAVEL_START')) {
        define('LARAVEL_START', microtime(true));
    }

    // 1. Ensure /tmp storage directories exist for serverless read-only filesystem
    $tmpStorage = '/tmp/storage';
    $directories = [
        $tmpStorage,
        $tmpStorage . '/framework',
        $tmpStorage . '/framework/views',
        $tmpStorage . '/framework/cache',
        $tmpStorage . '/framework/sessions',
        $tmpStorage . '/framework/testing',
        $tmpStorage . '/logs',
        $tmpStorage . '/bootstrap/cache',
    ];

    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
    }

    // 2. Set environment paths to /tmp
    putenv("APP_STORAGE_PATH={$tmpStorage}");
    putenv("VIEW_COMPILED_PATH={$tmpStorage}/framework/views");

    // 3. Register Composer autoloader
    require __DIR__ . '/../mahasolve/vendor/autoload.php';

    // 4. Bootstrap Laravel application
    $app = require_once __DIR__ . '/../mahasolve/bootstrap/app.php';

    // 5. Explicitly set storage path to /tmp/storage
    $app->useStoragePath($tmpStorage);

    // 6. Handle incoming HTTP request
    $app->handleRequest(\Illuminate\Http\Request::capture());
} catch (\Throwable $e) {
    http_response_code(500);
    echo "<h1>Server Exception Caught</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " on line " . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}


