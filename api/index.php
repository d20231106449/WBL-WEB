<?php

$storagePath = '/tmp/storage';
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

$setVercelDefault = static function (string $key, string $value): void {
    if (getenv($key) !== false && getenv($key) !== '') {
        return;
    }

    putenv($key.'='.$value);
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
};

$setVercelDefault('APP_ENV', 'production');
$setVercelDefault('APP_DEBUG', 'false');
$setVercelDefault('LOG_CHANNEL', 'stderr');
$setVercelDefault('LOG_STACK', 'stderr');
$setVercelDefault('SESSION_DRIVER', 'cookie');
$setVercelDefault('CACHE_STORE', 'array');
$setVercelDefault('QUEUE_CONNECTION', 'sync');
$setVercelDefault('FILESYSTEM_DISK', 'local');

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

if ($requestPath === '/php-check') {
    header('Content-Type: application/json');
    echo json_encode([
        'ok' => true,
        'php_version' => PHP_VERSION,
        'app_key_set' => getenv('APP_KEY') !== false && getenv('APP_KEY') !== '',
        'supabase_url_set' => getenv('SUPABASE_URL') !== false && getenv('SUPABASE_URL') !== '',
        'supabase_key_set' => getenv('SUPABASE_ANON_KEY') !== false && getenv('SUPABASE_ANON_KEY') !== '',
        'storage_path' => $storagePath,
        'storage_writable' => is_writable($storagePath),
        'vendor_autoload_exists' => file_exists(__DIR__.'/../vendor/autoload.php'),
        'laravel_index_exists' => file_exists(__DIR__.'/../public/index.php'),
    ]);
    exit;
}

try {
    require __DIR__.'/../public/index.php';
} catch (Throwable $exception) {
    error_log($exception);

    if ($requestPath === '/vercel-check') {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'ok' => false,
            'error_type' => $exception::class,
            'error_message' => $exception->getMessage(),
            'error_file' => str_replace(dirname(__DIR__), '', $exception->getFile()),
            'error_line' => $exception->getLine(),
        ]);
        exit;
    }

    throw $exception;
}
