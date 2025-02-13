<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\UserController;

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});
Route::post('login', [AuthenticatedSessionController::class, 'adminLogin'])->name('login');

// Admin Routes
Route::prefix('admin')->group(function () {

    //User Routes
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('create/{role}', [UserController::class, 'create'])->name('create');
        Route::post('store/{role}', [UserController::class, 'store'])->name('store');
        Route::get('{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('{user}', [UserController::class, 'update'])->name('update');
        Route::delete('{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::patch('{user}/toggle-ban', [UserController::class, 'toggleBan'])->name('toggleBan');
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


