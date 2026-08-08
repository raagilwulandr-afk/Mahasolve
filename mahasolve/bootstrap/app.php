<?php

ini_set('session.save_path', '/tmp/storage/framework/sessions');
ini_set('upload_tmp_dir', '/tmp/storage');

set_error_handler(function ($errno, $errstr) {
    if (str_contains($errstr, 'tempnam')) {
        return true;
    }
    return false;
});

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureRole::class,
        ]);
        if (env('APP_ENV') === 'testing') {
            $middleware->validateCsrfTokens(except: ['*']);
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
        $exceptions->reportable(function (\Throwable $e) {
            if (str_contains($e->getMessage(), 'tempnam')) {
                return false;
            }
        });
    })->create();

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

    $app->useStoragePath('/tmp/storage');
}

return $app;