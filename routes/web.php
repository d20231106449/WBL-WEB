<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserPortalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! session()->has('profile.id')) return redirect()->route('login');
    return redirect()->route(session('profile.role') === 'admin' ? 'admin.dashboard' : 'user.dashboard');
});

Route::get('/login', [AdminAuthController::class, 'show'])->name('login');
Route::post('/login', [AdminAuthController::class, 'login'])->name('login.store');
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
    Route::post('/bookings', [UserPortalController::class, 'storeBooking'])->name('bookings.store');
    Route::patch('/bookings/{booking}/cancel', [UserPortalController::class, 'cancelBooking'])->name('bookings.cancel');
    Route::get('/complaints', [UserPortalController::class, 'complaints'])->name('complaints');
    Route::post('/complaints', [UserPortalController::class, 'storeComplaint'])->name('complaints.store');
    Route::get('/bookings/{booking}/checkout', [UserPortalController::class, 'checkout'])->name('checkout');
    Route::post('/bookings/{booking}/checkout', [UserPortalController::class, 'storeCheckout'])->name('checkout.store');
    Route::get('/profile', [UserPortalController::class, 'profile'])->name('profile');
});
