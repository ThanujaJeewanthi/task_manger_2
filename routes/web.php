<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers;

use App\Http\Controllers\Dashboard\CommonDashboardController;
use App\Http\Controllers\Dashboard\ClientDashboardController;
use App\Http\Controllers\Dashboard\LaundryDashboardController;
use App\Http\Controllers\Dashboard\RiderDashboardController;
use App\Http\Controllers\Dashboard\AdminDashboardController;




Route::middleware(['auth', 'role.access:client'])->group(function () {
    // Only 'client' role can access these pages

    Route::get('/dashboard/client', [ClientDashboardController::class, 'index']);

});
Route::middleware(['auth', 'role.access:laundry'])->group(function () {
    // Only 'laundry' role can access these pages

    Route::get('/dashboard/laundry', [LaundryDashboardController::class, 'index']);

});
Route::middleware(['auth', 'role.access:rider'])->group(function () {
    // Only 'rider' role can access these pages

    Route::get('/dashboard/rider', [RiderDashboardController::class, 'index']);

});
Route::middleware(['auth', 'role.access:admin'])->group(function () {
    // Only 'admin' role can access these pages

    Route::get('/dashboard/admin', [AdminDashboardController::class, 'index']);

});
Route::middleware(['auth'])->group(function () {
    // Common routes for all roles

    Route::get('/dashboard', [CommonDashboardController::class, 'index']) ->name('dashboard');
    Route::get('/', [CommonDashboardController::class, 'index'])->name('home');
});

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Public routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register.form');
Route::post('/register', [RegisterController::class, 'register'])->name('register');

// Logout route
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
