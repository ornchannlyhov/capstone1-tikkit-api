<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PurchasedTicketController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketOfferController;
use App\Http\Controllers\TicketOptionController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    return Auth::check()
        ? response()->json(['message' => 'Authenticated', 'redirect' => '/homepage'])
        : response()->json(['message' => 'Welcome']);
});


// Get all Payment Methods
Route::get('/payment-methods', [PaymentMethodController::class, 'index']);

// Authentication Routes
Route::prefix('auth')->group(function () {

    // Social login routes
    Route::get('login/{provider}', [SocialiteController::class, 'redirectToProvider'])->name('social.login');
    Route::get('login/{provider}/callback', [SocialiteController::class, 'handleProviderCallback'])->name('social.callback');

    // Register and Login routes
    Route::post('register', [RegisteredUserController::class, 'store'])->name('register');
    Route::post('login', [AuthenticatedSessionController::class, 'userLogin'])->name('login');

    // Logout route
    Route::post('logout', [AuthenticatedSessionController::class, 'userLogout'])->name('logout');

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

    // User Routes
    Route::prefix('user')->group(function () {
        Route::prefix('profile')->group(function () {
            Route::get('/show', [ProfileController::class, 'edit'])->name('profile.show');
            Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/delete', [ProfileController::class, 'destroy'])->name('profile.delete');
        });
    });

    // Buyer Routes
    Route::prefix('buyer')->group(function () {

        // Event Routes 
        Route::prefix('events')->group(function () {
            // Get all events (filter by status)
            Route::get('/', [EventController::class, 'getEvents'])->name('buyer.events.index');
            // Get events filtered by category
            Route::get('category', [EventController::class, 'getEventsByCategory'])->name('buyer.events.category');
            // Get all tickets (TicketOptions) for a specific event
            Route::get('{eventId}/tickets', [TicketOptionController::class, 'getEventTickets'])->name('buyer.event.tickets');
        });

        // Cart handle
        Route::prefix('cart')->middleware('auth:sanctum')->group(function () {
            Route::post('add', [CartController::class, 'add'])->name('cart.add');
            Route::get('view', [CartController::class, 'view'])->name('cart.view');
            Route::delete('remove', [CartController::class, 'remove'])->name('cart.remove');
            Route::put('update', [CartController::class, 'update'])->name('cart.update');
        });

        // Order Routes (User API)
        Route::prefix('orders')->middleware('auth:sanctum')->group(function () {
            Route::post('/', [OrderController::class, 'store'])->name('user.order.store');
            Route::post('/{id}/cancel', [OrderController::class, 'cancelOrder'])->name('user.order.cancel');
            Route::get('/my-orders', [OrderController::class, 'userOrders'])->name('user.order.all'); 
        });

        // Payment process Routes
        Route::post('/process-payment', [PaymentMethodController::class, 'processPayment']);

        // Get all purchased tickets for the buyer
        Route::get('purchased-tickets', [PurchasedTicketController::class, 'viewPurchasedTicketsForBuyer'])->name('buyer.purchased.tickets');
    });

    // Vendor Routes
    Route::prefix('vendor')->middleware(['vendor'])->group(function () {

        // Vendor Authentication
        Route::post('login', [AuthenticatedSessionController::class, 'vendorLogin'])->withoutMiddleware('vendor')->name('vendor.login');
        Route::post('logout', [AuthenticatedSessionController::class, 'vendorLogout'])->name('vendor.logout');

        // Get all events created by the authenticated vendor
        Route::get('events', [EventController::class, 'getVendorEvents'])->name('vendor.events.index');

        // Validate purchased ticket
        Route::post('validate-ticket', [PurchasedTicketController::class, 'validateQR'])->name('vendor.validate.ticket');

        // View ticket options of their event
        Route::get('/events/{eventId}/ticketOptions', [TicketOptionController::class, 'vendorIndex'])->name('vendor.ticketOptions.index');

        // View offers in each ticket option
        Route::get('/ticketOptions/{ticketOptionId}/ticketOffers', [TicketOfferController::class, 'vendorIndex'])->name('vendor.ticketOffers.index');

        // Vendor requests cancellation of an order
        Route::post('order/{id}/cancel', [OrderController::class, 'cancelOrder'])->name('vendor.order.cancel');

        // Vendor views all their orders
        Route::get('orders', [OrderController::class, 'vendorIndex'])->name('vendor.orders.index');

        // Vendor views a specific order
        Route::get('orders/{id}', [OrderController::class, 'vendorShow'])->name('vendor.orders.show');

        // Vendor accepts a cancellation request
        Route::post('orders/{id}/accept-cancel', [OrderController::class, 'acceptCancellationRequest'])->name('vendor.orders.accept-cancel');

        // Vendor rejects a cancellation request
        Route::post('orders/{id}/reject-cancel', [OrderController::class, 'rejectCancellationRequest'])->name('vendor.orders.reject-cancel');

        // Sale Reports

        Route::get('ticket-sales', [ReportController::class, 'vendorTicketSalesReport']);
    });
});
