<?php
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PurchasedTicketController;
use App\Http\Controllers\TicketOfferController;
use App\Http\Controllers\TicketOptionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AddressController;

// Login Routes
Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('admin/login', [AuthenticatedSessionController::class, 'adminLogin'])->name('admin.login');
Route::post('admin/logout', [AuthenticatedSessionController::class, 'adminLogout'])->name('admin.logout');

// Admin Routes
Route::prefix('dashboard')->middleware(['admin'])->group(function () {
    // User Routes
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create/{role}', [UserController::class, 'create'])->name('create');
        Route::post('/store/{role}', [UserController::class, 'store'])->name('store');
        Route::get('/{id}', [UserController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-ban', [UserController::class, 'toggleBan'])->name('toggleBan');
        Route::get('/search', [UserController::class, 'search'])->name('search');
    });

    // Vendor Routes
    Route::prefix('vendors')->name('vendors.')->group(function () {
        Route::get('/', [UserController::class, 'vendorIndex'])->name('index');
        Route::get('/create', [UserController::class, 'vendorCreate'])->name('create');
        Route::post('/store', [UserController::class, 'vendorStore'])->name('store');
        Route::get('/{id}/edit', [UserController::class, 'vendorEdit'])->name('edit');
        Route::put('/{id}', [UserController::class, 'vendorUpdate'])->name('update');
        Route::delete('/{id}', [UserController::class, 'vendorDestroy'])->name('destroy');
    });

    // Category Routes
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('create', [CategoryController::class, 'create'])->name('create');
        Route::post('store', [CategoryController::class, 'store'])->name('store');
        Route::get('{category}', [CategoryController::class, 'show'])->name('show');
        Route::get('{category}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('{category}', [CategoryController::class, 'destroy'])->name('destroy');
    });

    // Event Routes
    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/', [EventController::class, 'index'])->name('index');
        Route::get('{id}', [EventController::class, 'show'])->name('show');
        Route::get('create', [EventController::class, 'create'])->name('create');
        Route::post('store', [EventController::class, 'store'])->name('store');
        Route::put('{id}', [EventController::class, 'update'])->name('update');
        Route::delete('{id}', [EventController::class, 'destroy'])->name('destroy');
        Route::get('search', [EventController::class, 'search'])->name('search');
        Route::post('{id}/toggle-public', [EventController::class, 'togglePublic'])->name('togglePublic');
    });

    // Address Routes
    Route::prefix('addresses')->name('addresses.')->group(function () {
        Route::get('/', [AddressController::class, 'index'])->name('index');
        Route::get('create', [AddressController::class, 'create'])->name('create');
        Route::post('store', [AddressController::class, 'store'])->name('store');
        Route::get('{address}', [AddressController::class, 'show'])->name('show');
        Route::get('{address}/edit', [AddressController::class, 'edit'])->name('edit');
        Route::put('{address}', [AddressController::class, 'update'])->name('update');
        Route::delete('{address}', [AddressController::class, 'destroy'])->name('destroy');
        Route::patch('{address}/assign-revoke', [AddressController::class, 'toggleEventAssignment'])->name('toggleEventAssignment');
    });

    // TicketOption Routes
    Route::prefix('ticket-options')->name('ticketOptions.')->group(function () {
        Route::get('event/{id}', [TicketOptionController::class, 'index'])->name('index');
        Route::get('event/{id}/create', [TicketOptionController::class, 'create'])->name('create');
        Route::post('event/{id}/store', [TicketOptionController::class, 'store'])->name('store');
        Route::get('edit/{id}', [TicketOptionController::class, 'edit'])->name('edit');
        Route::put('update/{id}', [TicketOptionController::class, 'update'])->name('update');
        Route::delete('destroy/{id}', [TicketOptionController::class, 'destroy'])->name('destroy');
    });

    // TicketOffer Routes
    Route::prefix('ticket-offers')->name('ticketOffers.')->group(function () {
        Route::get('ticket-option/{ticketOptionId}', [TicketOfferController::class, 'index'])->name('index');
        Route::get('ticket-option/{ticketOptionId}/create', [TicketOfferController::class, 'create'])->name('create');
        Route::post('ticket-option/{ticketOptionId}/store', [TicketOfferController::class, 'store'])->name('store');
        Route::get('edit/{ticketOfferId}', [TicketOfferController::class, 'edit'])->name('edit');
        Route::put('update/{ticketOfferId}', [TicketOfferController::class, 'update'])->name('update');
        Route::delete('destroy/{ticketOfferId}', [TicketOfferController::class, 'destroy'])->name('destroy');
    });

    // Purchased Ticket Route

    Route::get('purchased-tickets/{ticketOptionId}', [PurchasedTicketController::class, 'viewPurchasedTicketsForAdmin'])->name('purchasedTickets.index');
});

require __DIR__ . '/auth.php';
