<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserPortalController;
use Illuminate\Support\Facades\Route;

Route::get('/vercel-check', function () {
    return response()->json([
        'ok' => true,
        'app_env' => config('app.env'),
        'app_key_set' => filled(config('app.key')),
        'supabase_url_set' => filled(config('services.supabase.url')),
        'supabase_key_set' => filled(config('services.supabase.key')),
        'session_driver' => config('session.driver'),
        'cache_store' => config('cache.default'),
        'queue_connection' => config('queue.default'),
        'storage_path' => storage_path(),
        'storage_writable' => is_writable(storage_path()),
        'vite_manifest_exists' => file_exists(public_path('build/manifest.json')),
    ]);
});

Route::get('/', function () {
    if (! session()->has('profile.id')) {
        return redirect()->route('login');
    }

    return redirect()->route(session('profile.role') === 'admin' ? 'admin.dashboard' : 'user.dashboard');
});

Route::get('/login', [AdminAuthController::class, 'show'])->name('login');
Route::post('/login', [AdminAuthController::class, 'login'])->name('login.store');
Route::get('/register', [AdminAuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AdminAuthController::class, 'register'])->name('register.store');
Route::get('/forgot-password', [AdminAuthController::class, 'forgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AdminAuthController::class, 'sendPasswordReset'])->name('password.email');
Route::get('/reset-password', [AdminAuthController::class, 'resetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AdminAuthController::class, 'resetPassword'])->name('password.update');
Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

Route::prefix('admin')->name('admin.')->middleware('supabase.admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings');
    Route::patch('/bookings/{booking}', [AdminController::class, 'updateBooking'])->name('bookings.update');
    Route::get('/complaints', [AdminController::class, 'complaints'])->name('complaints');
    Route::patch('/complaints/{complaint}', [AdminController::class, 'resolveComplaint'])->name('complaints.resolve');
    Route::get('/checkouts', [AdminController::class, 'checkouts'])->name('checkouts');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::patch('/users/{profile}', [AdminController::class, 'updateUserRole'])->name('users.role');
});

Route::prefix('app')->name('user.')->middleware('supabase.auth')->group(function () {
    Route::get('/', [UserPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/bookings', [UserPortalController::class, 'bookings'])->name('bookings');
    Route::get('/bookings/create', [UserPortalController::class, 'createBooking'])->name('bookings.create');
    Route::get('/bookings/slots', [UserPortalController::class, 'bookedSlots'])->name('bookings.slots');
    Route::post('/bookings', [UserPortalController::class, 'storeBooking'])->name('bookings.store');
    Route::patch('/bookings/{booking}/cancel', [UserPortalController::class, 'cancelBooking'])->name('bookings.cancel');
    Route::get('/gallery', [UserPortalController::class, 'gallery'])->name('gallery');
    Route::get('/complaints', [UserPortalController::class, 'complaints'])->name('complaints');
    Route::post('/complaints', [UserPortalController::class, 'storeComplaint'])->name('complaints.store');
    Route::get('/bookings/{booking}/checkout', [UserPortalController::class, 'checkout'])->name('checkout');
    Route::post('/bookings/{booking}/checkout', [UserPortalController::class, 'storeCheckout'])->name('checkout.store');
    Route::get('/profile', [UserPortalController::class, 'profile'])->name('profile');
    Route::get('/profile/edit', [UserPortalController::class, 'editProfile'])->name('profile.edit');
    Route::patch('/profile', [UserPortalController::class, 'updateProfile'])->name('profile.update');
    Route::get('/profile/password', [UserPortalController::class, 'changePasswordForm'])->name('profile.password');
    Route::patch('/profile/password', [UserPortalController::class, 'changePassword'])->name('profile.password.update');
    Route::get('/about', [UserPortalController::class, 'about'])->name('about');
});
