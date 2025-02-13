<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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
});

require __DIR__ . '/auth.php';


});