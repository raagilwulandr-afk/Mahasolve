<?php

define('LARAVEL_START', microtime(true));

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

// 2. Set environment paths & SQLite database in /tmp
putenv("APP_STORAGE_PATH={$tmpStorage}");
putenv("VIEW_COMPILED_PATH={$tmpStorage}/framework/views");

$tmpSqlite = '/tmp/database.sqlite';
if (!file_exists($tmpSqlite)) {
    $seedSqlite = __DIR__ . '/../mahasolve/database/database.sqlite';
    if (file_exists($seedSqlite)) {
        @copy($seedSqlite, $tmpSqlite);
    } else {
        @touch($tmpSqlite);
    }
}

putenv("APP_URL=https://mahasolve.vercel.app");
putenv("DB_CONNECTION=sqlite");
putenv("DB_DATABASE={$tmpSqlite}");
putenv("SESSION_DRIVER=cookie");
putenv("CACHE_STORE=array");
putenv("QUEUE_CONNECTION=sync");

// 3. Set HTTPS environment server variables for reverse proxy
$_SERVER['HTTPS'] = 'on';
$_SERVER['SERVER_PORT'] = 443;
$_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';

// 4. Register Composer autoloader
require __DIR__ . '/../mahasolve/vendor/autoload.php';

// 5. Bootstrap Laravel application
$app = require_once __DIR__ . '/../mahasolve/bootstrap/app.php';

// 6. Explicitly set storage path to /tmp/storage
$app->useStoragePath($tmpStorage);

// 7. Handle incoming HTTP request
$app->handleRequest(\Illuminate\Http\Request::capture());



