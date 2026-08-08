<?php

// Vercel Serverless environment initialization
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
    $storageDirs = [
        '/tmp/storage/framework/views',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/framework/cache/data',
        '/tmp/storage/logs',
        '/tmp/bootstrap/cache',
    ];

    foreach ($storageDirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
    }

    putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
    $_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
    $_SERVER['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

    putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');
    $_ENV['APP_SERVICES_CACHE'] = '/tmp/bootstrap/cache/services.php';

    putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
    $_ENV['APP_PACKAGES_CACHE'] = '/tmp/bootstrap/cache/packages.php';

    putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
    $_ENV['APP_CONFIG_CACHE'] = '/tmp/bootstrap/cache/config.php';

    putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php');
    $_ENV['APP_ROUTES_CACHE'] = '/tmp/bootstrap/cache/routes.php';

    putenv('APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php');
    $_ENV['APP_EVENTS_CACHE'] = '/tmp/bootstrap/cache/events.php';
}

// Forward Vercel requests to Laravel entrypoint
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
    $app->useStoragePath('/tmp/storage');
}

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);

