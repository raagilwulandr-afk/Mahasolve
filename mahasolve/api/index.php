<?php

ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);

if (!defined('LARAVEL_START')) {
    define('LARAVEL_START', microtime(true));
}

// Vercel Serverless environment initialization
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

// Forward Vercel requests to public/index.php
require __DIR__ . '/../public/index.php';
