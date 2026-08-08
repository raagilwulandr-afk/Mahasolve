<?php

$compiledPath = env('VIEW_COMPILED_PATH', storage_path('framework/views'));
if (!is_dir($compiledPath) && is_dir('/tmp')) {
    @mkdir($compiledPath, 0755, true);
}

return [
    'paths' => [
        resource_path('views'),
    ],
    'compiled' => $compiledPath,
];
