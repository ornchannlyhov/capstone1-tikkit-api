<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Login Route
Route::get('/', function () {
    return view('auth.login');
});
Route::post('login', [AuthenticatedSessionController::class, 'adminLogin'])->name('login');

// Admin Routes
Route::prefix('dasboard')->group(function () {

    //User Routes
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

    //Event Routes
    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/', [EventController::class, 'index'])->name('index');
        Route::get('{id}', [EventController::class, 'show'])->name('show');
        Route::get('create', [EventController::class, 'create'])->name('create');
        Route::post('store', [EventController::class, 'store'])->name('store');
        Route::put('{id}', [EventController::class, 'update'])->name('update');
        Route::delete('{id}', [EventController::class, 'destroy'])->name('destroy');
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

});

require __DIR__ . '/auth.php';

