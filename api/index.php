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

// 2. Set environment paths & Database fallback configuration
putenv("APP_STORAGE_PATH={$tmpStorage}");
putenv("VIEW_COMPILED_PATH={$tmpStorage}/framework/views");

putenv("APP_URL=https://mahasolve-seven.vercel.app");

if (!getenv('DB_CONNECTION') || getenv('DB_CONNECTION') === 'sqlite') {
    putenv("DB_CONNECTION=pgsql");
    putenv("DB_HOST=aws-0-ap-southeast-1.pooler.supabase.com");
    putenv("DB_PORT=5432");
    putenv("DB_DATABASE=postgres");
    putenv("DB_USERNAME=postgres.mbqpxgbfjrexjmvfypip");
    putenv("DB_PASSWORD=cDt1DoEy8WZN5XKN");
    putenv("DB_SSLMODE=require");
}

putenv("SESSION_DRIVER=cookie");
putenv("CACHE_STORE=array");
putenv("QUEUE_CONNECTION=sync");

// 3. Set HTTPS environment server variables for reverse proxy
$_SERVER['HTTPS'] = 'on';
$_SERVER['SERVER_PORT'] = 443;
$_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';

// 4. Register Composer autoloader
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../mahasolve/vendor/autoload.php')) {
    require __DIR__ . '/../mahasolve/vendor/autoload.php';
}

// 5. Bootstrap Laravel application
$app = require_once __DIR__ . '/../mahasolve/bootstrap/app.php';

// 6. Explicitly set storage path to /tmp/storage
$app->useStoragePath($tmpStorage);

// 7. Handle incoming HTTP request
$app->handleRequest(\Illuminate\Http\Request::capture());
