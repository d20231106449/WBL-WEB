<?php

$storagePath = '/tmp/storage';

function setVercelDefault(string $key, string $value): void
{
    if (getenv($key) !== false && getenv($key) !== '') {
        return;
    }

    putenv($key.'='.$value);
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

setVercelDefault('APP_ENV', 'production');
setVercelDefault('APP_DEBUG', 'false');
setVercelDefault('LOG_CHANNEL', 'stderr');
setVercelDefault('LOG_STACK', 'stderr');
setVercelDefault('SESSION_DRIVER', 'cookie');
setVercelDefault('CACHE_STORE', 'array');
setVercelDefault('QUEUE_CONNECTION', 'sync');
setVercelDefault('FILESYSTEM_DISK', 'local');

foreach ([
    $storagePath,
    $storagePath.'/app',
    $storagePath.'/app/public',
    $storagePath.'/framework',
    $storagePath.'/framework/cache',
    $storagePath.'/framework/cache/data',
    $storagePath.'/framework/sessions',
    $storagePath.'/framework/testing',
    $storagePath.'/framework/views',
    $storagePath.'/logs',
] as $path) {
    if (! is_dir($path)) {
        mkdir($path, 0777, true);
    }
}

putenv('APP_STORAGE_PATH='.$storagePath);
$_ENV['APP_STORAGE_PATH'] = $storagePath;
$_SERVER['APP_STORAGE_PATH'] = $storagePath;

require __DIR__.'/../public/index.php';
