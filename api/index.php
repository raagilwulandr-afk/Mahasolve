<?php

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

// 3. Require vendor autoload and Laravel entrypoint
if (file_exists(__DIR__ . '/../mahasolve/vendor/autoload.php')) {
    require __DIR__ . '/../mahasolve/vendor/autoload.php';
}

require __DIR__ . '/../mahasolve/public/index.php';
