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
if (!file_exists($tmpSqlite) && file_exists(__DIR__ . '/../mahasolve/database/database.sqlite')) {
    @copy(__DIR__ . '/../mahasolve/database/database.sqlite', $tmpSqlite);
}

putenv("DB_CONNECTION=sqlite");
putenv("DB_DATABASE={$tmpSqlite}");
putenv("SESSION_DRIVER=cookie");
putenv("CACHE_STORE=array");
putenv("QUEUE_CONNECTION=sync");

// 3. Register Composer autoloader
require __DIR__ . '/../mahasolve/vendor/autoload.php';

// 4. Bootstrap Laravel application
$app = require_once __DIR__ . '/../mahasolve/bootstrap/app.php';

// 5. Explicitly set storage path to /tmp/storage
$app->useStoragePath($tmpStorage);

// 6. Handle incoming HTTP request
$app->handleRequest(\Illuminate\Http\Request::capture());



