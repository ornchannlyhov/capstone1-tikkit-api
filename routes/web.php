<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Login Route
Route::get('/', function () {
    return view('auth.login');
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
    
});

require __DIR__ . '/auth.php';

