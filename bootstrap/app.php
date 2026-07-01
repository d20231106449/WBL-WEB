<?php

use App\Http\Middleware\EnsureSupabaseAdmin;
use App\Http\Middleware\EnsureSupabaseAuthenticated;
use Illuminate\Cache\CacheServiceProvider;
use Illuminate\Cookie\CookieServiceProvider;
use Illuminate\Database\DatabaseServiceProvider;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Queue\QueueServiceProvider;
use Illuminate\Session\SessionServiceProvider;
use Illuminate\Translation\TranslationServiceProvider;
use Illuminate\Validation\ValidationServiceProvider;
use Illuminate\View\ViewServiceProvider;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'supabase.admin' => EnsureSupabaseAdmin::class,
            'supabase.auth' => EnsureSupabaseAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();

foreach ([
    CacheServiceProvider::class,
    CookieServiceProvider::class,
    DatabaseServiceProvider::class,
    EncryptionServiceProvider::class,
    FilesystemServiceProvider::class,
    QueueServiceProvider::class,
    SessionServiceProvider::class,
    TranslationServiceProvider::class,
    ValidationServiceProvider::class,
    ViewServiceProvider::class,
] as $provider) {
    if (! $app->providerIsLoaded($provider)) {
        $app->register($provider);
    }
}

if ($storagePath = getenv('APP_STORAGE_PATH')) {
    $app->useStoragePath($storagePath);
}

return $app;
