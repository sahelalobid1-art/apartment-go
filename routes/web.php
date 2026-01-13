<?php

use App\Http\Controllers\Web\AdminController;
use Illuminate\Support\Facades\Route;


Route::prefix('admin')->name('admin.')->group(function () {
    // Authentication Routes
    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminController::class, 'login']);

    // Protected Admin Routes
    Route::middleware(['admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

        // User Management Pages
        Route::get('/users/pending', [AdminController::class, 'pendingUsers'])->name('pending.users');
        Route::get('/users/all', [AdminController::class, 'allUsers'])->name('all.users');

        // User Actions
        Route::post('/users/{id}/approve', [AdminController::class, 'approveUser'])->name('users.approve');
        Route::post('/users/{id}/reject', [AdminController::class, 'rejectUser'])->name('users.reject');
        Route::delete('/users/{id}/delete', [AdminController::class, 'deleteUser'])->name('users.delete');
        Route::get('/users/{id}/details', [AdminController::class, 'userDetails'])->name('users.details');
    });
});
