<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\OfferController as AdminOfferController;
use App\Http\Controllers\Admin\BrandingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/offers/{offer}', [HomeController::class, 'showOffer'])->name('offers.show');

// Booking Routes
Route::prefix('bookings')->name('bookings.')->group(function () {
    Route::get('/{offer}/create', [BookingController::class, 'create'])->name('create');
    Route::post('/{offer}', [BookingController::class, 'store'])->name('store');
    Route::get('/{booking}/confirmation', [BookingController::class, 'confirmation'])->name('confirmation');
    Route::post('/{booking}/cancel', [BookingController::class, 'cancel'])->name('cancel');
    Route::get('/{offer}/available-slots', [BookingController::class, 'getAvailableSlots'])->name('available-slots');
});

// Payment Routes
Route::prefix('payment')->name('payment.')->group(function () {
    Route::get('/success', [PaymentController::class, 'success'])->name('success');
    Route::get('/cancel', [PaymentController::class, 'cancel'])->name('cancel');
    Route::get('/status', [PaymentController::class, 'status'])->name('status');
});

// Stripe Webhook (exclude from CSRF protection)
Route::post('/webhook/stripe', [PaymentController::class, 'webhook'])->name('webhook.stripe');

// Admin Routes
Route::prefix('admin')
    ->as('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::prefix('bookings')->as('bookings.')->group(function () {
            Route::get('/', [AdminBookingController::class, 'index'])->name('index');
            Route::get('/calendar', [AdminBookingController::class, 'calendar'])->name('calendar');
            Route::get('/calendar/events', [AdminBookingController::class, 'getCalendarEvents'])->name('calendar.events');
            Route::get('/{booking}', [AdminBookingController::class, 'show'])->name('show');
            Route::post('/{booking}/reschedule', [AdminBookingController::class, 'reschedule'])->name('reschedule');
            Route::post('/{booking}/update-status', [AdminBookingController::class, 'updateStatus'])->name('update-status');
            Route::post('/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('cancel');
            Route::delete('/{booking}', [AdminBookingController::class, 'destroy'])->name('destroy');
        });

        Route::resource('offers', AdminOfferController::class);
        Route::post('/offers/{offer}/toggle-status', [AdminOfferController::class, 'toggleStatus'])->name('offers.toggle-status');

        Route::get('/branding', [BrandingController::class, 'edit'])->name('branding.edit');
        Route::post('/branding', [BrandingController::class, 'update'])->name('branding.update');
    });

Route::get('/health', HealthController::class)->name('health');

Route::get('/login', [AuthController::class, 'showClientLogin'])
    ->name('login')
    ->middleware('guest');

Route::post('/login', [AuthController::class, 'loginClient'])
    ->name('login.post')
    ->middleware(['guest', 'throttle:login']);

Route::get('/client/login', [AuthController::class, 'showClientLogin'])
    ->name('client.login')
    ->middleware('guest');

Route::post('/client/login', [AuthController::class, 'loginClient'])
    ->name('client.login.post')
    ->middleware(['guest', 'throttle:login']);

Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])
    ->name('admin.login')
    ->middleware('guest');

Route::post('/admin/login', [AuthController::class, 'loginAdmin'])
    ->name('admin.login.post')
    ->middleware(['guest', 'throttle:login']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

require __DIR__.'/auth.php';
