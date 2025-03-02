<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PurchasedTicketController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    return Auth::check()
        ? response()->json(['message' => 'Authenticated', 'redirect' => '/homepage'])
        : response()->json(['message' => 'Welcome']);
});

// Authentication Routes
Route::prefix('auth')->group(function () {

    // Social login routes
    Route::get('login/{provider}', [SocialiteController::class, 'redirectToProvider'])->name('social.login');
    Route::get('login/{provider}/callback', [SocialiteController::class, 'handleProviderCallback'])->name('social.callback');

    // Register and Login routes
    Route::post('register', [RegisteredUserController::class, 'store'])->name('register');
    Route::post('login', [AuthenticatedSessionController::class, 'userLogin'])->name('login');

    // Logout route
    Route::post('logout', [AuthenticatedSessionController::class, 'apiLogout'])->name('logout');

    // Password reset routes
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');

    // Email verification routes
    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

// Protected Routes (requires authentication)
Route::middleware(['auth:sanctum'])->group(function () {

    // Fetch authenticated user
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Profile Routes
    Route::prefix('profile')->group(function () {
        Route::get('/show', [ProfileController::class, 'edit'])->name('profile.show');
        Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/delete', [ProfileController::class, 'destroy'])->name('profile.delete');
    });

    // Buyer Routes
    Route::prefix('buyer')->group(function () {
        // Get all active events
        Route::get('events', [EventController::class, 'getActiveEvents'])->name('events.index');

        // Search for events
        Route::get('events/search', [EventController::class, 'search'])->name('events.search');

        // Get all purchased tickets for the buyer
        Route::get('purchased-tickets', [PurchasedTicketController::class, 'viewPurchasedTicketsForBuyer'])->name('api.view.purchased.tickets');

        // Validate purchased ticket
        Route::post('validate-ticket', [PurchasedTicketController::class, 'validateQR'])->name('api.validate.ticket');
    });
});
